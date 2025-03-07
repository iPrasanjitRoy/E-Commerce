<?php
include 'discount-process.php';

?>

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
            <li class="breadcrumb-item"><a href="javascript:;">Shop</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cart</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->


    <!--start product details-->
    <section class="section-padding">
      <div class="container">
        <form action="address.php" method="POST">

          <div class="d-flex align-items-center px-3 py-2 border mb-4">
            <div class="text-start">
              <h4 class="mb-0 h4 fw-bold">My Bag (<?php echo count($cartItems); ?> items) </h4>
            </div>

            <div class="ms-auto">
              <button type="button" class="btn btn-light btn-ecomm">Continue Shopping</button>
            </div>
          </div>


          <div class="row g-4">
            <div class="col-12 col-xl-8">
              <?php foreach ($cartItems as $item): ?>

                <!-- Hidden Input for Combination ID -->
                <input type="hidden" name="combination_id[]" value="<?php echo $item['combination_id']; ?>">



                <div class="card rounded-0 mb-3">
                  <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row gap-3">

                      <div class="product-img">
                        <img src="backend/<?php echo $item['image_one']; ?>" width="150" alt="">
                      </div>


                      <div class="product-info flex-grow-1">
                        <h5 class="fw-bold mb-0"><?php echo $item['product_name']; ?></h5>


                        <div class="product-price d-flex align-items-center gap-2 mt-3">
                          <div class="h6 fw-bold">₹<?php echo number_format($item['price'], 2); ?></div>

                          <div class="h6 fw-light text-muted text-decoration-line-through">$2089</div>
                          <div class="h6 fw-bold text-danger">(70% off)</div>
                        </div>



                        <div class="mt-3 hstack gap-2">
                          <button type="button" class="btn btn-sm btn-light border rounded-0">Size :
                            <?php echo $item['size_name']; ?></button>


                          <button type="button" class="btn btn-sm btn-light border rounded-0">Colour :
                            <?php echo $item['colour_name']; ?> </button>
                        </div>


                      </div>

                      <div class="quantity-controls d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-dark decrement2"
                          data-id="<?php echo $item['combination_id']; ?>"
                          data-cartid="<?php echo $item['cartid']; ?>">-</button>

                        <span class="mx-2 quantity-text"
                          data-id="<?php echo $item['combination_id']; ?>"><?php echo $item['quantity']; ?></span>

                        <button class="btn btn-sm btn-outline-dark increment2"
                          data-id="<?php echo $item['combination_id']; ?>"
                          data-cartid="<?php echo $item['cartid']; ?>">+</button>
                      </div>




                      <div class="d-none d-lg-block vr"></div>

                      <div class="d-grid gap-2 align-self-start align-self-lg-center">
                        <button type="button" class="btn btn-ecomm remove-item2"
                          data-id="<?php echo $item['combination_id']; ?>">
                          <i class="bi bi-x-lg me-2"></i>Remove
                        </button>


                        <button type="button" class="btn dark btn-ecomm"><i class="bi bi-suit-heart me-2"></i>Move to
                          Wishlist</button>
                      </div>


                    </div>
                  </div>
                </div>
              <?php endforeach; ?>

            </div>

            <div class="col-12 col-xl-4">

              <div class="card rounded-0 mb-3">
                <div class="card-body">

                  <?php
                  include_once 'common-order-summery.php';
                  ?>



                  <div class="d-grid mt-4">

                    <!-- <a href="address.php" class="btn btn-dark btn-ecomm py-3 px-5">Place Order</a> -->
                    <button type="submit" class="btn btn-dark btn-ecomm py-3 px-5">Place Order </button>

                  </div>

                </div>
              </div>


              <div class="card rounded-0">
                <div class="card-body">
                  <h5 class="fw-bold mb-4">Apply Coupon</h5>

                  <div class="input-group">
                    <input type="text" class="form-control rounded-0" placeholder="Enter Coupon Code" id="couponCode"
                      name="couponcode"
                      value="<?php echo isset($_SESSION['applied_coupon']['coupon_code']) ? $_SESSION['applied_coupon']['coupon_code'] : ''; ?>">
                    <button class="btn btn-dark btn-ecomm rounded-0" type="button" id="applyCoupon">Apply</button>


                    <?php if (isset($_SESSION['applied_coupon']['coupon_code'])): ?>
                      <button class="btn btn-danger btn-ecomm rounded-0" type="button" id="removeCoupon">
                        <i class="bi bi-x-lg"></i>
                      </button>
                    <?php endif; ?>


                  </div>

                </div>
              </div>


            </div>
          </div><!--end row-->

        </form>


      </div>
    </section>
    <!--start product details-->




  </div>
  <!--end page content-->





  <?php
  include 'includes/footers.php';
  ?>

  <script>

    $(document).on('click', '#removeCoupon', function () {
      $.ajax({
        url: 'discount-process.php',
        type: 'POST',
        data: { remove_coupon: true },
        dataType: 'json',
        success: function (response) {
          if (response.status === 'success') {
            alert('Coupon removed successfully.');
            location.reload();
          } else {
            alert('Failed to remove coupon.');
          }
        },
        error: function () {
          alert('Error removing coupon.');
        }
      });
    });




    $(document).on('click', '#applyCoupon', function () {
      let couponCode = $('#couponCode').val().trim();
      let combinationIds = [];

      console.log(couponCode);


      $('input[name="combination_id[]"]').each(function () {
        combinationIds.push($(this).val());
      });

      console.log(combinationIds);


      if (couponCode === '') {
        alert('Please enter a valid coupon code.');
        return;
      }

      $.ajax({
        url: 'discount-process.php',
        type: 'POST',
        data: {
          apply_coupon: true,
          coupon_code: couponCode,
          combination_ids: combinationIds
        },
        dataType: 'json',

        success: function (response) {
          if (response.status === 'success') {
            // alert('Coupon applied successfully!');
            alert('Coupon applied successfully! Discount: ₹' + response.discount);
            location.reload();
          } else {
            alert(response.message);
          }
        },

        error: function () {
          alert('Failed to apply coupon.');
        }
      });
    });


    /* Increment Quantity */
    $(document).on('click', '.increment2', function () {
      let combinationId = $(this).data('id');
      updateCartQuantity(combinationId, 1);
    });

    /* Decrement Quantity */
    $(document).on('click', '.decrement2', function () {
      let combinationId = $(this).data('id');
      updateCartQuantity(combinationId, -1);
    });



    function updateCartQuantity(combinationId, change) {
      $.ajax({
        url: 'cart.php',
        type: 'POST',
        data: { update_quantity_id: combinationId, change: change },
        dataType: 'json',
        success: function (response) {
          if (response.status === 'success') {
            location.reload();



          } else {
            alert(response.message);
          }
        },
        error: function () {
          alert('Failed to update quantity.');
        }
      });
    }



    /* Remove Item */
    $(document).on('click', '.remove-item2', function () {
      let combinationId = $(this).data('id');
      removeFromCart(combinationId);
    });

    function removeFromCart(combinationId) {
      $.ajax({
        url: 'cart.php',
        type: 'POST',
        data: { remove_from_cart_id: combinationId },
        dataType: 'json',
        success: function (response) {
          if (response.status === 'success') {
            location.reload();

          } else {
            alert(response.message);
          }
        },
        error: function () {
          alert('Failed to remove product.');
        }
      });
    }




  </script>



</body>

</html>