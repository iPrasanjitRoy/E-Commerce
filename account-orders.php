<?php

include 'front-config.php';
include 'auth-check.php';


$customer_id = $_SESSION['user_id'];

if (isset($_POST['action']) && $_POST['action'] === 'raise_cancel_request') {
    $order_id = intval($_POST['order_id']);

    $updateQuery = "UPDATE orders SET is_requested_cancel = 1  WHERE order_id = $order_id";
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($updateResult) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}


if (isset($_POST['action']) && $_POST['action'] === 'view_details') {

    $order_id = intval($_POST['order_id']);

    $query = "SELECT 
                o.order_date, 
                o.name, 
                o.phone_number, 
                o.email, 
                o.address, 
                o.city_village, 
                o.district, 
                o.state, 
                o.pincode, 
                o.product_sub_total, 
                o.gst_rate, 
                o.gst_amount, 
                o.delivery_charges, 
                o.net_total_amount, 
                o.payment_method, 
                o.paid_amount, 
                o.pg_order_id_1, 
                o.razorpay_payment_id
            FROM orders o
            WHERE o.order_id = $order_id";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $orderDetails = mysqli_fetch_assoc($result);
        echo json_encode($orderDetails);
    } else {
        echo json_encode([]);
    }
    exit;



}

if (isset($_POST['action']) && $_POST['action'] === 'filter_orders') {

    $customer_id = $_SESSION['user_id'];
    $status = $_POST['status'] ?? '';
    $days = intval($_POST['days'] ?? 0);
    $search = mysqli_real_escape_string($conn, $_POST['search'] ?? '');

    $whereClauses = ["o.customer_id = $customer_id"];

    if (!empty($status)) {
        $whereClauses[] = "o.status = '$status'";
    }

    if ($days > 0) {
        $whereClauses[] = "o.order_date >= DATE_SUB(NOW(), INTERVAL $days DAY)";
    }

    if (!empty($search)) {
        $whereClauses[] = "(od.product_name LIKE '%$search%')";
    }


    $whereSql = implode(' AND ', $whereClauses);

    $query = "SELECT 
                    o.order_id,
                    o.status,
                    o.pg_order_id,
                    od.product_name,
                    p.image_one AS product_image,
                    od.size_name,
                    od.colour_name,
                    od.quantity
                FROM 
                    orders o
                JOIN 
                    order_details od ON o.order_id = od.order_id
                JOIN 
                    product p ON od.product_id = p.product_id
                WHERE 
                    $whereSql
                ORDER BY 
                    o.order_id DESC";

    $result = mysqli_query($conn, $query);

    $orders = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[$row['order_id']][] = $row;
        }
    }

    echo json_encode($orders);
    exit;
}



$ordersQuery = "SELECT 
                    o.order_id,
                    o.status,
                    o.is_requested_cancel,
                    o.pg_order_id,
                    od.product_name,
                    p.image_one AS product_image,
                    od.size_name,
                    od.colour_name,
                    od.quantity
                FROM 
                    orders o
                JOIN 
                    order_details od ON o.order_id = od.order_id
                JOIN 
                    product p ON od.product_id = p.product_id
                WHERE 
                    o.customer_id = $customer_id
                ORDER BY 
                    o.order_id ASC";

$ordersResult = mysqli_query($conn, $ordersQuery);

