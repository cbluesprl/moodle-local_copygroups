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


require('../../config.php');
require_once("$CFG->dirroot/local/copygroups/classes/forms/copygroups_form.php");
//require_once('lib.php');
//require_once( $CFG->dirroot.'/local/copygroups/classes/helpers.php');

$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
require_capability('moodle/course:managegroups', $context);

$PAGE->navbar->add(get_string('pluginname', 'local_copygroups'));
navigation_node::override_active_url(new moodle_url('/local/copygroups/index.php', array('courseid' => $course->id)));

$PAGE->set_url('/local/copygroups/index.php', array('courseid' => $course->id));
$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);

$returnurl = new moodle_url('/local/copygroups/index.php', array('courseid' => $course->id));

$instance = new stdClass();
$instance->courseid = $course->id;

$mform = new copygroups_form(null, array($course, $instance));

if($mform->is_cancelled()) {
    echo "<pre>"; var_dump($returnurl); die;
    redirect($returnurl);
} else if ($data = $mform->get_data()) {
    /**
     *
     */
    redirect($returnurl);
}

$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('pluginname', 'local_copygroups'));

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

