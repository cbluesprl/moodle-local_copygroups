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
 * Link to create, edit or delete course custom fields.
 *
 * @package   local_mrcoursecustomfield
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $category = new admin_category('local_copygroups',
        get_string('pluginname', 'local_copygroups'));
    $ADMIN->add('localplugins', $category);

    $settings = new admin_settingpage(
        'local_copygroups_settings',
        get_string('settings'),
    );

    $roles = array_map(function($role) { return $role->localname; } , role_get_names());

    $settings->add(new admin_setting_configmultiselect(
            'local_copygroups/roles_can_import_groups',
            get_string('settings:select_roles', 'local_copygroups'),
            '',
            [],
            $roles
        )
    );



    $ADMIN->add('local_copygroups', $settings);


}