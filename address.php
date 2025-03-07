<?php
include 'discount-process.php';

?>

<?php print_r($_REQUEST); ?>


<!doctype html>
<html lang="en">

<head>
  <?php
  include 'includes/style.php';
  ?>

  <title>Shopingo - eCommerce HTML Template</title>
</head>

<body>
  <?php
  include 'includes/headers.php';
  ?>


  <!--start page content-->
  <div class="page-content">


    <!--start breadcrumb-->
    <div class="py-4 border-bottom">
      <div class="container">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:;">checkout</a></li>
            <li class="breadcrumb-item active" aria-current="page">Address</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->


    <!--start product details-->
    <section class="section-padding">
      <div class="container">
        <form action="payment-method.php" method="POST">

          <div class="d-flex align-items-center px-3 py-2 border mb-4">
            <div class="text-start">
              <h4 class="mb-0 h4 fw-bold">Select Delivery Address</h4>
            </div>
          </div>


          <div class="row g-4">

            <div class="col-12 col-lg-8 col-xl-8">


              <?php
              include_once 'common-account-saved-address.php'
                ?>

            </div>
          </div><!--end row-->



          <div class="col-12 col-lg-4 col-xl-4">
            <div class="card rounded-0 mb-3">
              <div class="card-body">


                <?php
                include_once 'common-order-summery.php';
                ?>


                <div class="d-grid mt-4">
                  <!-- <a href="payment-method.php" class="btn btn-dark btn-ecomm py-3 px-5" role="button">Continue</a> -->
                  <button type="submit" class="btn btn-dark btn-ecomm py-3 px-5" role="button">Continue </button>

                </div>


              </div>
            </div>

          </div>

        </form>

      </div>
    </section>
    <!--start product details-->

  </div>
  <!--end page content-->

  <?php
  include 'includes/footers.php';
  ?>


</body>

</html>