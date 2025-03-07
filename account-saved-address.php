<?php

include 'front-config.php';
include 'auth-check.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateeditaddress') {

  $address_id = $_POST['editaddressid'];
  $name = $_POST['editname'];
  $mobile_no = $_POST['editmobileno'];
  $pin_code = $_POST['editpincode'];
  $address = $_POST['editaddress'];
  $city_village = $_POST['editcity'];
  $district = $_POST['editdistrict'];
  $state = $_POST['editstate'];

  $query = "UPDATE address SET 
              `name` = '$name', 
              mobile_no = '$mobile_no', 
              pin_code = '$pin_code', 
              `address` = '$address', 
              city_village = '$city_village', 
              district = '$district', 
              `state` = '$state' 
            WHERE id = '$address_id'";

  if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Address updated successfully!"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Database error: " . mysqli_error($conn)]);
  }

  mysqli_close($conn);
  exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'removed') {

  if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
  }

  $customer_id = $_SESSION['user_id'];

  $address_id = intval($_POST['id']);

  $query = "DELETE FROM address WHERE id = '$address_id' AND customer_id = '$customer_id'";

  if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Address removed successfully!"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Failed to remove address."]);
  }

  mysqli_close($conn);
  exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetchaddress') {
  $address_id = $_POST['address_id'];

  $query = "SELECT * FROM address WHERE id = '$address_id'";
  $result = mysqli_query($conn, $query);

  if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode(["status" => "success", "data" => $row]);
  } else {
    echo json_encode(["status" => "error", "message" => "Address not found."]);
  }

  mysqli_close($conn);
  exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addData') {

  if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
  }


  $customer_id = $_SESSION['user_id'];
  $name = $_POST['name'];
  $mobile_no = $_POST['mobile_no'];
  $pin_code = $_POST['pin_code'];
  $address = $_POST['address'];
  $city_village = $_POST['city_village'];
  $district = $_POST['district'];
  $state = $_POST['state'];

  $query = "INSERT INTO address (customer_id, `name`, mobile_no, pin_code, `address`, city_village, district, `state`) 
              VALUES ('$customer_id', '$name', '$mobile_no', '$pin_code', '$address', '$city_village', '$district', '$state')";

  if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Address added successfully!"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Database error: " . mysqli_error($conn)]);
  }

  mysqli_close($conn);
  exit;

}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'setDefault') {

  if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
  }

  $customer_id = $_SESSION['user_id'];
  $address_id = $_POST['address_id'];

  /* RESET ALL ADDRESSES TO NON-DEFAULT */
  mysqli_query($conn, "UPDATE address SET is_default = 0 WHERE customer_id = '$customer_id'");

  /*  SET THE SELECTED ADDRESS AS DEFAULT */
  $updateQuery = "UPDATE address SET is_default = 1 WHERE id = '$address_id' AND customer_id = '$customer_id'";

  if (mysqli_query($conn, $updateQuery)) {
    echo json_encode(["status" => "success", "message" => "Default address updated successfully!"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Database error: " . mysqli_error($conn)]);
  }

  mysqli_close($conn);
  exit;
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
            <h4 class="mb-0 h4 fw-bold">Account - Addresses</h4>
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
            <?php
            include_once 'common-account-saved-address.php';
            ?>
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




</body>

</html>