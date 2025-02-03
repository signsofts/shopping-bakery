

const clickConfirem = async (id, type) => {

    if (type == 'success') {
        if (confirm("ยืนการชำระ? ")) {
            location.assign("?type=" + type + "&idConfirem=" + id);
        }
        return;
    }

    if (type == 'not') {
        if (confirm("ยอดชำระไม่ถูกต้อง ? ")) {
            location.assign("?type=" + type + "&idConfirem=" + id);
        }
        return;
    }



}