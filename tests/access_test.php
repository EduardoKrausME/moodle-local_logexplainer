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
 * Tests for capability, user, group, and context access.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_logexplainer;

use advanced_testcase;
use context_course;
use dml_missing_record_exception;
use required_capability_exception;

/**
 * Access tests.
 *
 * @package local_logexplainer
 * @covers \local_logexplainer\access
 */
final class access_test extends advanced_testcase {
    /**
     * Method test_course_access_requires_native_log_capability_too.
     *
     * @return void Return value.
     */
    public function test_course_access_requires_native_log_capability_too(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $context = context_course::instance($course->id);
        $roleid = $this->getDataGenerator()->create_role();
        $this->getDataGenerator()->role_assign($roleid, $user->id, $context->id);
        assign_capability('local/logexplainer:view', CAP_ALLOW, $roleid, $context->id);
        unassign_capability('report/log:view', $roleid, $context->id);
        $this->setUser($user);

        $this->expectException(required_capability_exception::class);
        access::require_course_access($context);
    }

    /**
     * Method test_course_access_succeeds_with_both_capabilities.
     *
     * @return void Return value.
     */
    public function test_course_access_succeeds_with_both_capabilities(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $context = context_course::instance($course->id);
        $roleid = $this->getDataGenerator()->create_role();
        $this->getDataGenerator()->role_assign($roleid, $user->id, $context->id);
        assign_capability('local/logexplainer:view', CAP_ALLOW, $roleid, $context->id);
        assign_capability('report/log:view', CAP_ALLOW, $roleid, $context->id);
        $this->setUser($user);

        access::require_course_access($context);
        $this->assertTrue(true);
    }

    /**
     * Method test_target_user_must_belong_to_course.
     *
     * @return void Return value.
     */
    public function test_target_user_must_belong_to_course(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $viewer = $this->getDataGenerator()->create_user();
        $target = $this->getDataGenerator()->create_user();
        $this->setUser($viewer);

        $this->expectException(required_capability_exception::class);
        access::require_target_user($course, $target->id);
    }


    /**
     * Method test_target_user_respects_separate_groups.
     *
     * @return void Return value.
     */
    public function test_target_user_respects_separate_groups(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['groupmode' => SEPARATEGROUPS]);
        $viewer = $this->getDataGenerator()->create_user();
        $target = $this->getDataGenerator()->create_user();
        $studentrole = $this->getDataGenerator()->create_role();
        $context = context_course::instance($course->id);
        $this->getDataGenerator()->enrol_user($viewer->id, $course->id, $studentrole);
        $this->getDataGenerator()->enrol_user($target->id, $course->id, $studentrole);
        $context = context_course::instance($course->id);
        assign_capability('moodle/site:accessallgroups', CAP_PROHIBIT, $studentrole, $context->id);
        $groupa = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $groupb = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member($groupa, $viewer);
        groups_add_member($groupb, $target);
        $this->setUser($viewer);

        $this->expectException(required_capability_exception::class);
        access::require_target_user($course, $target->id);
    }

    /**
     * Activity-level separate groups also constrain the selected target user.
     */
    public function test_activity_target_user_respects_effective_group_mode(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $viewer = $this->getDataGenerator()->create_user();
        $target = $this->getDataGenerator()->create_user();
        $studentrole = $this->getDataGenerator()->create_role();
        $this->getDataGenerator()->enrol_user($viewer->id, $course->id, $studentrole);
        $this->getDataGenerator()->enrol_user($target->id, $course->id, $studentrole);
        $groupa = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $groupb = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member($groupa, $viewer);
        groups_add_member($groupb, $target);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        set_coursemodule_groupmode($forum->cmid, SEPARATEGROUPS);
        $modulecontext = \context_module::instance($forum->cmid);
        assign_capability('moodle/site:accessallgroups', CAP_PROHIBIT, $studentrole, $modulecontext->id);
        $this->setUser($viewer);

        $cm = get_fast_modinfo($course)->get_cm($forum->cmid);
        $this->assertSame(SEPARATEGROUPS, (int) groups_get_activity_groupmode($cm));
        $this->assertFalse(has_capability('moodle/site:accessallgroups', $modulecontext));

        $this->expectException(required_capability_exception::class);
        access::require_activity_target_user($course->id, $forum->cmid, $target->id);
    }

    /**
     * Method test_activity_context_must_belong_to_selected_course.
     *
     * @return void Return value.
     */
    public function test_activity_context_must_belong_to_selected_course(): void {
        $this->resetAfterTest();
        $coursea = $this->getDataGenerator()->create_course();
        $courseb = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $courseb->id]);

        $this->expectException(dml_missing_record_exception::class);
        access::require_activity($coursea->id, $page->cmid);
    }
}
