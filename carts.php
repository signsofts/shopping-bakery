<?php include_once('./inc/inc-html.php') ?>
<?php

function uploadeImage($_FILEPHOTO)
{
    $uploadDir = "upload/slip/";

    // Check if the directory exists, if not, create it
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Check if the file is uploaded
    $imageName = false;

    if (isset($_FILEPHOTO) && $_FILEPHOTO['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILEPHOTO['tmp_name'];
        $fileName = $_FILEPHOTO['name'];
        $fileSize = $_FILEPHOTO['size'];
        $fileType = $_FILEPHOTO['type'];

        // Extract file extension
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Allowed file extensions
        $allowedExtensions = ['png', 'jpg', 'jpeg'];

        // Validate file extension
        if (in_array($fileExtension, $allowedExtensions)) {
            // Generate a new unique file name
            $newFileName = uniqid('image_', true) . '.' . $fileExtension;

            // Define the destination path
            $destPath = $uploadDir . $newFileName;

            // Move the uploaded file to the destination directory
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // echo "File uploaded successfully! New name: $newFileName";
                $imageName = $newFileName;
            } else {
                // echo "Error moving the uploaded file.";
                $imageName = false;
            }
        } else {
            // echo "Invalid file type. Only PNG, JPG, and JPEG files are allowed.";
            $imageName = false;
        }
    } else {
        // echo "No file uploaded or there was an error uploading the file.";
        $imageName = false;
    }


    if ($imageName === false) {
        return false;
    }

    return $imageName;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์ม
    $quantity = $_POST['quantity']; // ยอดโอน
    $customer_name = $_POST['customer_name']; // ชื่อผู้ซื้อ
    $customer_address = $_POST['customer_address']; // ที่อยู่ผู้ซื้อ
    $customer_phone = $_POST['customer_phone']; // เบอร์โทรศัพท์ผู้ซื้อ
    $product_price = $_POST['product_price']; // ราคาสินค้า

    $type = $_POST["type"];

    // ตรวจสอบว่าคำขอเป็นการเพิ่มคำสั่งซื้อหรือไม่
    if ($type != "add") {
        echo "
        <script>
            alert('เกิดข้อผิดพลาด')
            location.assign('./carts.php')
        </script>";
        exit;
    }

    // ดึงข้อมูลสินค้าทั้งหมดในตะกร้าของผู้ใช้
    $_all_cart = $conn->query("SELECT * FROM `tbl_carts` INNER JOIN tbl_products ON tbl_carts.PRO_ID = tbl_products.PRO_ID WHERE tbl_carts.USER_ID= '$gUSER_ID' AND tbl_products.PRO_DELETE IS NULL ", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC);

    foreach ($_all_cart as $key => $value) {
        // ค้นหาจำนวนสินค้าที่เหลืออยู่
        $PRO_ID = $value["PRO_ID"];
        $query_row_pro = $conn->query("SELECT * FROM `tbl_products` WHERE PRO_ID ='$PRO_ID'")->fetch_assoc();
        $PRO_STOCK = $query_row_pro['PRO_STOCK'] - $value['CART_NUMBER'];

        // ตรวจสอบว่าสินค้าคงเหลือเพียงพอหรือไม่
        if ($PRO_STOCK < 0) {
            echo "
            <script>
                alert('สินค้าหมด')
                location.assign('./carts.php')
            </script>";
            exit;
        }
    }

    // อัปโหลดไฟล์ภาพหลักฐานการชำระเงิน
    $ORDER_PAYMENT_IMAGE = uploadeImage($_FILES['ORDER_PAYMENT_IMAGE']);
    if ($ORDER_PAYMENT_IMAGE === false) {
        return false;
    }

    // บันทึกข้อมูลการสั่งซื้อ
    $USER_ID = $gUSER_ID;
    $ORDER_PRICE = $product_price;
    $ORDER_PAYMENT_PRICE = $quantity;
    $ORDER_CUS_NAME = $customer_name;
    $ORDER_CUS_ADDRESS = $customer_address;
    $ORDER_CUS_PHONE = $customer_phone;

    $insert_order = "INSERT INTO `tbl_orders` (`ORDER_ID`, `USER_ID`, `ORDER_STAMP`, `ORDER_STATUS`, `ORDER_CANCEL`, `ORDER_PRICE`, `ORDER_PAYMENT_IMAGE`, `ORDER_PAYMENT_PRICE`, `ORDER_CUS_NAME`, `ORDER_CUS_PHONE`, `ORDER_CUS_ADDRESS`) 
                     VALUES (NULL, '$USER_ID', current_timestamp(), '1', NULL, '$ORDER_PRICE', '$ORDER_PAYMENT_IMAGE', '$ORDER_PAYMENT_PRICE', '$ORDER_CUS_NAME', '$ORDER_CUS_PHONE', '$ORDER_CUS_ADDRESS');";

    if (!$conn->query($insert_order)) {
        echo "
        <script>
            alert('เกิดข้อผิดพลาด')
            location.assign('./carts.php')
        </script>";
        exit;
    }

    // รับค่า ORDER_ID ของคำสั่งซื้อที่เพิ่งสร้าง
    $ORDER_ID = $conn->insert_id;

    // อัปเดตจำนวนสินค้าคงเหลือในสต็อก และบันทึกข้อมูลสินค้าในคำสั่งซื้อ
    foreach ($_all_cart as $key => $value) {
        $PRO_ID = $value["PRO_ID"];
        $query_row_pro = $conn->query("SELECT * FROM `tbl_products` WHERE PRO_ID ='$PRO_ID'")->fetch_assoc();
        $PRO_STOCK = $query_row_pro['PRO_STOCK'] - $value['CART_NUMBER'];

        // อัปเดตจำนวนสินค้าคงเหลือ
        $conn->query("UPDATE `tbl_products` SET `PRO_STOCK` = '$PRO_STOCK' WHERE PRO_ID = '{$PRO_ID}';");

        // เพิ่มข้อมูลลงในตาราง tbl_order_lists
        $inser_list = "INSERT INTO `tbl_order_lists` (`OLIST_ID`, `ORDER_ID`, `PRO_ID`, `OLIST_NUMBER`, `OLIST_PRICE`, `OLIST_CANCEL`, `OLIST_STATUS`, `PRO_NAME`) 
                       VALUES (NULL, '$ORDER_ID', '{$value['PRO_ID']}', '{$value['CART_NUMBER']}', '{$value['CART_PRICE']}', NULL, NULL, '{$value['PRO_NAME']}');";
        $conn->query($inser_list);
    }

    // ลบสินค้าทั้งหมดออกจากตะกร้าหลังจากสั่งซื้อเสร็จ
    $conn->query("DELETE FROM `tbl_carts` WHERE `tbl_carts`.`USER_ID` = '$gUSER_ID';");

    echo "
    <script>
        alert('สั่งซื้อสำเร็จ! รหัสคำสั่งซื้อของคุณคือ: ORDER-$ORDER_ID-TH ')
        location.assign('./history.php')
    </script>";
    exit;
}

