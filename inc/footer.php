<!-- ***** Footer Start ***** -->
<footer>
    <div class="container">
        <div class="row">

            <div class="col-lg-12">
                <div class="under-footer">
                    <p>Copyright © 2022 </p>

                </div>
            </div>
        </div>
    </div>
</footer>


<!-- jQuery -->
<script src="./assets/js/jquery-2.1.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap -->
<script src="./assets/js/popper.js"></script>
<script src="./assets/js/bootstrap.min.js"></script>

<script src="https://cdn.datatables.net/v/bs4/jq-3.7.0/dt-2.2.1/datatables.min.js"></script>

<!-- Plugins -->
<script src="./assets/js/owl-carousel.js"></script>
<script src="./assets/js/accordions.js"></script>
<script src="./assets/js/datepicker.js"></script>
<script src="./assets/js/scrollreveal.min.js"></script>
<script src="./assets/js/waypoints.min.js"></script>
<script src="./assets/js/jquery.counterup.min.js"></script>
<script src="./assets/js/imgfix.min.js"></script>
<script src="./assets/js/slick.js"></script>
<script src="./assets/js/lightbox.js"></script>
<script src="./assets/js/isotope.js"></script>

<!-- Global Init -->
<script src="./assets/js/custom.js"></script>

<script>

    $(function () {
        var selectedClass = "";
        $("p").click(function () {
            selectedClass = $(this).attr("data-rel");
            $("#portfolio").fadeTo(50, 0.1);
            $("#portfolio div").not("." + selectedClass).fadeOut();
            setTimeout(function () {
                $("." + selectedClass).fadeIn();
                $("#portfolio").fadeTo(50, 1);
            }, 500);

        });
    });

</script>