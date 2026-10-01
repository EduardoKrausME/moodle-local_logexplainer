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

/**
 * Tests for the log repository.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_logexplainer;

use advanced_testcase;
use ArrayIterator;
use context_course;
use core\event\base;
use core\event\course_viewed;
use core\log\sql_reader;
use stdClass;
use Traversable;

/**
 * Small SQL reader used by repository unit tests.
 */
class fake_sql_reader implements sql_reader {
    /** @var base Event returned by the reader. */
    private base $event;

    /**
     * Constructor.
     *
     * @param base $event Event.
     */
    public function __construct(base $event) {
        $this->event = $event;
    }

    /**
     * Method get_name.
     *
     * @return mixed Return value.
     */
    public function get_name() {
        return 'Fake';
    }

    /**
     * Method get_description.
     *
     * @return mixed Return value.
     */
    public function get_description() {
        return 'Fake reader';
    }

    /**
     * Method is_logging.
     *
     * @return mixed Return value.
     */
    public function is_logging() {
        return true;
    }

    /**
     * Return events.
     *
     * @param string $selectwhere SQL selector.
     * @param array $params Parameters.
     * @param string $sort Sort.
     * @param int $limitfrom Offset.
     * @param int $limitnum Limit.
     * @return base[]
     */
    public function get_events_select($selectwhere, array $params, $sort, $limitfrom, $limitnum) {
        return [$this->event];
    }

    /**
     * Return event count.
     *
     * @param string $selectwhere SQL selector.
     * @param array $params Parameters.
     * @return int
     */
    public function get_events_select_count($selectwhere, array $params) {
        return 1;
    }

    /**
     * Return existence.
     *
     * @param string $selectwhere SQL selector.
     * @param array $params Parameters.
     * @return bool
     */
    public function get_events_select_exists(string $selectwhere, array $params): bool {
        return true;
    }

    /**
     * Return iterator.
     *
     * @param string $selectwhere SQL selector.
     * @param array $params Parameters.
     * @param string $sort Sort.
     * @param int $limitfrom Offset.
     * @param int $limitnum Limit.
     * @return Traversable
     */
    public function get_events_select_iterator($selectwhere, array $params, $sort, $limitfrom, $limitnum) {
        return new ArrayIterator([$this->event]);
    }

    /**
     * Convert raw record into event.
     *
     * @param stdClass $data Raw record.
     * @return base
     */
    public function get_log_event($data) {
        return $this->event;
    }
}

/**
 * Repository tests.
 *
 * @covers \local_logexplainer\log_repository
 */
final class log_repository_test extends advanced_testcase {
    /**
     * Activity filters use the same context fields as the core log report.
     */
    public function test_build_select_for_user_activity_uses_core_context_fields(): void {
        $query = new query(10, query::MODE_USER_ACTIVITY, 20, 30, 100, 200);
        [$sql, $params] = log_repository::build_select($query);

        $this->assertStringContainsString('courseid = :courseid', $sql);
        $this->assertStringContainsString('userid = :userid', $sql);
        $this->assertStringContainsString('contextinstanceid = :contextinstanceid', $sql);
        $this->assertStringContainsString('contextlevel = :contextlevel', $sql);
        $this->assertSame(CONTEXT_MODULE, $params['contextlevel']);
        $this->assertSame(30, $params['contextinstanceid']);
    }

    /**
     * Stored event classes have a leading backslash.
     */
    public function test_build_select_for_event_normalises_leading_backslash(): void {
        $query = new query(10, query::MODE_EVENT, null, null, 100, 200, 'mod_quiz\\event\\attempt_submitted');
        [$sql, $params] = log_repository::build_select($query);

        $this->assertStringContainsString('eventname = :eventname', $sql);
        $this->assertSame('\\mod_quiz\\event\\attempt_submitted', $params['eventname']);
    }

    /**
     * Group visibility ids are translated into a portable userid IN filter.
     */
    public function test_build_select_applies_visible_user_ids(): void {
        $query = new query(10, query::MODE_SUMMARY, null, null, 100, 200);
        [$sql, $params] = log_repository::build_select($query, [21, 22]);

        $this->assertStringContainsString('userid IN', $sql);
        $this->assertContains(21, $params);
        $this->assertContains(22, $params);
    }

    /**
     * An empty visibility set must produce no events.
     */
    public function test_build_select_with_empty_visible_user_ids_is_false(): void {
        $query = new query(10, query::MODE_SUMMARY, null, null, 100, 200);
        [$sql] = log_repository::build_select($query, []);
        $this->assertStringContainsString('1 = 0', $sql);
    }

    /**
     * Events from a SQL reader are formatted into evidence records.
     */
    public function test_fetch_formats_events_from_sql_reader(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $event = course_viewed::create(['context' => context_course::instance($course->id)]);

        $repository = new log_repository(new fake_sql_reader($event));
        $query = new query($course->id, query::MODE_USER_COURSE, $user->id, null, 0, time() + 10);
        $events = $repository->fetch($query, 10);

        $this->assertCount(1, $events);
        $this->assertSame('E1', $events[0]['id']);
        $this->assertSame('selected_user', $events[0]['actor']);
        $this->assertArrayNotHasKey('ip', $events[0]);
    }
}
