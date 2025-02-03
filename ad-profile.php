<?php include_once('./inc/inc-html.php') ?>
<?php
// ตรวจสอบว่าการร้องขอเป็นแบบ POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $rp = false; // ตัวแปรสำหรับตรวจสอบผลลัพธ์การทำงาน

    // ตรวจสอบว่าค่า TYPE ใน POST เป็น "EDIT" หรือไม่
    if (isset($_POST["TYPE"]) && $_POST["TYPE"] === "EDIT") {
        $USER_ID = $gUSER_ID; // ดึงค่า USER_ID จากตัวแปร $gUSER_ID

        // ดึงข้อมูลผู้ใช้จากฐานข้อมูลตาม USER_ID
        $sql = "SELECT * FROM `tbl_users` WHERE USER_ID ='$USER_ID'";
        $query_row = $conn->query($sql)->fetch_assoc();

        // รับค่าข้อมูลจากฟอร์มที่ส่งมา
        $USER_FNAME = $_POST["USER_FNAME"];
        $USER_LNAME = $_POST["USER_LNAME"];
        $USER_PHONE = $_POST["USER_PHONE"];
        // $USER_ADDRESS = $_POST["USER_ADDRESS"]; // ตัวแปรนี้ถูกคอมเม้นไว้ ไม่ได้ใช้งาน
        $USER_USERNAME = $_POST["USER_USERNAME"];
        $USER_PASSWORD = $_POST["USER_PASSWORD"];

        // อัพเดตข้อมูลผู้ใช้ในฐานข้อมูล
        $sql = "UPDATE `tbl_users` 
                SET `USER_FNAME` = '$USER_FNAME', 
                    `USER_LNAME` = '$USER_LNAME', 
                    `USER_PHONE` = '$USER_PHONE', 
                    `USER_USERNAME` = '$USER_USERNAME', 
                    `USER_PASSWORD` = '$USER_PASSWORD' 
                WHERE `tbl_users`.`USER_ID` = '$USER_ID';";
        
        $query = $conn->query($sql); // รันคำสั่ง SQL อัพเดตข้อมูล
        
        if ($query) {
            $rp = true; // อัพเดตสำเร็จ
        }
    }

    // ตรวจสอบผลลัพธ์การอัพเดตและแสดงข้อความแจ้งเตือน
    if ($rp) {
        echo "
            <script>
                alert('บันทึกผลสำเร็จ')
                location.assign('./ad-profile.php') // กลับไปหน้าจัดการโปรไฟล์
            </script>";
        exit;
    } else {
        echo "
        <script>
            alert('บันทึกผลไม่สำเร็จ')
            window.history.back(-1) // ย้อนกลับไปหน้าก่อนหน้า
        </script>";
    }
    exit;
}




?>

<!DOCTYPE html>
<html lang="th">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="ข้อมูลส่วนตัว - SHOPPING">
    <meta name="author" content="ข้อมูลส่วนตัว - SHOPPING">

    <title>ข้อมูลส่วนตัว - SHOPPING</title>

    <?php $_SESSION["page"] = 'profile'; ?>
    <?php include_once('./inc/head.php') ?>

</head>

<body>
    <?php include_once('./inc/header.php') ?>

    <style>
        .contact-us {
            margin-top: 114px;
            border-bottom: 3px dotted #eee;
            padding-bottom: 90px;
        }
    </style>

    <?php
    $r_user = $conn->query("SELECT * FROM `tbl_users` WHERE USER_DELETE   IS NULL AND USER_ID ='$gUSER_ID';", MYSQLI_ASSOC)->fetch_assoc();
    ?>

    <div class="contact-us">
        <div class="container">
            <div id="profile" class="profile-container">
                <h2>ข้อมูลส่วนตัว</h2>

                <!-- ข้อมูลส่วนตัว -->
                <div class="profile-info mb-4">
                    <p><strong>ชื่อ:</strong>
                        <?= $r_user['USER_FNAME'] . " " . $r_user['USER_LNAME']; ?></p>
                    <!-- <p><strong>อีเมล:</strong> johndoe@example.com</p> -->
                    <p><strong>เบอร์โทรศัพท์:</strong> <?= $r_user['USER_PHONE']; ?> </p>

                </div>

                <!-- ปุ่มแก้ไขข้อมูล -->
                <button class="btn-main-dark-button " onclick="toggleEditForm()">แก้ไขข้อมูล</button>
                <!-- แบบฟอร์มแก้ไขข้อมูล -->
            </div>


            <div class="edit-form" id="editForm" style="display: none;">
                <form id="form-login" action="./ad-profile.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="TYPE" value="EDIT">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section-heading">
                                <h2>แก้ไขข้อมูลส่วนตัว</h2>
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
                                <input required type="text" class="form-control" placeholder="ระบุชื่อ"
                                    value="<?= $r_user['USER_FNAME']; ?>" name="USER_FNAME">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>นามสกุล</label>
                                <input required type="text" class="form-control" placeholder="ระบุนามสกุล"
                                    value="<?= $r_user['USER_LNAME']; ?>" name="USER_LNAME">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>เบอร์มือถือ</label>
                                <input required type="tel" class="form-control" value="<?= $r_user['USER_PHONE']; ?>"
                                    placeholder="ระบุเบอร์มือถือ" name="USER_PHONE">
                            </div>
                        </div>
        

                        <div class="col-12">
                            <h6 class="mb-2 mt-2 text-primary ">ข้อมูลเข้าสู่ระบบ</h6>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>ชื่อผู้ใช้</label>
                                <input required type="text" class="form-control"
                                    value="<?= $r_user['USER_USERNAME']; ?>" placeholder="ระบุชื่อผู้ใช้"
                                    name="USER_USERNAME">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>รหัสผ่าน</label>
                                <input required type="password" class="form-control"
                                    value="<?= $r_user['USER_PASSWORD']; ?>" placeholder="ระบุรหัสผ่าน"
                                    name="USER_PASSWORD">
                            </div>
                        </div>

                        <div class="col-lg-12 m-2">
                            <button type="submit" id="form-submit" class="main-dark-button">
                                บันทึก <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="main-dark-button" onclick="toggleEditForm()">ยกเลิก</button>
                        </div>

                    </div>

                </form>
            </div>

        </div>
    </div>



    <?php include_once('./inc/footer.php'); ?>
    <script src="./assets/js/profile.js"></script>
</body>

</html>