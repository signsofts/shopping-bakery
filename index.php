<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="เข้าสู่ระบบ - SHOPPING">
    <meta name="author" content="เข้าสู่ระบบ - SHOPPING">

    <title>เข้าสู่ระบบ - SHOPPING</title>

    <?php include_once('./inc/head.php') ?>


</head>

<body>

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
                        <a href="./" class="logo">
                            <img src="./assets/images/logo.png">
                        </a>
                        <!-- ***** Logo End ***** -->
                        <!-- ***** Menu Start ***** -->
                        <ul class="nav">
                            <li class="scroll-to-section"><a href="./" class="active">เข้าสู่ระบบ</a></li>
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

    <style>
        .contact-us {
            margin-top: 114px;
            border-bottom: 3px dotted #eee;
            padding-bottom: 90px;
        }
    </style>


    <!-- ***** Contact Area Starts ***** -->
    <div class="contact-us ">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-heading">
                        <h2>เข้าสู่ระบบ</h2>
                        <span>เข้าสู่ระบบเพื่อสั่งซื้อสินค้าจากร้านค้า</span>
                    </div>
                    <form id="form-login" action="./action-form-login.php" method="post" enctype="multipart/form-data" >
                        <div class="row">
                            <div class="col-lg-12 m-2">
                                <fieldset>
                                    <input name="USER_USERNAME" type="text" id="USER_USERNAME"
                                        placeholder="ชื่อผู้ใช้งาน" required="">
                                </fieldset>
                            </div>
                            <div class="col-lg-12 m-2">
                                <fieldset>
                                    <input name="USER_PASSWORD" type="password" id="USER_PASSWORD"
                                        placeholder="รหัสผ่าน" required="">
                                </fieldset>
                            </div>
                            <div class="col-lg-12 m-2">
                                <button type="submit" id="form-submit" class="main-dark-button">
                                    เข้าสู่ระบบ <i class="fa fa-info"></i>
                                </button>
                                <a class="text-dark" href="./register.php">
                                    สมัครสมาชิก <i class="fa fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include_once('./inc/footer.php'); ?>


</body>

</html>