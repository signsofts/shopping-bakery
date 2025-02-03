
const deleteProduct = (id) => {
    if (confirm("ต้องการลบรายการสินค้า ? ")) {
        location.assign("?delete=" + id);
    }
}