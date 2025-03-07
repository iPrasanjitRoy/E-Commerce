<?php

include 'include/db-config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetchInventory') {

    $sql = "SELECT i.inventory_id AS inventory_id, 
                   mc.main_category_name, 
                   sc.sub_category_name, 
                   p.product_name,                   
                   CONCAT(s.size_name, ' / ', c.colour_name) AS combination, 
                   i.added_stock_quantity, 
                   i.removed_stock_quantity,
                   i.date
            FROM inventory i
            LEFT JOIN main_category mc ON i.main_category_id = mc.cid
            LEFT JOIN sub_category sc ON i.sub_category_id = sc.sid
            LEFT JOIN product p ON i.product_id = p.product_id
            LEFT JOIN price_combination pc ON i.combination_id = pc.combination_id
            LEFT JOIN `size` s ON pc.size_id = s.sid
            LEFT JOIN colour c ON pc.colour_id = c.cid
            ORDER BY i.date DESC";

    $result = $conn->query($sql);
    $data = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    echo json_encode(['data' => $data]);
    $conn->close();
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {

    $mainCategoryId = $conn->real_escape_string($_POST['main_category']);
    $subCategoryId = $conn->real_escape_string($_POST['sub_category']);
    $productId = $conn->real_escape_string($_POST['product']);
    $combinationId = $conn->real_escape_string($_POST['combination']);
    $stockAction = $conn->real_escape_string($_POST['stock_action']);
    $addedStock = isset($_POST['added_stock_quantity']) ? $_POST['added_stock_quantity'] : 0;
    $removedStock = isset($_POST['removed_stock_quantity']) ? $_POST['removed_stock_quantity'] : 0;


    $sql = "INSERT INTO inventory (main_category_id, sub_category_id, product_id, combination_id, stock_action, added_stock_quantity, removed_stock_quantity) 
            VALUES ('$mainCategoryId', '$subCategoryId', '$productId', '$combinationId', '$stockAction', '$addedStock', '$removedStock')";

    if ($conn->query($sql) === TRUE) {

        $sqlFetch = "SELECT total_stock, used_stock, remaining_stock FROM price_combination WHERE combination_id = '$combinationId'";
        $result = $conn->query($sqlFetch);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $totalStock = $row['total_stock'];
            $usedStock = $row['used_stock'];
            $remainingStock = $row['remaining_stock'];

            if ($stockAction === 'add') {
                $totalStock += $addedStock;
                $remainingStock = $totalStock - $usedStock;
            } elseif ($stockAction === 'remove') {
                if ($removedStock > $remainingStock) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error: Removed stock quantity cannot exceed remaining stock.'
                    ]);
                    exit();
                } else {
                    $totalStock -= $removedStock;
                    $remainingStock = $totalStock - $usedStock;
                }
            }

            $sqlUpdate = "UPDATE price_combination 
                          SET total_stock = '$totalStock', used_stock = '$usedStock', remaining_stock = '$remainingStock' 
                          WHERE combination_id = '$combinationId'";
            if ($conn->query($sqlUpdate) === TRUE) {
                echo json_encode(['status' => 'success', 'message' => 'Inventory updated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update stock in price_combination table.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Combination not found in price_combination table.']);
        }



    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert into inventory table.']);
    }

    $conn->close();
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetchProductsWithStock' && isset($_POST['sub_category_id'])) {

    $subCategoryId = $conn->real_escape_string($_POST['sub_category_id']);

    $sql = "SELECT product_id, product_name 
            FROM product 
            WHERE sub_category_id = '$subCategoryId' AND is_stock = 1 
            ORDER BY product_name ASC";

    $result = $conn->query($sql);

    $products = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        echo json_encode(['status' => 'success', 'products' => $products]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No products found for the selected subcategory with stock']);
    }

    $conn->close();
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

    <div class="body d-flex py-3">
        <div class="container-xxl">


            <div class="row">
                <!-- Form Section (Left Side) -->
                <div class="col-md-3">
                    <h4>Update Inventory</h4>
                    <form id="inventoryForm">
                        <!-- Main Category Dropdown -->
                        <div class="mb-3">
                            <label for="main-category" class="form-label">Main Category:</label>
                            <select class="form-control" id="main-category" name="main_category" required>
                                <option value="">Select Main Category</option>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>

                        <!-- Sub Category Dropdown -->
                        <div class="mb-3">
                            <label for="sub-category" class="form-label">Sub Category:</label>
                            <select class="form-control" id="sub-category" name="sub_category" required>
                                <option value="">Select Sub Category</option>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>

                        <!-- Product Dropdown -->
                        <div class="mb-3">
                            <label for="product" class="form-label">Product:</label>
                            <select class="form-control" id="product" name="product" required>
                                <option value="">Select Product</option>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>

                        <!-- Price Combination Dropdown -->
                        <div class="mb-3">
                            <label for="price-combination" class="form-label">Price Combination:</label>
                            <select class="form-control" id="price-combination" name="combination" required>
                                <option value="">Select Price Combination</option>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>

                        <!-- Add or Remove Stock Dropdown -->
                        <div class="mb-3">
                            <label for="stock-action" class="form-label">Stock Action:</label>
                            <select class="form-control" id="stock-action" name="stock_action" required>
                                <option value="">Select Action</option>
                                <option value="add">Add Stock</option>
                                <option value="remove">Remove Stock</option>
                            </select>
                        </div>




                        <button type="submit" class="btn btn-primary">Update Inventory</button>
                    </form>
                </div>

                <!-- Table Section (Right Side) -->
                <div class="col-md-9">
                    <h4>Inventory Records</h4>
                    <table class="table" id="inventoryTable">
                        <thead>
                            <tr>
                                <th>Inventory ID</th>
                                <th>Main Category</th>
                                <th>Sub Category</th>
                                <th>Product</th>
                                <th>Combination</th>
                                <th>Stock Quantity</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Inventory records will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>




    <?php
    include 'include/footer.php';
    ?>


    <script>

        $(document).ready(function () {
            $('#inventoryTable').DataTable({
                ajax: {
                    url: 'inventory.php',
                    type: 'POST',
                    data: { action: 'fetchInventory' },
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'inventory_id', title: 'Inventory ID' },
                    { data: 'main_category_name', title: 'Main Category' },
                    { data: 'sub_category_name', title: 'Sub Category' },
                    { data: 'product_name', title: 'Product' },
                    { data: 'combination', title: 'Combination' },
                    {
                        data: null,
                        title: 'Stock Quantity',
                        className: 'text-center',
                        render: function (data, type, row) {
                            if (row.added_stock_quantity > 0) {
                                return `+ ${row.added_stock_quantity}`;
                            } else if (row.removed_stock_quantity > 0) {
                                return `- ${row.removed_stock_quantity}`;
                            } else {
                                return 0;
                            }
                        }
                    },
                    { data: 'date', title: 'Date Added' }
                ],
                createdRow: function (row, data, dataIndex) {
                    if (data.added_stock_quantity > 0) {
                        $(row).css('background-color', '#d8dee8');
                    } else if (data.removed_stock_quantity > 0) {
                        $(row).css('background-color', '#d18c94');
                    }
                },
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                language: {
                    emptyTable: "No inventory records available"
                }
            });
        });

        $(document).ready(function () {
            $.ajax({
                url: 'common-code.php',
                type: 'POST',
                data: { action: 'fetchMainCategories' },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        console.log(response);
                        var mainCategories = response.mainCategories;
                        $('#main-category').empty().append('<option value="">Select Main Category</option>');
                        $.each(mainCategories, function (index, category) {
                            $('#main-category').append('<option value="' + category.cid + '">' + category.main_category_name + '</option>');
                        });
                    }
                }
            });



            $('#main-category').on('change', function () {
                var mainCategoryId = $(this).val();

                $('#product').empty().append('<option value="">Select Product</option>');
                $('#price-combination').empty().append('<option value="">Select Price Combination</option>');





                $.ajax({
                    url: 'common-code.php',
                    type: 'POST',
                    data: { action: 'fetchSubCategories', main_category_id: mainCategoryId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            var subCategories = response.subCategories;
                            $('#sub-category').empty().append('<option value="">Select Sub Category</option>');
                            $.each(subCategories, function (index, category) {
                                $('#sub-category').append('<option value="' + category.sid + '">' + category.sub_category_name + '</option>');
                            });
                        }
                    }
                });
            });



            // Fetch products based on sub category selection
            $('#sub-category').on('change', function () {
                var subCategoryId = $(this).val();

                $('#price-combination').empty().append('<option value="">Select Price Combination</option>');


                $.ajax({
                    url: 'inventory.php',
                    type: 'POST',
                    data: { action: 'fetchProductsWithStock', sub_category_id: subCategoryId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            var products = response.products;
                            $('#product').empty().append('<option value="">Select Product</option>');
                            $.each(products, function (index, product) {
                                $('#product').append('<option value="' + product.product_id + '">' + product.product_name + '</option>');
                            });
                        }
                    }
                });
            });


            // Fetch price combinations based on product selection
            $('#product').on('change', function () {
                var productId = $(this).val();


                $.ajax({
                    url: 'common-code.php',
                    type: 'POST',
                    data: { action: 'fetchPriceCombinations', product_id: productId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            var combinations = response.priceCombinations;
                            $('#price-combination').empty().append('<option value="">Select Price Combination</option>');
                            $.each(combinations, function (index, combination) {
                                $('#price-combination').append('<option value="' + combination.combination_id + '">' + combination.size_name + ' / ' + combination.colour_name + ' - ₹ ' + combination.price + '</option>');
                            });
                        }
                    }
                });
            });



            $('#stock-action').on('change', function () {
                var action = $(this).val();


                if (action === 'add') {
                    if (!$('#add-stock-div').length) { // Ensure no duplicate div
                        $('<div id="add-stock-div" class="mb-3">' +
                            '<label for="added-stock" class="form-label">Added Stock Quantity:</label>' +
                            '<input type="number" class="form-control" id="added-stock" name="added_stock_quantity" required>' +
                            '</div>'
                        ).insertBefore('#inventoryForm button[type="submit"]');
                    }
                    $('#remove-stock-div').remove();
                } else if (action === 'remove') {
                    if (!$('#remove-stock-div').length) { // Ensure no duplicate div
                        $('<div id="remove-stock-div" class="mb-3">' +
                            '<label for="removed-stock" class="form-label">Removed Stock Quantity:</label>' +
                            '<input type="number" class="form-control" id="removed-stock" name="removed_stock_quantity" required>' +
                            '</div>'
                        ).insertBefore('#inventoryForm button[type="submit"]');
                    }
                    $('#add-stock-div').remove();
                } else {
                    $('#add-stock-div, #remove-stock-div').remove();
                }
            });



            $('#inventoryForm').on('submit', function (e) {
                e.preventDefault();

                var formData = $(this).serialize() + '&action=update';

                $.ajax({
                    url: 'inventory.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message || 'Inventory Updated Successfully!');
                            $('#inventoryTable').DataTable().ajax.reload();
                            $('#inventoryForm')[0].reset();
                            $('#add-stock-div, #remove-stock-div').remove();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function () {
                        alert('An error occurred while processing the request.');
                    }
                });
            });

        });
    </script>


</body>

</html>