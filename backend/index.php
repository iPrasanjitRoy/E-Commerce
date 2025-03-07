<?php
include 'include/db-config.php';

if (isset($_POST['action']) && $_POST['action'] === 'fetch_dashboard_data') {
    $filter = isset($_POST['period']) ? $_POST['period'] : 'today';
    $date = $_POST['selected_date'];

    $data = [
        'customers' => 0,
        'orders' => 0,
        'avg_sale' => 0,
        'total_item_sale' => 0,
        'total_sale' => 0,
        'visitors' => 0,
        'total_products' => 0,
        'top_selling' => 0,
        'dealership' => 0,
    ];

    /* NO NEED TO FETCH  */
    $data['dealership'] = 0;
    $data['top_selling'] = 0;
    $data['visitors'] = 0;


    $customer_query = "SELECT COUNT(*) FROM customers WHERE 1";
    $order_query = "SELECT COUNT(*) FROM orders WHERE 1";
    $total_products_query = "SELECT COUNT(*) FROM product WHERE 1";
    $total_sales_query = "SELECT COALESCE(SUM(net_total_amount), 0) FROM orders WHERE status != 'cancelled'";
    // $total_sales_query = "SELECT SUM(net_total_amount) FROM orders WHERE status != 'cancelled'";

    $avg_sales_query = "SELECT COALESCE(AVG(net_total_amount), 0) FROM orders WHERE status != 'cancelled'";
    $total_item_sales_query = "SELECT COALESCE(SUM(quantity),0) FROM order_details WHERE 1";



    if ($filter === 'today') {
        $today = date('Y-m-d');

        $customer_query .= " AND date_registered = '$today'";
        $order_query .= " AND order_date = '$today'";
        $total_products_query .= " AND DATE(created_at) = '$today'";
        $total_sales_query .= " AND order_date = '$today'";

        $avg_sales_query .= " AND order_date = '$today'";
        $total_item_sales_query .= " AND DATE(created_at) = '$today'";


    } elseif ($filter === 'week') {
        $today = date('Y-m-d');
        $last_day = date('Y-m-d', strtotime('-7 days', strtotime($today)));

        $customer_query .= " AND (date_registered  BETWEEN  '$last_day' AND '$today')";
        $order_query .= " AND (order_date  BETWEEN '$last_day' AND '$today')";
        $total_products_query .= " AND (DATE(created_at) BETWEEN '$last_day' AND '$today')";
        $total_sales_query .= " AND (order_date BETWEEN '$last_day' AND '$today')";

        $avg_sales_query .= " AND (order_date BETWEEN '$last_day' AND '$today')";
        $total_item_sales_query .= " AND (DATE(created_at) BETWEEN '$last_day' AND '$today')";

    } elseif ($filter === 'month') {
        $today = date('Y-m-d');
        $last_day = date('Y-m-d', strtotime('-30 days', strtotime($today)));

        $customer_query .= " AND (date_registered  BETWEEN  '$last_day' AND '$today')";
        $order_query .= " AND (order_date  BETWEEN '$last_day' AND '$today')";
        $total_products_query .= " AND (DATE(created_at) BETWEEN '$last_day' AND '$today')";
        $total_sales_query .= " AND (order_date BETWEEN '$last_day' AND '$today')";

        $avg_sales_query .= " AND (order_date BETWEEN '$last_day' AND '$today')";
        $total_item_sales_query .= " AND (DATE(created_at) BETWEEN '$last_day' AND '$today')";


    } elseif ($filter === 'year') {
        $today = date('Y-m-d');
        $last_day = date('Y-m-d', strtotime('-365 days', strtotime($today)));

        $customer_query .= " AND (date_registered  BETWEEN  '$last_day' AND '$today')";
        $order_query .= " AND (order_date  BETWEEN '$last_day' AND '$today')";
        $total_products_query .= " AND (DATE(created_at) BETWEEN '$last_day' AND '$today')";
        $total_sales_query .= " AND (order_date BETWEEN '$last_day' AND '$today')";

        $avg_sales_query .= " AND (order_date BETWEEN '$last_day' AND '$today')";
        $total_item_sales_query .= " AND (DATE(created_at) BETWEEN '$last_day' AND '$today')";

    } else {

        $customer_query .= " AND date_registered <= '$date'";
        $order_query .= " AND order_date <= '$date'";
        $total_products_query .= " AND DATE(created_at) <= '$date'";
        $total_sales_query .= " AND order_date <= '$date'";

        $avg_sales_query .= " AND order_date <= '$date'";
        $total_item_sales_query .= " AND DATE(created_at) <= '$date'";

    }

    $customer = $conn->query($customer_query)->fetch_row();
    $order = $conn->query($order_query)->fetch_row();
    $total_products = $conn->query($total_products_query)->fetch_row();
    $total_sales = $conn->query($total_sales_query)->fetch_row();

    $avg_sales = $conn->query($avg_sales_query)->fetch_row();
    $total_item_sales = $conn->query($total_item_sales_query)->fetch_row();


    $data['customers'] = $customer[0];
    $data['orders'] = $order[0];
    $data['total_products'] = $total_products[0];
    $data['total_sale'] = $total_sales[0];

    $data['avg_sale'] = $avg_sales[0];
    $data['total_item_sale'] = $total_item_sales[0];



    echo json_encode($data);
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
            <?php
            $revenueResult = mysqli_query($conn, "
                                            SELECT SUM(o.net_total_amount) AS revenue
                                            FROM orders o
                                            WHERE o.status != 'cancelled'
                                        ");
            $revenueRow = mysqli_fetch_assoc($revenueResult);
            $revenue = $revenueRow['revenue'] ?? 0;


            $clientsResult = mysqli_query($conn, "
                                            SELECT COUNT(*) AS total_clients
                                            FROM customers
                                        ");
            $clientsRow = mysqli_fetch_assoc($clientsResult);
            $totalClients = $clientsRow['total_clients'] ?? 0;

            $expense = 0;
            $newStoreOpen = 0;

            ?>

            <div class="row g-3 mb-3 row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-2 row-cols-xl-4">

                <div class="col">
                    <div class="alert-success alert mb-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar rounded no-thumbnail bg-success text-light"><i
                                    class="fa fa-dollar fa-lg"></i></div>
                            <div class="flex-fill ms-3 text-truncate">
                                <div class="h6 mb-0">Revenue</div>
                                <span class="small">₹<?= number_format($revenue, 2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col">
                    <div class="alert-danger alert mb-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar rounded no-thumbnail bg-danger text-light"><i
                                    class="fa fa-credit-card fa-lg"></i></div>
                            <div class="flex-fill ms-3 text-truncate">
                                <div class="h6 mb-0">Expense</div>
                                <span class="small">₹<?= number_format($expense, 2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col">
                    <div class="alert-warning alert mb-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar rounded no-thumbnail bg-warning text-light"><i
                                    class="fa fa-smile-o fa-lg"></i></div>
                            <div class="flex-fill ms-3 text-truncate">
                                <div class="h6 mb-0">Happy Clients</div>
                                <span class="small"><?= number_format($totalClients) ?></span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col">
                    <div class="alert-info alert mb-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar rounded no-thumbnail bg-info text-light"><i class="fa fa-shopping-bag"
                                    aria-hidden="true"></i></div>
                            <div class="flex-fill ms-3 text-truncate">
                                <div class="h6 mb-0">New StoreOpen</div>
                                <span class="small"><?= number_format($newStoreOpen) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- Row end  -->





            <div class="row g-3">
                <div class="col-lg-12 col-md-12">

                    <div class="tab-filter d-flex align-items-center justify-content-between mb-3 flex-wrap">
                        <ul class="nav nav-tabs tab-card tab-body-header rounded  d-inline-flex w-sm-100">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab"
                                    href="#summery-dashboard" data-period="today">Today</a></li>

                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#summery-dashboard"
                                    data-period="week">Week</a>
                            </li>

                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#summery-dashboard"
                                    data-period="month">Month</a></li>

                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#summery-dashboard"
                                    data-period="year">Year</a>
                            </li>
                        </ul>

                        <div class="date-filter d-flex align-items-center mt-2 mt-sm-0 w-sm-100">
                            <div class="input-group">
                                <input type="date" class="form-control">

                                <button class="btn btn-primary btn-date" type="button"><i
                                        class="icofont-filter fs-5"></i></button>
                            </div>
                        </div>
                    </div>


                    <div class="tab-content mt-1">
                        <!-- <div class="tab-pane fade show active" id="summery-dashboard"> -->


                        <div class="tab-pane fade show active" id="summery-dashboard">

                            <?php include_once 'common-dashboard.php' ?>


                        </div>


                    </div>

                </div>
            </div><!-- Row end  -->



            <?php
            $query = "
                    SELECT 
                        o.order_id,
                        o.pg_order_id, 
                        o.name AS customer_name, 
                        o.payment_method, 
                        o.net_total_amount, 
                        o.status, 
                        GROUP_CONCAT(CONCAT(p.image_one, '|', od.product_name) SEPARATOR ',') AS products
                    FROM orders o
                    JOIN order_details od ON o.order_id = od.order_id
                    JOIN product p ON od.product_id = p.product_id
                    GROUP BY o.order_id
                    ORDER BY o.created_at DESC
                    LIMIT 5
                    ";

            $result = mysqli_query($conn, $query);
            ?>

            <div class="row g-3 mb-3">
                <div class="col-md-12 ">
                    <div class="card">

                        <div
                            class="card-header py-3 d-flex justify-content-between align-items-center bg-transparent border-bottom-0">
                            <h6 class="m-0 fw-bold">Recent Transactions</h6>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="myDataTable" class="table table-hover align-middle mb-0"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#ORDERID</th>
                                            <th>Item</th>
                                            <th>Customer Name</th>
                                            <th>Payment Info</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                            <tr>
                                                <td><strong><?php echo $row['pg_order_id']; ?></strong></td>

                                                <td>

                                                    <?php
                                                    $products = explode(',', $row['products']);

                                                    foreach ($products as $product) {
                                                        list($image, $name) = explode('|', $product);

                                                        ?>

                                                        <div class="d-flex align-items-center mb-1">
                                                            <img src="<?php echo $image; ?>" class="avatar sm rounded me-2"
                                                                alt="product-image">
                                                            <span><?php echo $name; ?></span>
                                                        </div>
                                                    <?php } ?>

                                                </td>

                                                <td><?php echo $row['customer_name']; ?></td>
                                                <td><?php echo ucfirst($row['payment_method']); ?></td>
                                                <td>₹<?php echo $row['net_total_amount']; ?></td>

                                                <td>
                                                    <?php
                                                    $statusClass = '';
                                                    switch ($row['status']) {
                                                        case 'processing':
                                                            $statusClass = 'bg-warning';
                                                            break;
                                                        case 'shipped':
                                                            $statusClass = 'bg-info';
                                                            break;
                                                        case 'delivered':
                                                            $statusClass = 'bg-success';
                                                            break;
                                                        case 'cancelled':
                                                            $statusClass = 'bg-danger';
                                                            break;
                                                    }
                                                    ?>

                                                    <span class="badge <?php echo $statusClass; ?>">
                                                        <?php echo ucfirst($row['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- Row end  -->



        </div>
    </div>



    <?php
    include 'include/footer.php';
    ?>

    <script>
        $(document).ready(function () {
            fetchDetails('today');
        });



        function fetchDetails(period = null, selectedDate = null) {
            $.ajax({
                url: 'index.php',
                type: 'POST',
                data: {
                    action: 'fetch_dashboard_data',
                    period: period,
                    selected_date: selectedDate
                },
                dataType: 'json',
                success: function (data) {

                    $('#customers-count').text(data.customers);
                    $('#orders-count').text(data.orders);
                    $('#avg-sale').text('₹' + data.avg_sale);

                    $('#total-item-sale').text(data.total_item_sale);
                    $('#total-sale').text('₹' + data.total_sale);
                    $('#visitors-count').text(data.visitors);

                    $('#total-products').text(data.total_products);
                    $('#top-selling').text(data.top_selling);
                    $('#dealership').text(data.dealership);
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            });
        }



        $('.nav-link').on('click', function () {
            var period = $(this).data('period');
            fetchDetails(period);
        });

        $('.btn-date').on('click', function () {
            var selectedDate = $('input[type="date"]').val();
            fetchDetails(null, selectedDate);
        });

    </script>

</body>

</html>