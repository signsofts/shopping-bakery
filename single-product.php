<?php include_once('./inc/inc-html.php') ?>
<?php
if (!isset($_GET['PRO_ID'])) { // ตรวจสอบว่ามีการส่งค่า PRO_ID มาหรือไม่
    header('Location: ./home.php'); // ถ้าไม่มี ให้เปลี่ยนเส้นทางไปหน้า home.php
}

$PRO_ID = $_GET["PRO_ID"]; // รับค่า PRO_ID จาก URL

// ค้นหาข้อมูลสินค้าจากฐานข้อมูล
$sql = "SELECT * FROM `tbl_products` WHERE PRO_ID ='$PRO_ID'";
$query_row = $conn->query($sql)->fetch_assoc();

// ตรวจสอบว่ามีคำขอ GET และมีพารามิเตอร์ type และ PRO_ID
if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET["type"]) && isset($_GET["PRO_ID"])) {

    if ($_GET["type"] == 'add') { // ตรวจสอบว่าการดำเนินการเป็นการเพิ่มสินค้าเข้าตะกร้า
        $USER_ID = $_SESSION["USER"]['USER_ID']; // ดึง USER_ID ของผู้ใช้ปัจจุบันจาก SESSION

        if ($query_row) { // ตรวจสอบว่าสินค้ามีอยู่จริงในฐานข้อมูล

            // ค้นหาข้อมูลสินค้าในตะกร้าของผู้ใช้
            $query_row_cart = $conn->query("SELECT * FROM `tbl_carts` WHERE USER_ID = '$USER_ID' AND PRO_ID ='$PRO_ID' ")->fetch_assoc();

            if ($query_row_cart) { // ถ้าสินค้ามีอยู่แล้วในตะกร้า ให้เพิ่มจำนวนสินค้า
                $CART_NUMBER = $query_row_cart['CART_ID'] + 1;
                $conn->query("UPDATE `tbl_carts` SET `CART_NUMBER` = '$CART_NUMBER' WHERE `tbl_carts`.`CART_ID` = '{$query_row_cart['CART_ID']}';");

                echo "
                <script>
                    alert('อัพเดตตะกร้าสำเร็จ');
                    location.assign('./single-product.php?PRO_ID={$_GET['PRO_ID']}');
                </script>";
                exit;
            } else { // ถ้าไม่มีสินค้าในตะกร้า ให้เพิ่มรายการใหม่
                $cart_insert = $conn->query("INSERT INTO `tbl_carts` (`CART_ID`, `USER_ID`, `PRO_ID`, `CART_NUMBER`, `CART_STAMP`, `CART_PRICE`)
                                             VALUES (NULL, '$USER_ID', '$PRO_ID', '1', current_timestamp(), '{$query_row['PRO_PRICE']}');");

                echo "
                <script>
                    alert('เพิ่มเข้าตะกร้าสำเร็จ');
                    location.assign('./single-product.php?PRO_ID={$_GET['PRO_ID']}');
                </script>";
                exit;
            }
        } else { // ถ้าไม่มีสินค้า ให้เปลี่ยนเส้นทางกลับไปที่หน้าสินค้า
            header('Location: ./single-product.php?PRO_ID=' . $_GET['PRO_ID']);
        }
    }

    exit;
}

?>

<!DOCTYPE html>
<html lang="th">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="รายละเอียดรายการสินค้า - SHOPPING">
    <meta name="author" content="รายละเอียดรายการสินค้า - SHOPPING">

    <title>รายละเอียดรายการสินค้า - SHOPPING</title>

    <?php $_SESSION["page"] = 'product'; ?>
    <?php include_once('./inc/head.php') ?>

</head>

<body>
    <?php include_once('./inc/header.php') ?>
    <div class="page-heading" id="top">
        <div class="container">

        </div>
    </div>
    <section class="section mt" id="product">
        <div class="container">
            <h2 class="mb-3 mt-3">รายละเอียดรายการสินค้า</h2>
            <div class="row">
                <div class="col-lg-8">
                    <div class="left-images">
                        <img src="upload/<?= $query_row['PRO_PHOTO']; ?>" alt="">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="right-content">
                        <h4>ชื่อสินค้า : <?= $query_row['PRO_NAME']; ?></h4>
                        <span class="price">ราคา : <?= $query_row['PRO_PRICE']; ?> บาท</span>
                        <label for="">รายละเอียดสินค้า</label>
                        <span class="mt-0"><?= $query_row['PRO_DETAILS']; ?></span>
                        <div class="total">
                            <div class="main-border-button"><a
                                    href="javascript:addToCart('<?= $query_row['PRO_ID']; ?>')">Add To Cart</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once('./inc/footer.php'); ?>

    <script src="./assets/js/single-product.js"></script>


</body>

</html>