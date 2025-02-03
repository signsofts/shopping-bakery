<?php include_once('./inc/inc-html.php') ?>



<?php
function uploadeImage($_FILEPHOTO) 
{
    $uploadDir = "upload/slip/"; // กำหนดโฟลเดอร์ปลายทางที่ใช้เก็บไฟล์อัปโหลด

    // ตรวจสอบว่าโฟลเดอร์ปลายทางมีอยู่หรือไม่ ถ้าไม่มีให้สร้างใหม่
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // กำหนดค่าเริ่มต้นของตัวแปรที่ใช้เก็บชื่อไฟล์ที่อัปโหลด
    $imageName = false;

    // ตรวจสอบว่าไฟล์ถูกอัปโหลดและไม่มีข้อผิดพลาด
    if (isset($_FILEPHOTO) && $_FILEPHOTO['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILEPHOTO['tmp_name']; // ที่อยู่ชั่วคราวของไฟล์
        $fileName = $_FILEPHOTO['name']; // ชื่อไฟล์
        $fileSize = $_FILEPHOTO['size']; // ขนาดไฟล์
        $fileType = $_FILEPHOTO['type']; // ประเภทของไฟล์ (MIME type)

        // ดึงนามสกุลของไฟล์
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // กำหนดประเภทไฟล์ที่อนุญาตให้อัปโหลด
        $allowedExtensions = ['png', 'jpg', 'jpeg'];

        // ตรวจสอบว่านามสกุลไฟล์ถูกต้องหรือไม่
        if (in_array($fileExtension, $allowedExtensions)) {
            // สร้างชื่อไฟล์ใหม่ให้เป็นเอกลักษณ์ ป้องกันการชนกัน
            $newFileName = uniqid('image_', true) . '.' . $fileExtension;

            // กำหนดเส้นทางไฟล์ปลายทาง
            $destPath = $uploadDir . $newFileName;

            // ย้ายไฟล์จากที่อยู่ชั่วคราวไปยังโฟลเดอร์ที่กำหนด
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imageName = $newFileName; // เก็บชื่อไฟล์ที่อัปโหลดสำเร็จ
            } else {
                $imageName = false; // แจ้งว่าอัปโหลดไม่สำเร็จ
            }
        } else {
            $imageName = false; // แจ้งว่าประเภทไฟล์ไม่ถูกต้อง
        }
    } else {
        $imageName = false; // แจ้งว่าไม่มีไฟล์อัปโหลดหรือมีข้อผิดพลาด
    }

    return $imageName; // ส่งค่าชื่อไฟล์กลับ ถ้าอัปโหลดไม่สำเร็จจะคืนค่า false
}

// ------------------------------------------------------
// แจ้งชำระเงินใหม่
// ------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $quantity = $_POST['quantity']; // จำนวนเงินที่โอน
    $product_price = $_POST['product_price']; // ราคาสินค้า

    $type = $_POST["TYPE"];   // ประเภทคำสั่ง (เช่น RENEW)
    $ORDER_ID = $_POST["ORDER_ID"]; // หมายเลขคำสั่งซื้อ

    // ตรวจสอบว่าค่าประเภทคำสั่งถูกต้องหรือไม่
    if ($type != "RENEW") {
        echo "
        <script>
            alert('เกิดข้อผิดพลาด')
            location.assign('./carts.php')
        </script>";
        exit;
    }

    // อัปโหลดไฟล์หลักฐานการโอนเงิน
    $ORDER_PAYMENT_IMAGE = uploadeImage($_FILES['ORDER_PAYMENT_IMAGE']);
    if ($ORDER_PAYMENT_IMAGE === false) {
        return false; // ถ้าอัปโหลดไม่สำเร็จ ให้หยุดการทำงาน
    }

    // อัปเดตข้อมูลการชำระเงินในฐานข้อมูล
    $sql_up = "UPDATE `tbl_orders` SET 
                            `ORDER_PAYMENT_IMAGE` = '$ORDER_PAYMENT_IMAGE', 
                            `ORDER_PAYMENT_PRICE` = '$quantity', 
                            `ORDER_PAYMENT_CONFIRM` = NULL 
                            WHERE `tbl_orders`.`ORDER_ID` = '$ORDER_ID';";

    $qur = $conn->query($sql_up);

    if ($qur) {
        echo "
        <script>
            alert('แจ้งชำระใหม่สำเร็จ! รหัสคำสั่งซื้อของคุณคือ: ORDER-$ORDER_ID-TH ')
            location.assign('./history.php')
        </script>";
        exit;
    }
}

