<?php
include 'front-config.php';

/* RAZORPAYAPI CLASS FOR CREATING RAZORPAY ORDERS */
class RazorpayAPI
{
    private $key_id;
    private $key_secret;
    private $url = "https://api.razorpay.com/v1/orders";

    /* CONSTRUCTOR TO INITIALIZE RAZORPAY API CREDENTIALS */
    public function __construct($key_id, $key_secret)
    {
        $this->key_id = $key_id;
        $this->key_secret = $key_secret;
    }

    /*  METHOD TO CREATE A NEW RAZORPAY ORDER */
    public function createOrder($data)
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key_id . ":" . $this->key_secret);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode($response, true);
    }
}



/* START PROCESSING PAYMENT IF SESSION DATA IS SET */
if (isset($_SESSION['pg-razor-pay']['net_total_amount']) && intval($_SESSION['pg-razor-pay']['net_total_amount']) > 0) {
    $data_name = $_SESSION['pg-razor-pay']['name'];
    $data_phone = $_SESSION['pg-razor-pay']['phone'];
    $data_mail = $_SESSION['pg-razor-pay']['email'];

    /* CONVERT TO PAISE */
    // $data_amt = $_SESSION['pg-razor-pay']['net_total_amount'] * 100;
    $data_amt = intval($_SESSION['pg-razor-pay']['net_total_amount'] * 100);
    $data_tx = $_SESSION['pg-razor-pay']['pg_txn_id'];

    /*  RAZORPAY API CREDENTIALS */
    $razor_api_key = 'rzp_test_ad1w5ftNElZ9vh';
    $razor_secret_key = 'ziTHtjLsiVIO5NQup5C2xPPO';

    $razorpay = new RazorpayAPI($razor_api_key, $razor_secret_key);

    /* CREATE RAZORPAY ORDER */
    $order = $razorpay->createOrder([
        'amount' => $data_amt,
        'currency' => 'INR',
        'receipt' => $data_tx,
        'payment_capture' => 1
    ]);

    /*
    echo '<pre>';
    print_r($order);
    echo '</pre>';
    */


    if (!empty($order['id'])) {
        $order_id = $order['id'];

        $update_pg_order = "UPDATE orders SET pg_order_id_1 = '$order_id' WHERE pg_order_id = '$data_tx'";

        if (mysqli_query($conn, $update_pg_order)) {
            echo "Order ID updated successfully!";
        } else {
            echo "Error updating order ID: " . mysqli_error($conn);
        }

    } else {
        die('Order creation failed. Please try again.');
    }

    /* PAYMENT PAGE WITH RAZORPAY CHECKOUT */
    ?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>Razorpay Payment</title>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    </head>

    <body>
        <script>
            startPayment();
            function startPayment() {
                var payment_data = {
                    "key": "<?= $razor_api_key ?>",
                    "amount": "<?= $data_amt ?>",
                    "currency": "INR",
                    "name": "EBazar",
                    "image": "http://localhost/myshop/backend/uploads/67a2400b2ddc8_EBazar.png",
                    "description": "Payment",
                    "order_id": "<?= $order_id ?>",
                    theme: { "color": "#738276" },
                    callback_url: "http://localhost/myshop/razorpay-payment-gateway-1",
                    modal: {
                        "ondismiss": function () {
                            location.replace('/');
                        }
                    },
                    "prefill": {
                        "name": "<?= $data_name ?>",
                        "email": "<?= $data_mail ?>",
                        "contact": "<?= $data_phone ?>"
                    }
                };
                var rzp = new Razorpay(payment_data);
                rzp.open();
            }
        </script>
    </body>

    </html>

    <?php
} else {
    echo 'Invalid payment session or amount.';
}

?>


<?php /* RESPONSE PAGE  */ ?>

<?php

if (isset($_POST['razorpay_payment_id']) && !empty($_POST['razorpay_payment_id']) && isset($_SESSION['pg-razor-pay'])) {

    $razorpay_payment_id = $_POST['razorpay_payment_id'];

    $pg_order_id = $_SESSION['pg-razor-pay']['pg_txn_id'];

    $net_total_amount = $_SESSION['pg-razor-pay']['net_total_amount'];

    $customer_id = $_SESSION['user_id'] ?? null;

    $pg_response = json_encode($_POST);

    $update_order = "UPDATE orders SET 
                        paid_amount = '$net_total_amount',
                        due_amount = 0,
                        is_paid = 1,
                        razorpay_payment_id = '$razorpay_payment_id',
                        pg_response = '$pg_response'
                     WHERE pg_order_id = '$pg_order_id'";

    if (mysqli_query($conn, $update_order)) {

        $order_id_result = mysqli_query($conn, "SELECT order_id FROM orders WHERE pg_order_id = '$pg_order_id'");
        $order_data = mysqli_fetch_assoc($order_id_result);
        $order_id = $order_data['order_id'];



        $order_items = mysqli_query($conn, "SELECT combination_id, quantity, product_id FROM order_details WHERE order_id = '$order_id'");

        while ($item = mysqli_fetch_assoc($order_items)) {
            $combination_id = $item['combination_id'];
            $quantity = $item['quantity'];
            $product_id = $item['product_id'];


            $update_stock = "UPDATE price_combination 
                             SET 
                                used_stock = used_stock + $quantity,
                                remaining_stock = remaining_stock - $quantity
                             WHERE combination_id = '$combination_id'";

            mysqli_query($conn, $update_stock);
        }




        if (!empty($customer_id)) {
            $delete_cart = "DELETE FROM cart WHERE customer_id = '$customer_id'";
            mysqli_query($conn, $delete_cart);
        }




        unset($_SESSION['pg-razor-pay']);
        header("Location: thank-you.php?pg_order_id=" . $pg_order_id);
        exit();


    } else {
        echo "Error updating order: " . mysqli_error($conn);
    }

} else {
    echo 'Invalid payment response or session expired.';
}


?>