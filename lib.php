<?php
/**
 * This script is owned by CBlue SPRL, please contact CBlue regarding any licences issues.
 *
 * @date :       15/02/2024
 * @author:      gnormand@cblue.be
 * @copyright:   CBlue SPRL, 2024
 */


/** *
 * @throws coding_exception
 * @throws moodle_exception
 */
function local_copygroups_extend_navigation_course($navigation, $course, $context)
{
    if (has_capability('moodle/course:managegroups', $context)) {
        $url = new moodle_url('/local/copygroups/index.php', ['courseid' => $course->id]);
        $settingsnode = navigation_node::create(get_string('course_import_groups_link', 'local_copygroups'), $url,
            navigation_node::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));
        $navigation->add_node($settingsnode);
    }
}