<?php

include_once("./condb.php"); // เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === "POST") { // ตรวจสอบว่าคำขอเป็นแบบ POST หรือไม่

    $rp = false; // กำหนดตัวแปรตรวจสอบผลลัพธ์

    if (isset($_POST["TYPE"]) && $_POST["TYPE"] === "ADD") { // ตรวจสอบว่าคำขอเป็นการเพิ่มผู้ใช้หรือไม่

        // รับค่าจากฟอร์ม
        $USER_FNAME = $_POST["USER_FNAME"];
        $USER_LNAME = $_POST["USER_LNAME"];
        $USER_PHONE = $_POST["USER_PHONE"];
        $USER_ADDRESS = $_POST["USER_ADDRESS"];
        $USER_USERNAME = $_POST["USER_USERNAME"];
        $USER_PASSWORD = $_POST["USER_PASSWORD"];

        // ตรวจสอบว่าชื่อผู้ใช้ซ้ำหรือไม่
        $sql = "SELECT * FROM `tbl_users` WHERE USER_USERNAME ='$USER_USERNAME'";
        $query_row = $conn->query($sql)->fetch_all();

        if (count($query_row) > 0) { // ถ้าชื่อผู้ใช้ซ้ำ แสดงข้อความแจ้งเตือน
            echo "
            <script>
                alert('ชื่อผู้ใช้ซ้ำโปรดใช้ชื่อใหม่');
                window.history.back(-1);
            </script>";
            exit;
        }

        // เพิ่มข้อมูลผู้ใช้ลงในฐานข้อมูล
        $sql = "INSERT INTO `tbl_users` (`USER_ID`, `USER_FNAME`, `USER_LNAME`, `USER_PHONE`, `USER_ADDRESS`, `USER_USERNAME`, `USER_PASSWORD`, `USER_STAMP`, `USER_ROLE`, `USER_DELETE`, `USER_DELETE_TIME`, `USER_DELETE_USER`) 
                            VALUES (NULL, '$USER_FNAME', '$USER_LNAME', '$USER_PHONE', '$USER_ADDRESS', '$USER_USERNAME', '$USER_PASSWORD', current_timestamp(), 'USER', NULL, NULL, NULL);";
        $query = $conn->query($sql);

        if ($query) { // ถ้าการบันทึกสำเร็จ ตั้งค่า $rp เป็น true
            $rp = true;
        }
    }

    if ($rp) { // ถ้าบันทึกสำเร็จ แสดงข้อความแจ้งเตือนและเปลี่ยนเส้นทาง
        echo "
            <script>
                alert('บันทึกผลสำเร็จ');
                location.assign('./index.php');
            </script>";
        exit;
    } else { // ถ้าบันทึกไม่สำเร็จ แสดงข้อความแจ้งเตือนและกลับหน้าเดิม
        echo "
        <script>
            alert('บันทึกผลไม่สำเร็จ');
            window.history.back(-1);
        </script>";
    }
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="เข้าสู่ระบบ - SHOPPING">
    <meta name="author" content="เข้าสู่ระบบ - SHOPPING">

    <title>เข้าสู่ระบบ - SHOPPING</title>

    <?php include_once('./inc/head.php') ?>


</head>

<body>

    <!-- ***** Preloader Start ***** -->
    <div id="preloader">
        <div class="jumper">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <!-- ***** Preloader End ***** -->

    <!-- ***** Header Area Start ***** -->
    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <!-- ***** Logo Start ***** -->
                        <a href="./" class="logo">
                            <img src="./assets/images/logo.png">
                        </a>
                        <!-- ***** Logo End ***** -->
                        <!-- ***** Menu Start ***** -->
                        <ul class="nav">
                            <li class="scroll-to-section"><a href="./" class="active">เข้าสู่ระบบ</a></li>
                        </ul>
                        <a class='menu-trigger'>
                            <span>Menu</span>
                        </a>
                        <!-- ***** Menu End ***** -->
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <style>
        .contact-us {
            margin-top: 114px;
            border-bottom: 3px dotted #eee;
            padding-bottom: 90px;
        }
    </style>

    <div class="contact-us">
        <div class="container">
            <form id="form-login" action="./register.php" method="post" enctype="multipart/form-data">

                <input type="hidden" name="TYPE" value="ADD">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-heading">
                            <h2>สมัครสมาชิก</h2>
                            <span>สมัครสมาชิกเพื่อสั่งซื้อสินค้าจากร้านค้า</span>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h6 class="mb-2 text-primary">ข้อมูลส่วนตัว</h6>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>ชื่อ</label>
                            <input required type="text" class="form-control" placeholder="ระบุชื่อ" name="USER_FNAME">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>นามสกุล</label>
                            <input required type="text" class="form-control" placeholder="ระบุนามสกุล"
                                name="USER_LNAME">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>เบอร์มือถือ</label>
                            <input required type="tel" class="form-control" placeholder="ระบุเบอร์มือถือ"
                                name="USER_PHONE">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group ">
                            <label>ที่อยู่(จัดส่งสินค้า)</label>
                            <textarea required class="form-control mt-0" name="USER_ADDRESS" rows="3"></textarea>
                        </div>

                    </div>

                    <div class="col-12">
                        <h6 class="mb-2 mt-2 text-primary ">ข้อมูลเข้าสู่ระบบ</h6>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>ชื่อผู้ใช้</label>
                            <input required type="text" class="form-control" placeholder="ระบุชื่อผู้ใช้"
                                name="USER_USERNAME">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>รหัสผ่าน</label>
                            <input required type="password" class="form-control" placeholder="ระบุรหัสผ่าน"
                                name="USER_PASSWORD">
                        </div>
                    </div>

                    <div class="col-lg-12 m-2">
                        <button type="submit" id="form-submit" class="main-dark-button">
                            สมัครสมาชิก <i class="fa fa-edit"></i>
                        </button>
                        <a class="text-dark" href="./index.php">
                            เข้าสู่ระบบ <i class="fa fa-info"></i>
                        </a>
                    </div>

                </div>

            </form>
        </div>
    </div>
    <?php include_once('./inc/footer.php'); ?>


</body>

</html>