<?php
include 'discount-process.php';

?>


<?php
print_r($_REQUEST);

$address_id = $_POST['flexradioaddress'] ?? null;



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
            <li class="breadcrumb-item"><a href="javascript:;">Checkout</a></li>
            <li class="breadcrumb-item active" aria-current="page">Payment Method</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->


    <!--start product details-->
    <section class="section-padding">
      <div class="container">


        <div class="d-flex align-items-center px-3 py-2 border mb-4">
          <div class="text-start">
            <h4 class="mb-0 h4 fw-bold">Select Payment Method</h4>
          </div>
        </div>

        <input type="hidden" id="addressId" value="<?php echo htmlspecialchars($address_id); ?>">


        <div class="row g-4">
          <div class="col-12 col-lg-8 col-xl-8">

            <div class="card rounded-0 payment-method">
              <div class="row g-0">


                <div class="col-12 col-lg-4 bg-light">
                  <div class="nav flex-column nav-pills">

                    <button class="nav-link rounded-0" data-payment-method="cash" data-bs-toggle="pill"
                      data-bs-target="#v-pills-home" type="button"><i class="bi bi-cash-stack me-2"></i>Cash On
                      Delivery</button>

                    <button class="nav-link active rounded-0" data-payment-method="online" data-bs-toggle="pill"
                      data-bs-target="#v-pills-profile" type="button"> <img referrerpolicy="origin"
                        src="https://badges.razorpay.com/badge-light.png " style="height: 45px; width: 113px;"
                        alt="Razorpay | Payment Gateway | Neobank">
                    </button>

                    <button class="nav-link rounded-0" data-payment-method="card" data-bs-toggle="pill"
                      data-bs-target="#v-pills-messages" type="button"><i
                        class="bi bi-credit-card-2-back me-2"></i>Credit/Debit Card</button>

                    <button class="nav-link rounded-0 border-bottom-0" data-payment-method="net-banking"
                      id="v-pills-settings-tab" data-bs-toggle="pill" data-bs-target="#v-pills-settings"
                      type="button"><i class="bi bi-bank2 me-2"></i>Net
                      Banking</button>

                  </div>
                </div>


                <div class="col-12 col-lg-8">
                  <div class="tab-content p-3">


                    <div class="tab-pane fade" id="v-pills-home">
                      <h5 class="mb-3 fw-bold">I would like to pay after product delivery</h5>
                      <button type="button" class="btn btn-ecomm btn-dark w-100 py-3 px-5">Place Order</button>
                    </div>



                    <div class="tab-pane fade show active" id="v-pills-profile">
                      <h5 class="mb-3 fw-bold">Secure Payment with Razorpay</h5>

                      <div class="mb-3">
                        <p class="mb-0">Note: After clicking on the button, you will be directed to a secure gateway for
                          payment. After completing the payment process, you will be redirected back to the website to
                          view details of your order.</p>
                      </div>

                      <button type="button" class="btn btn-ecomm btn-dark w-100 py-3 px-5">Pay Now</button>
                    </div>







                    <div class="tab-pane fade" id="v-pills-messages">
                      <div class="row g-3">

                        <div class="col-12">
                          <div class="form-floating">
                            <input type="text" class="form-control rounded-0" id="floatingCardNumber"
                              placeholder="Card Number">
                            <label for="floatingCardNumber">Card Number</label>
                          </div>
                        </div>

                        <div class="col-12">
                          <div class="form-floating">
                            <input type="text" class="form-control rounded-0" id="floatingNameonCard"
                              placeholder="Name on Card">
                            <label for="floatingNameonCard">Name on Card</label>
                          </div>
                        </div>

                        <div class="col-12 col-lg-8">
                          <div class="form-floating">
                            <input type="text" class="form-control rounded-0" id="floatingValidity"
                              placeholder="Validity (MM/YY)">
                            <label for="floatingValidity">Validity (MM/YY)</label>
                          </div>
                        </div>

                        <div class="col-12 col-lg-4">
                          <div class="form-floating">
                            <input type="text" class="form-control rounded-0" id="floatingCCV" placeholder="CCV">
                            <label for="floatingCCV">CCV</label>
                          </div>
                        </div>

                        <div class="col-12">
                          <button type="button" class="btn btn-ecomm btn-dark w-100 py-3 px-5">Pay Now</button>
                        </div>

                      </div><!--end row-->
                    </div>




                    <div class="tab-pane fade" id="v-pills-settings">
                      <div class="mb-3">
                        <p>Select your Bank</p>

                        <select class="form-select form-select-lg rounded-0" aria-label="Default select example">
                          <option selected="">--Please Select Your Bank--</option>
                          <option value="1">Bank Name 1</option>
                          <option value="2">Bank Name 2</option>
                          <option value="3">Bank Name 3</option>
                        </select>

                      </div>

                      <button type="button" class="btn btn-ecomm btn-dark w-100 py-3 px-5 mb-3">Pay Now</button>

                      <div class="">
                        <p class="mb-0">Note: After clicking on the button, you will be directed to a secure gateway for
                          payment. After completing the payment process, you will be redirected back to the website to
                          view details of your order.</p>
                      </div>

                    </div>



                  </div>

                </div>
              </div><!--end row-->
            </div>

          </div>


          <div class="col-12 col-lg-4 col-xl-4">
            <div class="card rounded-0 mb-3">
              <div class="card-body">


                <?php
                include_once 'common-order-summery.php';
                ?>


              </div>
            </div>

          </div>
        </div><!--end row-->

      </div>
    </section>
    <!--start product details-->




  </div>
  <!--end page content-->


  <?php
  include 'includes/footers.php';
  ?>




  <script>
    $(document).ready(function () {

      $('.btn-ecomm').on('click', function () {

        var addressId = $('#addressId').val();
        var paymentMethod = $('.nav-link.active').data('payment-method');
        console.log(paymentMethod);

        /* 
        var paymentMethod = $(this).text().trim(); 
        */

        if (!addressId) {
          alert('Please select a delivery address.');
          return;
        }

        $.ajax({
          url: 'order-process.php',
          method: 'POST',
          dataType: 'json',
          data: {
            flexradioaddress: addressId,
            payment_method: paymentMethod
          },
          beforeSend: function () {
            console.log('Sending address and payment method...');
          },
          success: function (response) {

            if (response.status === 'success') {
              window.location.href = 'razorpay-payment-gateway-1.php';
            }

            if (response.status === 'cash-success' && response.pg_order_id) {
              window.location.href = 'thank-you.php?pg_order_id=' + response.pg_order_id;
            }

          },
          error: function (xhr, status, error) {
            console.error('Error:', error);

          }
        });
      });




    });


  </script>

</body>

</html>