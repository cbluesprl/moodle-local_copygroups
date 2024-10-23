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
use local_copygroups\form\copy_groups;
use local_copygroups\group_helper;

require('../../config.php');

global $PAGE, $CFG, $DB, $OUTPUT;

$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
require_capability('moodle/course:managegroups', $context);

$strcopygroups = get_string('pluginname', 'local_copygroups');

$PAGE->set_url('/local/copygroups/index.php', ['courseid' => $course->id]);
$PAGE->set_title("$course->shortname: $strcopygroups");
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->requires->js_call_amd('local_copygroups/validation', 'init', ['from' => 'index']);

$returnurl = new moodle_url('/group/index.php', ['id' => $course->id]);

$mform = new copy_groups(null, ['courseid' => $course->id]);

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $mform->get_data()) {
    if (isset($data->select_distinct_groups) && $data->select_distinct_groups == '1') {
        redirect(new moodle_url('/local/copygroups/groups_select.php', ['target' => $data->source_course, 'original' => $course->id]));
    } else {
        group_helper::copy_all_groups($data->source_course, $data->courseid);
        redirect($returnurl, get_string('form:success', 'local_copygroups'), 1, notification::NOTIFY_SUCCESS);
    }
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

