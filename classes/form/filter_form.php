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

namespace local_logexplainer\form;

use local_logexplainer\query;
use moodleform;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/../../../../lib/formslib.php');

/**
 * Log explainer filter form.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_form extends moodleform {
    /**
     * Form definition.
     *
     * @return void
     */
    protected function definition(): void {
        $mform = $this->_form;
        $custom = $this->_customdata;

        $mform->addElement('hidden', 'courseid', (int)$custom['courseid']);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('select', 'mode', get_string('mode', 'local_logexplainer'), [
            query::MODE_USER_COURSE => get_string('mode_usercourse', 'local_logexplainer'),
            query::MODE_USER_ACTIVITY => get_string('mode_useractivity', 'local_logexplainer'),
            query::MODE_EVENT => get_string('mode_event', 'local_logexplainer'),
            query::MODE_SUMMARY => get_string('mode_summary', 'local_logexplainer'),
        ]);

        $mform->addElement('autocomplete', 'userid', get_string('user', 'local_logexplainer'), $custom['users'], [
            'noselectionstring' => get_string('allusers', 'local_logexplainer'),
        ]);
        $mform->setType('userid', PARAM_INT);

        $mform->addElement('autocomplete', 'cmid', get_string('activity', 'local_logexplainer'), $custom['activities'], [
            'noselectionstring' => get_string('chooseactivity', 'local_logexplainer'),
        ]);
        $mform->setType('cmid', PARAM_INT);
        $mform->hideIf('cmid', 'mode', 'neq', query::MODE_USER_ACTIVITY);

        $mform->addElement('text', 'eventname', get_string('eventname', 'local_logexplainer'));
        $mform->setType('eventname', PARAM_RAW_TRIMMED);
        $mform->addHelpButton('eventname', 'eventname', 'local_logexplainer');
        $mform->hideIf('eventname', 'mode', 'neq', query::MODE_EVENT);

        $mform->addElement('date_time_selector', 'fromtime', get_string('fromtime', 'local_logexplainer'));
        $mform->addElement('date_time_selector', 'totime', get_string('totime', 'local_logexplainer'));
        $mform->setDefault('fromtime', $custom['defaultfrom']);
        $mform->setDefault('totime', time());

        $this->add_action_buttons(false, get_string('analyse', 'local_logexplainer'));
    }

    /**
     * Validate submitted fields.
     *
     * @param array $data Data.
     * @param array $files Files.
     * @return array
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        if ((int)$data['fromtime'] > (int)$data['totime']) {
            $errors['totime'] = get_string('error_period', 'local_logexplainer');
        }
        if (in_array($data['mode'], [query::MODE_USER_COURSE, query::MODE_USER_ACTIVITY], true) && empty($data['userid'])) {
            $errors['userid'] = get_string('error_userrequired', 'local_logexplainer');
        }
        if ($data['mode'] === query::MODE_USER_ACTIVITY && empty($data['cmid'])) {
            $errors['cmid'] = get_string('error_activityrequired', 'local_logexplainer');
        }
        if ($data['mode'] === query::MODE_EVENT && trim((string)$data['eventname']) === '') {
            $errors['eventname'] = get_string('error_eventrequired', 'local_logexplainer');
        }
        return $errors;
    }
}
