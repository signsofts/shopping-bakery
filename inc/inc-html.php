<?php 
// นำเข้าไฟล์เชื่อมต่อฐานข้อมูล
include_once('./condb.php'); 

// ตรวจสอบว่ามีการเข้าสู่ระบบหรือไม่
if (!isset($_SESSION["USER"])) { 
    // ถ้ายังไม่ได้เข้าสู่ระบบ ให้เปลี่ยนเส้นทางไปที่หน้า logout เพื่อออกจากระบบ
    header('Location: ./logout.php'); 
    exit(); // หยุดการทำงานของสคริปต์ทันที
}

// ดึงค่า USER_ID ของผู้ใช้ที่เข้าสู่ระบบจาก SESSION
$gUSER_ID = $_SESSION["USER"]['USER_ID'];