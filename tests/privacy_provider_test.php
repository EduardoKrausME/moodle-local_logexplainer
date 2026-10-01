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
 * Tests for the privacy provider.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_logexplainer;

use advanced_testcase;
use core_privacy\local\metadata\null_provider;
use local_logexplainer\privacy\provider;
use ReflectionClass;

/**
 * Privacy provider tests.
 *
 * @package local_logexplainer
 * @covers \local_logexplainer\privacy\provider
 */
final class privacy_provider_test extends advanced_testcase {
    /**
     * Method test_provider_is_null_provider.
     *
     * @return void Return value.
     */
    public function test_provider_is_null_provider(): void {
        $provider = new ReflectionClass(provider::class);
        $this->assertTrue($provider->implementsInterface(null_provider::class));
        $this->assertSame('privacy:metadata', provider::get_reason());
    }
}
