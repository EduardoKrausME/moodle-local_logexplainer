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
use context_module;
use core\report_helper;
use invalid_parameter_exception;
use moodle_exception;
use required_capability_exception;
use stdClass;

/**
 * Access checks for log analysis.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access {
    /**
     * Require both the plugin capability and Moodle's real log-report capability.
     *
     * @param context_course $context Course context.
     * @return void
     */
    public static function require_course_access(context_course $context): void {
        require_capability('local/logexplainer:view', $context);
        require_capability('report/log:view', $context);
        if (!report_helper::has_valid_group($context)) {
            throw new moodle_exception('notingroup');
        }
    }

    /**
     * Validate a target user against course enrollment/viewing and group visibility.
     *
     * @param stdClass $course Course record.
     * @param int $userid Target user id.
     * @return void
     */
    public static function require_target_user(stdClass $course, int $userid): void {
        global $DB;

        if ($userid <= 0) {
            throw new invalid_parameter_exception('A valid target user is required.');
        }
        $user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', MUST_EXIST);
        $context = context_course::instance($course->id);

        if (!is_enrolled($context, $user) && !is_viewing($context, $user)) {
            throw new required_capability_exception($context, 'report/log:view', 'nopermissions', '');
        }
        if (!groups_user_groups_visible($course, $userid)) {
            throw new required_capability_exception($context, 'moodle/site:accessallgroups', 'nopermissions', '');
        }
    }

    /**
     * Validate that an activity belongs to the selected course.
     *
     * @param int $courseid Course id.
     * @param int $cmid Course module id.
     * @return context_module Module context.
     */
    public static function require_activity(int $courseid, int $cmid): context_module {
        $cm = get_coursemodule_from_id('', $cmid, $courseid, false, MUST_EXIST);
        return context_module::instance($cm->id);
    }

    /**
     * Validate target-user visibility for an activity's effective group mode.
     *
     * @param int $courseid Course id.
     * @param int $cmid Course module id.
     * @param int $userid Target user id.
     * @return void
     */
    public static function require_activity_target_user(int $courseid, int $cmid, int $userid): void {
        $modinfo = get_fast_modinfo($courseid);
        $cm = $modinfo->get_cm($cmid);
        $context = context_module::instance($cmid);

        $groupmode = (int) groups_get_activity_groupmode($cm);
        if ($groupmode !== SEPARATEGROUPS || has_capability('moodle/site:accessallgroups', $context)) {
            return;
        }

        $sharedusers = groups_get_activity_shared_group_members($cm);
        if (!isset($sharedusers[$userid])) {
            throw new required_capability_exception($context, 'moodle/site:accessallgroups', 'nopermissions', '');
        }
    }

    /**
     * Whether the current user may see a target user in a selector.
     *
     * @param stdClass $course Course record.
     * @param int $userid Target user id.
     * @return bool
     */
    public static function target_user_visible(stdClass $course, int $userid): bool {
        return groups_user_groups_visible($course, $userid);
    }
}
