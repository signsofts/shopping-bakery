<?php 
session_start(); // เริ่มต้น session เพื่อใช้ในการเก็บค่าตัวแปรข้ามหน้าเว็บ

date_default_timezone_set("Asia/Bangkok"); // กำหนดโซนเวลาเป็นโซนของประเทศไทย

// กำหนดค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost"; // ชื่อเซิร์ฟเวอร์ฐานข้อมูล
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
$password = "12345678"; // รหัสผ่านของฐานข้อมูล
$database = "signsoft_bakery"; // ชื่อฐานข้อมูลที่ต้องการเชื่อมต่อ


// $servername = "localhost"; // ชื่อเซิร์ฟเวอร์ฐานข้อมูล
// $username = "signsoft_bakery"; // ชื่อผู้ใช้ฐานข้อมูล
// $password = "signsoft_bakery"; // รหัสผ่านของฐานข้อมูล
// $database = "signsoft_bakery"; // ชื่อฐานข้อมูลที่ต้องการเชื่อมต่อ



// หมายเหตุ: มีการคอมเมนต์ตัวเลือกการตั้งค่าฐานข้อมูลอื่นๆ ที่อาจถูกใช้

// ทำการเชื่อมต่อกับฐานข้อมูล MySQL
$conn = mysqli_connect($servername, $username, $password, $database);

// ตั้งค่าชุดอักขระให้รองรับ UTF-8 เพื่อให้สามารถใช้งานภาษาไทยได้อย่างถูกต้อง
$conn->set_charset('utf8mb4');

// ตรวจสอบว่าการเชื่อมต่อสำเร็จหรือไม่
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error); // แสดงข้อความผิดพลาดหากเชื่อมต่อไม่สำเร็จ
}

// เก็บตัวแปรการเชื่อมต่อไว้ใน session เพื่อให้สามารถเรียกใช้ในไฟล์อื่นๆ ได้
$_SESSION["CONN"] = $conn;
?>
