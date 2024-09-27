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

$string['pluginname'] = 'Copy groups from courses';
$string['privacy:metadata'] = 'Copy groups plugins does not store any personal data';
$string['form:no_group'] = 'You don\'t have any group to import';
$string['form:btn_import'] = 'Import';
$string['form:btn_import_from_courses'] = 'Import groups from courses';
$string['form:input_shortname'] = 'Shortname of the course you want to import groups from';
$string['form:input_shortname_help'] = 'Select the course from which you want to import groups';
$string['form:select_distinct_groups'] = 'Select specific groups';
$string['form:select_distinct_groups:desc'] = 'Link to a page where you can select the groups you wish to import';
$string['form:select_distinct_groups:desc_help'] = 'Link to a page where you can select the groups you wish to import';
$string['form:success'] = 'Group(s) imported with success';
$string['course_import_groups_link'] = 'Import groups';
/** Settings */
$string['settings:select_roles'] = 'Select roles that are allowed to import';

/** Modal */
$string['modalcontentvalidation'] = 'Please note that if groups exist in your course, they may be modified.';
$string['modalcontentvalidationtitle'] = 'Validate groups';