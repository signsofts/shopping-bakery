<?php include_once('./inc/inc-html.php') ?>


<?php
if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET["type"]) && isset($_GET["PRO_ID"])) {

    if ($_GET["type"] == 'add') { // ตรวจสอบว่าการดำเนินการเป็นการเพิ่มสินค้าเข้าตะกร้า
        $PRO_ID = $_GET["PRO_ID"]; // รับค่า PRO_ID จาก URL
        $USER_ID = $_SESSION["USER"]['USER_ID']; // ดึง USER_ID ของผู้ใช้ปัจจุบันจาก SESSION

        // ค้นหาข้อมูลสินค้าจากฐานข้อมูล
        $sql = "SELECT * FROM `tbl_products` WHERE PRO_ID ='$PRO_ID'";
        $query_row = $conn->query($sql)->fetch_assoc();

        if ($query_row) { // ตรวจสอบว่าสินค้ามีอยู่จริงในฐานข้อมูล

            // ค้นหาข้อมูลสินค้าในตะกร้าของผู้ใช้
            $query_row_cart = $conn->query("SELECT * FROM `tbl_carts` WHERE USER_ID = '$USER_ID' AND PRO_ID ='$PRO_ID' ")->fetch_assoc();

            if ($query_row_cart) { // ถ้าสินค้ามีอยู่แล้วในตะกร้า ให้เพิ่มจำนวนสินค้า
                $CART_NUMBER = $query_row_cart['CART_NUMBER'] + 1;

                // ตรวจสอบว่าสินค้าในสต็อกเพียงพอหรือไม่
                if ($query_row['PRO_STOCK'] < $CART_NUMBER) {
                    echo "
                    <script>
                        alert('สินค้าหมด คงเหลือ ({$query_row['PRO_STOCK']}) ');
                        location.assign('./home.php');
                    </script>";
                    exit;
                }

                // อัพเดตจำนวนสินค้าในตะกร้า
                $conn->query("UPDATE `tbl_carts` SET `CART_NUMBER` = '$CART_NUMBER' WHERE `tbl_carts`.`CART_ID` = '{$query_row_cart['CART_ID']}';");

                echo "
                <script>
                    alert('อัพเดตตะกร้าสำเร็จ');
                    location.assign('./home.php');
                </script>";
                exit;
            } else { // ถ้าไม่มีสินค้าในตะกร้า ให้เพิ่มรายการใหม่
                $cart_insert = $conn->query("INSERT INTO `tbl_carts` (`CART_ID`, `USER_ID`, `PRO_ID`, `CART_NUMBER`, `CART_STAMP`, `CART_PRICE`)
                                             VALUES (NULL, '$USER_ID', '$PRO_ID', '1', current_timestamp(), '{$query_row['PRO_PRICE']}');");

                echo "
                <script>
                    alert('เพิ่มเข้าตะกร้าสำเร็จ');
                    location.assign('./home.php');
                </script>";
                exit;
            }
        } else { // ถ้าไม่มีสินค้า ให้เปลี่ยนเส้นทางกลับไปที่หน้าหลัก
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
    <meta name="description" content="รายการสินค้า - SHOPPING">
    <meta name="author" content="รายการสินค้า - SHOPPING">

    <title>รายการสินค้า - SHOPPING</title>

    <?php $_SESSION["page"] = 'product'; ?>
    <?php include_once('./inc/head.php') ?>

</head>

<body>
    <?php include_once('./inc/header.php') ?>
    <div class="page-heading" id="top">
        <div class="container">

        </div>
    </div>

    <section class="section" id="products">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-heading">
                        <h2>รายการสินค้า</h2>
                        <span>ตรวจสอบผลิตภัณฑ์ทั้งหมดของเรา.</span>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-end">
                    <form action="./product.php" method="get">
                        <select class="s-product" name="sort" id="sort" onchange="this.form.submit()">
                            <option value=""> -- จัดเรียง -- </option>
                            <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>ราคาต่ำสุด</option>
                            <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>ราคาสูงสุด</option>
                            <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : '' ?>>ชื่อ: A-Z</option>
                            <option value="name_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : '' ?>>ชื่อ: Z-A</option>
                            <option value="date_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date_desc') ? 'selected' : '' ?>>ใหม่ล่าสุด</option>
                            <option value="date_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date_asc') ? 'selected' : '' ?>>เก่าที่สุด</option>
                        </select>
                        <select name="g" class="s-product" onchange="this.form.submit()">
                            <option value="" <?= isset($_GET["g"]) && $_GET["g"] == '' ? 'selected' : ""; ?>> -- หมวด --
                            </option>
                            <?php
                            $sql = "SELECT PRO_GROUP_NAME FROM `tbl_products` WHERE PRO_DELETE IS NULL GROUP BY PRO_GROUP_NAME;";
                            $query_group = $conn->query($sql, MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC);
                            ?>
                            <?php foreach ($query_group as $ke => $item): ?>
                                <option <?= isset($_GET["g"]) && $_GET["g"] == $item['PRO_GROUP_NAME'] ? 'selected' : ""; ?>
                                    value="<?= $item['PRO_GROUP_NAME']; ?>"><?= $item['PRO_GROUP_NAME']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" value="<?= isset($_GET["v"]) ? $_GET["v"] : ""; ?>" name="v"
                            class="input-product">
                        <button type="submit" id="form-submit" class="btn-main-dark-button">
                            ค้นหา <i class="fa fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="container">
            <?php
            // ค่าจำนวนรายการต่อหน้า
            $items_per_page = 80;

            // รับค่าหน้าปัจจุบันจาก URL (ค่าเริ่มต้นเป็น 1)
            $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;

            // คำนวณ OFFSET
            $offset = ($current_page - 1) * $items_per_page;

            // เงื่อนไขการค้นหา
            $g = !isset($_GET["g"]) || empty($_GET["g"]) ? '' : "AND PRO_GROUP_NAME= '{$_GET["g"]}'";
            $v = !isset($_GET["v"]) || empty($_GET["v"]) ? '' : "AND PRO_NAME  LIKE '%{$_GET["v"]}%'";


            // การจัดเรียง
            $order_by = "PRO_STAMP DESC"; // ค่าเริ่มต้น
            if (isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'price_asc':
                        $order_by = "PRO_PRICE ASC";
                        break;
                    case 'price_desc':
                        $order_by = "PRO_PRICE DESC";
                        break;
                    case 'name_asc':
                        $order_by = "PRO_NAME ASC";
                        break;
                    case 'name_desc':
                        $order_by = "PRO_NAME DESC";
                        break;
                    case 'date_asc':
                        $order_by = "PRO_STAMP ASC";
                        break;
                    case 'date_desc':
                        $order_by = "PRO_STAMP DESC";
                        break;
                }
            }


            // ดึงจำนวนรายการทั้งหมด
            $sql_count = "SELECT COUNT(*) as total FROM `tbl_products` WHERE PRO_DELETE IS NULL $g $v;";
            $total_items = $conn->query($sql_count)->fetch_assoc()['total'];

            // คำนวณจำนวนหน้าทั้งหมด
            $total_pages = ceil($total_items / $items_per_page);

            // ดึงข้อมูลสำหรับหน้าปัจจุบัน
            $sqls = "SELECT * FROM `tbl_products` WHERE PRO_DELETE IS NULL $g $v ORDER BY $order_by LIMIT $items_per_page OFFSET $offset;";
            $query_group_list = $conn->query($sqls, MYSQLI_ASSOC)->fetch_all(MYSQLI_ASSOC);
            ?>

            <div class="row">
                <?php foreach ($query_group_list as $ilist): ?>
                    <div class="col-lg-4">
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
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="col-lg-12">
                <div class="pagination">
                    <ul>
                        <!-- ปุ่ม "ย้อนกลับ" -->
                        <?php if ($current_page > 1): ?>
                            <li>
                                <a
                                    href="?page=<?= $current_page - 1; ?>&g=<?= $_GET['g'] ?? ''; ?>&v=<?= $_GET['v'] ?? ''; ?>">
                                    < </a>
                            </li>
                        <?php endif; ?>

                        <!-- ปุ่มหมายเลขหน้า -->
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="<?= $i == $current_page ? 'active' : ''; ?>">
                                <a
                                    href="?page=<?= $i; ?>&g=<?= $_GET['g'] ?? ''; ?>&v=<?= $_GET['v'] ?? ''; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- ปุ่ม "ถัดไป" -->
                        <?php if ($current_page < $total_pages): ?>
                            <li><a
                                    href="?page=<?= $current_page + 1; ?>&g=<?= $_GET['g'] ?? ''; ?>&v=<?= $_GET['v'] ?? ''; ?>">></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

        </div>
    </section>
    <?php include_once('./inc/footer.php'); ?>

    <script src="./assets/js/product.js"></script>


</body>

</html>