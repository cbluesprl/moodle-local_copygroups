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
 * @package     local_copygroups
 * @copyright   2024 CBlue SPRL
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use local_copygroups\group_helper;

global $PAGE, $CFG, $DB, $OUTPUT;

require('../../config.php');
require_once("$CFG->dirroot/local/copygroups/classes/forms/copydistinctgroups_form.php");
require_once("$CFG->dirroot/local/copygroups/classes/group_helper.php");

$courseid = required_param('target', PARAM_INT);
$original_courseid = required_param('original', PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$original_course = $DB->get_record('course', ['id' => $original_courseid], '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
require_capability('moodle/course:managegroups', $context);

$PAGE->set_url('/local/copygroups/index.php', ['courseid' => $course->id]);
$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

$returnurl = new moodle_url('/group/index.php', ['id' => $original_course->id]);
$validate_url = new moodle_url('/local/copygroups/groups_select.php', ['target' => $course->id, 'original' => $original_courseid]);
$PAGE->requires->js_call_amd('local_copygroups/validation', 'init', ['from' => 'groups_select']);

$mform = new copydistinctgroups_form($validate_url, ['targetid' => $course->id, 'originalid' => $original_courseid]);

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $mform->get_data()) {
    $data = array_filter((array) $data, function ($entry) {
        return strpos($entry, 'group_') === 0;
    }, ARRAY_FILTER_USE_KEY);

    $groupids = [];
    foreach ($data as $key => $da) {
        $groupid = explode('_', $key)[1];
        $groupids[$groupid] = $DB->get_record('groups', ['id' => $groupid]);
    }
    group_helper::copy_groups($groupids, $courseid, $original_courseid);
    redirect($returnurl, get_string('form:success', 'local_copygroups'), 1, notification::NOTIFY_SUCCESS);

}
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
