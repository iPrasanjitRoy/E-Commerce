<?php
include 'front-config.php';


if (isset($_POST['action']) && $_POST['action'] == 'register' && $_SERVER['REQUEST_METHOD'] == 'POST') {

  $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
  $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);


  $hashedPassword = md5($password);

  $query = "INSERT INTO customers (first_name, last_name, email, phone_number, `password`) 
            VALUES ('$firstName', '$lastName', '$email', '$phoneNumber', '$hashedPassword')";



  if (mysqli_query($conn, $query)) {
    echo json_encode(['status' => 'success', 'message' => 'Registration successful! Redirecting...']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'There was an error while processing your registration.']);
  }
  mysqli_close($conn);
  exit();
}



?>

<!doctype html>
<html lang="en">

<head>
  <title>Shopingo - eCommerce HTML Template</title>
  <?php
  include 'includes/style.php';
  ?>

</head>

<body>

  <?php
  include 'includes/headers.php';
  ?>

  <!--page loader-->
  <div class="loader-wrapper">
    <div class="d-flex justify-content-center align-items-center position-absolute top-50 start-50 translate-middle">
      <div class="spinner-border text-dark" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  </div>
  <!--end loader-->



  <!--start page content-->
  <div class="page-content">


    <!--start breadcrumb-->
    <div class="py-4 border-bottom">
      <div class="container">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:;">Authentication</a></li>
            <li class="breadcrumb-item active" aria-current="page">Register</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->




    <!--start product details-->
    <section class="section-padding">
      <div class="container">

        <div class="row">
          <div class="col-12 col-lg-6 col-xl-5 col-xxl-5 mx-auto">
            <div class="card rounded-0">
              <div class="card-body p-4">
                <h4 class="mb-0 fw-bold text-center">Registration</h4>
                <hr>

                <form id="registrationForm">
                  <div class="row g-4">

                    <!-- First Name -->
                    <div class="col-12">
                      <label for="firstName" class="form-label">First Name</label>
                      <input type="text" class="form-control rounded-0" id="firstName" name="firstName" required>
                    </div>

                    <!-- Last Name -->
                    <div class="col-12">
                      <label for="lastName" class="form-label">Last Name</label>
                      <input type="text" class="form-control rounded-0" id="lastName" name="lastName" required>
                    </div>


                    <!-- Email -->
                    <div class="col-12">
                      <label for="email" class="form-label">Email ID</label>
                      <input type="email" class="form-control rounded-0" id="email" name="email" required>
                    </div>

                    <!-- Phone Number -->
                    <div class="col-12">
                      <label for="phoneNumber" class="form-label">Phone Number</label>
                      <input type="text" class="form-control rounded-0" id="phoneNumber" name="phoneNumber" required>
                    </div>



                    <!-- Password -->
                    <div class="col-12">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" class="form-control rounded-0" id="password" name="password" required>
                    </div>

                    <!-- Forgot Password -->
                    <div class="col-12 text-center">
                      <p class="mb-0 rounded-0 w-100">Forgot Password? <a href="javascript:;" class="text-danger">Click
                          Here</a></p>
                    </div>


                    <!-- Agree to Terms -->
                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="termsCheck" required>
                        <label class="form-check-label" for="termsCheck">
                          I agree to Terms and Conditions
                        </label>
                      </div>
                    </div>


                    <div class="col-12">
                      <hr class="my-0">
                    </div>


                    <div class="col-12">
                      <button type="submit" class="btn btn-dark rounded-0 btn-ecomm w-100" disabled>Sign Up</button>
                    </div>

                    <div class="col-12 text-center">
                      <p class="mb-0 rounded-0 w-100">Already have an account? <a href="auth-login.php"
                          class="text-danger">Sign In</a></p>
                    </div>

                  </div><!---end row-->
                </form>



              </div>
            </div>
          </div>
        </div><!--end row-->

      </div>
    </section>

  </div>
  <!--end page content-->


  <?php
  include 'includes/footers.php';
  ?>


  <script>
    $(document).ready(function () {

      $('#termsCheck').change(function () {
        if (this.checked) {
          $('.btn-ecomm').removeAttr('disabled');
        } else {
          $('.btn-ecomm').attr('disabled', 'disabled');
        }
      });



      $('#registrationForm').submit(function (e) {
        e.preventDefault();

        $('.btn-ecomm').attr('disabled', 'disabled');

        var formData = $(this).serialize();
        formData += '&action=register';

        $.ajax({
          url: 'auth-register.php',
          type: 'POST',
          data: formData,
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              alert(response.message);
              $('#registrationForm')[0].reset();
              window.location.href = 'auth-login.php';
            } else {
              alert('Registration failed: ' + response.message);
            }
          },
          error: function () {
            alert('There was an error processing your request.');
          },
          complete: function () {
            $('.btn-ecomm').removeAttr('disabled');
          }
        });
      });
    });
  </script>

</body>

</html>