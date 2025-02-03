<?php

include_once("./condb.php");// ตรวจสอบให้แน่ใจว่าไฟล์นี้มีการเชื่อมต่อฐานข้อมูลที่ปลอดภัย


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $USER_USERNAME = $_POST["USER_USERNAME"];
    $USER_PASSWORD = $_POST["USER_PASSWORD"];


    $sql = "SELECT * FROM `tbl_users` WHERE USER_USERNAME = '$USER_USERNAME' AND USER_PASSWORD ='$USER_PASSWORD' ";
    $resp = $conn->query($sql, MYSQLI_STORE_RESULT)->fetch_assoc();

    if ($resp) {
        $_SESSION["USER"] = $resp;

        
        // ตรวจสอบสิทธิ์ผู้ใช้และเปลี่ยนเส้นทาง
        if ($resp['USER_ROLE'] == "ADMIN") { // แก้ไข "ADMIN" เป็น "ADMIN" 
            header('Location: ./ad-home.php');
            exit();
        } else if ($resp['USER_ROLE'] == "USER") {
            header('Location: ./home.php');
            exit();
        }
    }


}


header('Location: ./index.php');
exit();



?>