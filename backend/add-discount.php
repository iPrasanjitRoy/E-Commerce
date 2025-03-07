<?php
$action = 'Add';
include 'include/db-config.php';

$response = ['success' => false, 'message' => ''];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetchdata') {

    $discount_type = isset($_POST['discount_type']) ? mysqli_real_escape_string($conn, $_POST['discount_type']) : '';
    $product_based = isset($_POST['product_based']) ? mysqli_real_escape_string($conn, $_POST['product_based']) : '';


    $query = "SELECT d.coupon_id, d.coupon_name, d.discount_type, d.flat, d.percentage, 
                     p.product_name, d.validity_date 
              FROM discount d 
              LEFT JOIN product p ON d.product_id = p.product_id 
              WHERE 1=1";

    if (!empty($discount_type)) {
        $query .= " AND d.discount_type = '$discount_type'";
    }


    if ($product_based === 'product_based') {
        $query .= " AND d.product_id IS NOT NULL";
    } elseif ($product_based === 'without_product') {
        $query .= " AND d.product_id IS NULL";
    }


    $query .= " ORDER BY d.coupon_id DESC";


    $result = mysqli_query($conn, $query);

    $data = [];
    $serial_number = 1;

    while ($row = mysqli_fetch_assoc($result)) {
        $row['serial_number'] = $serial_number++;

        $row['discount_type'] = ucfirst($row['discount_type']);

        $row['flat'] = ($row['flat'] == '0.00') ? '' : $row['flat'];
        $row['percentage'] = ($row['percentage'] == '0.00') ? '' : $row['percentage'];


        $row['action'] = '
        <button class="btn btn-warning btn-sm edit-btn mb-2" data-id="' . $row['coupon_id'] . '">Edit</button>
        <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row['coupon_id'] . '">Delete</button>
    ';

        $data[] = $row;
    }

    echo json_encode(['data' => $data]);
    exit();

}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'Add') {
    if (isset($_POST['coupon_name'], $_POST['discount_type'], $_POST['validity_date'])) {

        $coupon_name = mysqli_real_escape_string($conn, $_POST['coupon_name']);
        $discount_type = mysqli_real_escape_string($conn, $_POST['discount_type']);
        $flat = isset($_POST['flat']) ? floatval($_POST['flat']) : 0;

        $percentage = isset($_POST['percentage']) ? floatval($_POST['percentage']) : 0;

        $product_id = !empty($_POST['product_id']) ? intval($_POST['product_id']) : 'NULL';

        $validity_date = mysqli_real_escape_string($conn, $_POST['validity_date']);

        $query = "INSERT INTO discount (coupon_name, discount_type, flat, `percentage`, product_id, validity_date)
                  VALUES ('$coupon_name', '$discount_type', '$flat', '$percentage', $product_id, '$validity_date')";

        if (mysqli_query($conn, $query)) {
            $response['success'] = true;
            $response['message'] = 'Discount added successfully!';
        } else {
            $response['message'] = 'Database error: ' . mysqli_error($conn);
        }
    } else {
        $response['message'] = 'Invalid form data.';
    }
    echo json_encode($response);
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

            <div class="row">
                <div class="col-md-5 col-12">
                    <div class="card">

                        <div class="card-header">
                            <h4 class="card-title"><?= $action ?> Discount </h4>
                        </div>

                        <div class="card-body">
                            <form id="discount-form" method="POST">

                                <div class="mb-3">
                                    <label for="coupon_name" class="form-label">Coupon Name</label>
                                    <input type="text" class="form-control" id="coupon_name" name="coupon_name"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="discount_type" class="form-label">Discount Type</label>
                                    <select class="form-select" id="discount_type" name="discount_type" required>
                                        <option value="">Select Type</option>
                                        <option value="flat">Flat</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                </div>


                                <div class="mb-3">
                                    <label for="flat" class="form-label">Flat Discount</label>
                                    <input type="number" class="form-control" id="flat" name="flat" value="" min="0"
                                        step="0.01">
                                </div>


                                <div class="mb-3">
                                    <label for="percentage" class="form-label">Percentage Discount</label>
                                    <input type="number" class="form-control" id="percentage" name="percentage" value=""
                                        min="0" max="100" step="0.01">
                                </div>

                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Product</label>

                                    <select class="form-select" id="product_id" name="product_id">
                                        <option value="">Select Product</option>
                                        <?php
                                        $query = "SELECT product_id, product_name FROM product ORDER BY product_name ASC";
                                        $result = mysqli_query($conn, $query);
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '<option value="' . $row['product_id'] . '">' . $row['product_name'] . '</option>';
                                        }
                                        ?>
                                    </select>

                                </div>

                                <div class="mb-3">
                                    <label for="validity_date" class="form-label">Validity Date</label>
                                    <input type="date" class="form-control" id="validity_date" name="validity_date"
                                        required>
                                </div>

                                <button class="btn btn-primary mt-3" type="submit">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-7 col-12">

                    <!-- Filter Section -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Filter Discounts</h5>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label for="filter-discount-type" class="form-label">Discount Type</label>
                                    <select class="form-select" id="filter-discount-type">
                                        <option value="">All Types</option>
                                        <option value="flat">Flat</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="filter-product-based" class="form-label">Product Association</label>
                                    <select class="form-select" id="filter-product-based">
                                        <option value="">All Discounts</option>
                                        <option value="product_based">Product-Based</option>
                                        <option value="without_product">Without Product</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Data Table Section -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Discount List</h5>

                            <div class="table-responsive">
                                <table id="data-table" class="table table-striped table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Coupon Name</th>
                                            <th>Type</th>
                                            <th>Flat Discount (₹)</th>
                                            <th>Percentage (%)</th>
                                            <th>Product</th>
                                            <th>Validity Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>


                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>



    <?php
    include 'include/footer.php';
    ?>




    <script>
        $(document).ready(function () {

            /* HANDLE FILTER CHANGES */
            /*
            $('#filter-discount-type, #filter-product-based').change(function () {
                var filterData = {
                    discount_type: $('#filter-discount-type').val(),
                    product_based: $('#filter-product-based').val()
                };
                loadDataTable(filterData);
            });
            /*

            /* LOAD DATATABLE */
            function loadDataTable(filterData = {}) {

                /*
                if ($.fn.DataTable.isDataTable('#data-table')) {
                    $('#data-table').DataTable().destroy();
                } 
                */

                var dataTable = $('#data-table').DataTable({

                    ajax: {
                        url: 'add-discount.php',
                        type: 'POST',
                        // data: { action: 'fetchdata', ...filterData },
                        data: function (d) {
                            d.action = 'fetchdata';
                            d.discount_type = $('#filter-discount-type').val();
                            d.product_based = $('#filter-product-based').val();
                        },
                        dataSrc: 'data'
                    },
                    columns: [
                        { data: 'serial_number' },
                        { data: 'coupon_name' },
                        { data: 'discount_type' },
                        { data: 'flat' },
                        { data: 'percentage' },
                        { data: 'product_name' },
                        { data: 'validity_date' },
                        { data: 'action' }
                    ]
                });

                $('#filter-discount-type, #filter-product-based').change(function () {
                    dataTable.ajax.reload();
                });




            }

            loadDataTable();











            /* INITIALLY HIDE BOTH INPUT FIELDS */
            $('#flat, #percentage').closest('.mb-3').hide();

            /* SHOW & HIDE INPUTS BASED ON DISCOUNT TYPE SELECTION */
            $('#discount_type').change(function () {
                var type = $(this).val();
                if (type === 'flat') {
                    $('#flat').closest('.mb-3').show();
                    $('#percentage').closest('.mb-3').hide();
                    /* CLEAR PERCENTAGE INPUT */
                    $('#percentage').val('');
                } else if (type === 'percentage') {
                    $('#percentage').closest('.mb-3').show();
                    $('#flat').closest('.mb-3').hide();
                    $('#flat').val('');
                } else {
                    $('#flat, #percentage').closest('.mb-3').hide();
                    $('#flat, #percentage').val('');
                }
            });


            $('#discount-form').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: 'add-discount.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=Add',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert('Discount added successfully!');
                            $('#discount-form')[0].reset(); /* RESET THE FORM */
                            $('#flat, #percentage').closest('.mb-3').hide(); /* HIDE INPUTS */
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function () {
                        alert('An error occurred while submitting the form.');
                    }
                });
            });




        });
    </script>



</body>

</html>