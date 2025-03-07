<?php
include 'front-config.php';

if (isset($_POST['action']) && $_POST['action'] == 'login' && $_SERVER['REQUEST_METHOD'] == 'POST') {

  $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
  $password = isset($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : '';

  if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required!']);
    exit();
  }

  $hashedPassword = md5($password);

  $query = "SELECT * FROM customers WHERE email = '$email' AND password = '$hashedPassword'";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    $_SESSION['user_id'] = $user['customer_id'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_email'] = $user['email'];

    echo json_encode(['status' => 'success', 'message' => 'Login successful! Redirecting...']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
  }

  mysqli_close($conn);
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
            <li class="breadcrumb-item active" aria-current="page">Login</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->




    <!--start product details-->
    <section class="section-padding">
      <div class="container">

        <div class="row">
          <div class="col-12 col-lg-6 col-xl-5 col-xxl-4 mx-auto">
            <div class="card rounded-0">
              <div class="card-body p-4">
                <h4 class="mb-0 fw-bold text-center">User Login</h4>
                <hr>
                <form id="loginForm">
                  <div class="row g-4">

                    <!-- Username (Email) -->
                    <div class="col-12">
                      <label for="email" class="form-label">Email</label>
                      <input type="text" class="form-control rounded-0" id="email" name="email" required>
                    </div>

                    <!-- Password -->
                    <div class="col-12">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" class="form-control rounded-0" id="password" name="password" required>
                    </div>

                    <div class="col-12">
                      <a href="#" class="text-content btn bg-light rounded-0 w-100"><i
                          class="bi bi-lock me-2"></i>Forgot Password</a>
                    </div>

                    <div class="col-12">
                      <hr class="my-0">
                    </div>

                    <!-- Login Button -->
                    <div class="col-12">
                      <button type="submit" class="btn btn-dark rounded-0 btn-ecomm w-100">Login</button>
                    </div>

                    <div class="col-12 text-center">
                      <p class="mb-0 rounded-0 w-100">Don't have an account? <a href="auth-register.php"
                          class="text-danger">Sign Up</a></p>
                    </div>

                  </div>
                </form>


              </div>
            </div>
          </div>
        </div><!--end row-->

      </div>
    </section>



  </div>


  <?php
  include 'includes/footers.php';
  ?>

  <script>
    $(document).ready(function () {
      $('#loginForm').submit(function (e) {
        e.preventDefault();

        var loginButton = $('button[type="submit"]');
        loginButton.prop('disabled', true).text('Logging in...');

        var formData = {
          email: $('#email').val(),
          password: $('#password').val(),
          action: 'login'
        };


        $.ajax({
          url: 'auth-login.php',
          type: 'POST',
          data: formData,
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              alert(response.message);

              $.ajax({
                url: "cart.php",
                type: "GET",
                data: { transfer_session_cart: 1 },
                success: function (cartResponse) {
                  console.log("Session cart transferred:", cartResponse);
                  window.location.href = 'index.php';
                }
              });



            } else {
              alert('Login failed: ' + response.message);
              loginButton.prop('disabled', false).text('Login');
            }
          },
          error: function () {
            alert('There was an error processing your request.');
            loginButton.prop('disabled', false).text('Login');
          }
        });
      });
    });

  </script>

</body>

</html>