// เพิ่มหรือลบสินค้าจากตะกร้า
if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET["type"]) && isset($_GET["CART_ID"])) {
    $CART_ID = $_GET["CART_ID"];
    $_row_cart = $conn->query("SELECT * FROM `tbl_carts` WHERE CART_ID = '$CART_ID' ")->fetch_assoc();

    if ($_row_cart) {
        if ($_GET["type"] == 'add') {
            $CART_NUMBER = $_row_cart['CART_NUMBER'] + 1;

            // ตรวจสอบสต็อกสินค้าก่อนเพิ่ม
            $PRO_ID = $_row_cart["PRO_ID"];
            $query_row = $conn->query("SELECT * FROM `tbl_products` WHERE PRO_ID ='$PRO_ID'")->fetch_assoc();

            if ($query_row['PRO_STOCK'] < $CART_NUMBER) {
                echo "
                <script>
                    alert('สินค้าหมด คงเหลือ({$query_row['PRO_STOCK']}) ')
                    location.assign('./carts.php')
                </script>";
                exit;
            }
        } elseif ($_GET["type"] == 'delete') {
            $CART_NUMBER = $_row_cart['CART_NUMBER'] - 1;
        }

        if ($CART_NUMBER == 0) {
            // ลบสินค้าจากตะกร้าเมื่อจำนวนเหลือ 0
            $conn->query("DELETE FROM carts WHERE `tbl_carts`.`CART_ID` = '{$_row_cart['CART_ID']}';");
            echo "<script>location.assign('./carts.php')</script>";
            exit;
        }

        // อัปเดตจำนวนสินค้าภายในตะกร้า
        $conn->query("UPDATE `tbl_carts` SET `CART_NUMBER` = '$CART_NUMBER' WHERE `tbl_carts`.`CART_ID` = '{$_row_cart['CART_ID']}';");
        echo "<script>location.assign('./carts.php')</script>";
        exit;
    } else {
        echo "
        <script>
            alert('พบข้อผิดพลาด')
            location.assign('./carts.php')
        </script>";
        exit;
    }
}