// ------------------------------------------------------
// รับค่าเมื่อมีการกด ยืนยัน หรือ ยกเลิก
// ------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET["idRe"]) && isset($_GET["type"])) {

    $id = $_GET["idRe"]; // รหัสคำสั่งซื้อ
    $type = $_GET["type"]; // ประเภทคำสั่ง (ยืนยันหรือยกเลิก)

    // ค้นหาข้อมูลคำสั่งซื้อจากฐานข้อมูล
    $query_row = $conn->query("SELECT * FROM `tbl_orders` WHERE ORDER_ID = {$id};", MYSQLI_ASSOC)->fetch_assoc();

    // ตรวจสอบว่าพบข้อมูลหรือไม่
    if ($query_row) {
        $update = false;

        if ($type == 're') {
            $update = $conn->query("UPDATE `tbl_orders` SET 
            `ORDER_PAYMENT_CONFIRM` = NULL 
            WHERE `tbl_orders`.`ORDER_ID` = '$id';");
        }

        if ($update) {
            echo "
            <script>
                alert('บันทึกผลสำเร็จ')
                location.assign('./ad-home.php')
            </script>";
            exit;
        } else {
            echo "
            <script>
                alert('บันทึกผลไม่สำเร็จ')
                window.history.back(-1)
            </script>";
        }
    } else {
        echo "
        <script>
            alert('บันทึกผลไม่สำเร็จ')
            window.history.back(-1)
        </script>";
    }
}

?>


<!DOCTYPE html>
<html lang="th">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="ประวัติสั่งซื้อ - SHOPPING">
    <meta name="author" content="ประวัติสั่งซื้อ - SHOPPING">

    <title>ประวัติสั่งซื้อ - SHOPPING</title>

    <?php $_SESSION["page"] = 'history'; ?>
    <?php include_once('./inc/head.php') ?>

</head>

