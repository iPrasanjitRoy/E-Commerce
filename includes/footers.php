<?php
include 'front-config.php';

$companyQuery = "SELECT * FROM companies WHERE company_id = 1";
$companyQueryresult = mysqli_query($conn, $companyQuery);
$company = mysqli_fetch_assoc($companyQueryresult);
$logoUrl = $company['logo'];


$sql = "SELECT * FROM main_category";
$main_category_result = mysqli_query($conn, $sql);


?>






<!-- New Address Modal -->
<div class="modal" id="NewAddress" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-0">

      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add New Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>


      <form id="addAddressForm">
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

          <div class="">
            <h6 class="fw-bold mb-3">Contact Details</h6>

            <div class="form-floating mb-3">
              <input type="text" class="form-control rounded-0" id="floatingName" placeholder="Name" name="name"
                required>
              <label for="floatingName">Name</label>
            </div>

            <div class="form-floating mb-3">
              <input type="text" class="form-control rounded-0" id="floatingMobileNo" placeholder="Mobile No"
                name="mobile_no" required>
              <label for="floatingMobileNo">Mobile No</label>
            </div>
          </div>


          <div class="mt-4">
            <h6 class="fw-bold mb-3">Address</h6>

            <div class="form-floating mb-3">
              <input type="text" class="form-control rounded-0" id="floatingPinCode" placeholder="Pin Code"
                name="pin_code" required>
              <label for="floatingPinCode">Pin Code</label>
            </div>

            <div class="form-floating mb-3">
              <input type="text" class="form-control rounded-0" id="floatingAddress"
                placeholder="Address (House No, Building, Street, Area)" name="address" required>
              <label for="floatingAddress">Address</label>
            </div>

            <div class="form-floating mb-3">
              <input type="text" class="form-control rounded-0" id="floatingLocalityTown"
                placeholder="City / Town / Village" name="city_village" required>
              <label for="floatingLocalityTown">City / Town / Village</label>
            </div>

            <div class="row">
              <div class="col">
                <div class="form-floating">
                  <input type="text" class="form-control rounded-0" id="floatingCity" placeholder="City / District"
                    name="district" required>
                  <label for="floatingAddress">District</label>
                </div>
              </div>

              <div class="col">
                <div class="form-floating">
                  <input type="text" class="form-control rounded-0" id="floatingState" placeholder="State" name="state"
                    required>
                  <label for="floatingState">State</label>
                </div>
              </div>

            </div>
          </div>

        </div>

        <div class="modal-footer">
          <div class="d-grid w-100">
            <button type="submit" class="btn btn-dark py-3 px-5 btn-ecomm">Add Address</button>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>
<!-- end New Address Modal -->



<!-- Edit Address Modal -->
<div class="modal" id="EditAddress" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-0">

      <div class="modal-header">
        <h5 class="modal-title fw-bold">Edit Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="editAddressForm">
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
          <div class="">

            <input type="hidden" id="editAddressId" name="editaddressid" value="">


            <h6 class="fw-bold mb-3">Contact Details</h6>

            <div class="form-floating mb-3">
              <input type="text" class="form-control rounded-0" id="editName" placeholder="Name" value=""
                name="editname">
              <label for="editName">Name</label>
            </div>

            <div class="form-floating mb-3">
              <input type="text" class="form-control rounded-0" id="editMobileNo" placeholder="Mobile No" value=""
                name="editmobileno">
              <label for="editMobileNo">Mobile No</label>
            </div>

          </div>


          <div class="mt-4">
            <h6 class="fw-bold mb-3">Address</h6>

            <div class="form-floating mb-3">
              <input type="text" class="form-control rounded-0" id="editPinCode" placeholder="Pin Code" value=""
                name="editpincode">
              <label for="editPinCode">Pin Code</label>
            </div>


            <div class="form-floating mb-3">
              <input type="text" class="form-control rounded-0" id="editAddress"
                placeholder="Address (House No, Building, Street, Area)" value="" name="editaddress">
              <label for="editAddress">Address</label>
            </div>


            <div class="form-floating mb-3">
              <input type="text" class="form-control rounded-0" id="editCity" placeholder="Locality/Town" value=""
                name="editcity">
              <label for="editCity">Locality / Town</label>
            </div>

            <div class="row">
              <div class="col">
                <div class="form-floating">
                  <input type="text" class="form-control rounded-0" id="editDistrict" placeholder="District" value=""
                    name="editdistrict">
                  <label for="editDistrict">District</label>
                </div>
              </div>

              <div class="col">
                <div class="form-floating">
                  <input type="text" class="form-control rounded-0" id="editState" placeholder="State" value=""
                    name="editstate">
                  <label for="editState">State</label>
                </div>
              </div>

            </div>
          </div>

        </div>


        <div class="modal-footer">
          <div class="d-grid w-100">
            <button type="submit" class="btn btn-dark py-3 px-5 btn-ecomm updateaddress">Save Address</button>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>
