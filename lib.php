<?php
/**
 * This script is owned by CBlue SPRL, please contact CBlue regarding any licences issues.
 *
 * @date :       15/02/2024
 * @author:      gnormand@cblue.be
 * @copyright:   CBlue SPRL, 2024
 */


/**
 * Display the edit custom information link in the course administration menu.
 *
 * @param settings_navigation $navigation The settings navigation object
 * @param stdClass $course The course
 * @param stdclass $context Course context
 */
function local_copygroups_extend_navigation_course($navigation, $course, $context) {
    // Check that they can add an instance.

    $url = new moodle_url('/local/copygroups/index.php', ['courseid' => $course->id]);
    $settingsnode = navigation_node::create(get_string('course_import_groups_link', 'local_copygroups'), $url,
        navigation_node::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));
    $navigation->add_node($settingsnode);

}