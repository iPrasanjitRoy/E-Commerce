<?php
include 'front-config.php';

$cartItems = [];
$totalAmount = 0;


if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];

  $cart_query = $conn->query("
        SELECT c.cartid, c.combination_id, c.quantity, pc.price, p.product_name, p.image_one, s.size_name, co.colour_name
        FROM cart c
        JOIN price_combination pc ON c.combination_id = pc.combination_id
        JOIN product p ON pc.product_id = p.product_id
        JOIN size s ON pc.size_id = s.sid
        JOIN colour co ON pc.colour_id = co.cid
        WHERE c.customer_id = $user_id
    ");


  while ($row = $cart_query->fetch_assoc()) {
    $cartItems[] = $row;
    $totalAmount += $row['price'] * $row['quantity']; /* CALCULATE TOTAL AMOUNT */
  }
}


$totalDiscount = isset($_SESSION['applied_coupon']['total_discount']) ? $_SESSION['applied_coupon']['total_discount'] : 0;




$discountedAmount = $totalAmount - $totalDiscount;

$gstRate = ($discountedAmount < 1000) ? 0.05 : 0.10;
$gstAmount = $discountedAmount * $gstRate;



$deliveryCharge = ($totalAmount <= 0) ? 0 : 30;


$netTotalAmount = $discountedAmount + $gstAmount + $deliveryCharge;




?>




<?php


if (isset($_POST['apply_coupon']) && !empty($_POST['coupon_code']) && !empty($_POST['combination_ids'])) {

  $couponCode = mysqli_real_escape_string($conn, $_POST['coupon_code']);

  $combinationIds = $_POST['combination_ids'];
  $currentDate = date('Y-m-d');

  $couponQuery = $conn->query("
      SELECT * FROM discount 
      WHERE coupon_name = '$couponCode' 
      AND (validity_date IS NULL OR validity_date >= '$currentDate')
  ");

  if ($couponQuery->num_rows > 0) {
    $couponData = $couponQuery->fetch_assoc();

    $discountType = $couponData['discount_type'];

    $productSpecific = $couponData['product_id'];

    $flatDiscount = $couponData['flat'];
    $percentageDiscount = $couponData['percentage'];

    $totalDiscount = 0;



    $productIds = [];

    $combinationIdsEscaped = implode(',', array_map('intval', $combinationIds));

    $productQuery = $conn->query("
          SELECT DISTINCT product_id FROM price_combination 
          WHERE combination_id IN ($combinationIdsEscaped)
      ");

    while ($row = $productQuery->fetch_assoc()) {
      $productIds[] = $row['product_id'];
    }

    /* DETERMINE IF THE COUPON IS APPLICABLE */
    $isApplicable = false;

    if (is_null($productSpecific)) {
      /* GLOBAL COUPON, APPLY TO ALL */
      $isApplicable = true;
    } elseif (in_array($productSpecific, $productIds)) {
      /* PRODUCT-SPECIFIC COUPON, CHECK IF THE PRODUCT EXISTS IN THE CART */
      $isApplicable = true;
    }

    if ($isApplicable) {
      if ($discountType === 'flat') {
        $totalDiscount = min($flatDiscount, $totalAmount);
      } elseif ($discountType === 'percentage') {
        $totalDiscount = $totalAmount * ($percentageDiscount / 100);
      }

      /* STORE COUPON DATA IN SESSION */
      $_SESSION['applied_coupon'] = [
        'coupon_code' => $couponCode,
        'discount_type' => $discountType,
        'total_discount' => $totalDiscount
      ];

      echo json_encode([
        'status' => 'success',
        'discount' => $totalDiscount
      ]);
    } else {
      echo json_encode([
        'status' => 'error',
        'message' => 'Coupon not applicable to the items in your cart.'
      ]);
    }
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'Invalid or expired coupon.'
    ]);
  }
  exit;
}

?>

<?php

if (isset($_POST['remove_coupon'])) {
  unset($_SESSION['applied_coupon']);
  echo json_encode(['status' => 'success']);
  exit;
}


?>