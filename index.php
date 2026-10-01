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
 * Log explainer page.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use local_logexplainer\access;
use local_logexplainer\explainer;
use local_logexplainer\form\filter_form;
use local_logexplainer\log_repository;
use local_logexplainer\query;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/course/lib.php');

$courseid = required_param('courseid', PARAM_INT);
$course = get_course($courseid);
require_login($course);

$context = context_course::instance($course->id);
access::require_course_access($context);

$url = new moodle_url('/local/logexplainer/index.php', ['courseid' => $course->id]);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('pluginname', 'local_logexplainer'));
$PAGE->set_heading(format_string($course->fullname));

$users = [];
$userfields = 'u.id,u.firstname,u.lastname,u.firstnamephonetic,u.lastnamephonetic,u.middlename,u.alternatename';
$enrolled = get_enrolled_users($context, '', 0, $userfields);
foreach ($enrolled as $user) {
    if (access::target_user_visible($course, (int)$user->id)) {
        $users[$user->id] = fullname($user);
    }
}
asort($users, SORT_NATURAL | SORT_FLAG_CASE);

$activities = [];
$modinfo = get_fast_modinfo($course);
foreach ($modinfo->get_cms() as $cm) {
    if ($cm->deletioninprogress) {
        continue;
    }
    $activities[$cm->id] = $cm->get_formatted_name();
}
asort($activities, SORT_NATURAL | SORT_FLAG_CASE);

$defaultdays = max(1, min(90, (int)get_config('local_logexplainer', 'defaultperioddays') ?: 7));
$form = new filter_form(null, [
    'courseid' => $course->id,
    'users' => $users,
    'activities' => $activities,
    'defaultfrom' => time() - ($defaultdays * DAYSECS),
]);

$events = null;
$explanation = null;
$aierror = null;
$truncated = false;
$totalcount = 0;

if ($data = $form->get_data()) {
    $userid = empty($data->userid) ? null : (int)$data->userid;
    $cmid = empty($data->cmid) ? null : (int)$data->cmid;

    if ($userid !== null) {
        access::require_target_user($course, $userid);
    }
    if ($data->mode === query::MODE_USER_ACTIVITY && $cmid !== null) {
        access::require_activity($course->id, $cmid);
        access::require_activity_target_user($course->id, $cmid, $userid);
    }

    $query = new query(
        $course->id,
        (string)$data->mode,
        $userid,
        $cmid,
        (int)$data->fromtime,
        (int)$data->totime,
        (string)($data->eventname ?? '')
    );

    $maxevents = max(1, min(200, (int)get_config('local_logexplainer', 'maxevents') ?: 100));
    $repository = new log_repository();
    $totalcount = $repository->count($query);
    $events = $repository->fetch($query, $maxevents);
    $truncated = $totalcount > count($events);

    if ($events) {
        try {
            $explanation = (new explainer())->explain(
                $events,
                $query->mode,
                $truncated,
                $totalcount
            );
        } catch (Throwable $e) {
            debugging('local_logexplainer AI explanation failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
            $aierror = get_string('aiunavailable', 'local_logexplainer');
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_logexplainer'));
echo $OUTPUT->notification(get_string('privacywarning', 'local_logexplainer'), notification::NOTIFY_INFO);
$form->display();

if ($events !== null) {
    if (!$events) {
        echo $OUTPUT->notification(get_string('noevents', 'local_logexplainer'), notification::NOTIFY_INFO);
    } else {
        if ($truncated) {
            echo $OUTPUT->notification(get_string('truncatednotice', 'local_logexplainer', [
                'shown' => count($events),
                'total' => $totalcount,
            ]), notification::NOTIFY_WARNING);
        }
        echo $OUTPUT->heading(get_string('factsheading', 'local_logexplainer'), 3);
        echo $OUTPUT->render_from_template('local_logexplainer/facts', ['events' => $events]);

        if ($aierror) {
            echo $OUTPUT->notification($aierror, notification::NOTIFY_WARNING);
        } else if ($explanation) {
            echo $OUTPUT->heading(get_string('explanationheading', 'local_logexplainer'), 3);
            echo $OUTPUT->render_from_template('local_logexplainer/explanation', $explanation);
        }
    }
}

echo $OUTPUT->footer();
