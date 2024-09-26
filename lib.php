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