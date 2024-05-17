# local_copygroups

## Getting started

1) Start by configuring the plugin in `/admin/settings.php?section=local_copygroups_settings`
The roles selected will be those with which users must be enrolled in a course, so that it can be reused for importing groups.
2) Go to group import from course navigation (Import groups from courses) or via the direct link `/local/copygroups/index.php?courseid=ID`
3) The user will see a Select field showing all the courses in which he/she has a role among those selected in step 1.

## Capabilities

Required to import groups: `moodle/course:managegroups`