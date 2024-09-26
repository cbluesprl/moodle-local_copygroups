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
//
// @package     local_copygroups
// @copyright   2024 CBlue SPRL
// @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import {get_string as getString} from 'core/str';

export const init = (from) => {
    let popupAlreadyDisplayed = false;
    let form = document.getElementsByClassName("mform")[0];
    form.addEventListener('submit', async(e) => {
        if (!popupAlreadyDisplayed) {
            e.preventDefault();
            popupAlreadyDisplayed = true;
            const modal = await ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: getString('modalcontentvalidationtitle', 'local_copygroups'),
                large: false,
                body: getString('modalcontentvalidation', 'local_copygroups'),
            });
            modal.getRoot().on(ModalEvents.save, () => {
                document.getElementById("id_submitbutton").click();
            });
            modal.getRoot().on(ModalEvents.cancel, () => {
                popupAlreadyDisplayed = false;
            });
            if (from === 'index') {
                if (!document.getElementById('id_select_distinct_groups').checked) {
                    modal.show();
                }
            } else if (from === 'groups_select') {
                modal.show();
            }
        }
    });
};