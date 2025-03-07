<?php
include 'front-config.php';
include 'auth-check.php';


$customer_id = $_SESSION['user_id'];

$query = "SELECT first_name, last_name, date_of_birth, email, phone_number, gender 
          FROM customers WHERE customer_id = $customer_id";
$result = mysqli_query($conn, $query);


if ($result && mysqli_num_rows($result) > 0) {
  $user = mysqli_fetch_assoc($result);
} else {
  die("User not found.");
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateprofile') {

  $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
  $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
  $gender = mysqli_real_escape_string($conn, $_POST['gender']);


  $name_parts = explode(' ', $full_name);
  $first_name = $name_parts[0];
  $last_name = isset($name_parts[1]) ? $name_parts[1] : '';



  $query = "UPDATE customers SET 
            first_name = '$first_name', 
            last_name = '$last_name', 
            email = '$email', 
            phone_number = '$phone_number', 
            date_of_birth = '$date_of_birth', 
            gender = '$gender' 
            WHERE customer_id = $customer_id";

  if (mysqli_query($conn, $query)) {
    echo json_encode(["success" => true, "message" => "Profile update."]);
  } else {
    echo json_encode(["success" => false, "message" => "Database update failed."]);
  }
  exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
  $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
  $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

  if ($new_password !== $confirm_password) {
    echo json_encode(["success" => false, "message" => "Passwords do not match."]);
    exit();
  }

  $hashed_password = md5($new_password);

  $query = "UPDATE customers SET password = '$hashed_password' WHERE customer_id = $customer_id";

  if (mysqli_query($conn, $query)) {
    echo json_encode(["success" => true, "message" => "Password changed successfully."]);
  } else {
    echo json_encode(["success" => false, "message" => "Database update failed."]);
  }
  exit();
}






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
            <li class="breadcrumb-item"><a href="javascript:;">Account</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile</li>
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
            <h4 class="mb-0 h4 fw-bold">Account - Edit Profile</h4>
          </div>
        </div>

        <div class="btn btn-dark btn-ecomm d-xl-none position-fixed top-50 start-0 translate-middle-y"
          data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbarFilter"><span><i
              class="bi bi-person me-2"></i>Account</span></div>

        <div class="row">
          <?php
          include 'includes/common-account-dashboard.php';
          ?>

          <div class="col-12 col-xl-7">
            <div class="card rounded-0">
              <div class="card-body p-lg-5">
                <h5 class="mb-0 fw-bold">Edit Details</h5>
                <hr>

                <form id="editProfileForm">
                  <div class="row row-cols-1 g-3">
                    <div class="col">
                      <div class="form-floating">
                        <input type="text" class="form-control rounded-0" name="full_name" placeholder="Name"
                          value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>">
                        <label for="floatingInputName">Name</label>
                      </div>
                    </div>


                    <div class="col">
                      <div class="form-floating">
                        <input type="text" class="form-control rounded-0" name="phone_number"
                          placeholder="Mobile Number"
                          value="<?php echo htmlspecialchars(!empty($user['phone_number']) ? $user['phone_number'] : ''); ?>">
                        <label for="floatingInputNumber">Mobile Number</label>
                      </div>
                    </div>


                    <div class="col">
                      <div class="form-floating">
                        <input type="email" class="form-control rounded-0" name="email" placeholder="Email"
                          value="<?php echo htmlspecialchars($user['email']); ?>">
                        <label for="floatingInputEmail">Email</label>
                      </div>
                    </div>


                    <div class="col">

                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="Male" <?php echo ($user['gender'] == 'Male') ? 'checked' : ''; ?>>
                        <label class="form-check-label">Male</label>
                      </div>

                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="Female" <?php echo ($user['gender'] == 'Female') ? 'checked' : ''; ?>>
                        <label class="form-check-label">Female</label>
                      </div>

                    </div>



                    <div class="col">
                      <div class="form-floating">
                        <input type="date" class="form-control rounded-0" name="date_of_birth"
                          value="<?php echo htmlspecialchars($user['date_of_birth']); ?>">

                        <label for="floatingInputDOB">Date of Birth</label>
                      </div>
                    </div>


                    <!-- <div class="col">
                      <div class="form-floating">
                        <input type="text" class="form-control rounded-0" id="floatingInputLocation"
                          placeholder="Location" value="United Kingdom">
                        <label for="floatingInputLocation">Location</label>
                      </div>
                    </div> -->


                    <div class="col">
                      <button type="submit" class="btn btn-dark py-3 btn-ecomm w-100">Save Details</button>
                    </div>

                    <div class="col">
                      <button type="button" class="btn btn-outline-dark py-3 btn-ecomm w-100" data-bs-toggle="modal"
                        data-bs-target="#ChangePasswordModal">Change Password</button>
                    </div>


                  </div>
                </form>
              </div>
            </div>
          </div>
        </div><!--end row-->
      </div>
    </section>
    <!--start product details-->


    <!-- Change Password Modal -->
    <div class="modal" id="ChangePasswordModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-0">

          <div class="modal-body">
            <h5 class="fw-bold mb-3">Change Password</h5>
            <hr>


            <form id="editPassword">

              <div class="form-floating mb-3">
                <input type="password" class="form-control rounded-0" id="floatingInputNewPass"
                  placeholder="New Password" name="new_password" required>
                <label for="floatingInputNewPass">New Password</label>
              </div>

              <div class="form-floating mb-3">
                <input type="password" class="form-control rounded-0" id="floatingInputConPass"
                  placeholder="Confirm New Password" name="confirm_password" required>
                <label for="floatingInputConPass">Confirm New Password</label>
              </div>

              <div class="d-grid gap-3 w-100">
                <button type="submit" class="btn btn-dark py-3 btn-ecomm">Change</button>

                <button type="button" class="btn btn-outline-dark py-3 btn-ecomm" data-bs-dismiss="modal"
                  aria-label="Close">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- end Change Password Modal -->


  </div>
  <!--end page content-->





  <?php
  include 'includes/footers.php';
  ?>


  <script>
    $(document).ready(function () {

      $("#editProfileForm").submit(function (e) {
        e.preventDefault();

        let formData = $(this).serialize() + "&action=updateprofile";

        $.ajax({
          type: "POST",
          url: "account-edit-profile.php",
          data: formData,
          dataType: "json",
          success: function (response) {
            if (response.success) {
              alert("Profile updated successfully!");
            } else {
              alert("Error: " + response.message);
            }
          },
          error: function () {
            alert("An error occurred. Please try again.");
          }
        });
      });


      $("#editPassword").submit(function (e) {
        e.preventDefault();

        let newPassword = $("#floatingInputNewPass").val();
        let confirmPassword = $("#floatingInputConPass").val();

        if (newPassword.length < 6) {
          alert("Password must be at least 6 characters long.");
          return;
        }

        if (newPassword !== confirmPassword) {
          alert("Passwords do not match.");
          return;
        }

        let formData = {
          action: "change_password",
          new_password: newPassword,
          confirm_password: confirmPassword
        };

        $.ajax({
          type: "POST",
          url: "account-edit-profile.php",
          data: formData,
          dataType: "json",
          success: function (response) {
            if (response.success) {
              alert("Password changed successfully!");
              $("#ChangePasswordModal").modal("hide");
              $("#editPassword")[0].reset();
            } else {
              alert("Error: " + response.message);
            }
          },
          error: function () {
            alert("An error occurred. Please try again.");
          }
        });
      });


    });
  </script>

</body>

</html>