<!-- end Edit Address Modal -->






<!--start footer-->
<section class="footer-section bg-section-2 section-padding">
  <div class="container">
    <div class="row row-cols-1 row-cols-lg-4 g-4">

      <div class="col">
        <div class="footer-widget-6">
          <img src="backend/<?php echo htmlspecialchars($logoUrl); ?>" class="logo-img" alt="Company Logo">

          <h5 class="mb-3 fw-bold">About Us</h5>
          <p class="mb-2"> <?php echo $company['about_company'] ?> </p>

          <a class="link-dark" href="javascript:;">Read More</a>
        </div>
      </div>


      <div class="col">
        <div class="footer-widget-7">
          <h5 class="mb-3 fw-bold">Explore</h5>
          <ul class="widget-link list-unstyled">
            <?php while ($category = mysqli_fetch_assoc($main_category_result)): ?>
              <li>
                <a href="shop-grid.php?main_category_url=<?php echo $category['main_category_url']; ?>" class="mb-2">
                  <?php echo htmlspecialchars($category['main_category_name']); ?>
                </a>
              </li>
            <?php endwhile; ?>
          </ul>
        </div>
      </div>



      <div class="col">
        <div class="footer-widget-8">
          <h5 class="mb-3 fw-bold">Company</h5>
          <ul class="widget-link list-unstyled">
            <li><a href="javascript:;">About Us</a></li>
            <li><a href="javascript:;">Contact Us</a></li>
            <li><a href="javascript:;">FAQ</a></li>
            <li><a href="javascript:;">Privacy</a></li>
            <li><a href="javascript:;">Terms</a></li>
            <li><a href="javascript:;">Complaints</a></li>
          </ul>
        </div>
      </div>



      <div class="col">
        <div class="footer-widget-9">
          <h5 class="mb-3 fw-bold">Follow Us</h5>
          <div class="social-link d-flex align-items-center gap-2">

            <?php if (!empty($company['facebook_link'])): ?>
              <a href="<?php echo $company['facebook_link']; ?>"><i class="bi bi-facebook"></i></a>
            <?php endif; ?>

            <?php if (!empty($company['instagram_link'])): ?>
              <a href="<?php echo $company['instagram_link']; ?>"><i class="bi bi-instagram"></i></a>
            <?php endif; ?>

            <?php if (!empty($company['twitter_link'])): ?>
              <a href="<?php echo $company['twitter_link']; ?>"><i class="bi bi-twitter"></i></a>
            <?php endif; ?>

            <?php if (!empty($company['linked_link'])): ?>
              <a href="<?php echo $company['linked_link']; ?>"><i class="bi bi-linkedin"></i></a>
            <?php endif; ?>

            <?php if (!empty($company['youtube_link'])): ?>
              <a href="<?php echo $company['youtube_link']; ?>"><i class="bi bi-youtube"></i></a>
            <?php endif; ?>

          </div>


          <div class="mb-3 mt-3">
            <h5 class="mb-0 fw-bold">Support</h5>
            <p class="mb-0 text-muted"><?php echo $company['email']; ?></p>
          </div>


          <div class="">
            <h5 class="mb-0 fw-bold">Toll Free</h5>
            <p class="mb-0 text-muted"><?php echo $company['phone_number']; ?></p>
          </div>

        </div>
      </div>
    </div><!--end row-->


    <div class="my-5"></div>
    <div class="row">

      <div class="col-12">
        <div class="text-center">
          <h5 class="fw-bold mb-3">Download Mobile App</h5>
        </div>

        <div class="app-icon d-flex flex-column flex-sm-row align-items-center justify-content-center gap-2">
          <div>
            <a href="javascript:;">
              <img src="assets/images/play-store.webp" width="160" alt="">
            </a>
          </div>


          <div>
            <a href="javascript:;">
              <img src="assets/images/apple-store.webp" width="160" alt="">
            </a>
          </div>
        </div>


      </div>
    </div><!--end row-->

  </div>
