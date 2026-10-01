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
$string['pluginname'] = 'Log explainer';
$string['privacy:metadata'] = 'The Log explainer does not store personal data itself. It reads existing Moodle logs on demand and does not persist prompts or analyses.';
$string['privacywarning'] = 'This tool reads personal log data on demand. Analyses are not persisted by this plugin, and IP addresses are not sent to AI.';
$string['summary'] = 'Summary';
$string['time'] = 'Time';
$string['timeline'] = 'Readable timeline';
$string['totime'] = 'To';
$string['truncatedgap'] = 'The analysis contains only {$a->shown} of {$a->total} matching events, so it must not be treated as a complete history.';
$string['truncatednotice'] = 'The query matched {$a->total} events. Only the first {$a->shown} are shown and sent to AI; narrow the period for a complete sequence.';
$string['user'] = 'User';
