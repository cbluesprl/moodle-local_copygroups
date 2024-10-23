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

namespace local_copygroups\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

class copy_groups extends moodleform
{
    /**
     * Defines the form fields.
     */
    public function definition()
    {
        global $DB, $USER;

        $mform = $this->_form;
        $data = $this->_customdata;

        $mform->addElement('header', 'general', get_string('pluginname', 'local_copygroups'));

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('autocomplete', 'source_course', get_string('form:input_shortname', 'local_copygroups'), [], [
            'multiple' => false,
            'ajax' => 'local_copygroups/get_courses'
        ]);
        $mform->setType('source_course', PARAM_INT);
        $mform->addHelpButton('source_course', 'form:input_shortname', 'local_copygroups');

        $mform->addElement('checkbox', 'select_distinct_groups', get_string('form:select_distinct_groups', 'local_copygroups'));
        $mform->addHelpButton('select_distinct_groups', 'form:select_distinct_groups:desc', 'local_copygroups');

        $mform->disabledIf('submitbutton', 'source_course', 'eq', null);
        $mform->disabledIf('submitbutton', 'source_course', 'eq', 0);
        $this->add_action_buttons(true, get_string('form:btn_import', 'local_copygroups'));
        $this->set_data($data);
    }
}

