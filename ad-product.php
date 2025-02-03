<?php include_once('./inc/inc-html.php') ?>


<?php
// $connn = $conn;

function uploadeImage($_FILESPRO_PHOTO)
{
    $uploadDir = "upload/";

    // Check if the directory exists, if not, create it
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Check if the file is uploaded
    $imageName = false;

    if (isset($_FILESPRO_PHOTO) && $_FILESPRO_PHOTO['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILESPRO_PHOTO['tmp_name'];
        $fileName = $_FILESPRO_PHOTO['name'];
        $fileSize = $_FILESPRO_PHOTO['size'];
        $fileType = $_FILESPRO_PHOTO['type'];

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

// ตรวจสอบว่าคำขอเป็นแบบ POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $rp = false; // ตัวแปรสำหรับเก็บผลลัพธ์การดำเนินการ

    // ตรวจสอบว่าการดำเนินการเป็นการเพิ่มข้อมูล (ADD)
    if (isset($_POST["TYPE"]) && $_POST["TYPE"] === "ADD") {

        // อัปโหลดรูปภาพสินค้า
        $PRO_PHOTO = uploadeImage($_FILES['PRO_PHOTO']);
        if ($PRO_PHOTO === false) {
            return false; // ถ้าอัปโหลดไม่สำเร็จ ให้หยุดการทำงาน
        }

        // เก็บข้อมูลจากฟอร์มลงในตัวแปร
        $USER_ID = $_SESSION["USER"]['USER_ID'];
        $PRO_NAME = $_POST["PRO_NAME"];
        $PRO_PRICE = $_POST["PRO_PRICE"];
        $PRO_GROUP_NAME = $_POST["PRO_GROUP_NAME"];
        $PRO_DETAILS = $_POST["PRO_DETAILS"];
        $PRO_STOCK = $_POST["PRO_STOCK"];

        // คำสั่ง SQL สำหรับเพิ่มข้อมูลสินค้าใหม่ลงในฐานข้อมูล
        $sql = "INSERT INTO `tbl_products` (`PRO_ID`, `PRO_NAME`, `PRO_PRICE`, `PRO_STAMP`, `PRO_DETAILS`, `PRO_PHOTO`, `USER_ID`, `PRO_DELETE`, `PRO_STATUS`, `PRO_GROUP_NAME`, `PRO_STOCK`) 
                VALUES (NULL, '$PRO_NAME', '$PRO_PRICE', current_timestamp(), '$PRO_DETAILS', '$PRO_PHOTO', '$USER_ID', NULL, '1', '$PRO_GROUP_NAME','$PRO_STOCK');";
        
        $query = $conn->query($sql); // รันคำสั่ง SQL

        if ($query) {
            $rp = true; // ถ้าเพิ่มสำเร็จ กำหนดค่า $rp เป็น true
        }

    // ตรวจสอบว่าการดำเนินการเป็นการแก้ไขข้อมูล (EDIT)
    } elseif (isset($_POST["TYPE"]) && $_POST["TYPE"] === "EDIT") {

        $PRO_ID = isset($_POST["PRO_ID"]) ? $_POST["PRO_ID"] : null;
        if (is_null($PRO_ID)) {
            // ถ้าไม่มีรหัสสินค้า แสดงข้อความแจ้งเตือนและย้อนกลับไปหน้าเดิม
            echo "
            <script>
                alert('บันทึกผลไม่สำเร็จ')
                window.history.back(-1)
            </script>";
            exit;
        }

        // ดึงข้อมูลสินค้าจากฐานข้อมูลตามรหัสสินค้า
        $sql = "SELECT * FROM `tbl_products` WHERE PRO_ID ='$PRO_ID'";
        $query_row = $conn->query($sql)->fetch_assoc();

        // เก็บข้อมูลจากฟอร์มหรือใช้ข้อมูลเดิมถ้าไม่มีการกรอกใหม่
        $USER_ID = $_SESSION["USER"]['USER_ID'];
        $PRO_ID = !empty($_POST["PRO_ID"]) ? $_POST["PRO_ID"] : $query_row['PRO_ID'];
        $PRO_NAME = !empty($_POST["PRO_NAME"]) ? $_POST["PRO_NAME"] : $query_row['PRO_NAME'];
        $PRO_PRICE = !empty($_POST["PRO_PRICE"]) ? $_POST["PRO_PRICE"] : $query_row['PRO_PRICE'];
        $PRO_GROUP_NAME = !empty($_POST["PRO_GROUP_NAME"]) ? $_POST["PRO_GROUP_NAME"] : $query_row['PRO_GROUP_NAME'];
        $PRO_DETAILS = !empty($_POST["PRO_DETAILS"]) ? $_POST["PRO_DETAILS"] : $query_row['PRO_DETAILS'];
        $PRO_STOCK = !empty($_POST["PRO_STOCK"]) ? $_POST["PRO_STOCK"] : $query_row['PRO_STOCK'];

        $PRO_PHOTO = null;
        if (isset($_FILES['PRO_PHOTO']) && $_FILES['PRO_PHOTO']['error'] === UPLOAD_ERR_OK) {
            // ลบรูปเก่าออก
            unlink("upload/" . $query_row['PRO_PHOTO']);

            // อัปโหลดรูปใหม่
            $PRO_PHOTO = uploadeImage($_FILES['PRO_PHOTO']);
        } else {
            // ถ้าไม่มีการอัปโหลดรูปใหม่ ใช้รูปเดิม
            $PRO_PHOTO = $query_row['PRO_PHOTO'];
        }

        // คำสั่ง SQL สำหรับอัปเดตข้อมูลสินค้า
        $sql = "UPDATE `tbl_products` SET   
                    `PRO_NAME` = '$PRO_NAME', 
                    `PRO_PRICE` = '$PRO_PRICE', 
                    `PRO_DETAILS` = '$PRO_DETAILS', 
                    `PRO_PHOTO` = '$PRO_PHOTO', 
                    `USER_ID` = '$USER_ID', 
                    `PRO_GROUP_NAME` = '$PRO_GROUP_NAME', 
                    `PRO_STOCK` = '$PRO_STOCK' 
                WHERE `tbl_products`.`PRO_ID` = '$PRO_ID';";
        
        $query = $conn->query($sql); // รันคำสั่ง SQL

        if ($query) {
            $rp = true; // ถ้าแก้ไขสำเร็จ กำหนดค่า $rp เป็น true
        }
    }

    // ตรวจสอบผลลัพธ์การดำเนินการ
    if ($rp) {
        // ถ้าสำเร็จ แสดงข้อความและเปลี่ยนหน้าไปยัง ad-product.php
        echo "
            <script>
                alert('บันทึกผลสำเร็จ')
                location.assign('./ad-product.php')
            </script>";
        exit;
    } else {
        // ถ้าไม่สำเร็จ แสดงข้อความแจ้งเตือนและย้อนกลับไปหน้าเดิม
        echo "
        <script>
            alert('บันทึกผลไม่สำเร็จ')
            window.history.back(-1)
        </script>";
    }
    exit;

// ตรวจสอบว่าคำขอเป็นแบบ GET และมีการส่งค่า delete มาหรือไม่
} elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET["delete"])) {

    $PRO_ID = $_GET["delete"]; // รหัสสินค้าที่จะลบ
    $sql = "SELECT * FROM `tbl_products` WHERE PRO_ID ='$PRO_ID'";
    $query_row = $conn->query($sql)->fetch_assoc(); // ดึงข้อมูลสินค้า

    if ($query_row) {
        // ถ้ามีข้อมูลสินค้า ให้ทำการอัปเดตสถานะ PRO_DELETE เป็น 1 (ลบแบบ soft delete)
        if ($conn->query("UPDATE `tbl_products` SET `PRO_DELETE` = '1' WHERE `tbl_products`.`PRO_ID` = '$PRO_ID';")) {
            header('Location: ./ad-product.php'); // กลับไปที่หน้า ad-product.php หลังลบสำเร็จ
        } else {
            header('Location: ./ad-product.php'); // กลับไปที่หน้าเดิมถ้าลบไม่สำเร็จ
        }
    } else {
        // ถ้าไม่พบข้อมูลสินค้า กลับไปที่หน้าเดิม
        header('Location: ./ad-product.php');
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

    <?php $_SESSION["page"] = 'product'; ?>
    <?php include_once('./inc/head.php') ?>

</head>

<body>
    <?php include_once('./inc/header.php') ?>

    <section class="section  mt-5" id="men">
        <div class="container">

            <div class="d-flex justify-content-between mb-4">
                <div class=""></div>
                <div class=""><button class="btn btn-md btn-outline-primary" data-toggle="modal"
                        data-target="#modal-add-product"> เพิ่มสินค้า </button></div>
            </div>
            <table class="table table-bordered table-show">
                <thead>
                    <tr>
                        <th style="width: 1%;">ลำดับ</th>
                        <th class="text-start">ชื่อสินค้า</th>
                        <th class="text-start">หมวดหมู่</th>
                        <th class="text-center">ราคา</th>
                        <th class="text-center">คงเหลือ</th>
                        <th class="text-center">เพิ่มเติม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_all = $conn->query("SELECT * FROM `tbl_products` WHERE PRO_DELETE IS NULL;", MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC); // อัพเดตลงฐานข้อมูล
                    // var_dump($query_all);
                    // exit;
                    ?>
                    <?php foreach ($query_all as $key => $item): ?>
                        <tr>
                            <td class="text-center" style="width: 1%;"><?= $key + 1; ?></td>
                            <td class="text-start"><?= $item['PRO_NAME']; ?></td>
                            <td class="text-start"><?= $item['PRO_GROUP_NAME']; ?></td>
                            <td class="text-center"><?= $item['PRO_PRICE']; ?> บาท</td>
                            <td class="text-center"><?= $item['PRO_STOCK']; ?> หน่วย</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning" data-toggle="modal"
                                    data-target="#modal-edit-product-<?= $item['PRO_ID'] ?>"> แก้ไข </button>

                                <button class="btn btn-sm btn-danger" onclick="deleteProduct('<?= $item['PRO_ID'] ?>')">
                                    ลบ
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="modal-edit-product-<?= $item['PRO_ID'] ?>" tabindex="-1" role="dialog"
                            aria-labelledby="modal-edit-product-<?= $item['PRO_ID'] ?>Label" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <form action="./ad-product.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="TYPE" value="EDIT">
                                        <input type="hidden" name="PRO_ID" value="<?= $item['PRO_ID'] ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modal-edit-product-<?= $item['PRO_ID'] ?>Label">
                                                แก้ไขสินค้า</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>ชื่อสินค้า</label>
                                                <input required type="text" class="form-control"
                                                    placeholder="ระบุชื่อสินค้า" name="PRO_NAME"
                                                    value="<?= $item['PRO_NAME']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>ราคา</label>
                                                <input required type="number" min="0" step="0.1" class="form-control"
                                                    placeholder="ราคาสินค้า" name="PRO_PRICE"
                                                    value="<?= $item['PRO_PRICE']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>จำนวนสินค้า</label>
                                                <input required type="number" min="0" step="1" class="form-control"
                                                    placeholder="จำนวนสินค้า" name="PRO_STOCK"
                                                    value="<?= $item['PRO_STOCK']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>หมวดหมู่</label>
                                                <input required type="text" class="form-control"
                                                    placeholder="ระบุหมวดหมู่สินค้า" name="PRO_GROUP_NAME"
                                                    value="<?= $item['PRO_GROUP_NAME']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>รายละเอียดสินค้า</label>
                                                <textarea required class="form-control" name="PRO_DETAILS"
                                                    rows="3"><?= $item['PRO_DETAILS']; ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="PRO_PHOTO">อัพโหลด <span class="text-danger">(PNG, JPG,
                                                        JPEG)</span></label>
                                                <input type="file" class="form-control-file" name="PRO_PHOTO" id="PRO_PHOTO"
                                                    accept="image/png, image/jpeg" >

                                            </div>
                                            <div class="form-group">
                                                <label for="PRO_PHOTO">ภาพสินค้า</label>
                                                <img class="w-100" src="./upload/<?= $item['PRO_PHOTO'] ?>" alt=""
                                                    srcset="">
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

    <div class="modal fade" id="modal-add-product" tabindex="-1" role="dialog" aria-labelledby="modal-add-productLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="./ad-product.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="TYPE" value="ADD">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-add-productLabel">เพิ่มสินค้า</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>ชื่อสินค้า</label>
                            <input required type="text" class="form-control" placeholder="ระบุชื่อสินค้า"
                                name="PRO_NAME">
                        </div>
                        <div class="form-group">
                            <label>ราคา</label>
                            <input required type="number" min="0" step="0.1" class="form-control"
                                placeholder="ราคาสินค้า" name="PRO_PRICE">
                        </div>
                        <div class="form-group">
                            <label>จำนวนสินค้า</label>
                            <input required type="number" min="0" step="1" class="form-control"
                                placeholder="จำนวนสินค้า" name="PRO_STOCK">
                        </div>
                        <div class="form-group">
                            <label>หมวดหมู่</label>
                            <input required type="text" class="form-control" placeholder="ระบุหมวดหมู่สินค้า"
                                name="PRO_GROUP_NAME">
                        </div>
                        <div class="form-group">
                            <label>รายละเอียดสินค้า</label>
                            <textarea required class="form-control" name="PRO_DETAILS" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="PRO_PHOTO">อัพโหลด <span class="text-danger">(PNG, JPG, JPEG)</span></label>
                            <input type="file" class="form-control-file" name="PRO_PHOTO" id="PRO_PHOTO"
                                accept="image/png, image/jpeg" required>

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
    <script src="./assets/js/ad-product.js"></script>

</body>

</html>