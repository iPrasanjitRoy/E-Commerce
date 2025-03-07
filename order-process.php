<?php

include 'front-config.php';

$customer_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;

$applied_coupon = $_SESSION['applied_coupon']['coupon_code'] ?? null;

$address_id = $_POST['flexradioaddress'] ?? null;
$payment_method = $_POST['payment_method'] ?? null;

if (!$customer_id || !$address_id || !$payment_method) {
    die('Invalid request');
}

/*  FETCHING ADDRESS DETAILS */
$address_query = "SELECT * FROM `address` WHERE id = $address_id AND customer_id = $customer_id";
$address_result = mysqli_query($conn, $address_query);
$address = mysqli_fetch_assoc($address_result);
if (!$address) {
    die('Invalid address');
}


/* CALCULATING CART TOTALS */
$cart_query = "SELECT c.*, p.product_id, p.product_name, pc.price, pc.combination_id, s.sid, s.size_name, col.cid, col.colour_name 
    FROM cart c 
    JOIN price_combination pc ON c.combination_id = pc.combination_id 
    JOIN product p ON pc.product_id = p.product_id 
    JOIN `size` s ON pc.size_id = s.sid 
    JOIN colour col ON pc.colour_id = col.cid 
    WHERE c.customer_id = $customer_id";

$cart_result = mysqli_query($conn, $cart_query);


$product_sub_total = 0;

$order_items = [];

while ($cart_item = mysqli_fetch_assoc($cart_result)) {

    $amount = $cart_item['price'] * $cart_item['quantity'];

    $product_sub_total += $amount;

    $order_items[] = array_merge($cart_item, ['amount' => $amount]);
}


if (empty($order_items)) {
    die(json_encode(['status' => 'error', 'message' => 'Your cart is empty']));
}




/*  APPLYING DISCOUNT */
$discount_amount = 0;

if ($applied_coupon) {
    $discount_query = "SELECT * FROM discount WHERE coupon_name = '$applied_coupon' AND validity_date >= CURDATE() LIMIT 1";
    $discount_result = mysqli_query($conn, $discount_query);
    $discount = mysqli_fetch_assoc($discount_result);

    if ($discount) {

        if ($discount['discount_type'] == 'flat') {
            $discount_amount = $discount['flat'];
        } elseif ($discount['discount_type'] == 'percentage') {
            $discount_amount = ($product_sub_total * $discount['percentage']) / 100;
        }

    }
}

$discountedAmount = max($product_sub_total - $discount_amount, 0);


/* GST AND DELIVERY CHARGES (ASSUMING FIXED VALUES) */
$gst_rate = ($discountedAmount < 1000) ? 0.05 : 0.10;

$gst_amount = $discountedAmount * $gst_rate;


$delivery_charges = ($product_sub_total <= 0) ? 0 : 30;


/* NET TOTAL CALCULATION */
$net_total_amount = max(0, $discountedAmount + $gst_amount + $delivery_charges);


/*  INSERTING INTO ORDERS TABLE */
$order_query = "INSERT INTO orders (`name`, customer_id, phone_number, email, `address`, city_village, district, `state`, pincode,
    product_sub_total, discount_amount, gst_rate, gst_amount, delivery_charges, net_total_amount,
    payment_method, paid_amount, due_amount, is_paid, order_date)
    VALUES ('{$address['name']}', '$customer_id', '{$address['mobile_no']}', '$user_email', '{$address['address']}', '{$address['city_village']}',
    '{$address['district']}', '{$address['state']}', '{$address['pin_code']}',
    '$product_sub_total', '$discount_amount', '$gst_rate', '$gst_amount', '$delivery_charges', '$net_total_amount',
    '$payment_method', '0.00', '$net_total_amount', '0', CURDATE())";

if (mysqli_query($conn, $order_query)) {
    $order_id = mysqli_insert_id($conn);

    /*  INSERTING INTO ORDER_DETAILS TABLE */
    foreach ($order_items as $item) {
        $product_name = mysqli_real_escape_string($conn, $item['product_name']);


        $details_query = "INSERT INTO order_details (order_id, combination_id, product_id, product_name, size_id, size_name, colour_id, colour_name, quantity, rate, amount)
        VALUES ('$order_id', '{$item['combination_id']}', '{$item['product_id']}', '$product_name', '{$item['sid']}', '{$item['size_name']}', '{$item['cid']}', '{$item['colour_name']}', '{$item['quantity']}', '{$item['price']}', '{$item['amount']}')";
        mysqli_query($conn, $details_query);
    }

    $pg_razor_pay = strtolower('pg-' . substr(str_replace(' ', '', $address['name']), 0, 5) . '-' . uniqid());
    mysqli_query($conn, "UPDATE orders SET pg_order_id = '$pg_razor_pay' WHERE order_id = '$order_id'");

    $_SESSION['pg-razor-pay'] = [
        'pg_txn_id' => $pg_razor_pay,
        'name' => $address['name'],
        'phone' => $address['mobile_no'],
        'email' => $user_email,
        'net_total_amount' => $net_total_amount
    ];


    if ($payment_method === 'online') {
        unset($_SESSION['applied_coupon']);
        echo json_encode(['status' => 'success', 'message' => 'Redirecting to payment gateway', 'redirect' => 'payment-gateway-url']);
        exit();
    } else if ($payment_method === 'cash') {

        foreach ($order_items as $item) {
            $quantity = (int) $item['quantity'];
            $combination_id = (int) $item['combination_id'];

            $update_stock = "UPDATE price_combination 
                             SET 
                                used_stock = used_stock + $quantity,
                                remaining_stock = remaining_stock - $quantity
                             WHERE combination_id = '$combination_id'";

            mysqli_query($conn, $update_stock);
        }

        $delete_cart = "DELETE FROM cart WHERE customer_id = '$customer_id'";
        mysqli_query($conn, $delete_cart);


        unset($_SESSION['pg-razor-pay']);
        unset($_SESSION['applied_coupon']);


        echo json_encode([
            'status' => 'cash-success',
            'message' => 'Order placed successfully',
            'pg_order_id' => $pg_razor_pay
        ]);



        // header("Location: thank-you.php?pg_order_id=" . $pg_razor_pay);
        exit();


    }


} else {
    echo json_encode(['status' => 'error', 'message' => 'Order failed']);
    exit();
}

?>