<body>
    <?php include_once('./inc/header.php') ?>



    <?php
    // คิวรี รอตรวจสอบ
    $query_all_w = $conn->query("SELECT * FROM `tbl_orders` INNER JOIN `tbl_users` ON `tbl_users`.USER_ID = `tbl_orders`.USER_ID WHERE `tbl_orders`.USER_ID='$gUSER_ID'  AND `tbl_orders`.ORDER_PAYMENT_CONFIRM IS NULL ;", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
    ?>
    <?php
    // คิวรีตรวจสอบแล้ว
    $query_all = $conn->query("SELECT * FROM `tbl_orders` INNER JOIN `tbl_users` ON `tbl_users`.USER_ID = `tbl_orders`.USER_ID WHERE `tbl_orders`.USER_ID='$gUSER_ID'  AND `tbl_orders`.ORDER_PAYMENT_CONFIRM = '1'  ;", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
    ?>
    <?php
    // คิวรีไม่ผ่าานตรวจสอบ
    $query_all_c = $conn->query("SELECT * FROM `tbl_orders` INNER JOIN `tbl_users` ON `tbl_users`.USER_ID = `tbl_orders`.USER_ID WHERE `tbl_orders`.USER_ID='$gUSER_ID'  AND `tbl_orders`.ORDER_PAYMENT_CONFIRM = '0'  ;", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
    ?>

    <section class="section  mt-5" id="men">
        <div class="container">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                        aria-controls="home" aria-selected="true">รอตรวจสอบ
                        <span class="badge badge-warning">
                            <?= count($query_all_w); ?>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                        aria-controls="profile" aria-selected="false">ตรวจสอบแล้ว
                        <span class="badge badge-success">
                            <?= count($query_all); ?>
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="profile-c-tab" data-toggle="tab" href="#profile-c" role="tab"
                        aria-controls="profile" aria-selected="false">ไม่ผ่านตรวจสอบ
                        <span class="badge badge-danger">
                            <?= count($query_all_c); ?>
                        </span>
                    </a>
                </li>


            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active pt-5" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <table class="table table-bordered table-show  mt-5">
                        <thead>
                            <tr>
                                <th style="width: 1%;">ลำดับ</th>
                                <th class="text-start">วันที่สั่งซื้อ</th>
                                <th class="text-start">รหัสคำสั่งซื้อ</th>
                                <th class="text-center">ราคารวม</th>
                                <th class="text-center">การชำระเงิน</th>
                                <th class="text-center">หลักฐานการชำระ</th>
                                <th class="text-center">ยอดชำระ</th>
                                <th class="text-center">เพิ่มเติม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($query_all_w as $key => $item): ?>
                                <tr>
                                    <td class="text-center" style="width: 1%;"><?= $key + 1; ?></td>
                                    <td class="text-start"><?= date("d-m-Y", strtotime($item['ORDER_STAMP'])); ?></td>
                                    <td class="text-start">ORDER-<?= $item['ORDER_ID'] ?>-TH</td>
                                    <td class="text-center"><?= $item['ORDER_PRICE']; ?> บาท</td>
                                    <td class="text-center">
                                        <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                            <span class="text-success">เสร็จสิ้น</span>
                                        <?php else: ?>
                                            <span class="text-danger">รอชำระ</span>
                                        <?php endif ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                            <a href="./upload/slip/<?= $item['ORDER_PAYMENT_IMAGE']; ?>" target="_blank"
                                                rel="noopener noreferrer">หลักฐานการชำระ</a>
                                        <?php else: ?>
                                            -
                                        <?php endif ?>
                                    </td>
                                    <td><?= $item['ORDER_PAYMENT_PRICE']; ?> บาท</td>
                                    <td class="text-center">
                                        <a href="./print.php?id=<?php echo $item['ORDER_ID'] ?>" target="_blank"
                                            rel="noopener noreferrer" class="btn btn-sm btn-dark " >ใบเสร็จ</a>
                                        <button class="btn btn-sm btn-outline-primary" data-toggle="modal"
                                            data-target="#modal-show-order-w-<?= $item['ORDER_ID'] ?>"> รายละเอียด </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modal-show-order-w-<?= $item['ORDER_ID'] ?>" tabindex="-1"
                                    role="dialog" aria-labelledby="modal-show-order-w-<?= $item['ORDER_ID'] ?>Label"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="modal-show-order-w-<?= $item['ORDER_ID'] ?>Label">
                                                    รายละเอียดคำสั่งซื้อ (
                                                    <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                                        <span class="text-success">เสร็จสิ้น</span>
                                                    <?php else: ?>
                                                        <span class="text-danger">รอชำระ</span>
                                                    <?php endif ?>
                                                    )
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class=" pt-2 pb-2 bg-secondary col-1"><b>ลำดับ</b></div>
                                                            <div class=" pt-2 pb-2 bg-secondary col"><b>ชื่อสินค้า</b></div>
                                                            <div class=" pt-2 pb-2 bg-secondary col-2"><b>จำนวน</b></div>
                                                            <div class=" pt-2 pb-2 bg-secondary col-2"><b>ราคา</b></div>
                                                            <div class=" pt-2 pb-2 bg-secondary col-2"><b>รวม</b></div>
                                                        </div>
                                                        <?php

                                                        $query_all_list = $conn->query("SELECT * FROM `tbl_orders` INNER JOIN `tbl_users` ON `tbl_users`.USER_ID = `tbl_orders`.USER_ID INNER JOIN tbl_order_lists ON tbl_orders.ORDER_ID = tbl_order_lists.ORDER_ID INNER JOIN tbl_products ON tbl_products.PRO_ID = tbl_order_lists.PRO_ID WHERE tbl_orders.ORDER_ID = '{$item['ORDER_ID']}';", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
                                                        $sum_order_list = 0;
                                                        ?>

                                                        <?php foreach ($query_all_list as $ke => $it): ?>
                                                            <div class="row">
                                                                <div class=" pt-2 pb-2  col-1"><?= $ke + 1; ?></div>
                                                                <div class=" pt-2 pb-2  col">
                                                                    <?= $it['PRO_NAME']; ?>
                                                                </div>
                                                                <div class=" pt-2 pb-2  col-2">
                                                                    <?= $it['OLIST_NUMBER']; ?>
                                                                </div>
                                                                <div class=" pt-2 pb-2  col-2">
                                                                    <?= $it['OLIST_PRICE']; ?> บาท
                                                                </div>
                                                                <div class=" pt-2 pb-2  col-2">
                                                                    <?= $it['OLIST_PRICE'] * $it['OLIST_NUMBER']; ?> บาท
                                                                </div>
                                                            </div>

                                                            <?php $sum_order_list += $it['OLIST_PRICE'] * $it['OLIST_NUMBER']; ?>
                                                        <?php endforeach ?>

                                                        <div class="row">
                                                            <div class=" pt-2 pb-2  col-1"></div>
                                                            <div class=" pt-2 pb-2  col"><b>ยอดรวม</b></div>
                                                            <div class=" pt-2 pb-2  col-2">
                                                            </div>
                                                            <div class=" pt-2 pb-2  col-2">
                                                            </div>
                                                            <div class=" pt-2 pb-2  col-2">
                                                                <b><?= $sum_order_list ?> บาท</b>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <hr class=" mt-4">
                                                <div class="row">
                                                    <div class="col-12"><b>การชำระ</b></div>
                                                    <div class="col-12">สถานะ :
                                                        <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                                            <span class="text-success">เสร็จสิ้น</span>
                                                        <?php else: ?>
                                                            <span class="text-danger">รอชำระ</span>
                                                        <?php endif ?>
                                                    </div>
                                                    <div class="col-12">ยอดชำระ :
                                                        <span><?= $item['ORDER_PAYMENT_PRICE']; ?></span>
                                                        บาท
                                                    </div>
                                                    <div class="col-12">หลักฐาน :
                                                        <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                                            <a href="./upload/slip/<?= $item['ORDER_PAYMENT_IMAGE']; ?>"
                                                                target="_blank" rel="noopener noreferrer">หลักฐานการชำระ</a>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">ปิด</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach ?>

                        </tbody>
                    </table>


                </div>
                <div class="tab-pane fade  pt-5" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <table class="table table-bordered table-show  mt-5">
                        <thead>
                            <tr>
                                <th style="width: 1%;">ลำดับ</th>
                                <th class="text-start">วันที่สั่งซื้อ</th>
                                <th class="text-start">รหัสคำสั่งซื้อ</th>
                                <th class="text-center">ราคารวม</th>
                                <th class="text-center">การชำระเงิน</th>
                                <th class="text-center">หลักฐานการชำระ</th>
                                <th class="text-center">ยอดชำระ</th>
                                <th class="text-center">เพิ่มเติม</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($query_all as $key => $item): ?>
                                <tr>
                                    <td class="text-center" style="width: 1%;"><?= $key + 1; ?></td>
                                    <td class="text-start"><?= date("d-m-Y", strtotime($item['ORDER_STAMP'])); ?></td>
                                    <td class="text-start">ORDER-<?= $item['ORDER_ID'] ?>-TH</td>
                                    <td class="text-center"><?= $item['ORDER_PRICE']; ?> บาท</td>
                                    <td class="text-center">
                                        <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                            <span class="text-success">เสร็จสิ้น</span>
                                        <?php else: ?>
                                            <span class="text-danger">รอชำระ</span>
                                        <?php endif ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                            <a href="./upload/slip/<?= $item['ORDER_PAYMENT_IMAGE']; ?>" target="_blank"
                                                rel="noopener noreferrer">หลักฐานการชำระ</a>
                                        <?php else: ?>
                                            -
                                        <?php endif ?>

                                    </td>
                                    <td><?= $item['ORDER_PAYMENT_PRICE']; ?> บาท</td>
                                    <td class="text-center">
                                        <a href="./print.php?id=<?php echo $item['ORDER_ID'] ?>" target="_blank"
                                            rel="noopener noreferrer" class="btn btn-sm btn-dark " >ใบเสร็จ</a>
                                        <button class="btn btn-sm btn-outline-primary" data-toggle="modal"
                                            data-target="#modal-show-order-<?= $item['ORDER_ID'] ?>"> รายละเอียด </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modal-show-order-<?= $item['ORDER_ID'] ?>" tabindex="-1"
                                    role="dialog" aria-labelledby="modal-show-order-<?= $item['ORDER_ID'] ?>Label"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modal-show-order-<?= $item['ORDER_ID'] ?>Label">
                                                    รายละเอียดคำสั่งซื้อ (
                                                    <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                                        <span class="text-success">เสร็จสิ้น</span>
                                                    <?php else: ?>
                                                        <span class="text-danger">รอชำระ</span>
                                                    <?php endif ?>
                                                    )
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">


                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class=" pt-2 pb-2 bg-secondary col-1"><b>ลำดับ</b></div>
                                                            <div class=" pt-2 pb-2 bg-secondary col"><b>ชื่อสินค้า</b></div>
                                                            <div class=" pt-2 pb-2 bg-secondary col-2"><b>จำนวน</b></div>
                                                            <div class=" pt-2 pb-2 bg-secondary col-2"><b>ราคา</b></div>
                                                            <div class=" pt-2 pb-2 bg-secondary col-2"><b>รวม</b></div>
                                                        </div>
                                                        <?php

                                                        $query_all_list = $conn->query("SELECT * FROM `tbl_orders` INNER JOIN `tbl_users` ON `tbl_users`.USER_ID = `tbl_orders`.USER_ID INNER JOIN tbl_order_lists ON tbl_orders.ORDER_ID = tbl_order_lists.ORDER_ID INNER JOIN tbl_products ON tbl_products.PRO_ID = tbl_order_lists.PRO_ID WHERE tbl_orders.ORDER_ID = '{$item['ORDER_ID']}';", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
                                                        $sum_order_list = 0;
                                                        ?>

                                                        <?php foreach ($query_all_list as $ke => $it): ?>
                                                            <div class="row">
                                                                <div class=" pt-2 pb-2  col-1"><?= $ke + 1; ?></div>
                                                                <div class=" pt-2 pb-2  col">
                                                                    <?= $it['PRO_NAME']; ?>
                                                                </div>
                                                                <div class=" pt-2 pb-2  col-2">
                                                                    <?= $it['OLIST_NUMBER']; ?>
                                                                </div>
                                                                <div class=" pt-2 pb-2  col-2">
                                                                    <?= $it['OLIST_PRICE']; ?> บาท
                                                                </div>
                                                                <div class=" pt-2 pb-2  col-2">
                                                                    <?= $it['OLIST_PRICE'] * $it['OLIST_NUMBER']; ?> บาท
                                                                </div>
                                                            </div>

                                                            <?php $sum_order_list += $it['OLIST_PRICE'] * $it['OLIST_NUMBER']; ?>
                                                        <?php endforeach ?>

                                                        <div class="row">
                                                            <div class=" pt-2 pb-2  col-1"></div>
                                                            <div class=" pt-2 pb-2  col"><b>ยอดรวม</b></div>
                                                            <div class=" pt-2 pb-2  col-2">
                                                            </div>
                                                            <div class=" pt-2 pb-2  col-2">
                                                            </div>
                                                            <div class=" pt-2 pb-2  col-2">
                                                                <b><?= $sum_order_list ?> บาท</b>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <hr class=" mt-4">
                                                <div class="row">
                                                    <div class="col-12"><b>การชำระ</b></div>
                                                    <div class="col-12">สถานะ :
                                                        <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                                            <span class="text-success">เสร็จสิ้น</span>
                                                        <?php else: ?>
                                                            <span class="text-danger">รอชำระ</span>
                                                        <?php endif ?>
                                                    </div>
                                                    <div class="col-12">ยอดชำระ :
                                                        <span><?= $item['ORDER_PAYMENT_PRICE']; ?></span>
                                                        บาท
                                                    </div>
                                                    <div class="col-12">หลักฐาน :
                                                        <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                                            <a href="./upload/slip/<?= $item['ORDER_PAYMENT_IMAGE']; ?>"
                                                                target="_blank" rel="noopener noreferrer">หลักฐานการชำระ</a>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">ปิด</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach ?>

                        </tbody>
                    </table>


                </div>
                <div class="tab-pane fade  pt-5" id="profile-c" role="tabpanel" aria-labelledby="profile-c-tab">
                    <table class="table table-bordered table-show  mt-5">
                        <thead>
                            <tr>
                                <th style="width: 1%;">ลำดับ</th>
                                <th class="text-start">วันที่สั่งซื้อ</th>
                                <th class="text-start">รหัสคำสั่งซื้อ</th>
                                <th class="text-center">ราคารวม</th>
                                <th class="text-center">การชำระเงิน</th>
                                <th class="text-center">หลักฐานการชำระ</th>
                                <th class="text-center">ยอดชำระ</th>
                                <th class="text-center">เพิ่มเติม</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($query_all_c as $key => $item): ?>
                                <tr>
                                    <td class="text-center" style="width: 1%;"><?= $key + 1; ?></td>
                                    <td class="text-start"><?= date("d-m-Y", strtotime($item['ORDER_STAMP'])); ?></td>
                                    <td class="text-start">ORDER-<?= $item['ORDER_ID'] ?>-TH</td>
                                    <td class="text-center"><?= $item['ORDER_PRICE']; ?> บาท</td>
                                    <td class="text-center">
                                        <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                            <span class="text-success">เสร็จสิ้น</span>
                                        <?php else: ?>
                                            <span class="text-danger">รอชำระ</span>
                                        <?php endif ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                            <a href="./upload/slip/<?= $item['ORDER_PAYMENT_IMAGE']; ?>" target="_blank"
                                                rel="noopener noreferrer">หลักฐานการชำระ</a>
                                        <?php else: ?>
                                            -
                                        <?php endif ?>

                                    </td>
                                    <td><?= $item['ORDER_PAYMENT_PRICE']; ?> บาท</td>
                                    <td class="text-center">
                                        <a href="./print.php?id=<?php echo $item['ORDER_ID'] ?>" target="_blank"
                                            rel="noopener noreferrer" class="btn btn-sm btn-dark " >ใบเสร็จ</a>
                                        <button class="btn btn-sm btn-outline-primary" data-toggle="modal"
                                            data-target="#modal-show-order-c-<?= $item['ORDER_ID'] ?>"> รายละเอียด </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modal-show-order-c-<?= $item['ORDER_ID'] ?>" tabindex="-1"
                                    role="dialog" aria-labelledby="modal-show-order-c-<?= $item['ORDER_ID'] ?>Label"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="modal-show-order-c-<?= $item['ORDER_ID'] ?>Label">
                                                    รายละเอียดคำสั่งซื้อ (
                                                    <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                                        <span class="text-success">เสร็จสิ้น</span>
                                                    <?php else: ?>
                                                        <span class="text-danger">รอชำระ</span>
                                                    <?php endif ?>
                                                    )
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">


                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class=" pt-2 pb-2 bg-secondary col-1"><b>ลำดับ</b></div>
                                                            <div class=" pt-2 pb-2 bg-secondary col"><b>ชื่อสินค้า</b></div>
                                                            <div class=" pt-2 pb-2 bg-secondary col-2"><b>จำนวน</b></div>
                                                            <div class=" pt-2 pb-2 bg-secondary col-2"><b>ราคา</b></div>
                                                            <div class=" pt-2 pb-2 bg-secondary col-2"><b>รวม</b></div>
                                                        </div>
                                                        <?php

                                                        $query_all_list = $conn->query("SELECT * FROM `tbl_orders` INNER JOIN `tbl_users` ON `tbl_users`.USER_ID = `tbl_orders`.USER_ID INNER JOIN tbl_order_lists ON tbl_orders.ORDER_ID = tbl_order_lists.ORDER_ID INNER JOIN tbl_products ON tbl_products.PRO_ID = tbl_order_lists.PRO_ID WHERE tbl_orders.ORDER_ID = '{$item['ORDER_ID']}';", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
                                                        $sum_order_list = 0;
                                                        ?>

                                                        <?php foreach ($query_all_list as $ke => $it): ?>
                                                            <div class="row">
                                                                <div class=" pt-2 pb-2  col-1"><?= $ke + 1; ?></div>
                                                                <div class=" pt-2 pb-2  col">
                                                                    <?= $it['PRO_NAME']; ?>
                                                                </div>
                                                                <div class=" pt-2 pb-2  col-2">
                                                                    <?= $it['OLIST_NUMBER']; ?>
                                                                </div>
                                                                <div class=" pt-2 pb-2  col-2">
                                                                    <?= $it['OLIST_PRICE']; ?> บาท
                                                                </div>
                                                                <div class=" pt-2 pb-2  col-2">
                                                                    <?= $it['OLIST_PRICE'] * $it['OLIST_NUMBER']; ?> บาท
                                                                </div>
                                                            </div>

                                                            <?php $sum_order_list += $it['OLIST_PRICE'] * $it['OLIST_NUMBER']; ?>
                                                        <?php endforeach ?>

                                                        <div class="row">
                                                            <div class=" pt-2 pb-2  col-1"></div>
                                                            <div class=" pt-2 pb-2  col"><b>ยอดรวม</b></div>
                                                            <div class=" pt-2 pb-2  col-2">
                                                            </div>
                                                            <div class=" pt-2 pb-2  col-2">
                                                            </div>
                                                            <div class=" pt-2 pb-2  col-2">
                                                                <b><?= $sum_order_list ?> บาท</b>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <hr class=" mt-4">
                                                <div class="row">
                                                    <div class="col-12"><b>การชำระ</b></div>
                                                    <div class="col-12">สถานะ :
                                                        <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                                            <span class="text-success">เสร็จสิ้น</span>
                                                        <?php else: ?>
                                                            <span class="text-danger">รอชำระ</span>
                                                        <?php endif ?>
                                                    </div>
                                                    <div class="col-12">ยอดชำระ :
                                                        <span><?= $item['ORDER_PAYMENT_PRICE']; ?></span>
                                                        บาท
                                                    </div>
                                                    <div class="col-12">หลักฐาน :
                                                        <?php if (!is_null($item['ORDER_PAYMENT_IMAGE'])): ?>
                                                            <a href="./upload/slip/<?= $item['ORDER_PAYMENT_IMAGE']; ?>"
                                                                target="_blank" rel="noopener noreferrer">หลักฐานการชำระ</a>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif ?>
                                                    </div>
                                                </div>


                                                <hr>

                                                <div id="editForm-c-<?= $item['ORDER_ID'] ?>" class="row"
                                                    style="display: none;">
                                                    <form id="form-login" action="./history.php" method="post"
                                                        enctype="multipart/form-data">
                                                        <input type="hidden" name="TYPE" value="RENEW">
                                                        <input type="hidden" name="ORDER_ID"
                                                            value="<?= $item['ORDER_ID']; ?>">
                                                        <input type="hidden" name="product_price"
                                                            value="<?= $sum_order_list; ?>">

                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="bank-info">
                                                                    <h2>ช่องทางการชำระเงิน</h2>
                                                                    <!-- ข้อมูลธนาคาร -->
                                                                    <div class="bank-account">
                                                                        <h3>บัญชีธนาคาร</h3>
                                                                        <p>ธนาคาร: <strong>กสิกรไทย (Kasikorn Bank)</strong>
                                                                        </p>
                                                                        <p>ชื่อบัญชี: <strong>บริษัท ABC จำกัด</strong></p>
                                                                        <p>เลขที่บัญชี: <strong>123-4-56789-0</strong></p>
                                                                    </div>

                                                                    <!-- ข้อมูลพร้อมเพย์ -->
                                                                    <div class="promptpay">
                                                                        <h3>พร้อมเพย์</h3>
                                                                        <p>ชื่อบัญชี: <strong>บริษัท ABC จำกัด</strong></p>
                                                                        <p>เลขพร้อมเพย์: <strong>0123456789012</strong></p>
                                                                        <p><img src="assets/images/promptpay-qr.jpg"
                                                                                alt="QR พร้อมเพย์" class="promptpay-qr"></p>
                                                                    </div>

                                                                    <h5><u>แจ้งชำระเงิน (ใหม่)</u></h5>
                                                                    <h4 class="text-danger">ยอดสั่งซื้อ:
                                                                        <?= $sum_order_list; ?>
                                                                        บาท
                                                                    </h4>
                                                                    <label for="quantity">ยอดโอน</label>
                                                                    <input type="number" name="quantity"
                                                                        class="form-control" id="quantity" min="0"
                                                                        step="0.1" required>
                                                                    <div class="form-group">
                                                                        <label for="ORDER_PAYMENT_IMAGE">หลักฐานการโอน <span
                                                                                class="text-danger">(PNG, JPG,
                                                                                JPEG)</span></label>
                                                                        <input type="file" class="form-control-file"
                                                                            name="ORDER_PAYMENT_IMAGE"
                                                                            id="ORDER_PAYMENT_IMAGE"
                                                                            accept="image/png, image/jpeg" required>

                                                                    </div>
                                                                    <div class="d-flex justify-content-end">
                                                                        <button type="submit" id="form-submit"
                                                                            class="btn btn-primary mr-3">
                                                                            บันทึก <i class="fa fa-edit"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn-secondary"
                                                                            onclick="location.reload()">ยกเลิก</button>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button id="btn-editForm-c-<?= $item['ORDER_ID'] ?>" type="button"
                                                    onclick="toggleEditForm(this,'<?= $item['ORDER_ID'] ?>')"
                                                    class="btn btn-outline-warning">แจ้งชำระใหม่
                                                </button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">ปิด</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach ?>

                        </tbody>
                    </table>


                </div>

            </div>






        </div>
    </section>

    <?php include_once('./inc/footer.php'); ?>
    <script src="./assets/js/history.js"></script>


</body>

</html>