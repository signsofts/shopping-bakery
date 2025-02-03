<!-- ***** Preloader Start ***** -->
<div id="preloader">
    <div class="jumper">
        <div></div>
        <div></div>
        <div></div>
    </div>
</div>
<!-- ***** Preloader End ***** -->


<!-- ***** Header Area Start ***** -->
<header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- ***** Logo Start ***** -->
                    <a href="<?= $_SESSION["USER"]['USER_ROLE'] === "ADMIN" ? "ad-home.php" : "home.php"  ;?> " class="logo">
                        <img src="assets/images/logo.png">
                    </a>
                    <!-- ***** Logo End ***** -->
                    <!-- ***** Menu Start ***** -->
                    <ul class="nav">

                        <?php if ($_SESSION["USER"]['USER_ROLE'] === "ADMIN"): ?>
                            <li class="scroll-to-section">
                                <a href="./ad-home.php"
                                    class="<?php echo $_SESSION["page"] == "home" ? "active" : ""; ?>">หน้าหลัก</a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="./ad-product.php"
                                    class="<?php echo $_SESSION["page"] == "product" ? "active" : ""; ?>">จัดการสินค้า
                                </a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="./ad-user.php"
                                    class="<?php echo $_SESSION["page"] == "user" ? "active" : ""; ?>">จัดการลูกค้า
                                </a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="./ad-user-admin.php"
                                    class="<?php echo $_SESSION["page"] == "user-admin" ? "active" : ""; ?>">จัดการผู้ดูแลระบบ
                                </a>
                            </li>

                            <li class="scroll-to-section">
                                <a href="./ad-profile.php"
                                    class="<?php echo $_SESSION["page"] == "profile" ? "active" : ""; ?>">ข้อมูลส่วนตัว
                                </a>
                            </li>

                        <?php else: ?>
                            <li class="scroll-to-section">
                                <a href="./home.php" class="<?php echo $_SESSION["page"] == "home" ? "active" : ""; ?>">หน้าหลัก</a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="./product.php"
                                    class="<?php echo $_SESSION["page"] == "product" ? "active" : ""; ?>">รายการสินค้า</a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="./carts.php"
                                    class="<?php echo $_SESSION["page"] == "carts" ? "active" : ""; ?>">ตะกร้าสินค้า</a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="./history.php"
                                    class="<?php echo $_SESSION["page"] == "history" ? "active" : ""; ?>">ประวัติสั่งซื้อ</a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="./profile.php"
                                    class="<?php echo $_SESSION["page"] == "profile" ? "active" : ""; ?>">ข้อมูลส่วนตัว</a>
                            </li>
                        <?php endif; ?>
                        <li class="scroll-to-section">
                            <a href="./logout.php"
                                class="<?php echo $_SESSION["page"] == "logout" ? "active" : ""; ?>">ออกจากระบบ</a>
                        </li>
                    </ul>
                    <a class='menu-trigger'>
                        <span>Menu</span>
                    </a>
                    <!-- ***** Menu End ***** -->
                </nav>
            </div>
        </div>
    </div>
</header>
<!-- ***** Header Area End ***** -->