import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import {get_string as getString} from 'core/str';

export const init = (from) => {
    let popupAlreadyDisplayed = false;
    let form = document.getElementsByClassName("mform")[0];
    form.addEventListener('submit', async (e) => {
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
            if(from == 'index') {
                if(!document.getElementById('id_select_distinct_groups').checked) {
                    modal.show();
                }
            } else if (from == 'groups_select') {
                modal.show();
            }
        }
    });
};