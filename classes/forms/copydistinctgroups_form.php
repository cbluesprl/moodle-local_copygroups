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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/formslib.php');

class copydistinctgroups_form extends moodleform
{

    /**
     * Defines the form fields.
     */
    public function definition()
    {
        $mform = $this->_form;
        $data = $this->_customdata;

        $mform->addElement('header', 'general', get_string('pluginname', 'local_copygroups'));

        $mform->addElement('hidden', 'targetid');
        $mform->setType('targetid', PARAM_INT);

        $mform->addElement('hidden', 'originalid');
        $mform->setType('originalid', PARAM_INT);

        $groups = groups_get_all_groups($data['targetid']);
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $mform->addElement('checkbox', 'group_' . $group->id, $group->name);
            }
        }

        $this->add_action_buttons(true, get_string('form:btn_import', 'local_copygroups'));
        $this->set_data($data);
    }
}

