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

use context;
use core\event\base;
use moodle_url;
use Throwable;

/**
 * Convert Moodle events into minimal factual records.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_formatter {
    /**
     * Format one event.
     *
     * @param base $event Event.
     * @param int|null $selecteduserid Selected target user, used only to pseudonymise the actor.
     * @param int $sequence 1-based factual sequence id.
     * @return array
     */
    public static function format(base $event, ?int $selecteduserid, int $sequence): array {
        $contextname = '';
        try {
            $context = context::instance_by_id((int)$event->contextid, IGNORE_MISSING);
            if ($context) {
                $contextname = $context->get_context_name(false);
            }
        } catch (Throwable $e) {
            $contextname = '';
        }

        $description = '';
        try {
            $description = clean_param((string)$event->get_description(), PARAM_TEXT);
        } catch (Throwable $e) {
            $description = '';
        }

        $name = (string)$event->eventname;
        try {
            $name = (string)$event->get_name();
        } catch (Throwable $e) {
            $name = (string)$event->eventname;
        }

        $url = '';
        try {
            $eventurl = $event->get_url();
            if ($eventurl instanceof moodle_url) {
                $url = $eventurl->out(false);
            }
        } catch (Throwable $e) {
            $url = '';
        }

        $origin = '';
        try {
            $logextra = $event->get_logextra();
            if (is_array($logextra) && isset($logextra['origin'])) {
                $origin = (string)$logextra['origin'];
            }
        } catch (Throwable $e) {
            $origin = '';
        }

        $actor = 'other_user';
        if ((int)$event->userid === 0) {
            $actor = 'not_logged_in';
        } else if ((int)$event->userid < 0) {
            $actor = 'system';
        } else if ($selecteduserid !== null && (int)$event->userid === $selecteduserid) {
            $actor = 'selected_user';
        }

        return [
            'id' => 'E' . $sequence,
            'timestamp' => (int)$event->timecreated,
            'time' => userdate((int)$event->timecreated, get_string('strftimedatetimeshort', 'langconfig')),
            'eventname' => $name,
            'eventclass' => (string)$event->eventname,
            'component' => (string)$event->component,
            'action' => (string)$event->action,
            'target' => (string)$event->target,
            'objectid' => $event->objectid === null ? null : (int)$event->objectid,
            'contextid' => (int)$event->contextid,
            'context' => $contextname,
            'relateduserid' => empty($event->relateduserid) ? null : (int)$event->relateduserid,
            'origin' => $origin,
            'actor' => $actor,
            'description' => $description,
            'url' => $url,
        ];
    }

    /**
     * Build the minimal record sent to AI. IP and arbitrary event other data are intentionally excluded.
     *
     * @param array $event Formatted event.
     * @return array
     */
    public static function for_ai(array $event): array {
        return [
            'id' => $event['id'],
            'timestamp' => $event['timestamp'],
            'time' => $event['time'],
            'eventname' => $event['eventname'],
            'eventclass' => $event['eventclass'],
            'component' => $event['component'],
            'action' => $event['action'],
            'target' => $event['target'],
            'objectid' => $event['objectid'],
            'context' => self::redact_network_identifiers((string)$event['context']),
            'relateduserid' => $event['relateduserid'],
            'origin' => $event['origin'],
            'actor' => $event['actor'],
            'description' => self::redact_network_identifiers((string)$event['description']),
        ];
    }

    /**
     * Redact common IPv4/IPv6 literals before text is sent to AI.
     *
     * @param string $text Text.
     * @return string
     */
    private static function redact_network_identifiers(string $text): string {
        $text = preg_replace('/\b(?:\d{1,3}\.){3}\d{1,3}\b/', '[IP redacted]', $text) ?? $text;
        $text = preg_replace('/(?<![A-Za-z0-9])(?:[A-Fa-f0-9]{0,4}:){2,7}[A-Fa-f0-9]{0,4}(?![A-Za-z0-9])/',
            '[IP redacted]', $text) ?? $text;
        return $text;
    }
}