function getStatusBadgeClass($status)
{
    switch ($status) {
        case 'processing':
            return 'warning';
        case 'shipped':
            return 'info';
        case 'delivered':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
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
                        <li class="breadcrumb-item"><a href="javascript:;">Shop</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Shop With Grid</li>
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
                        <h4 class="mb-0 h4 fw-bold">Account - Orders</h4>
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

                        <div class="card rounded-0 mb-3 bg-light">
                            <div class="card-body">
                                <div class="d-flex flex-column flex-xl-row gap-3 align-items-center">

                                    <div class="">
                                        <h5 class="mb-1 fw-bold">All Orders</h5>
                                    </div>


                                    <div class="order-search flex-grow-1">
                                        <form>
                                            <div class="position-relative">
                                                <input type="text" class="form-control ps-5 rounded-0"
                                                    placeholder="Search Product..." id="searchOrders">
                                                <span
                                                    class="position-absolute top-50 product-show translate-middle-y"><i
                                                        class="bi bi-search ms-3"></i></span>
                                            </div>
                                        </form>
                                    </div>


                                    <div class="filter">
                                        <button type="button" class="btn btn-dark rounded-0" data-bs-toggle="modal"
                                            data-bs-target="#FilterOrders"><i
                                                class="bi bi-filter me-2"></i>Filter</button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="card rounded-0 mb-3">
                            <div class="card-body body-data">

                                <?php if ($ordersResult && mysqli_num_rows($ordersResult) > 0):

                                    $orders = [];
                                    while ($row = mysqli_fetch_assoc($ordersResult)) {
                                        $orders[$row['order_id']][] = $row;
                                    }
                                    ?>

                                    <?php foreach ($orders as $order_id => $products): ?>
                                        <div class="product-order">


                                            <h5 class="fw-bold mb-3">Order ID: <?php echo $products[0]['pg_order_id']; ?></h5>

                                            <p class="text-muted mb-2">
                                                <strong>Status:</strong>
                                                <span
                                                    class="badge bg-<?php echo getStatusBadgeClass($products[0]['status']); ?>">
                                                    <?php echo ucfirst($products[0]['status']); ?>
                                                </span>
                                            </p>


                                            <div class="d-flex flex-column flex-xl-row gap-3 mb-4">


                                                <div class="d-grid align-self-start align-self-xl-center">
                                                    <button type="button"
                                                        class="btn btn-outline-dark btn-ecomm view-details-btn"
                                                        data-id="<?php echo $products[0]['order_id']; ?>">View
                                                        Details</button>
                                                </div>

                                                <div class="d-none d-xl-block vr"></div>


                                                <!-- Add the Raise Cancel Request button here -->
                                                <?php if ($products[0]['status'] === 'processing' && $products[0]['is_requested_cancel'] == 0): ?>
                                                    <div class="d-grid align-self-start align-self-xl-center">
                                                        <button type="button" class="btn btn-danger raise-cancel-request"
                                                            data-id="<?php echo $products[0]['order_id']; ?>"
                                                            title="Click to raise a cancel request for this order.">
                                                            <i class="bi bi-x-circle"></i> Raise Cancel Request
                                                        </button>
                                                    </div>

                                                <?php elseif ($products[0]['is_requested_cancel'] == 1 && $products[0]['status'] !== 'cancelled'): ?>
                                                    <div class="d-grid align-self-start align-self-xl-center">
                                                        <button type="button" class="btn btn-secondary" disabled>
                                                            Cancel Request Raised
                                                        </button>
                                                    </div>

                                                <?php elseif ($products[0]['status'] == 'cancelled'): ?>
                                                    <!-- No button displayed when status is 'cancelled' -->


                                                <?php endif; ?>


                                            </div>

                                            <?php foreach ($products as $product): ?>
                                                <div class="d-flex flex-column flex-xl-row gap-3 mb-4">

                                                    <div class="product-img">
                                                        <img src="backend/<?php echo $product['product_image']; ?>" width="120"
                                                            alt="<?php echo $product['product_name']; ?>">
                                                    </div>

                                                    <div class="product-info flex-grow-1">
                                                        <h5 class="fw-bold mb-1"><?php echo $product['product_name']; ?></h5>

                                                        <div class="mt-3 hstack gap-2">
                                                            <button type="button" class="btn btn-sm border rounded-0">
                                                                Size: <?php echo $product['size_name']; ?>
                                                            </button>

                                                            <button type="button" class="btn btn-sm border rounded-0">
                                                                Color: <?php echo $product['colour_name']; ?>
                                                            </button>

                                                            <button type="button" class="btn btn-sm border rounded-0">
                                                                Qty: <?php echo $product['quantity']; ?>
                                                            </button>

                                                        </div>

                                                    </div>

                                                </div>

                                                <hr>


                                            <?php endforeach; ?>
                                        </div>

                                    <?php endforeach; ?>
                                    <div class="order-details-container">

                                    </div>


                                <?php else: ?>
                                    <p>No orders found for this customer.</p>
                                <?php endif; ?>

                            </div>
                        </div>


                    </div>
                </div><!--end row-->
            </div>
        </section>
        <!--start product details-->



        <!-- Filter Modal -->
        <div class="modal" id="FilterOrders" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content rounded-0">

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Filter Orders</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <h6 class="mb-3 fw-bold">Status</h6>
                        <div class="status-radio d-flex flex-column gap-2">

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="orderStatus" id="statusAll" value=""
                                    checked>
                                <label class="form-check-label" for="statusAll">All</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="orderStatus" id="statusShipped"
                                    value="shipped">
                                <label class="form-check-label" for="statusShipped">Shipped</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="orderStatus" id="statusDelivered"
                                    value="delivered">
                                <label class="form-check-label" for="statusDelivered">Delivered</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="orderStatus" id="statusCancelled"
                                    value="cancelled">
                                <label class="form-check-label" for="statusCancelled">Cancelled</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="orderStatus" id="statusProcessing"
                                    value="processing">
                                <label class="form-check-label" for="statusProcessing">Processing</label>
                            </div>

                        </div>
                        <hr>

                        <h6 class="mb-3 fw-bold">Time</h6>
                        <div class="status-radio d-flex flex-column gap-2">

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="orderTime" id="timeAny" value=""
                                    checked>
                                <label class="form-check-label" for="timeAny">Anytime</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="orderTime" id="time30Days"
                                    value="30">
                                <label class="form-check-label" for="time30Days">Last 30 Days</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="orderTime" id="time6Months"
                                    value="180">
                                <label class="form-check-label" for="time6Months">Last 6 Months</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="orderTime" id="time1Year"
                                    value="365">
                                <label class="form-check-label" for="time1Year">Last Year</label>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <div class="d-flex align-items-center gap-3 w-100">
                            <button type="button" class="btn btn-outline-dark w-50" id="clearFilters">Clear
                                Filters</button>
                            <button type="button" class="btn btn-dark w-50" id="applyFilters"
                                data-bs-dismiss="modal">Apply</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- end Filters Modal -->


    </div>
    <!--end page content-->


    <?php
    include 'includes/footers.php';
    ?>


    <script>

        $(document).ready(function () {

            function fetchOrders(status = '', days = '', search = '') {

                $.ajax({
                    url: 'account-orders.php',
                    type: 'POST',
                    data: {
                        action: 'filter_orders',
                        status: status,
                        days: days,
                        search: search
                    },
                    dataType: 'json',
                    success: function (orders) {
                        let ordersHtml = '';

                        if ($.isEmptyObject(orders)) {
                            ordersHtml = '<p>No orders found for this filter.</p>';
                        } else {
                            $.each(orders, function (order_id, products) {
                                ordersHtml += `<div class="product-order"> 
                                               <h5 class="fw-bold mb-3">Order ID: ${products[0].pg_order_id}</h5>`;

                                ordersHtml += `<p class="text-muted mb-2">
                                        <strong>Status:</strong>
                                        <span class="badge bg-${getStatusBadgeClass(products[0].status)}">
                                            ${products[0].status.charAt(0).toUpperCase() + products[0].status.slice(1)}
                                        </span>
                                    </p>`;

                                ordersHtml += `
                                            <div class="d-flex flex-column flex-xl-row gap-3 mb-4">
                                                <div class="d-grid align-self-start align-self-xl-center mb-3">
                                                    <button type="button" class="btn btn-outline-dark btn-ecomm view-details-btn" 
                                                        data-id="${products[0].order_id}">View Details</button>
                                                </div>
                                            </div>
                                        `;


                                $.each(products, function (index, product) {
                                    ordersHtml += `
                                <div class="d-flex flex-column flex-xl-row gap-3 mb-4">
                                    <div class="product-img">
                                        <img src="backend/${product.product_image}" width="120" alt="${product.product_name}">
                                    </div>
                                    <div class="product-info flex-grow-1">
                                        <h5 class="fw-bold mb-1">${product.product_name}</h5>
                                        <div class="mt-3 hstack gap-2">
                                            <button class="btn btn-sm border rounded-0">Size: ${product.size_name}</button>
                                            <button class="btn btn-sm border rounded-0">Color: ${product.colour_name}</button>
                                            <button class="btn btn-sm border rounded-0">Qty: ${product.quantity}</button>
                                        </div>
                                    </div>
                                </div><hr>`;
                                });

                                ordersHtml += `</div>`;
                            });
                        }

                        $('.body-data').html(ordersHtml);
                    }
                });


            }

            $('#searchOrders').on('keyup', function () {
                const search = $(this).val();
                const status = $('input[name="orderStatus"]:checked').val();
                const days = $('input[name="orderTime"]:checked').val();
                fetchOrders(status, days, search);
            });


            $('#applyFilters').on('click', function () {
                const status = $('input[name="orderStatus"]:checked').val();
                const days = $('input[name="orderTime"]:checked').val();
                const search = $('#searchOrders').val();
                fetchOrders(status, days, search);
            });


            $('#clearFilters').on('click', function () {
                $('input[name="orderStatus"][value=""]').prop('checked', true);
                $('input[name="orderTime"][value=""]').prop('checked', true);
                location.reload();

            });
        });

        function getStatusBadgeClass(status) {
            switch (status) {
                case 'processing':
                    return 'warning';
                case 'shipped':
                    return 'info';
                case 'delivered':
                    return 'success';
                case 'cancelled':
                    return 'danger';
                default:
                    return 'secondary';
            }
        }


        $(document).on('click', '.view-details-btn', function () {
            const orderId = $(this).data('id');
            const $button = $(this);



            if ($button.hasClass('remove-details')) {
                $button.removeClass('remove-details');
                $('.product-order-det').remove();
                return; /* STOP FURTHER EXECUTION */
            }

            /* REMOVE CLASS FROM ALL BUTTONS */
            $('.view-details-btn').removeClass('remove-details');
            $button.addClass('remove-details');

            $.ajax({
                url: 'account-orders.php',
                type: 'POST',
                data: { order_id: orderId, action: 'view_details', },
                dataType: 'json',
                success: function (orderDetails) {
                    if (orderDetails) {
                        const detailsHtml = `
                                                <div class="card rounded-0 mb-3 product-order-det">
                                                    <div class="card-body details-data">
                                                        <p><strong>Order Date:</strong> ${orderDetails.order_date}</p>
                                                        <p><strong>Name:</strong> ${orderDetails.name}</p>
                                                        <p><strong>Phone:</strong> ${orderDetails.phone_number}</p>
                                                        <p><strong>Email:</strong> ${orderDetails.email}</p>
                                                        <p><strong>Address:</strong> ${orderDetails.address}, ${orderDetails.city_village}, ${orderDetails.district}, ${orderDetails.state} - ${orderDetails.pincode}</p>
                                                        <p><strong>Product Sub Total:</strong> ₹${orderDetails.product_sub_total}</p>
                                                        <p><strong>GST Rate:</strong> ${orderDetails.gst_rate}%</p>
                                                        <p><strong>GST Amount:</strong> ₹${orderDetails.gst_amount}</p>
                                                        <p><strong>Delivery Charges:</strong> ₹${orderDetails.delivery_charges}</p>
                                                        <p><strong>Net Total Amount:</strong> ₹${orderDetails.net_total_amount}</p>
                                                        <p><strong>Payment Method:</strong> ${orderDetails.payment_method}</p>
                                                        <p><strong>Paid Amount:</strong> ₹${orderDetails.paid_amount}</p>
                                                        <p><strong>PG Order ID:</strong> ${orderDetails.pg_order_id_1}</p>
                                                        <p><strong>Razorpay Payment ID:</strong> ${orderDetails.razorpay_payment_id}</p>
                                                    </div>
                                                </div>
                                    `;

                        /* $('.order-details-container').append(detailsHtml); */
                        $('.product-order-det').remove();
                        $button.parents('.product-order').append(detailsHtml);


                    } else {
                        alert('Failed to load order details.');
                    }
                }
            });
        });


        $(document).on('click', '.raise-cancel-request', function () {
            const orderId = $(this).data('id');
            const $button = $(this);


            if (confirm('Are you sure you want to raise a cancel request for this order?')) {
                $.ajax({
                    url: 'account-orders.php',
                    type: 'POST',
                    data: {
                        action: 'raise_cancel_request',
                        order_id: orderId
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert('Cancel request raised successfully.');
                            // $button.text('Cancel Request Raised').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
                            location.reload();
                        } else {
                            alert('Failed to raise cancel request. Please try again.');
                        }
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        });


    </script>

</body>

</html>