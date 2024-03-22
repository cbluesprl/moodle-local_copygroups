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
 * Provides the {@link copygroups_form} class.
 *
 * @package    local_copygroups
 * @copyright  2024 Cblue
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class copygroups_form extends moodleform {

    /**
     * Defines the form fields.
     */
    public function definition() {
        global $DB, $USER;

        $mform  = $this->_form;
        $data  = $this->_customdata;

        $mform->addElement('header', 'general', get_string('pluginname', 'local_copygroups'));

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        /**
         * On sélectionne tous les cours où l'utilisateur a le rôle souhaité, et où il existe des groupes à importer
         */
        $roles_can_import = get_config('local_copygroups', 'roles_can_import_groups');
        if(empty($roles_can_import)) {

            return false;
        }

        $sql = "SELECT DISTINCT(c.id), c.shortname
            FROM {role_assignments} ra
            JOIN {context} ctx ON ra.contextid = ctx.id AND ctx.contextlevel = :context_course
            JOIN {course} c ON ctx.instanceid = c.id
            JOIN {user} u ON ra.userid = u.id
            JOIN {role} r ON ra.roleid = r.id
            JOIN {groups} g ON c.id = g.courseid
            WHERE r.id IN (" . $roles_can_import . ")
            AND u.id = :userid
            AND c.id <> :thiscourseid; -- on n'importe pas depuis le cours courant
";
        $req = $DB->get_records_sql($sql, [
            'context_course' => CONTEXT_COURSE,
            'roles_can_import' => get_config('local_copygroups', 'roles_can_import_groups'),
            'userid' => $USER->id,
            'thiscourseid' => $data['courseid']
        ]);

        $courses = array();
        $courses[0] = get_string('select');
        foreach($req as $c) {
            $courses[$c->id] = $c->shortname;
        }

        $mform->addElement('autocomplete', 'source_course', get_string('form:input_shortname', 'local_copygroups'), $courses);
        $mform->setType('source_course', PARAM_INT);
        $mform->addHelpButton('source_course', 'form:input_shortname', 'local_copygroups');

        $mform->addElement('checkbox', 'select_distinct_groups', get_string('form:select_distinct_groups', 'local_copygroups'));
        $mform->addHelpButton('select_distinct_groups', 'form:select_distinct_groups:desc', 'local_copygroups');

        $this->add_action_buttons(true, get_string('form:btn_import', 'local_copygroups'));
        $this->set_data($data);
    }

    /**
     * Validates the form data.
     *
     * @param array $data submitted form data
     * @param array $files not used here
     * @return array errors
     */
    public function validation($data, $files) {
        global $DB;

//        $errors = parent::validation($data, $files);
//
//        $params = array('customint2' => $data['customint2'], 'customint1' => $data['customint1'], 'courseid' => $data['courseid']);
//        if ($DB->record_exists_select('enrol', "customint1 = :customint1 AND customint2 = :customint2 AND courseid = :courseid
//                AND enrol = 'groupsync'", $params)) {
//            $errors['customint2'] = get_string('instanceexists', 'enrol_groupsync');
//        }
//
//        return $errors;
    }
}