</section>
<!--end footer-->

<footer class="footer-strip text-center py-3 bg-section-2 border-top positon-absolute bottom-0">
  <p class="mb-0 text-muted"><?php echo $company['footer_text']; ?></p>
</footer>







<!--Start Back To Top Button-->
<a href="javaScript:;" class="back-to-top"><i class="bi bi-arrow-up"></i></a>
<!--End Back To Top Button-->



<!-- JavaScript files -->
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/plugins/slick/slick.min.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/index.js"></script>
<script src="assets/js/loader.js"></script>





<script>
  $(document).ready(function () {
    $("#addAddressForm").submit(function (e) {
      e.preventDefault();

      let formData = $(this).serialize() + "&action=addData";


      $.ajax({
        url: "account-saved-address.php",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            alert(response.message);
            location.reload(); /*  REFRESH THE PAGE TO SHOW THE NEW ADDRESS */
          } else {
            alert("Error: " + response.message);
          }
        },
        error: function () {
          alert("An error occurred while processing the request.");
        }
      });
    });






    $(".address-radio").change(function () {
      let addressId = $(this).data("id");

      $.ajax({
        url: "account-saved-address.php",
        type: "POST",
        data: { address_id: addressId, action: "setDefault" },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            alert(response.message);
            location.reload();
          } else {
            alert("Error: " + response.message);
          }
        },
        error: function () {
          alert("An error occurred while updating the default address.");
        }
      });
    });




  });


  $(document).ready(function () {
    $(".editaddress").click(function () {
      let addressId = $(this).data("id");

      $.ajax({
        url: "account-saved-address.php",
        type: "POST",
        data: { address_id: addressId, action: "fetchaddress" },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            $("#editAddressId").val(response.data.id);
            $("#editName").val(response.data.name);
            $("#editMobileNo").val(response.data.mobile_no);
            $("#editPinCode").val(response.data.pin_code);
            $("#editAddress").val(response.data.address);
            $("#editCity").val(response.data.city_village);
            $("#editDistrict").val(response.data.district);
            $("#editState").val(response.data.state);

            $("#EditAddress").modal("show");
          } else {
            alert("Error: " + response.message);
          }
        },
        error: function () {
          alert("An error occurred while fetching the address details.");
        }
      });
    });

    $(document).on("submit", "#editAddressForm", function (e) {
      e.preventDefault();
      let formData = $(this).serialize() + "&action=updateeditaddress";

      $.ajax({
        url: "account-saved-address.php",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            alert(response.message);
            location.reload();
          } else {
            alert("Error: " + response.message);
          }
        },
        error: function () {
          alert("An error occurred while updating the address.");
        }
      });
    });
  });




  $(document).ready(function () {

    $(".removeaddress").click(function () {
      let addressId = $(this).data("id");

      if (confirm("Are you sure you want to remove this address?")) {
        $.ajax({
          url: "account-saved-address.php",
          type: "POST",
          data: { id: addressId, action: "removed" },
          success: function (response) {
            location.reload();
          },
          error: function () {
            alert("An error occurred while deleting the address.");
          }
        });
      }
    });


  });

</script>





<script>
  $(document).ready(function () {

    /* HANDLE ADD TO WISHLIST */
    $(document).on('click', '.wishlistbutton', function () {
      let productId = $(this).data('product-id');
      console.log("Wishlist Product ID:", productId);

      $.ajax({
        url: '',
        type: 'POST',
        data: { wishlistAdd: productId },
        success: function (response) {
          alert('Added to Wishlist!');
        },
        error: function () {
          alert('Error adding to Wishlist.');
        }
      });
    });

    /* HANDLE ADD TO CART */
    $(document).on('click', '.cartbutton', function () {
      let combinationId = $(this).data('combination-id');
      console.log("Cart Combination ID:", combinationId);

      $.ajax({
        url: '',
        type: 'POST',
        data: { add_to_cart_id: combinationId },
        success: function (response) {
          alert('Added to Cart!');
        },
        error: function () {
          alert('Error adding to Cart.');
        }
      });
    });

  });

</script>