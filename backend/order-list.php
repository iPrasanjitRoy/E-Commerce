<?php
include 'include/db-config.php';

$statusArray = ['processing', 'shipped', 'delivered', 'cancelled'];

if (isset($_POST['action']) && $_POST['action'] === 'fetch_print_data' && isset($_POST['order_id'])) {
    $orderId = intval($_POST['order_id']);

    $orderQuery = "SELECT o.*, 
    GROUP_CONCAT(CONCAT(od.product_name, ' (', od.size_name, ', ', od.colour_name, ') - Quantity: ', od.quantity) SEPARATOR '<br>') AS product_details
                FROM orders o
                JOIN order_details od ON o.order_id = od.order_id
                WHERE o.order_id = $orderId
                GROUP BY o.order_id";

    $orderResult = $conn->query($orderQuery);

    if ($orderResult && $orderResult->num_rows > 0) {
        $orderData = $orderResult->fetch_assoc();

        $printHtml = '<h1>Order Details</h1>';
        $printHtml .= '<p><strong>Order ID:</strong> ' . $orderData['pg_order_id'] . '</p>';
        $printHtml .= '<p><strong>Name:</strong> ' . $orderData['name'] . '</p>';
        $printHtml .= '<p><strong>Email:</strong> ' . $orderData['email'] . '</p>';
        $printHtml .= '<p><strong>Phone:</strong> ' . $orderData['phone_number'] . '</p>';
        $printHtml .= '<p><strong>Address:</strong> ' . $orderData['address'] . ', ' . $orderData['city_village'] . ', ' . $orderData['district'] . ', ' . $orderData['state'] . ', ' . $orderData['pincode'] . '</p>';

        $printHtml .= '<h2>Products</h2>';
        $printHtml .= '<div>' . $orderData['product_details'] . '</div>';

        $printHtml .= '<h2>Status:</h2>';
        $printHtml .= '<p>' . ucfirst($orderData['status']) . '</p>';

        $printHtml .= '<h2>Payment Info:</h2>';
        $printHtml .= '<p><strong>Product Subtotal:</strong> ' . ucfirst($orderData['product_sub_total']) . '</p>';
        $printHtml .= '<p><strong>Discount Amount:</strong> ' . ucfirst($orderData['discount_amount']) . '</p>';

        $printHtml .= '<p><strong>GST Rate:</strong> ' . ucfirst($orderData['gst_rate']) . '</p>';
        $printHtml .= '<p><strong>GST Amount:</strong> ' . ucfirst($orderData['gst_amount']) . '</p>';
        $printHtml .= '<p><strong>Delivery Charges:</strong> ' . ucfirst($orderData['delivery_charges']) . '</p>';
        $printHtml .= '<p><strong>Net Total Amount:</strong> ' . ucfirst($orderData['net_total_amount']) . '</p>';

        $printHtml .= '<p><strong>Payment Method:</strong> ' . ucfirst($orderData['payment_method']) . '</p>';

        $printHtml .= '<p><strong>Paid Amount:</strong> ' . ucfirst($orderData['paid_amount']) . '</p>';
        $printHtml .= '<p><strong>Due Amount:</strong> ' . ucfirst($orderData['due_amount']) . '</p>';
        $printHtml .= '<p><strong>Payment ID:</strong> ' . ucfirst($orderData['pg_order_id_1']) . '</p>';
        $printHtml .= '<p><strong>Transaction ID:</strong> ' . ucfirst($orderData['razorpay_payment_id']) . '</p>';

        $printHtml .= '<h2>Order Date:</h2>';
        $printHtml .= '<p>' . date('d-M-Y', strtotime($orderData['order_date'])) . '</p>';

        echo json_encode(['status' => 'success', 'html' => $printHtml]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No order found.']);
    }
    exit();
}



if (isset($_POST['action']) && $_POST['action'] === 'fetch_payment_details' && isset($_POST['order_id'])) {
    $orderId = intval($_POST['order_id']);

    $query = "SELECT 
                product_sub_total, discount_amount, gst_rate, gst_amount, 
                delivery_charges, net_total_amount, paid_amount, due_amount, 
                pg_order_id, pg_order_id_1, razorpay_payment_id 
              FROM orders 
              WHERE order_id = $orderId";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $paymentData = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'data' => $paymentData]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No data found.']);
    }
    exit();
}




