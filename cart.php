<?php
include 'front-config.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


/* FETCH CART ITEMS */
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_REQUEST['fetchCartItems'])) {
    $cartItems = [];

    if ($user_id) {
        $cart_query = $conn->query("
            SELECT c.combination_id, p.product_name AS name, pc.price, c.quantity, p.image_one AS image
            FROM cart c
            JOIN price_combination pc ON c.combination_id = pc.combination_id
            JOIN product p ON pc.product_id = p.product_id
            WHERE c.customer_id = '$user_id'
        ");
        while ($row = $cart_query->fetch_assoc()) {
            $cartItems[] = $row;
        }
    } else {
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {

            $combination_ids = implode(",", array_keys($_SESSION['cart']));

            if ($combination_ids) {
                $cart_query = $conn->query("
                    SELECT pc.combination_id, p.product_name AS name, pc.price, p.image_one AS image
                    FROM price_combination pc
                    JOIN product p ON pc.product_id = p.product_id
                    WHERE pc.combination_id IN ($combination_ids)
                ");

                while ($row = $cart_query->fetch_assoc()) {
                    $row['quantity'] = $_SESSION['cart'][$row['combination_id']]['quantity'];
                    $cartItems[] = $row;
                }
            }

        }
    }

    echo json_encode(['status' => 'success', 'cartItems' => $cartItems]);
    exit;
}


/* HANDLE UPDATING QUANTITY */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity_id']) && isset($_POST['change'])) {
    $combination_id = (int) $_POST['update_quantity_id'];
    $change = (int) $_POST['change'];

    if ($user_id) {

        $cart_check = $conn->query("SELECT quantity FROM cart WHERE customer_id = '$user_id' AND combination_id = '$combination_id'");

        if ($cart_check->num_rows > 0) {
            $cart_item = $cart_check->fetch_assoc();

            $stock_check = $conn->query("SELECT remaining_stock FROM price_combination WHERE combination_id = '$combination_id'");
            $stock_data = $stock_check->fetch_assoc();
            $remaining_stock = (int) $stock_data['remaining_stock'];


            /* MAX(1, RESULT) */
            /* ENSURES THAT THE MINIMUM QUANTITY IS ALWAYS 1. 
            IF THE CALCULATED QUANTITY IS LESS THAN 1, IT DEFAULTS TO 1. */
            $new_quantity = max(1, (int) $cart_item['quantity'] + $change);

            if ($new_quantity <= $remaining_stock) {
                $conn->query("UPDATE cart SET quantity = '$new_quantity' WHERE customer_id = '$user_id' AND combination_id = '$combination_id'");

                echo json_encode(['status' => 'success', 'message' => 'Quantity updated.']);

            } else {
                echo json_encode(['status' => 'error', 'message' => 'Not enough stock available.']);
            }

        } else {
            echo json_encode(['status' => 'error', 'message' => 'Item not found in cart.']);
        }

    } else {
        if (isset($_SESSION['cart'][$combination_id])) {

            $stock_check = $conn->query("SELECT remaining_stock FROM price_combination WHERE combination_id = '$combination_id'");
            $stock_data = $stock_check->fetch_assoc();
            $remaining_stock = (int) $stock_data['remaining_stock'];

            $new_quantity = max(1, $_SESSION['cart'][$combination_id]['quantity'] + $change);


            if ($new_quantity <= $remaining_stock) {
                $_SESSION['cart'][$combination_id]['quantity'] = $new_quantity;
                echo json_encode(['status' => 'success', 'message' => 'Quantity updated!']);

            } else {
                echo json_encode(['status' => 'error', 'message' => 'Not enough stock available.']);
            }

        } else {
            echo json_encode(['status' => 'error', 'message' => 'Item not found in cart.']);
        }

    }

    exit;
}



/* HANDLE ADDING TO CART */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart_id'])) {
    $combination_id = (int) $_POST['add_to_cart_id'];


    if (!$combination_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product selection.']);
        exit;
    }

    $stock_check = $conn->query("SELECT remaining_stock FROM price_combination WHERE combination_id = '$combination_id'");

    if ($stock_check->num_rows > 0) {
        $stock_result = $stock_check->fetch_assoc();
        $remaining_stock = (int) $stock_result['remaining_stock'];

        if ($remaining_stock < 1) {
            echo json_encode(['status' => 'error', 'message' => 'This product is out of stock!']);
            exit;
        }
    }

    if ($user_id) {

        /* CHECK IF THE PRODUCT (COMBINATION_ID) IS ALREADY IN THE CART FOR THIS USER */
        $check_cart = $conn->query("SELECT * FROM cart WHERE customer_id = '$user_id' AND combination_id = '$combination_id'");

        if ($check_cart->num_rows > 0) {

            echo json_encode(['status' => 'error', 'message' => 'This product is already in your cart!']);
        } else {

            $conn->query("INSERT INTO cart (customer_id, combination_id, quantity) VALUES ('$user_id', '$combination_id', 1)");

            echo json_encode(['status' => 'success', 'message' => 'Product added to cart successfully!']);
        }
    } else {
        /* IF NOT LOGGED IN, STORE IN SESSION */
        /* CHECKS IF THE SESSION CART EXISTS */

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        /* CHECK IF PRODUCT ALREADY EXISTS IN SESSION */
        if (isset($_SESSION['cart'][$combination_id])) {
            /* PREVENTS DUPLICATE ENTRIES */

            echo json_encode(['status' => 'error', 'message' => 'This product is already in your cart! (Session)']);

        } else {
            $_SESSION['cart'][$combination_id] = ['combination_id' => $combination_id, 'quantity' => 1];
        }
        echo json_encode(['status' => 'success', 'message' => 'Added to cart (Session)']);
    }
    exit();
}


/* TRANSFER SESSION CART TO DATABASE UPON LOGIN */
/* CHECK IF THE USER IS LOGGED IN & SESSION CART EXISTS */
if ($user_id && isset($_SESSION['cart'])) {
    $response = [];


    foreach ($_SESSION['cart'] as $item) {
        $combination_id = (int) $item['combination_id'];
        $quantity = (int) $item['quantity'];


        $check_cart = $conn->query("SELECT * FROM cart WHERE customer_id = '$user_id' AND combination_id = '$combination_id'");
        if ($check_cart->num_rows > 0) {

            $response[] = ['status' => 'error', 'message' => "Product ID $combination_id is already in your cart!"];
        } else {

            $conn->query("INSERT INTO cart (customer_id, combination_id, quantity) VALUES ('$user_id', '$combination_id', '$quantity')");

            $response[] = ['status' => 'success', 'message' => "Product ID $combination_id added successfully!"];

        }


    }

    /* CLEAR SESSION CART AFTER MOVING TO DATABASE */
    unset($_SESSION['cart']);
    echo json_encode($response);
    exit();

}





if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_from_cart_id'])) {
    $combination_id = (int) $_POST['remove_from_cart_id'];

    if (!$combination_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product selection.']);
        exit;
    }

    if ($user_id) {

        $delete_cart = $conn->query("DELETE FROM cart WHERE customer_id = '$user_id' AND combination_id = '$combination_id'");

        if ($conn->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Product removed from cart!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Product not found in cart!']);
        }
    } else {
        /* REMOVE FROM SESSION CART */
        if (isset($_SESSION['cart'][$combination_id])) {
            unset($_SESSION['cart'][$combination_id]);
            echo json_encode(['status' => 'success', 'message' => 'Product removed from cart (Session)!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Product not found in cart! (Session)']);
        }
    }
    exit();
}


?>