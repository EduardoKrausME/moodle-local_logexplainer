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
 * Local callbacks.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\report_helper;

/**
 * Add Log explainer to course navigation.
 *
 * @param navigation_node $navigation Navigation node.
 * @param stdClass $course Course record.
 * @param context_course $context Course context.
 * @return void
 */
function local_logexplainer_extend_navigation_course($navigation, $course, $context): void {
    if (!has_capability('local/logexplainer:view', $context) ||
        !has_capability('report/log:view', $context) ||
        !report_helper::has_valid_group($context)) {
        return;
    }

    $url = new moodle_url('/local/logexplainer/index.php', ['courseid' => $course->id]);
    $navigation->add(
        get_string('pluginname', 'local_logexplainer'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        'local_logexplainer',
        new pix_icon('i/report', '')
    );
}
