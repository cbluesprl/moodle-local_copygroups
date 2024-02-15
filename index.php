<?php
/**
 * This script is owned by CBlue SPRL, please contact CBlue regarding any licences issues.
 *
 * @date :       15/02/2024
 * @author:      gnormand@cblue.be
 * @copyright:   CBlue SPRL, 2024
 */


/**
 * Dans l'idée, vérifier que le courseid existe
 * Vérifier les droits de l'utilisateur dans le cours
 */
use local_copygroups\group_helper;


require('../../config.php');
require_once("$CFG->dirroot/local/copygroups/classes/forms/copygroups_form.php");
require_once("$CFG->dirroot/local/copygroups/classes/group_helper.php");


$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
require_capability('moodle/course:managegroups', $context);

$strcopygroups = get_string('pluginname', 'local_copygroups');
$PAGE->navbar->add($strcopygroups);
navigation_node::override_active_url(new moodle_url('/local/copygroups/index.php', array('courseid' => $course->id)));

$PAGE->set_url('/local/copygroups/index.php', array('courseid' => $course->id));
$PAGE->set_title("$course->shortname: $strcopygroups");
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

$returnurl = new moodle_url('/group/index.php', ['id' => $course->id]);


$mform = new copygroups_form(null, ['courseid' => $course->id]);

if($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $mform->get_data()) {
    group_helper::copy_all_groups($data->source_course, $data->courseid);
    redirect($returnurl, get_string('form:success', 'local_copygroups'), 1, \core\output\notification::NOTIFY_SUCCESS);
}


echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

