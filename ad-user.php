<?php include_once('./inc/inc-html.php') ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === "POST") { 
    // ตัวแปรสำหรับเช็คผลลัพธ์ของการทำงาน
    $rp = false;

    if (isset($_POST["TYPE"]) && $_POST["TYPE"] === "ADD") {
        // รับค่าจากฟอร์มเพื่อลงทะเบียนผู้ใช้ใหม่
        $USER_FNAME = $_POST["USER_FNAME"]; // ชื่อจริง
        $USER_LNAME = $_POST["USER_LNAME"]; // นามสกุล
        $USER_PHONE = $_POST["USER_PHONE"]; // เบอร์โทรศัพท์
        $USER_ADDRESS = $_POST["USER_ADDRESS"]; // ที่อยู่
        $USER_USERNAME = $_POST["USER_USERNAME"]; // ชื่อผู้ใช้
        $USER_PASSWORD = $_POST["USER_PASSWORD"]; // รหัสผ่าน

        // ตรวจสอบว่ามีชื่อผู้ใช้นี้ในระบบแล้วหรือไม่
        $sql = "SELECT * FROM `tbl_users` WHERE USER_USERNAME ='$USER_USERNAME'";
        $query_row = $conn->query($sql)->fetch_all();

        if (count($query_row) > 0) {
            echo "
            <script>
                alert('ชื่อผู้ใช้ซ้ำ โปรดใช้ชื่อใหม่')
                window.history.back(-1)
            </script>";
            exit;
        }

        // เพิ่มข้อมูลผู้ใช้ใหม่ลงในฐานข้อมูล
        $sql = "INSERT INTO `tbl_users` (`USER_ID`, `USER_FNAME`, `USER_LNAME`, `USER_PHONE`, `USER_ADDRESS`, `USER_USERNAME`, `USER_PASSWORD`, `USER_STAMP`, `USER_ROLE`, `USER_DELETE`, `USER_DELETE_TIME`, `USER_DELETE_USER`) 
                VALUES (NULL, '$USER_FNAME', '$USER_LNAME', '$USER_PHONE', '$USER_ADDRESS', '$USER_USERNAME', '$USER_PASSWORD', current_timestamp(), 'USER', NULL, NULL, NULL);";
        $query = $conn->query($sql);

        if ($query) {
            $rp = true;
        }

    } elseif (isset($_POST["TYPE"]) && $_POST["TYPE"] === "EDIT") {
        // รับค่าจากฟอร์มเพื่อแก้ไขข้อมูลผู้ใช้
        $USER_ID = $_POST["USER_ID"];

        // ดึงข้อมูลของผู้ใช้ที่ต้องการแก้ไข
        $sql = "SELECT * FROM `tbl_users` WHERE USER_ID ='$USER_ID'";
        $query_row = $conn->query($sql)->fetch_assoc();

        $USER_FNAME = $_POST["USER_FNAME"];
        $USER_LNAME = $_POST["USER_LNAME"];
        $USER_PHONE = $_POST["USER_PHONE"];
        $USER_ADDRESS = $_POST["USER_ADDRESS"];
        $USER_USERNAME = $_POST["USER_USERNAME"];
        $USER_PASSWORD = $_POST["USER_PASSWORD"];

        // อัปเดตข้อมูลผู้ใช้ในฐานข้อมูล
        $sql = "UPDATE `tbl_users` 
                SET `USER_FNAME` = '$USER_FNAME', 
                    `USER_LNAME` = '$USER_LNAME', 
                    `USER_PHONE` = '$USER_PHONE', 
                    `USER_ADDRESS` = '$USER_ADDRESS', 
                    `USER_USERNAME` = '$USER_USERNAME', 
                    `USER_PASSWORD` = '$USER_PASSWORD' 
                WHERE `tbl_users`.`USER_ID` = '$USER_ID';";
        $query = $conn->query($sql);

        if ($query) {
            $rp = true;
        }
    }

    // ตรวจสอบว่าการดำเนินการสำเร็จหรือไม่
    if ($rp) {
        echo "
            <script>
                alert('บันทึกผลสำเร็จ')
                location.assign('./ad-user.php')
            </script>";
        exit;
    } else {
        echo "
        <script>
            alert('บันทึกผลไม่สำเร็จ')
            window.history.back(-1)
        </script>";
    }
    exit;

} elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET["delete"])) {
    // รับค่า USER_ID ที่ต้องการลบ
    $USER_ID = $_GET["delete"];

    // ตรวจสอบว่ามีผู้ใช้ในฐานข้อมูลหรือไม่
    $sql = "SELECT * FROM `tbl_users` WHERE USER_ID ='$USER_ID'";
    $query_row = $conn->query($sql)->fetch_assoc();

    if ($query_row) {
        // ทำเครื่องหมายลบผู้ใช้ โดยเปลี่ยนค่า `USER_DELETE` เป็น 1
        if ($conn->query("UPDATE `tbl_users` SET `USER_DELETE` = '1' WHERE `tbl_users`.`USER_ID` = '$USER_ID';")) {
            header('Location: ./ad-user.php'); // กลับไปหน้าจัดการผู้ใช้
        } else {
            header('Location: ./ad-user.php'); // กลับไปหน้าจัดการผู้ใช้หากเกิดข้อผิดพลาด
        }
    } else {
        header('Location: ./ad-user.php'); // กลับไปหน้าจัดการผู้ใช้หากไม่พบข้อมูล
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

    <?php $_SESSION["page"] = 'user'; ?>
    <?php include_once('./inc/head.php') ?>

</head>

<body>
    <?php include_once('./inc/header.php') ?>

    <section class="section  mt-5" id="men">
        <div class="container">

            <div class="d-flex justify-content-between mb-4">
                <div class=""></div>
                <div class=""><button class="btn btn-md btn-outline-primary" data-toggle="modal"
                        data-target="#modal-add-user"> เพิ่มข้อมูลลูกค้า </button></div>
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
                    $query_all = $conn->query("SELECT * FROM `tbl_users` WHERE USER_DELETE   IS NULL AND USER_ROLE ='USER';", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
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
                                    data-target="#modal-edit-user-<?= $item['USER_ID'] ?>"> แก้ไข </button>

                                <button class="btn btn-sm btn-danger" onclick="deleteuser('<?= $item['USER_ID'] ?>')">
                                    ลบ
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="modal-edit-user-<?= $item['USER_ID'] ?>" tabindex="-1" role="dialog"
                            aria-labelledby="modal-edit-user-<?= $item['USER_ID'] ?>Label" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <form action="./ad-user.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="TYPE" value="EDIT">
                                        <input type="hidden" name="USER_ID" value="<?= $item['USER_ID'] ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modal-edit-user-<?= $item['USER_ID'] ?>Label">
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
                                                <label>ที่อยู่(จัดส่งสินค้า)</label>
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
                <form action="./ad-user.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="TYPE" value="ADD">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-add-userLabel">เพิ่มข้อมูลลูกค้า</h5>
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
                            <label>ที่อยู่(จัดส่งสินค้า)</label>
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
    <script src="./assets/js/ad-user.js"></script>

</body>

</html>