// ตรวจสอบสต็อกสินค้าก่อนทำคำสั่งซื้อ
if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET["order"]) && $_GET["order"] == 'true') {
    $_all_cart = $conn->query("SELECT * FROM `tbl_carts` INNER JOIN tbl_products ON tbl_carts.PRO_ID = tbl_products.PRO_ID WHERE tbl_carts.USER_ID= '$gUSER_ID' AND tbl_products.PRO_DELETE IS NULL ", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC);

    $proName = '';
    $checkStock = false;

    foreach ($_all_cart as $key => $value) {
        if ((int) $value['CART_NUMBER'] > (int) $value['PRO_STOCK']) {
            $proName = $value['PRO_NAME'] . " " . " คงเหลือ  " . $value['PRO_STOCK'];
            $checkStock = true;
            break;
        }
    }

    if ($checkStock) {
        echo "
        <script>
            alert('สินค้า $proName ')
            location.assign('./carts.php')
        </script>";
        exit;
    }
}

?>




<!DOCTYPE html>
<html lang="th">


<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="ตะกร้าสินค้า - SHOPPING">
    <meta name="author" content="ตะกร้าสินค้า - SHOPPING">

    <title>ตะกร้าสินค้า - SHOPPING</title>

    <?php $_SESSION["page"] = 'carts'; ?>
    <?php include_once('./inc/head.php') ?>

</head>

