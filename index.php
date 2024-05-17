<?php
/**
 * This script is owned by CBlue SPRL, please contact CBlue regarding any licences issues.
 *
 * @date :       15/02/2024
 * @author:      gnormand@cblue.be
 * @copyright:   CBlue SPRL, 2024
 */


use local_copygroups\group_helper;

global $PAGE, $CFG, $DB, $OUTPUT;


require('../../config.php');
require_once("$CFG->dirroot/local/copygroups/classes/forms/copygroups_form.php");
require_once("$CFG->dirroot/local/copygroups/classes/group_helper.php");


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

$mform = new copygroups_form(null, ['courseid' => $course->id]);

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $mform->get_data()) {
    if (isset($data->select_distinct_groups) && $data->select_distinct_groups == '1') {
        redirect(new moodle_url('/local/copygroups/groups_select.php', ['target' => $data->source_course, 'original' => $course->id]));
    } else {
        group_helper::copy_all_groups($data->source_course, $data->courseid);
        redirect($returnurl, get_string('form:success', 'local_copygroups'), 1, \core\output\notification::NOTIFY_SUCCESS);
    }
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

