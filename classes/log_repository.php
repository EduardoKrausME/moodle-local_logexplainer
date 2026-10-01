<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_logexplainer;

use context_course;
use core\event\base;
use core\log\sql_reader;
use core\report_helper;
use moodle_exception;

/**
 * Read Moodle logs through the Logging API.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class log_repository {
    /** @var sql_reader */
    private sql_reader $reader;

    /**
     * Constructor.
     *
     * @param sql_reader|null $reader Optional reader for tests.
     */
    public function __construct(?sql_reader $reader = null) {
        $this->reader = $reader ?? self::default_reader();
    }

    /**
     * Return an enabled SQL log reader, mirroring the core report's supported API.
     *
     * @return sql_reader
     */
    public static function default_reader(): sql_reader {
        $readers = get_log_manager()->get_readers('core\\log\\sql_reader');
        if (!$readers) {
            throw new moodle_exception('nologreader', 'local_logexplainer');
        }
        $reader = reset($readers);
        if (!$reader instanceof sql_reader) {
            throw new moodle_exception('nologreader', 'local_logexplainer');
        }
        return $reader;
    }

    /**
     * Fetch and format events.
     *
     * @param query $query Query.
     * @param int $limit Event cap.
     * @return array
     */
    public function fetch(query $query, int $limit): array {
        $limit = max(1, min(500, $limit));
        $visibleuserids = $this->visible_user_ids($query);
        [$select, $params] = self::build_select($query, $visibleuserids);
        $events = $this->reader->get_events_select($select, $params, 'timecreated ASC', 0, $limit);

        $formatted = [];
        $sequence = 1;
        foreach ($events as $event) {
            if (!$event instanceof base) {
                continue;
            }
            $formatted[] = event_formatter::format($event, $query->userid, $sequence++);
        }
        return $formatted;
    }

    /**
     * Count matching events without loading them.
     *
     * @param query $query Query.
     * @return int
     */
    public function count(query $query): int {
        $visibleuserids = $this->visible_user_ids($query);
        [$select, $params] = self::build_select($query, $visibleuserids);
        return (int)$this->reader->get_events_select_count($select, $params);
    }

    /**
     * Resolve user ids visible through Moodle's group rules when there is no explicit target user.
     *
     * @param query $query Query.
     * @return int[]|null Null means no additional user filter is needed.
     */
    private function visible_user_ids(query $query): ?array {
        if ($query->userid !== null) {
            return null;
        }

        $course = get_course($query->courseid);
        $coursecontext = context_course::instance($query->courseid);
        if (has_capability('moodle/site:accessallgroups', $coursecontext)) {
            return null;
        }

        if ($query->mode === query::MODE_USER_ACTIVITY && $query->cmid) {
            $cm = get_fast_modinfo($course)->get_cm($query->cmid);
            if ($cm->effectivegroupmode !== SEPARATEGROUPS) {
                return null;
            }
            return array_map('intval', array_keys(groups_get_activity_shared_group_members($cm)));
        }

        if (groups_get_course_groupmode($course) !== SEPARATEGROUPS) {
            return null;
        }

        $groupfilter = report_helper::get_group_filter((object)[
            'courseid' => $query->courseid,
            'userid' => 0,
            'groupid' => 0,
        ]);
        if ($groupfilter['useridfilter'] === null) {
            return null;
        }
        return array_map('intval', array_keys($groupfilter['useridfilter']));
    }

    /**
     * Build the SQL-reader portable selector.
     *
     * @param query $query Query.
     * @param int[]|null $visibleuserids Optional current-user visibility filter.
     * @return array{0:string,1:array}
     */
    public static function build_select(query $query, ?array $visibleuserids = null): array {
        $where = [
            'courseid = :courseid',
            'anonymous = :anonymous',
            'timecreated >= :fromtime',
            'timecreated <= :totime',
        ];
        $params = [
            'courseid' => $query->courseid,
            'anonymous' => 0,
            'fromtime' => $query->from,
            'totime' => $query->to,
        ];

        if ($query->userid) {
            $where[] = 'userid = :userid';
            $params['userid'] = $query->userid;
        } else if ($visibleuserids !== null) {
            if (!$visibleuserids) {
                $where[] = '1 = 0';
            } else {
                global $DB;
                [$insql, $inparams] = $DB->get_in_or_equal(
                    array_values($visibleuserids),
                    SQL_PARAMS_NAMED,
                    'visibleuser'
                );
                $where[] = "userid {$insql}";
                $params += $inparams;
            }
        }

        if ($query->mode === query::MODE_USER_ACTIVITY && $query->cmid) {
            $where[] = 'contextinstanceid = :contextinstanceid';
            $where[] = 'contextlevel = :contextlevel';
            $params['contextinstanceid'] = $query->cmid;
            $params['contextlevel'] = CONTEXT_MODULE;
        }

        if ($query->mode === query::MODE_EVENT && $query->eventname !== '') {
            $eventname = $query->eventname;
            if ($eventname[0] !== '\\') {
                $eventname = '\\' . $eventname;
            }
            $where[] = 'eventname = :eventname';
            $params['eventname'] = $eventname;
        }

        return [implode(' AND ', $where), $params];
    }
}
