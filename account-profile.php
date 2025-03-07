<?php
include 'front-config.php';
include 'auth-check.php';

$customer_id = $_SESSION['user_id'];

$query = "SELECT first_name, last_name, date_of_birth, email, phone_number, gender FROM customers WHERE customer_id = $customer_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
  $user = mysqli_fetch_assoc($result);
} else {
  $user = [
    'first_name' => 'Unknown',
    'last_name' => '',
    'date_of_birth' => 'Not Provided',
    'email' => 'Not Available',
    'phone_number' => 'Not Provided',
    'gender' => 'Not Specified'
  ];
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
            <h4 class="mb-0 h4 fw-bold">Account - Profile</h4>
          </div>
        </div>

        <div class="btn btn-dark btn-ecomm d-xl-none position-fixed top-50 start-0 translate-middle-y"
          data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbarFilter"><span><i
              class="bi bi-person me-2"></i>Account</span></div>


        <div class="row">

          <?php
          include 'includes/common-account-dashboard.php';
          ?>

          <div class="col-12 col-xl-9">
            <div class="card rounded-0">
              <div class="card-body p-lg-5">

                <h5 class="mb-0 fw-bold">Profile Details</h5>
                <hr>

                <div class="table-responsive">
                  <table class="table table-striped">
                    <tbody>
                      <tr>
                        <td>Full Name</td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                      </tr>

                      <tr>
                        <td>Mobile Number</td>
                        <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                      </tr>

                      <tr>
                        <td>Email ID</td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                      </tr>

                      <tr>
                        <td>Gender</td>
                        <td><?php echo htmlspecialchars(!empty($user['gender']) ? $user['gender'] : 'Not Specified'); ?>
                        </td>
                      </tr>

                      <tr>
                        <td>DOB</td>
                        <td>
                          <?php echo htmlspecialchars(!empty($user['date_of_birth']) ? $user['date_of_birth'] : 'Not Provided'); ?>
                        </td>
                      </tr>

                      <!-- <tr>
                        <td>Location</td>
                        <td>United Kingdom</td>
                      </tr> -->

                    </tbody>
                  </table>
                </div>

                <div class="">
                  <a href="account-edit-profile.php" class="btn btn-outline-dark btn-ecomm px-5">
                    <i class="bi bi-pencil me-2"></i>Edit
                  </a>
                </div>

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


</body>

</html>