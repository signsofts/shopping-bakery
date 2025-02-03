

const reNewOreder = async (id, type) => {

    if (type == 're') {
        if (confirm("ยืนการชำระ? ")) {
            location.assign("?type=" + type + "&idRe=" + id);
        }
        return;
    }


}


function toggleEditForm(ev, id) {
    ev.style.display = 'none';
    const form = document.getElementById(`editForm-c-${id}`);
    const btn = document.getElementById(`btn-editForm-c-${id}`);

    // console.log(form)
    if (form.style.display === 'none') {
        form.style.display = 'block';
        btn.style.display = 'none';
    } else {
        form.style.display = 'none';
        btn.style.display = 'block';
    }
}
