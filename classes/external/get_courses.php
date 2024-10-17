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

namespace local_copygroups\external;

global $CFG;

use coding_exception;
use context_course;
use dml_exception;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use required_capability_exception;
use restricted_context_exception;

require_once $CFG->dirroot . '/lib/externallib.php';

class get_courses extends external_api
{
    /**
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     * @throws required_capability_exception
     */
    public static function get_courses(string $query, int $course_id)
    {
        global $DB, $USER;

        $params = self::validate_parameters(self::get_courses_parameters(), [
            'query' => $query,
            'course_id' => $course_id
        ]);

        $context = context_course::instance($params['course_id']);
        self::validate_context($context);
        require_capability('moodle/course:managegroups', $context);


        $roles_can_import = get_config('local_copygroups', 'roles_can_import_groups');
        if (!empty($roles_can_import)) {
            [$insql, $inparams] = $DB->get_in_or_equal(explode(',', $roles_can_import), SQL_PARAMS_NAMED);

            $sql = "SELECT DISTINCT c.id, c.shortname
            FROM {role_assignments} ra
            JOIN {context} ctx ON ra.contextid = ctx.id AND ctx.contextlevel = :context_course
            JOIN {course} c ON ctx.instanceid = c.id
            JOIN {user} u ON ra.userid = u.id
            JOIN {role} r ON ra.roleid = r.id
            JOIN {groups} g ON c.id = g.courseid
            WHERE r.id $insql
            AND u.id = :userid
            AND " . $DB->sql_like('c.shortname', ':search', false) . "
            LIMIT 10
        ";
            $params = [
                'context_course' => CONTEXT_COURSE,
                'roles_can_import' => get_config('local_copygroups', 'roles_can_import_groups'),
                'userid' => $USER->id,
                'search' => '%' . $DB->sql_like_escape($params['query']) . '%'
            ];

            $req = $DB->get_records_sql($sql, $params + $inparams);

            $courses = [];
            foreach ($req as $c) {
                $courses[] = [
                    'id' => $c->id,
                    'shortname' => $c->shortname
                ];
            }

            return $courses;
        } else {
            return [];
        }
    }

    public static function get_courses_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'query' => new external_value(PARAM_TEXT, 'Course shortname', VALUE_OPTIONAL),
            'course_id' => new external_value(PARAM_INT, 'Source course id', VALUE_REQUIRED),
        ]);
    }

    public static function get_courses_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Course id'),
                'shortname' => new external_value(PARAM_TEXT, 'Course shortname')
            ])
        );
    }
}