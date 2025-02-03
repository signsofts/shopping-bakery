<?php include_once('./inc/inc-html.php') ?>

<?php
// ตรวจสอบว่าการร้องขอเป็นแบบ POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // ตัวแปรสำหรับตรวจสอบว่าการดำเนินการสำเร็จหรือไม่
    $rp = false;

    // ตรวจสอบว่าการร้องขอเป็นการเพิ่มข้อมูล (ADD)
    if (isset($_POST["TYPE"]) && $_POST["TYPE"] === "ADD") {

        // เก็บค่าจากฟอร์มลงในตัวแปร
        $USER_FNAME = $_POST["USER_FNAME"];
        $USER_LNAME = $_POST["USER_LNAME"];
        $USER_PHONE = $_POST["USER_PHONE"];
        $USER_ADDRESS = $_POST["USER_ADDRESS"];
        $USER_USERNAME = $_POST["USER_USERNAME"];
        $USER_PASSWORD = $_POST["USER_PASSWORD"];

        // ตรวจสอบว่ามีชื่อผู้ใช้นี้ในระบบแล้วหรือไม่
        $sql = "SELECT * FROM `tbl_users` WHERE USER_USERNAME ='$USER_USERNAME'";
        $query_row = $conn->query($sql)->fetch_all();

        // ถ้าพบชื่อผู้ใช้ซ้ำ จะแจ้งเตือนและย้อนกลับไปหน้าก่อนหน้า
        if (count($query_row) > 0) {
            echo "
            <script>
                alert('ชื่อผู้ใช้ซ้ำโปรดใช้ชื่อใหม่')
                window.history.back(-1)
            </script>";
            exit;
        }

        // เพิ่มข้อมูลผู้ใช้ใหม่ลงในฐานข้อมูล
        $sql = "INSERT INTO `tbl_users` (`USER_ID`, `USER_FNAME`, `USER_LNAME`, `USER_PHONE`, `USER_ADDRESS`, `USER_USERNAME`, `USER_PASSWORD`, `USER_STAMP`, `USER_ROLE`, `USER_DELETE`, `USER_DELETE_TIME`, `USER_DELETE_USER`) 
                VALUES (NULL, '$USER_FNAME', '$USER_LNAME', '$USER_PHONE', '$USER_ADDRESS', '$USER_USERNAME', '$USER_PASSWORD', current_timestamp(), 'ADMIN', NULL, NULL, NULL);";
        $query = $conn->query($sql); // รันคำสั่ง SQL เพื่อเพิ่มข้อมูล

        if ($query) {
            $rp = true; // ถ้าเพิ่มข้อมูลสำเร็จ
        }

    } elseif (isset($_POST["TYPE"]) && $_POST["TYPE"] === "EDIT") {
        // ตรวจสอบว่าการร้องขอเป็นการแก้ไขข้อมูล (EDIT)
        $USER_ID = $_POST["USER_ID"]; // รับค่า USER_ID จากฟอร์ม

        // ดึงข้อมูลผู้ใช้จากฐานข้อมูลตาม USER_ID
        $sql = "SELECT * FROM `tbl_users` WHERE USER_ID ='$USER_ID'";
        $query_row = $conn->query($sql)->fetch_assoc();

        // เก็บค่าข้อมูลใหม่จากฟอร์มลงในตัวแปร
        $USER_FNAME = $_POST["USER_FNAME"];
        $USER_LNAME = $_POST["USER_LNAME"];
        $USER_PHONE = $_POST["USER_PHONE"];
        $USER_ADDRESS = $_POST["USER_ADDRESS"];
        $USER_USERNAME = $_POST["USER_USERNAME"];
        $USER_PASSWORD = $_POST["USER_PASSWORD"];

        // อัพเดตข้อมูลผู้ใช้ในฐานข้อมูล
        $sql = "UPDATE `tbl_users` 
                SET `USER_FNAME` = '$USER_FNAME', 
                    `USER_LNAME` = '$USER_LNAME', 
                    `USER_PHONE` = '$USER_PHONE', 
                    `USER_ADDRESS` = '$USER_ADDRESS', 
                    `USER_USERNAME` = '$USER_USERNAME', 
                    `USER_PASSWORD` = '$USER_PASSWORD' 
                WHERE `tbl_users`.`USER_ID` = '$USER_ID';";
        $query = $conn->query($sql); // รันคำสั่ง SQL เพื่ออัพเดตข้อมูล

        if ($query) {
            $rp = true; // ถ้าอัพเดตสำเร็จ
        }
    }

    // ตรวจสอบผลลัพธ์การเพิ่มหรือแก้ไขข้อมูล
    if ($rp) {
        // ถ้าการดำเนินการสำเร็จ จะแจ้งเตือนและกลับไปหน้าจัดการผู้ใช้
        echo "
            <script>
                alert('บันทึกผลสำเร็จ')
                location.assign('./ad-user-admin.php')
            </script>";
        exit;
    } else {
        // ถ้าการดำเนินการไม่สำเร็จ จะแจ้งเตือนและย้อนกลับไปหน้าก่อนหน้า
        echo "
        <script>
            alert('บันทึกผลไม่สำเร็จ')
            window.history.back(-1)
        </script>";
    }
    exit;

} elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET["delete"])) {
    // ตรวจสอบว่าการร้องขอเป็นแบบ GET และมีการส่งค่า delete มา
    $USER_ID = $_GET["delete"]; // รับค่า USER_ID ที่ต้องการลบ

    // ดึงข้อมูลผู้ใช้จากฐานข้อมูลตาม USER_ID
    $sql = "SELECT * FROM `tbl_users` WHERE USER_ID ='$USER_ID'";
    $query_row = $conn->query($sql)->fetch_assoc();

    if ($query_row) {
        // ถ้าพบข้อมูลผู้ใช้ จะทำการตั้งค่า USER_DELETE เป็น '1' (ลบแบบ soft delete)
        if ($conn->query("UPDATE `tbl_users` SET `USER_DELETE` = '1' WHERE `tbl_users`.`USER_ID` = '$USER_ID';")) {
            header('Location: ./ad-user-admin.php'); // กลับไปหน้าจัดการผู้ใช้
        } else {
            header('Location: ./ad-user-admin.php'); // ถ้าเกิดข้อผิดพลาดในการลบ ก็กลับไปหน้าจัดการผู้ใช้เช่นกัน
        }
    } else {
        header('Location: ./ad-user-admin.php'); // ถ้าไม่พบผู้ใช้ก็กลับไปหน้าจัดการผู้ใช้
    }
    exit;
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

    <?php $_SESSION["page"] = 'user-admin'; ?>
    <?php include_once('./inc/head.php') ?>

</head>

<body>
    <?php include_once('./inc/header.php') ?>

    <section class="section  mt-5" id="men">
        <div class="container">

            <div class="d-flex justify-content-between mb-4">
                <div class=""></div>
                <div class=""><button class="btn btn-md btn-outline-primary" data-toggle="modal"
                        data-target="#modal-add-user"> เพิ่มข้อมูลผู้ดูแลระบบ </button></div>
            </div>
            <table class="table table-bordered table-show">
                <thead>
                    <tr>
                        <th style="width: 1%;">ลำดับ</th>
                        <th class="text-start">ชื่อ - นามสกุล</th>
                        <th class="text-start">เบอร์มือถือ</th>
                        <th class="text-center">ที่อยู่</th>
                        <th class="text-center">ชื่อผู้ใช้</th>
                        <th class="text-center">รหัสผ่าน</th>
                        <th class="text-center">เพิ่มเติม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_all = $conn->query("SELECT * FROM `tbl_users` WHERE USER_DELETE  IS NULL AND USER_ROLE ='ADMIN' AND USER_ID != '1' ;", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
                    // var_dump($query_all);
                    // exit;
                    ?>
                    <?php foreach ($query_all as $key => $item): ?>
                        <tr>
                            <td class="text-center" style="width: 1%;"><?= $key + 1; ?></td>
                            <td class="text-start"><?= $item['USER_FNAME'] . " " . $item['USER_LNAME']; ?></td>
                            <td class="text-start"><?= $item['USER_PHONE']; ?></td>
                            <td class="text-center"><?= $item['USER_ADDRESS']; ?></td>
                            <td class="text-center"><?= $item['USER_USERNAME']; ?></td>
                            <td class="text-center"><?= $item['USER_PASSWORD']; ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning" data-toggle="modal"
                                    data-target="#modal-edit-product-<?= $item['USER_ID'] ?>"> แก้ไข </button>

                                <button class="btn btn-sm btn-danger" onclick="deleteProduct('<?= $item['USER_ID'] ?>')">
                                    ลบ
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="modal-edit-product-<?= $item['USER_ID'] ?>" tabindex="-1" role="dialog"
                            aria-labelledby="modal-edit-product-<?= $item['USER_ID'] ?>Label" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <form action="./ad-user-admin.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="TYPE" value="EDIT">
                                        <input type="hidden" name="USER_ID" value="<?= $item['USER_ID'] ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modal-edit-product-<?= $item['USER_ID'] ?>Label">
                                                แก้ไข</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <h6 class="mb-2 text-primary">ข้อมูลส่วนตัว</h6>
                                            <div class="form-group">
                                                <label>ชื่อ</label>
                                                <input required type="text" class="form-control" placeholder="ระบุชื่อ"
                                                    value="<?= $item['USER_FNAME']; ?>" name="USER_FNAME">
                                            </div>
                                            <div class="form-group">
                                                <label>นามสกุล</label>
                                                <input required type="text" class="form-control" placeholder="ระบุนามสกุล"
                                                    value="<?= $item['USER_LNAME']; ?>" name="USER_LNAME">
                                            </div>
                                            <div class="form-group">
                                                <label>เบอร์มือถือ</label>
                                                <input required type="tel" class="form-control"
                                                    placeholder="ระบุเบอร์มือถือ" value="<?= $item['USER_PHONE']; ?>"
                                                    name="USER_PHONE">
                                            </div>
                                            <div class="form-group">
                                                <label>ที่อยู่</label>
                                                <textarea required class="form-control" name="USER_ADDRESS"
                                                    rows="3"><?= $item['USER_ADDRESS']; ?></textarea>
                                            </div>
                                            <h6 class="mb-2 mt-2 text-primary ">ข้อมูลเข้าสู่ระบบ</h6>
                                            <div class="form-group">
                                                <label>ชื่อผู้ใช้</label>
                                                <input required type="text" class="form-control"
                                                    placeholder="ระบุชื่อผู้ใช้" value="<?= $item['USER_USERNAME']; ?>"
                                                    name="USER_USERNAME">
                                            </div>
                                            <div class="form-group">
                                                <label>รหัสผ่าน</label>
                                                <input required type="password" class="form-control"
                                                    placeholder="ระบุรหัสผ่าน" value="<?= $item['USER_PASSWORD']; ?>"
                                                    name="USER_PASSWORD">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">ปิด</button>
                                            <button type="submit" class="btn btn-primary">บันทึก</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach ?>

                </tbody>
            </table>

        </div>
    </section>

    <div class="modal fade" id="modal-add-user" tabindex="-1" role="dialog" aria-labelledby="modal-add-userLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="./ad-user-admin.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="TYPE" value="ADD">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-add-userLabel">เพิ่มข้อมูลผู้ดูแลระบบ</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6 class="mb-2 text-primary">ข้อมูลส่วนตัว</h6>
                        <div class="form-group">
                            <label>ชื่อ</label>
                            <input required type="text" class="form-control" placeholder="ระบุชื่อ" name="USER_FNAME">
                        </div>
                        <div class="form-group">
                            <label>นามสกุล</label>
                            <input required type="text" class="form-control" placeholder="ระบุนามสกุล"
                                name="USER_LNAME">
                        </div>
                        <div class="form-group">
                            <label>เบอร์มือถือ</label>
                            <input required type="tel" class="form-control" placeholder="ระบุเบอร์มือถือ"
                                name="USER_PHONE">
                        </div>
                        <div class="form-group">
                            <label>ที่อยู่</label>
                            <textarea required class="form-control" name="USER_ADDRESS" rows="3"></textarea>
                        </div>


                        <h6 class="mb-2 mt-2 text-primary ">ข้อมูลเข้าสู่ระบบ</h6>
                        <div class="form-group">
                            <label>ชื่อผู้ใช้</label>
                            <input required type="text" class="form-control" placeholder="ระบุชื่อผู้ใช้"
                                name="USER_USERNAME">
                        </div>
                        <div class="form-group">
                            <label>รหัสผ่าน</label>
                            <input required type="password" class="form-control" placeholder="ระบุรหัสผ่าน"
                                name="USER_PASSWORD">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <?php include_once('./inc/footer.php'); ?>
    <script src="./assets/js/ad-user-admin.js"></script>

</body>

</html>