


const addToCart = (id) => {
    if (confirm("ต้องการเพิ่มเข้าตะกร้าสินต้า ? ")) {
        location.assign("?type=add&PRO_ID=" + id);
    }
}