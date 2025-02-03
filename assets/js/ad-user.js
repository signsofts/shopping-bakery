
const deleteuser = (id) => {
    if (confirm("ต้องการลบข้อมูลลูกค้า ? ")) {
        location.assign("?delete=" + id);
    }
}