<body>
    <?php include_once('./inc/header.php') ?>

    <section class="section  mt-5" id="men">
        <div class="container <?= isset($_GET["order"]) && $_GET["order"] == 'true' ? "d-none" : ""; ?> ">
            <h4>
                ตะกร้าสินค้า
            </h4>
            <table class="table table-bordered" id="ff">
                <thead>
                    <tr>
                        <th style="width: 1%;">ลำดับ</th>
                        <th class="text-start">ชื่อสินค้า</th>
                        <th class="text-center">ราคา</th>
                        <th class="text-center">จำนวน</th>
                        <th class="text-center">รวม</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_all = $conn->query("SELECT * FROM `tbl_carts` INNER JOIN tbl_products ON tbl_carts.PRO_ID = tbl_products.PRO_ID WHERE tbl_carts.USER_ID= '$gUSER_ID' AND tbl_products.PRO_DELETE IS NULL;", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
                    $price_sum = 0;
                    ?>
                    <?php foreach ($query_all as $key => $item): ?>
                        <tr>
                            <td class="text-center" style="width: 1%;"><?= $key + 1; ?></td>
                            <td class="text-start"><?= $item['PRO_NAME']; ?></td>
                            <td class="text-center"><?= $item['CART_PRICE']; ?> บาท</td>
                            <td class="text-center"><?= $item['CART_NUMBER']; ?> </td>
                            <td class="text-center"><?= $item['CART_PRICE'] * $item['CART_NUMBER']; ?> บาท</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning" onclick="addToCart('<?= $item['CART_ID'] ?>','add')">
                                    +
                                </button>
                                <button class="btn btn-sm btn-danger"
                                    onclick="addToCart('<?= $item['CART_ID'] ?>','delete')">
                                    -
                                </button>
                            </td>
                        </tr>
                        <?php $price_sum += $item['CART_PRICE'] * $item['CART_NUMBER']; ?>


                    <?php endforeach ?>
                    <tr class="table-secondary">

                        <td class="text-center" colspan="4"><b>รวมราคาสินค้า</b></td>
                        <td class="text-center" colspan="2"><b><?= $price_sum; ?> บาท</b></td>

                    </tr>
                </tbody>
            </table>

            <div class="d-flex justify-content-end mt-5 ">
                <button onclick="orderSuccess()" style="width: 500px;" class="btn btn-mg btn-primary"> สั่งซื้อสินค้า
                </button>
            </div>

        </div>

        <div class="container <?= isset($_GET["order"]) && $_GET["order"] == 'true' ? "" : "d-none"; ?> ">

            <div class="order-form">
                <h2>สั่งซื้อสินค้า</h2>
                <?php
                $_all_cart = $conn->query("SELECT * FROM `tbl_carts` INNER JOIN tbl_products ON tbl_carts.PRO_ID = tbl_products.PRO_ID WHERE tbl_carts.USER_ID= '$gUSER_ID' AND tbl_products.PRO_DELETE IS NULL ", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC);
                $sum_order_ = 0;
                ?>
                <?php foreach ($_all_cart as $ilist): ?>
                    <div class="d-flex mt-2">
                        <img src="upload/<?= $ilist['PRO_PHOTO']; ?>" width="90px" height="70px"
                            alt="<?= $ilist['PRO_NAME']; ?>" class="product-image">
                        <div class="product-details text-left ml-3">
                            <h5 class="m-0"> ชื่อ:<?= $ilist['PRO_NAME']; ?></h5>
                            <p>จำนวน: <?= $ilist['CART_NUMBER']; ?> บาท</p>
                            <p>ราคา: <?= $ilist['CART_PRICE']; ?> บาท</p>
                        </div>

                    </div>

                    <?php
                    $sum_order_ = $ilist['CART_NUMBER'] * $ilist['CART_PRICE'];
                endforeach ?>
                <!-- รายละเอียดสินค้า -->
                <h4 class="mt-2 mb-2">ยอดสั่งซื้อ: <?= $sum_order_; ?> บาท</h4>

                <hr class="mt-1 mb-1">

                <!-- แบบฟอร์มสั่งซื้อ -->
                <form method="POST" action="carts.php" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="add">
                    <input type="hidden" name="product_price" value="<?= $sum_order_; ?>">

                    <!-- <h6>ข้อมูลผู้สั่งซื้อ</h6> -->
                    <label for="customer_name">ชื่อผู้สั่งซื้อ:</label>
                    <input value="<?= $_SESSION["USER"]['USER_FNAME'] . " " . $_SESSION["USER"]['USER_LNAME']; ?>"
                        type="text" name="customer_name" id="customer_name" placeholder="กรอกชื่อของคุณ" required>

                    <label for=" customer_address">ที่อยู่จัดส่ง:</label>
                    <textarea name="customer_address" id="customer_address" placeholder="กรอกที่อยู่สำหรับจัดส่งสินค้า"
                        required> <?= $_SESSION["USER"]['USER_ADDRESS']; ?></textarea>

                    <label for="customer_phone">เบอร์ติดต่อ:</label>
                    <input type="text" name="customer_phone" id="customer_phone" placeholder="กรอกเบอร์โทรศัพท์"
                        required value="<?= $_SESSION["USER"]['USER_PHONE']; ?>">

                    <hr>

                    <div class="bank-info">
                        <h2>ช่องทางการชำระเงิน</h2>
                        <!-- ข้อมูลธนาคาร -->
                        <div class="bank-account">
                            <h3>บัญชีธนาคาร</h3>
                            <p>ธนาคาร: <strong>กสิกรไทย (Kasikorn Bank)</strong></p>
                            <p>ชื่อบัญชี: <strong>บริษัท ABC จำกัด</strong></p>
                            <p>เลขที่บัญชี: <strong>123-4-56789-0</strong></p>
                        </div>

                        <!-- ข้อมูลพร้อมเพย์ -->
                        <div class="promptpay">
                            <h3>พร้อมเพย์</h3>
                            <p>ชื่อบัญชี: <strong>บริษัท ABC จำกัด</strong></p>
                            <p>เลขพร้อมเพย์: <strong>0123456789012</strong></p>
                            <p><img src="assets/images/promptpay-qr.jpg" alt="QR พร้อมเพย์" class="promptpay-qr"></p>
                        </div>
                    </div>

                    <h5><u>แจ้งชำระเงิน</u></h5>
                    <h4 class="text-danger">ยอดสั่งซื้อ: <?= $sum_order_; ?> บาท</h4>
                    <label for="quantity">ยอดโอน</label>
                    <input type="number" name="quantity" id="quantity" min="0" step="0.1" required>
                    <div class="form-group">
                        <label for="ORDER_PAYMENT_IMAGE">หลักฐานการโอน <span class="text-danger">(PNG, JPG,
                                JPEG)</span></label>
                        <input type="file" class="form-control-file" name="ORDER_PAYMENT_IMAGE" id="ORDER_PAYMENT_IMAGE"
                            accept="image/png, image/jpeg" required>

                    </div>
                    <button type="submit" class="order-button">ยืนยันการสั่งซื้อ</button>
                </form>
            </div>

        </div>
    </section>

    <?php include_once('./inc/footer.php'); ?>
    <script src="./assets/js/carts.js"></script>

</body>

</html>