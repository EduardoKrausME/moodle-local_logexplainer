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

namespace local_logexplainer\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\provider as metadata_provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\plugin\provider as request_provider;

/**
 * Describe the event data transferred to an AI provider without retaining local copies.
 *
 * Moodle owns and manages the original log records. This plugin only reads them on
 * demand and passes selected fields to local_ai_bridge, which calls the configured
 * AI provider. No user data, prompts, or model responses are stored by this plugin.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements metadata_provider, request_provider {
    /**
     * Declare the event fields sent to the configured external AI provider.
     *
     * Keep the field list in sync with event_formatter::for_ai(). In particular,
     * descriptions and related-user/object IDs can identify course participants.
     *
     * @param collection $collection Initial privacy metadata collection.
     * @return collection Populated collection.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_external_location_link('aiprovider', [
            'id' => 'privacy:metadata:aiprovider:id',
            'timestamp' => 'privacy:metadata:aiprovider:timestamp',
            'time' => 'privacy:metadata:aiprovider:time',
            'eventname' => 'privacy:metadata:aiprovider:eventname',
            'eventclass' => 'privacy:metadata:aiprovider:eventclass',
            'component' => 'privacy:metadata:aiprovider:component',
            'action' => 'privacy:metadata:aiprovider:action',
            'target' => 'privacy:metadata:aiprovider:target',
            'objectid' => 'privacy:metadata:aiprovider:objectid',
            'context' => 'privacy:metadata:aiprovider:context',
            'relateduserid' => 'privacy:metadata:aiprovider:relateduserid',
            'origin' => 'privacy:metadata:aiprovider:origin',
            'actor' => 'privacy:metadata:aiprovider:actor',
            'description' => 'privacy:metadata:aiprovider:description',
        ], 'privacy:metadata:aiprovider');

        // The local_ai_bridge plugin transports the payload but does not store it here.
        // No add_plugin_link() API exists in the supported Moodle privacy collection.
        return $collection;
    }

    /**
     * No per-user contexts are stored by this plugin; Moodle owns the source logs.
     *
     * @param int $userid User ID.
     * @return contextlist Empty list.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        return new contextlist();
    }

    /**
     * There is no plugin-owned data to export.
     *
     * @param approved_contextlist $contextlist Approved contexts.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
    }

    /**
     * There is no plugin-owned data to delete from a context.
     *
     * @param \context $context Context to clear.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
    }

    /**
     * There is no plugin-owned data to delete for an individual user.
     *
     * @param approved_contextlist $contextlist Approved contexts.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
    }
}
