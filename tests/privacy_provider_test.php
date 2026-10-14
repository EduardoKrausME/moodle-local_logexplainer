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
use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\provider as metadata_provider;
use core_privacy\local\metadata\types\external_location;
use core_privacy\local\request\plugin\provider as request_provider;
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
     * The provider declares the external AI transfer instead of claiming no personal data.
     */
    public function test_provider_exposes_external_location_metadata(): void {
        $reflection = new ReflectionClass(provider::class);
        $this->assertTrue($reflection->implementsInterface(metadata_provider::class));
        $this->assertTrue($reflection->implementsInterface(request_provider::class));

        $collection = provider::get_metadata(new collection('local_logexplainer'));
        $locations = $collection->get_collection();
        $this->assertCount(1, $locations);
        $this->assertInstanceOf(external_location::class, $locations[0]);
        $this->assertSame('aiprovider', $locations[0]->get_name());
        $this->assertSame('privacy:metadata:aiprovider', $locations[0]->get_summary());

        // Detect regressions when fields are added to the AI payload without privacy metadata.
        $event = array_fill_keys([
            'id', 'timestamp', 'time', 'eventname', 'eventclass', 'component', 'action',
            'target', 'objectid', 'context', 'relateduserid', 'origin', 'actor', 'description',
        ], '');
        $this->assertSame(
            array_keys(event_formatter::for_ai($event)),
            array_keys($locations[0]->get_privacy_fields())
        );
        foreach ($locations[0]->get_privacy_fields() as $stringid) {
            $this->assertTrue(get_string_manager()->string_exists($stringid, 'local_logexplainer'));
        }
        $this->assertTrue(get_string_manager()->string_exists('privacy:metadata:aiprovider', 'local_logexplainer'));
    }

    /**
     * Moodle remains the owner of the original log data and the plugin stores no user records.
     */
    public function test_no_plugin_owned_user_data(): void {
        $this->assertSame([], provider::get_contexts_for_userid(42)->get_contextids());
    }
}
