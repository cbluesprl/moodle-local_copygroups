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

namespace local_copygroups;

use cache_helper;

global $CFG;

defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot . '/group/lib.php';


class group_helper
{
    static public function copy_all_groups($coursesourceid, $coursedestinationid)
    {
        global $DB;

        $groups = $DB->get_records('groups', ['courseid' => $coursesourceid]);

        return self::copy_groups($groups, $coursesourceid, $coursedestinationid);
    }

    static public function copy_groups(array $groupstocopy, int $coursesourceid, int $coursedestinationid)
    {
        global $DB;
        if (count($groupstocopy) > 0) {
            $groupstocopybyname = self::get_groups_by_name($groupstocopy);

            $existinggroups = $DB->get_records('groups', ['courseid' => $coursedestinationid]);
            $existinggroupsbyname = self::get_groups_by_name($existinggroups);

            // Create missing groups
            $groups_to_insert = array_diff_key($groupstocopybyname, $existinggroupsbyname);
            foreach ($groups_to_insert as $group) {
                $copy = clone $group;
                $copy->courseid = $coursedestinationid;
                $copy->id = $DB->insert_record('groups', $copy, true);
                $existinggroups[$copy->id] = $copy;
                $existinggroupsbyname[$copy->name] = $copy;
            }

            // Remove members from existing groups
            [$insql, $inparams] = $DB->get_in_or_equal(array_keys($existinggroups));
            $sql = "SELECT userid, groupid FROM {groups_members} WHERE groupid $insql";
            $members = $DB->get_records_sql($sql, $inparams);
            foreach ($members as $member) {
                groups_remove_member($member->groupid, $member->userid);
            }

            [$insql, $inparams] = $DB->get_in_or_equal(array_keys($groupstocopy), SQL_PARAMS_NAMED);
            $now = time();
            $sql = "SELECT gm.id as uselesskeytoavoidarrayoverided, ue.id, ue.userid, gm.groupid FROM {user_enrolments} ue
                            JOIN {enrol} e ON ue.enrolid = e.id AND e.courseid = :coursesourceid
                            JOIN {groups_members} gm ON gm.userid = ue.userid AND gm.groupid $insql
                            JOIN {user_enrolments} uedest ON uedest.userid = ue.userid
                            JOIN {enrol} edest ON uedest.enrolid = edest.id AND edest.courseid = :coursedestinationid
                            WHERE ue.status = 0
                              AND ue.timestart < $now
                              AND (ue.timeend = 0 OR ue.timeend > $now)
                              AND uedest.status = 0
                              AND uedest.timestart < $now
                              AND (uedest.timeend = 0 OR uedest.timeend > $now)";

            $params = $inparams;
            $params['coursesourceid'] = $coursesourceid;
            $params['coursedestinationid'] = $coursedestinationid;
            $results = $DB->get_records_sql($sql, $params);

            foreach ($results as $result) {
                $groupsourcename = $groupstocopy[$result->groupid]->name;
                $groupid = $existinggroupsbyname[$groupsourcename]->id;
                groups_add_member($groupid, $result->userid);
            }
            cache_helper::invalidate_by_definition('core', 'groupdata', [], [$coursesourceid, $coursedestinationid]);

            return true;
        } else {
            debugging('No groups found');
        }

        return false;
    }

    static protected function get_groups_by_name($groups)
    {
        $groupsbyname = [];

        foreach ($groups as $group) {
            $groupsbyname[$group->name] = $group;
        }
        return $groupsbyname;
    }
}
