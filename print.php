<?php include_once('./inc/inc-html.php') ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน</title>

    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css">

</head>

<style>
    body {
        background: rgb(204, 204, 204);
    }

    page {
        background: white;
        display: block;
        margin: 0 auto;
        margin-bottom: 0.5cm;
        box-shadow: 0 0 0.5cm rgba(0, 0, 0, 0.5);
    }

    page[size="A4"] {
        width: 21cm;
        height: 29.7cm;
    }

    page[size="A4"][layout="landscape"] {
        width: 29.7cm;
        height: 21cm;
    }

    page[size="A3"] {
        width: 29.7cm;
        height: 42cm;
    }

    page[size="A3"][layout="landscape"] {
        width: 42cm;
        height: 29.7cm;
    }

    page[size="A5"] {
        width: 14.8cm;
        height: 21cm;
        padding: 0.5cm;
    }

    page[size="A5"][layout="landscape"] {
        width: 21cm;
        height: 14.8cm;
    }

    @media print {

        body,
        page {
            background: white;
            margin: 0;
            box-shadow: 0;
        }
    }
</style>

<?php
// คิวรีตรวจสอบแล้ว

if (!isset($_GET["id"])) { // ตรวจสอบว่ามีการส่งค่า id มาหรือไม่
    echo "
    <script>
        window.close(); // ปิดหน้าต่างถ้าไม่มีค่า id
    </script>";
}

$ORDER_ID = $_GET["id"]; // รับค่า ORDER_ID จาก URL

// ดึงข้อมูลคำสั่งซื้อพร้อมข้อมูลผู้ใช้ที่ทำการสั่งซื้อ
$sql = "SELECT * FROM `tbl_orders` 
        INNER JOIN `tbl_users` ON `tbl_users`.USER_ID = `tbl_orders`.USER_ID 
        WHERE `tbl_orders`.ORDER_ID='$ORDER_ID';";
$query_row = $conn->query($sql)->fetch_assoc();

// ดึงข้อมูลรายการสินค้าที่อยู่ในคำสั่งซื้อ พร้อมข้อมูลผู้ใช้ และรายละเอียดสินค้า
$query_all_list = $conn->query("SELECT * FROM `tbl_orders` 
                                INNER JOIN `tbl_users` ON `tbl_users`.USER_ID = `tbl_orders`.USER_ID 
                                INNER JOIN tbl_order_lists ON tbl_orders.ORDER_ID = tbl_order_lists.ORDER_ID 
                                INNER JOIN tbl_products ON tbl_products.PRO_ID = tbl_order_lists.PRO_ID 
                                WHERE tbl_orders.ORDER_ID = '$ORDER_ID';", MYSQLI_ASSOC)
                                ->fetch_all(MYSQLI_ASSOC);

?>

<body>
    <page size="A5">
        <h5 class="m-0">ใบเสร็จรับเงิน</h5>
        <h5>Receipt</h5>

        <table class="table table-borderless">
            <tbody>
                <tr>
                    <td class="pl-0" style="width: 30%;">ลูกค้า/Customer</td>
                    <td style="width: 40%;"> <?= $query_row['USER_FNAME'] . " " . $query_row['USER_LNAME']; ?> </td>
                    <td style="width: 30%;"> วันที่ : <?= date("d-m-Y", strtotime($query_row['ORDER_STAMP'])); ?></td>
                </tr>
            </tbody>
        </table>
        <table class="table">
            <thead>
                <tr>
                    <th>รหัส</th>
                    <th>รายการ</th>
                    <th>จำนวน</th>
                    <th>ราคา</th>
                </tr>
            </thead>
            <tbody>

                <?php $sum_all = 0; ?>
                <?php foreach ($query_all_list as $ke => $it): ?>
                    <tr>
                        <td><?= $ke + 1; ?></td>
                        <td><?= $it['PRO_NAME']; ?></td>
                        <td> <?= $it['OLIST_NUMBER']; ?></td>
                        <td><?= $it['OLIST_PRICE']; ?></td>
                    </tr>
                <?php endforeach ?>


                <tr class="table-secondary">
                    <th colspan="3" class="text-right">จำนวนเงินรวมท้งสิ้น</th>
                    <th colspan="1" class="text-left"><?= $query_row['ORDER_PRICE']; ?> บาท</th>
                </tr>

            </tbody>
        </table>

        <h6><u>การชำระเงิน</u></h6>
        <p>เงินโอน : <b><?= $query_row['ORDER_PAYMENT_PRICE']; ?> บาท</b></p>
    </page>
</body>

</html>