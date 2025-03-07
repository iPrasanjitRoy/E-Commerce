<?php
include 'front-config.php';

?>

<!doctype html>
<html lang="en">

<head>
    <?php
    include 'includes/style.php';
    ?>
    <title>Thank You for Your Purchase | Shopingo</title>
</head>

<body>
    <?php
    include 'includes/headers.php';

    /* CHECK IF AN ORDER ID IS PRESENT */
    if (isset($_GET['pg_order_id'])) {
        $pg_order_id = $_GET['pg_order_id'];

        $customer_id = $_SESSION['user_id'] ?? null;

        /* FETCH ORDER DETAILS FROM THE DATABASE */
        $order_query = "SELECT o.order_id, o.name, o.pg_order_id, o.paid_amount, o.order_date
                        FROM orders o 
                        WHERE o.pg_order_id = '$pg_order_id' LIMIT 1";

        $order_result = mysqli_query($conn, $order_query);

        if ($order_result && mysqli_num_rows($order_result) > 0) {
            $order = mysqli_fetch_assoc($order_result);
            ?>

            <section class="thank-you-section py-5" style="margin-top: 100px;">
                <div class="container text-center">
                    <h1>Thank You, <?= htmlspecialchars($order['name']) ?>!</h1>
                    <p>Your order has been successfully placed.</p>

                    <div class="order-summary">
                        <h2>Order Summary</h2>
                        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['pg_order_id']) ?></p>
                        <p><strong>Amount Paid:</strong> ₹<?= number_format($order['paid_amount'], 2) ?></p>
                        <p><strong>Order Date:</strong> <?= date('F j, Y', strtotime($order['order_date'])) ?></p>
                    </div>

                    <a href="http://localhost/myshop/" class="btn btn-primary">Continue Shopping</a>
                </div>
            </section>

            <?php
        } else {
            echo '<div class="container text-center"><p>Invalid or missing order details.</p></div>';
        }
    } else {
        echo '<div class="container text-center"><p>No order ID found.</p></div>';
    }

    include 'includes/footers.php';
    ?>
</body>

</html>