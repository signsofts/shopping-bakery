const addToCart = (id, type) => {
    if (confirm("อัพเดตตะกร้าสินค้า ? ")) {
        location.assign(`?type=${type}&CART_ID=` + id);
    }
}

const orderSuccess = () => {
    if (confirm("ต้องการสั่งซื้อสินค้า ? ")) {
        location.assign(`?order=true`);
    }
}