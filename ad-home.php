<?php include_once('./inc/inc-html.php') ?>





<?php
// รับค่าเมื่อมีการกด "ยืนยัน" หรือ "ยกเลิก"
if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET["idConfirem"]) && isset($_GET["type"])) {

    // รับค่า ID ของคำสั่งซื้อจาก URL
    $id = $_GET["idConfirem"];

    // กำหนดค่าประเภทการยืนยัน
    // หากค่า type เป็น 'success' จะตั้งค่าเป็น "1" (อนุมัติ)
    // หากค่า type ไม่ใช่ 'success' จะตั้งค่าเป็น "0" (ไม่อนุมัติ)
    $type = $_GET["type"] == 'success' ? "1" : "0";

    // ค้นหาคำสั่งซื้อในฐานข้อมูล โดยใช้ ORDER_ID ที่ได้รับมา
    $query_row = $conn->query("SELECT * FROM `tbl_orders` WHERE ORDER_ID = {$id}  ;", MYSQLI_ASSOC)->fetch_assoc();

    // ตรวจสอบว่าพบข้อมูลคำสั่งซื้อหรือไม่
    if ($query_row) {
        // อัปเดตสถานะการยืนยันการชำระเงินของคำสั่งซื้อนี้
        $update = $conn->query("UPDATE `tbl_orders` SET 
                `ORDER_PAYMENT_CONFIRM` = '$type' 
                WHERE `tbl_orders`.`ORDER_ID` = '$id';");

        // ตรวจสอบว่าอัปเดตสำเร็จหรือไม่
        if ($update) {
            echo "
            <script>
                alert('บันทึกผลสำเร็จ') // แจ้งเตือนผู้ใช้
                location.assign('./ad-home.php') // เปลี่ยนเส้นทางกลับไปที่หน้าแอดมินหลัก
            </script>";
            exit;
        } else {
            echo "
            <script>
                alert('บันทึกผลไม่สำเร็จ') // แจ้งเตือนว่าบันทึกข้อมูลล้มเหลว
                window.history.back(-1) // กลับไปหน้าก่อนหน้า
            </script>";
        }

    } else {
        echo "
        <script>
            alert('บันทึกผลไม่สำเร็จ') // แจ้งเตือนว่าคำสั่งซื้อนี้ไม่มีอยู่
            window.history.back(-1) // กลับไปหน้าก่อนหน้า
        </script>";
    }
}


?>









<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="หน้าหลัก - SHOPPING">
    <meta name="author" content="หน้าหลัก - SHOPPING">
    <title>หน้าหลัก - SHOPPING</title>

    <?php $_SESSION["page"] = 'home'; ?>
    <?php include_once('./inc/head.php') ?>

</head>

<body>
    <?php include_once('./inc/header.php') ?>

    <?php
    // คิวรี รอตรวจสอบ
    $query_all_w = $conn->query("SELECT * FROM `tbl_orders` INNER JOIN `tbl_users` ON `tbl_users`.USER_ID = `tbl_orders`.USER_ID WHERE `tbl_orders`.ORDER_PAYMENT_CONFIRM IS NULL ;", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
    ?>
    <?php
    // คิวรีตรวจสอบแล้ว
    $query_all = $conn->query("SELECT * FROM `tbl_orders` INNER JOIN `tbl_users` ON `tbl_users`.USER_ID = `tbl_orders`.USER_ID WHERE `tbl_orders`.ORDER_PAYMENT_CONFIRM = '1'  ;", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
    ?>

    <?php
    // คิวรีไม่ผ่าานตรวจสอบ
    $query_all_c = $conn->query("SELECT * FROM `tbl_orders` INNER JOIN `tbl_users` ON `tbl_users`.USER_ID = `tbl_orders`.USER_ID WHERE `tbl_orders`.ORDER_PAYMENT_CONFIRM = '0'  ;", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
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
                                            rel="noopener noreferrer" class="btn btn-sm btn-dark ">ใบเสร็จ</a>
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
                                                <button type="button"
                                                    onclick="clickConfirem('<?= $item['ORDER_ID'] ?>','success')"
                                                    class="btn btn-success">
                                                    ยืนยันการชำระ
                                                </button>
                                                <button type="button"
                                                    onclick="clickConfirem('<?= $item['ORDER_ID'] ?>','not')"
                                                    class="btn btn-outline-danger">ยอดชำระไม่ถูกต้อง</button>
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
                                            rel="noopener noreferrer" class="btn btn-sm btn-dark ">ใบเสร็จ</a>
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
                                            rel="noopener noreferrer" class="btn btn-sm btn-dark ">ใบเสร็จ</a>
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

            </div>






        </div>
    </section>

    <?php include_once('./inc/footer.php'); ?>
    <script src="./assets/js/ad-home.js"></script>


</body>

</html>