if (isset($_POST['action']) && $_POST['action'] === 'update_status' && isset($_POST['order_id'])) {

    $orderId = intval($_POST['order_id']);
    $newStatus = in_array($_POST['new_status'], $statusArray) ? $_POST['new_status'] : 'processing';
    $remarks = $conn->real_escape_string($_POST['remarks']);

    /* GET THE CURRENT STATUS OF THE ORDER */
    $currentStatusQuery = "SELECT `status`, `is_requested_cancel` FROM orders WHERE order_id = $orderId";
    $currentStatusResult = $conn->query($currentStatusQuery);
    $orderData = $currentStatusResult->fetch_assoc();
    $currentStatus = $orderData['status'];
    $isRequestedCancel = $orderData['is_requested_cancel'];

    /* CHECK IF IS_REQUESTED_CANCEL IS 1 */
    if ($isRequestedCancel == 1 && $newStatus !== 'cancelled') {
        echo json_encode(['status' => 'error', 'message' => 'Cannot change status to anything other than "Cancelled".']);
        exit();
    }



    /* UPDATE ORDER STATUS AND INSERT STATUS DETAILS */
    $updateOrder = "UPDATE orders SET `status` = '$newStatus' WHERE order_id = $orderId";
    $insertStatusDetail = "INSERT INTO order_status_details (order_id, `status`, remarks) 
                           VALUES ($orderId, '$newStatus', '$remarks')";


    $success = $conn->query($updateOrder) && $conn->query($insertStatusDetail);

    /* FETCH ORDER DETAILS (COMBINATION_ID AND QUANTITY) */
    $orderDetailsQuery = "SELECT pc.combination_id, od.quantity 
                          FROM order_details od 
                          JOIN price_combination pc ON od.combination_id = pc.combination_id
                          WHERE od.order_id = $orderId";
    $orderDetailsResult = $conn->query($orderDetailsQuery);

    if ($success) {
        while ($row = $orderDetailsResult->fetch_assoc()) {
            $combinationId = $row['combination_id'];
            $quantity = intval($row['quantity']);

            if ($currentStatus !== 'cancelled' && $newStatus === 'cancelled') {
                /* MOVING TO 'CANCELLED': RESTORE STOCK */
                $updateQuantityQuery = "UPDATE price_combination 
                                        SET 
                                            used_stock = used_stock - $quantity,
                                            remaining_stock = remaining_stock + $quantity
                                        WHERE combination_id = $combinationId";
            } elseif ($currentStatus === 'cancelled' && $newStatus !== 'cancelled') {
                /* MOVING FROM 'CANCELLED' TO ANY OTHER STATUS: DEDUCT STOCK AGAIN */
                $updateQuantityQuery = "UPDATE price_combination 
                                        SET 
                                            used_stock = used_stock + $quantity,
                                            remaining_stock = remaining_stock - $quantity
                                        WHERE combination_id = $combinationId";
            } else {
                /*  NO STOCK UPDATE NEEDED FOR OTHER STATUS CHANGES */
                continue;
            }
            $conn->query($updateQuantityQuery);
        }
    }


    if ($success) {
        echo json_encode(['status' => 'success', 'message' => 'Order status updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update order status.']);
    }
    exit();
}


