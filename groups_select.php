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
require_once("$CFG->dirroot/local/copygroups/classes/forms/copydistinctgroups_form.php");
require_once("$CFG->dirroot/local/copygroups/classes/group_helper.php");

$courseid = required_param('target', PARAM_INT);
$original_courseid = required_param('original', PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$original_course = $DB->get_record('course', ['id' => $original_courseid], '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
require_capability('moodle/course:managegroups', $context);

$PAGE->set_url('/local/copygroups/index.php', array('courseid' => $course->id));
$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

$returnurl = new moodle_url('/group/index.php', ['id' => $original_course->id]);
$validate_url = new moodle_url('/local/copygroups/groups_select.php', ['target' => $course->id, 'original' => $original_courseid]);
$PAGE->requires->js_call_amd('local_copygroups/validation', 'init');

$mform = new copydistinctgroups_form($validate_url, ['targetid' => $course->id, 'originalid' => $original_courseid]);

if($mform->is_cancelled()) {
    redirect($returnurl);
} elseif ($data = $mform->get_data()) {
    $data = array_filter((array) $data, function($entry) {
        return strpos($entry, 'group_') === 0;
    }, ARRAY_FILTER_USE_KEY);

    $groupids = [];
    foreach ($data as $key => $da) {
        $groupid = explode('_' , $key)[1];
        $groupids[] = $DB->get_record('groups' ,['id' => $groupid]);
    }
    group_helper::copy_groups($groupids , $courseid, $original_courseid);
    redirect($returnurl,get_string('form:success', 'local_copygroups'), 1, \core\output\notification::NOTIFY_SUCCESS);

}


echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
