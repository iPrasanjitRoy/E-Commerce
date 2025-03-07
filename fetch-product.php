<?php
include 'front-config.php';

if (isset($_POST['action']) && $_POST['action'] == 'quickview' && isset($_POST['product_id'])) {
    $product_id = (int) $_POST['product_id'];


    $product_query = $conn->query("
        SELECT 
            p.product_id, 
            p.product_name, 
            p.description, 
            p.image_one,
            p.image_two,
            p.image_three
        FROM product p
        WHERE p.product_id = $product_id
    ");

    if ($product_query->num_rows > 0) {
        $product = $product_query->fetch_assoc();


        $price_query = $conn->query("
            SELECT 
                pc.combination_id,
                pc.size_id,
                s.size_name,
                pc.colour_id,
                c.colour_name,
                pc.price,
                pc.remaining_stock
            FROM price_combination pc
            INNER JOIN size s ON pc.size_id = s.sid
            INNER JOIN colour c ON pc.colour_id = c.cid
            WHERE pc.product_id = $product_id
        ");

        $price_combinations = [];
        while ($row = $price_query->fetch_assoc()) {

            $combination_id = $row['combination_id'];
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;



            if ($user_id > 0) {

                $cart_check = $conn->query("SELECT COUNT(*) as count FROM cart WHERE combination_id = $combination_id AND customer_id  = '$user_id'");
                $cart_result = $cart_check->fetch_assoc();
                $row['in_cart'] = $cart_result['count'] > 0 ? true : false;

            } else {

                /*  IF USER IS NOT LOGGED IN, CHECK THE SESSION CART */
                $session_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

                /*  CHECK IF THE COMBINATION EXISTS IN SESSION CART */
                $row['in_cart'] = array_key_exists($combination_id, $session_cart);
            }


            $price_combinations[] = $row;

        }


        $product['price_combinations'] = $price_combinations;

        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
}
?>