if (isset($_POST['action']) && $_POST['action'] === 'fetch-data' && isset($_REQUEST['draw'])) {

    $start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
    $length = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 5;
    $searchValue = isset($_REQUEST['search']['value']) ? $conn->real_escape_string($_REQUEST['search']['value']) : '';
    $orderColumn = isset($_REQUEST['order'][0]['column']) ? intval($_REQUEST['order'][0]['column']) : 0;
    $orderDir = isset($_REQUEST['order'][0]['dir']) && in_array(strtolower($_REQUEST['order'][0]['dir']), ['asc', 'desc']) ? $_REQUEST['order'][0]['dir'] : 'asc';

    $columns = ['o.order_id', 'personal_details', 'product_details', 'o.status', 'o.order_date'];
    $orderBy = $columns[$orderColumn] . ' ' . $orderDir;

    $statusFilter = isset($_POST['status']) && in_array($_POST['status'], $statusArray) ? $_POST['status'] : '';
    $cancelRequestFilter = isset($_POST['cancel_request']) && in_array($_POST['cancel_request'], ['0', '1']) ? intval($_POST['cancel_request']) : '';

    $startDate = isset($_POST['start_date']) ? $_POST['start_date'] : '';
    $endDate = isset($_POST['end_date']) ? $_POST['end_date'] : '';

    $paymentMethodFilter = isset($_POST['payment_method']) ? $conn->real_escape_string($_POST['payment_method']) : '';





    $whereClause = "WHERE (
        o.name LIKE '%$searchValue%' 
        OR o.email LIKE '%$searchValue%'
        OR o.phone_number LIKE '%$searchValue%'
        OR o.address LIKE '%$searchValue%'
        OR o.city_village LIKE '%$searchValue%'
        OR o.district LIKE '%$searchValue%'
        OR o.state LIKE '%$searchValue%'
        OR o.pincode LIKE '%$searchValue%'

        OR od.product_name LIKE '%$searchValue%'
        OR od.size_name LIKE '%$searchValue%'
        OR od.colour_name LIKE '%$searchValue%'
    )";

    if (!empty($statusFilter)) {
        $whereClause .= " AND o.status = '$statusFilter'";
    }

    if (!empty($cancelRequestFilter)) {
        $whereClause .= " AND o.is_requested_cancel = $cancelRequestFilter";
    }

    if (!empty($startDate) && !empty($endDate)) {
        $whereClause .= " AND DATE(o.order_date) BETWEEN '$startDate' AND '$endDate'";
    } elseif (!empty($startDate)) {
        $whereClause .= " AND DATE(o.order_date) >= '$startDate'";
    } elseif (!empty($endDate)) {
        $whereClause .= " AND DATE(o.order_date) <= '$endDate'";
    }

    if (!empty($paymentMethodFilter)) {
        $whereClause .= " AND o.payment_method = '$paymentMethodFilter'";
    }




    $sql = "SELECT o.order_id, 
        CONCAT('<strong>Name:</strong> ', o.name, '<br><strong>Email:</strong>  ', o.email, '<br><strong>Phone:</strong> ', o.phone_number, 
               '<br><strong>Address:</strong> ', o.address, ', ', o.city_village, ', ', o.district, 
               ', ', o.state, ', ', o.pincode) AS personal_details,
        GROUP_CONCAT(
            CONCAT('<strong>Product Name:</strong> ', od.product_name, 
                   '<br><strong>Size:</strong> ', od.size_name, 
                   '<br><strong>Color:</strong> ', od.colour_name, 
                   '<br><strong>Quantity:</strong> ', od.quantity
            ) 
            SEPARATOR '<hr>'
        ) AS product_details,
        o.status,
        o.payment_method,
        o.is_requested_cancel, 
        o.order_date
    FROM orders o
    JOIN order_details od ON o.order_id = od.order_id
    $whereClause
    GROUP BY o.order_id
    ORDER BY $orderBy 
    LIMIT $start, $length";

    $result = $conn->query($sql);

    $totalQuery = "SELECT COUNT(*) AS total FROM orders";
    $totalRecords = $conn->query($totalQuery)->fetch_assoc()['total'];

    $filteredQuery = "SELECT COUNT(DISTINCT o.order_id) AS filtered FROM orders o
       JOIN order_details od ON o.order_id = od.order_id
       WHERE (
        o.name LIKE '%$searchValue%' 
        OR o.email LIKE '%$searchValue%'
        OR o.phone_number LIKE '%$searchValue%'
        OR o.address LIKE '%$searchValue%'
        OR o.city_village LIKE '%$searchValue%'
        OR o.district LIKE '%$searchValue%'
        OR o.state LIKE '%$searchValue%'
        OR o.pincode LIKE '%$searchValue%'

        OR od.product_name LIKE '%$searchValue%'
        OR od.size_name LIKE '%$searchValue%'
        OR od.colour_name LIKE '%$searchValue%'
       )";

    if (!empty($statusFilter)) {
        $filteredQuery .= " AND o.status = '$statusFilter'";
    }

    if (!empty($cancelRequestFilter)) {
        $filteredQuery .= " AND o.is_requested_cancel = $cancelRequestFilter";
    }

    if (!empty($startDate) && !empty($endDate)) {
        $filteredQuery .= " AND DATE(o.order_date) BETWEEN '$startDate' AND '$endDate'";
    } elseif (!empty($startDate)) {
        $filteredQuery .= " AND DATE(o.order_date) >= '$startDate'";
    } elseif (!empty($endDate)) {
        $filteredQuery .= " AND DATE(o.order_date) <= '$endDate'";
    }

    if (!empty($paymentMethodFilter)) {
        $filteredQuery .= " AND o.payment_method = '$paymentMethodFilter'";
    }




    $filteredRecords = $conn->query($filteredQuery)->fetch_assoc()['filtered'];

    $statusClassMap = [
        'processing' => 'btn-warning',
        'shipped' => 'btn-info',
        'delivered' => 'btn-success',
        'cancelled' => 'btn-danger'
    ];

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'serial_no' => $start + count($data) + 1,

            'personal_details' => $row['personal_details'],
            'product_details' => $row['product_details'],
            'status' => '<button class="btn ' . $statusClassMap[$row['status']] . ' btn-status" 
                data-id="' . $row['order_id'] . '" 
                data-status="' . $row['status'] . '">' . ucfirst($row['status']) . '</button>',

            'actions' => '
                        <div class="d-flex flex-column align-items-center">
                        <button class="btn btn-primary print-btn mb-2" data-id="' . $row['order_id'] . '">Print</button>
                        <button class="btn btn-info payment-info-btn" data-id="' . $row['order_id'] . '">Payment Details</button>
                        </div>
                        ',
            'is_requested_cancel' => $row['is_requested_cancel'] == 1 ? 'Yes' : 'No',
            'payment_method' => ucwords($row['payment_method'], '-'),
            'order_date' => date('d-M-Y', strtotime($row['order_date']))
        ];
    }

    echo json_encode([
        "draw" => intval($_POST['draw']),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $data
    ]);
    exit();
}
?>

