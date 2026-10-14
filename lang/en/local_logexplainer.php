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
 * English strings.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['activity'] = 'Activity';
$string['aiunavailable'] = 'The factual events are shown below, but the AI explanation could not be produced.';
$string['allusers'] = 'All users';
$string['analyse'] = 'Build timeline and explain';
$string['chooseactivity'] = 'Choose an activity';
$string['component'] = 'Component / action / target';
$string['context'] = 'Context';
$string['defaultperioddays'] = 'Default period in days';
$string['defaultperioddays_desc'] = 'Default look-back period shown in the filter form.';
$string['details'] = 'Factual details';
$string['error_activityrequired'] = 'Select an activity for this mode.';
$string['error_eventrequired'] = 'Enter an exact Moodle event class.';
$string['error_period'] = 'The end date must be after the start date.';
$string['error_userrequired'] = 'Select a user for this mode.';
$string['event'] = 'Event';
$string['eventname'] = 'Exact event class';
$string['eventname_help'] = 'Example: \\mod_quiz\\event\\attempt_submitted. The query is intentionally exact to keep log-store queries portable and predictable.';
$string['evidenceid'] = 'ID';
$string['explanationheading'] = 'Explanation';
$string['factsheading'] = 'Factual log events';
$string['fromtime'] = 'From';
$string['gaps'] = 'What the logs do not establish';
$string['hypotheses'] = 'Possible interpretations';
$string['invalidairesponse'] = 'The AI response was rejected because it did not preserve the factual event references.';
$string['logexplainer:view'] = 'View and explain authorised Moodle logs';
$string['maxevents'] = 'Maximum events per analysis';
$string['maxevents_desc'] = 'Caps the number of log events read and sent to the explanation layer. Values are limited to 200 at runtime.';
$string['mode'] = 'Mode';
$string['mode_event'] = 'Specific event';
$string['mode_summary'] = 'Summarised timeline';
$string['mode_useractivity'] = 'User + activity';
$string['mode_usercourse'] = 'User + course + period';
$string['noevents'] = 'No matching log events were found.';
$string['nologreader'] = 'No enabled SQL-compatible Moodle log reader is available.';
$string['notingroup'] = 'You must belong to a visible group to view these logs.';
$string['pluginname'] = 'Log explainer';
$string['privacy:metadata'] = 'This plugin does not store its own personal records, prompts or AI responses, but it sends selected Moodle log data through local_ai_bridge to the configured AI provider when an analysis is requested.';
$string['privacy:metadata:aiprovider'] = 'For each requested analysis, up to 200 authorized Moodle log events are sent via local_ai_bridge to the configured AI provider. Event descriptions, related user IDs and object IDs may identify participants; the plugin does not retain this content.';
$string['privacy:metadata:aiprovider:action'] = 'Action recorded in the event.';
$string['privacy:metadata:aiprovider:actor'] = 'Pseudonymous category of the event actor relative to the selected user.';
$string['privacy:metadata:aiprovider:component'] = 'Moodle component that generated the event.';
$string['privacy:metadata:aiprovider:context'] = 'Human-readable Moodle context name, possibly identifying a course or activity.';
$string['privacy:metadata:aiprovider:description'] = 'Moodle event description, which can contain participant IDs and other identifying information.';
$string['privacy:metadata:aiprovider:eventclass'] = 'Moodle event class name.';
$string['privacy:metadata:aiprovider:eventname'] = 'Human-readable event name.';
$string['privacy:metadata:aiprovider:id'] = 'Sequence reference used within this analysis.';
$string['privacy:metadata:aiprovider:objectid'] = 'ID of the object affected by the event.';
$string['privacy:metadata:aiprovider:origin'] = 'Origin of the logged action.';
$string['privacy:metadata:aiprovider:relateduserid'] = 'ID of the user related to the event, if present.';
$string['privacy:metadata:aiprovider:target'] = 'Target type recorded in the event.';
$string['privacy:metadata:aiprovider:time'] = 'Human-readable date and time of the event.';
$string['privacy:metadata:aiprovider:timestamp'] = 'Event creation timestamp (Unix time).';
$string['privacywarning'] = 'This analysis sends up to 200 Moodle log events to the external AI provider configured through local_ai_bridge. Event descriptions and IDs may identify course participants. Direct IP fields are omitted and common IP literals in text are redacted; this plugin does not store prompts or responses.';
$string['summary'] = 'Summary';
$string['time'] = 'Time';
$string['timeline'] = 'Readable timeline';
$string['totime'] = 'To';
$string['truncatedgap'] = 'The analysis contains only {$a->shown} of {$a->total} matching events, so it must not be treated as a complete history.';
$string['truncatednotice'] = 'The query matched {$a->total} events. Only the first {$a->shown} are shown and sent to AI; narrow the period for a complete sequence.';
$string['user'] = 'User';
