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
 * Tests for event formatting and data minimisation.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_logexplainer;

use advanced_testcase;
use context_course;
use core\event\course_viewed;

/**
 * Event formatter tests.
 *
 * @package local_logexplainer
 * @covers \local_logexplainer\event_formatter
 */
final class event_formatter_test extends advanced_testcase {
    /**
     * Method test_ai_payload_excludes_ip_and_other.
     *
     * @return void Return value.
     */
    public function test_ai_payload_excludes_ip_and_other(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $event = course_viewed::create(['context' => context_course::instance($course->id)]);
        $formatted = event_formatter::format($event, $user->id, 1);
        $payload = event_formatter::for_ai($formatted);

        $this->assertSame('E1', $payload['id']);
        $this->assertSame('selected_user', $payload['actor']);
        $this->assertArrayNotHasKey('ip', $payload);
        $this->assertArrayNotHasKey('other', $payload);

        $formatted['description'] = 'Connection from 192.168.10.20 and 2001:db8::1.';
        $redacted = event_formatter::for_ai($formatted);
        $this->assertStringNotContainsString('192.168.10.20', $redacted['description']);
        $this->assertStringNotContainsString('2001:db8::1', $redacted['description']);
    }
}
