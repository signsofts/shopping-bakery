<?php

session_start(); // เริ่มต้นเซสชัน
unset($_SESSION["USER"]); // ลบค่าของตัวแปรเซสชัน USER
session_destroy(); // ทำลายเซสชันทั้งหมด

header('Location: ./index.php'); // เปลี่ยนเส้นทางไปที่หน้า index.php
exit(); // หยุดการทำงานของสคริปต์