<!doctype html>
<html class="no-js" lang="en" dir="ltr">


<head>
    <?php
    include 'include/style.php';
    ?>
</head>

<body>
    <?php
    include 'include/header.php';
    ?>

    <!-- Body: Body -->
    <div class="body d-flex py-3">
        <div class="container-xxl">

            <div class="row g-3">

                <!-- Status Filter Card -->
                <div class="col-md-3">
                    <div class="card h-100">

                        <div class="card-body">
                            <label for="status_filter" class="form-label">Filter By Status:</label>

                            <select id="status_filter" class="form-control">
                                <option value="">All Statuses</option>
                                <?php foreach ($statusArray as $status): ?>
                                    <option value="<?= $status ?>"><?= ucfirst($status) ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                    </div>
                </div>

                <!-- Cancel Request Filter Card -->
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <label for="cancel_request_filter" class="form-label">Filter By Cancel Request:</label>
                            <select id="cancel_request_filter" class="form-control">
                                <option value="">All</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Date Filter Card -->
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <label for="date_filter" class="form-label">Filter By Date:</label>
                            <input type="date" id="start_date" class="form-control mb-2" placeholder="Start Date">
                            <input type="date" id="end_date" class="form-control" placeholder="End Date">
                        </div>
                    </div>
                </div>

                <!-- Payment Method Filter Card -->
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <label for="payment_method_filter" class="form-label">Filter By Payment Method:</label>
                            <select id="payment_method_filter" class="form-control">
                                <option value="">All Methods</option>
                                <option value="cash">Cash on Delivery</option>
                                <option value="online">Online</option>
                                <option value="card">Debit & Credit Card</option>
                                <option value="net-banking">Net Banking</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>





            <table id="order-table" class="table table-bordered">

            </table>



        </div>
    </div>


    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <form id="statusForm">
                        <input type="hidden" id="order_id" name="order_id">

                        <!-- Current Status and Dropdown for New Status -->
                        <div class="mb-3">
                            <label for="current_status" class="form-label">Current Status:</label>
                            <input type="text" class="form-control" id="current_status" name="current_status" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="new_status" class="form-label">Update Status:</label>
                            <select class="form-control" id="new_status" name="new_status">

                                <?php foreach ($statusArray as $status): ?>
                                    <option value="<?= $status ?>"> <?= ucfirst($status) ?> </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <!-- Remarks Input -->
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks:</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>

                </div>
            </div>
        </div>
    </div>


    <!-- Payment Details Modal -->
    <div class="modal fade" id="paymentInfoModal" tabindex="-1" aria-labelledby="paymentInfoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="paymentInfoModalLabel">Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- CONTENT WILL BE LOADED VIA AJAX -->
                    <div id="paymentInfoContent" class="p-3">

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>







    <?php
    include 'include/footer.php';
    ?>

    <script>
        $(document).ready(function () {

            const table = $('#order-table').DataTable({

                processing: true,
                serverSide: true,
                ajax: {
                    url: "order-list.php",
                    type: "POST",
                    // data: { action: "fetch-data" }
                    data: function (d) {
                        d.action = "fetch-data";
                        d.status = $('#status_filter').val();
                        d.cancel_request = $('#cancel_request_filter').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.payment_method = $('#payment_method_filter').val();

                    }
                },
                columns: [
                    { data: "serial_no", title: "S.No" },
                    { data: "personal_details", title: "Personal Details" },
                    { data: "product_details", title: "Product Details" },
                    { data: "status", title: "Status", orderable: false },
                    { data: "payment_method", title: "Payment Method", orderable: false },
                    { data: "actions", title: "Actions", orderable: false },
                    { data: "is_requested_cancel", title: "Cancel Request", orderable: false },
                    { data: "order_date", title: "Order Date" }
                ]

            });

            $('#status_filter').on('change', function () {
                table.ajax.reload();
            });

            $('#cancel_request_filter, #start_date, #end_date, #payment_method_filter').on('change', function () {
                table.ajax.reload();
            });




            /* OPEN MODAL ON STATUS BUTTON CLICK */
            $(document).on('click', '.btn-status', function () {
                const orderId = $(this).data('id');
                const currentStatus = $(this).data('status');

                $('#order_id').val(orderId);

                $('#current_status').val(currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1));
                $('#remarks').val('');

                $('#statusModal').modal('show');
            });


            /* HANDLE STATUS FORM SUBMISSION */
            $('#statusForm').on('submit', function (e) {
                e.preventDefault();

                const currentStatus = $('#current_status').val().toLowerCase();
                const newStatus = $('#new_status').val().toLowerCase();

                if (currentStatus === newStatus) {
                    alert('This is already the current status.');
                    return;
                }

                $.ajax({
                    url: 'order-list.php',
                    type: 'POST',
                    dataType: 'json',
                    data: $(this).serialize() + '&action=update_status',
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);

                            $('#statusModal').modal('hide');
                            table.ajax.reload(null, false); /* REFRESH DATATABLE */

                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('Failed to update status.');
                    }
                });
            });


            $(document).on('click', '.payment-info-btn', function () {
                const orderId = $(this).data('id');

                $.ajax({
                    url: 'order-list.php',
                    method: 'POST',
                    data: {
                        action: 'fetch_payment_details',
                        order_id: orderId
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            let details = response.data;

                            $('#paymentInfoContent').html(`
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr><th>Order ID</th><td>${details.pg_order_id}</td></tr>
                            <tr><th>Product Subtotal</th><td>${details.product_sub_total}</td></tr>
                            <tr><th>Discount Amount</th><td>${details.discount_amount}</td></tr>
                            <tr><th>GST Rate</th><td>${details.gst_rate}%</td></tr>
                            <tr><th>GST Amount</th><td>${details.gst_amount}</td></tr>
                            <tr><th>Delivery Charges</th><td>${details.delivery_charges}</td></tr>
                            <tr><th>Net Total Amount</th><td>${details.net_total_amount}</td></tr>
                            <tr><th>Paid Amount</th><td>${details.paid_amount}</td></tr>
                            <tr><th>Due Amount</th><td>${details.due_amount}</td></tr>
                            <tr><th>Payment ID</th><td>${details.pg_order_id_1}</td></tr>
                            <tr><th>Transaction ID</th><td>${details.razorpay_payment_id}</td></tr>
                        </table>
                    </div>
                `);

                            $('#paymentInfoModal').modal('show');

                        } else {
                            $('#paymentInfoContent').html('<p class="text-danger">Failed to load payment details.</p>');
                        }
                    },
                    error: function () {
                        $('#paymentInfoContent').html('<p class="text-danger">An error occurred while fetching data.</p>');
                    }
                });
            });

            $(document).on('click', '.print-btn', function () {
                var orderId = $(this).data('id');

                $.ajax({
                    url: 'order-list.php',
                    type: 'POST',
                    data: { order_id: orderId, action: 'fetch_print_data' },
                    dataType: 'json',
                    success: function (response) {

                        if (response.status === 'success') {

                            /* OPEN A NEW WINDOW AND WRITE THE HTML CONTENT */

                            var printWindow = window.open('', '_blank');

                            printWindow.document.write('<html><head><title>Print Order</title></head><body>');

                            printWindow.document.write(response.html);

                            printWindow.document.write('</body></html>');

                            printWindow.document.close();

                            printWindow.print();

                        } else {
                            alert(response.message || 'Failed to load order details for printing.');
                        }
                    },
                    error: function () {
                        alert('An error occurred while fetching order details.');
                    }

                });
            });







        });
    </script>



</body>

</html>