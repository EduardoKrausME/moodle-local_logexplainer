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
 * Admin settings.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_logexplainer', get_string('pluginname', 'local_logexplainer'));
    $settings->add(new admin_setting_configtext(
        'local_logexplainer/maxevents',
        get_string('maxevents', 'local_logexplainer'),
        get_string('maxevents_desc', 'local_logexplainer'),
        100,
        PARAM_INT
    ));
    $settings->add(new admin_setting_configtext(
        'local_logexplainer/defaultperioddays',
        get_string('defaultperioddays', 'local_logexplainer'),
        get_string('defaultperioddays_desc', 'local_logexplainer'),
        7,
        PARAM_INT
    ));
    $ADMIN->add('localplugins', $settings);
}
