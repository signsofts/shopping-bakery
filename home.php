<?php include_once('./inc/inc-html.php') ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET["type"]) && isset($_GET["PRO_ID"])) {

    if ($_GET["type"] == 'add') {
        $PRO_ID = $_GET["PRO_ID"];
        $USER_ID = $_SESSION["USER"]['USER_ID'];

        // ดึงข้อมูลสินค้าจากฐานข้อมูล
        $sql = "SELECT * FROM `tbl_products` WHERE PRO_ID ='$PRO_ID'";
        $query_row = $conn->query($sql)->fetch_assoc();

        if ($query_row) {
            // ตรวจสอบว่าสินค้าอยู่ในตะกร้าหรือยัง
            $query_row_cart = $conn->query("SELECT * FROM `tbl_carts` WHERE USER_ID = '$USER_ID' AND PRO_ID ='$PRO_ID' ")->fetch_assoc();

            if ($query_row_cart) {
                // เพิ่มจำนวนสินค้าในตะกร้า
                $CART_NUMBER = $query_row_cart['CART_NUMBER'] + 1;

                // ตรวจสอบว่าสินค้าเพียงพอหรือไม่
                if ($query_row['PRO_STOCK'] < $CART_NUMBER) {
                    echo "
                    <script>
                        alert('สินค้าหมด คงเหลือ({$query_row['PRO_STOCK']}) ')
                        location.assign('./home.php')
                    </script>";
                    exit;
                }

                // อัปเดตจำนวนสินค้าในตะกร้า
                $conn->query("UPDATE `tbl_carts` SET `CART_NUMBER` = '$CART_NUMBER' WHERE `tbl_carts`.`CART_ID` = '{$query_row_cart['CART_ID']}';");
                echo "
                <script>
                    alert('อัพเดตตะกร้าสำเร็จ')
                    location.assign('./home.php')
                </script>";
                exit;
            } else {
                // เพิ่มสินค้าใหม่ลงตะกร้า
                $conn->query("INSERT INTO `tbl_carts` (`CART_ID`, `USER_ID`, `PRO_ID`, `CART_NUMBER`, `CART_STAMP`, `CART_PRICE`)
                              VALUES (NULL, '$USER_ID', '$PRO_ID', '1', current_timestamp(), '{$query_row['PRO_PRICE']}');");
                echo "
                    <script>
                        alert('เพิ่มเข้าตะกร้าสำเร็จ')
                        location.assign('./home.php')
                    </script>";
                exit;
            }
        } else {
            header('Location: ./home.php');
        }
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

    <?php $_SESSION["page"] = 'home'; ?>
    <?php include_once('./inc/head.php') ?>

</head>

<body>
    <?php include_once('./inc/header.php') ?>

    <?php
    $sql = "SELECT PRO_GROUP_NAME FROM `tbl_products` WHERE PRO_DELETE IS NULL GROUP BY PRO_GROUP_NAME;";
    $query_group = $conn->query($sql, MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC);
    ?>

    <?php foreach ($query_group as $ke => $item): ?>
        <section class="section  <?= $ke == 0 ? "mt-5" : "mt-1 pt-3"; ?> " id="men">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="section-heading">
                            <h2>หมวดหมู่ <?= $item['PRO_GROUP_NAME']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="men-item-carousel">
                            <div class="owl-men-item owl-carousel">
                                <?php
                                $sqls = "SELECT * FROM `tbl_products` WHERE PRO_GROUP_NAME = '{$item['PRO_GROUP_NAME']}' AND PRO_DELETE IS null;";
                                $query_group_list = $conn->query($sqls, MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC);
                                ?>
                                <?php foreach ($query_group_list as $ilist): ?>
                                    <div class="item">
                                        <div class="thumb">
                                            <div class="hover-content">
                                                <ul>
                                                    <li>
                                                        <a href="single-product.php?PRO_ID=<?= $ilist['PRO_ID']; ?>"><i
                                                                class="fa fa-eye"></i></a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:addToCart('<?= $ilist['PRO_ID']; ?>')"><i
                                                                class="fa fa-shopping-cart"></i></a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <img src="upload/<?= $ilist['PRO_PHOTO']; ?>" alt="">
                                        </div>
                                        <div class="down-content">
                                            <h4><?= $ilist['PRO_NAME']; ?></h4>
                                            <span><?= $ilist['PRO_PRICE']; ?> บาท</span>

                                        </div>
                                    </div>
                                <?php endforeach ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endforeach ?>

    <section class="section" id="social">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-heading">
                        <h2>รูปภาพสินค้า</h2>
                        <span>แสดงรูปภาพสินค้าบางส่วนที่นิยม</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row images">
                <?php
                $sqls = "SELECT * FROM `tbl_products` WHERE PRO_DELETE IS null LIMIT 6;";
                $query_group_list = $conn->query($sqls, MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC);
                ?>
                <?php foreach ($query_group_list as $ilist): ?>
                    <div class="col-2">
                        <div class="thumb">
                            <img src="upload/<?= $ilist['PRO_PHOTO']; ?>" alt="">
                        </div>
                    </div>
                <?php endforeach ?>

            </div>
        </div>
    </section>


    <?php include_once('./inc/footer.php'); ?>

    <script src="./assets/js/home.js"></script>


</body>

</html>