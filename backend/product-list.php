<?php
include 'include/db-config.php';

$action = $_POST['action'] ?? '';
$filter = $_POST['filter_by'] ?? '';



if ($action == 'fetch') {


    $query = " 
        SELECT 
            p.product_id, 
            p.product_name, 
            mc.main_category_name AS main_category, 
            sc.sub_category_name AS sub_category,
            p.description, 
            p.is_stock,
            p.image_one AS image,
            p.is_featured,
            p.is_trending,
            p.is_best_seller,
            p.is_under_special_offer,
            b.brand_name 
            
        FROM 
            product p
        JOIN 
            main_category mc ON p.main_category_id = mc.cid
        JOIN 
            sub_category sc ON p.sub_category_id = sc.sid
        JOIN 
            brands b ON p.brand_id = b.brand_id
    ";



    if ($filter) {
        $query .= " WHERE p.$filter = 1";
    }


    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['image'] = '<img src="' . htmlspecialchars($row['image']) . '" alt="Product Image 1" style="width: 100px; height: 100px;">';
        $row['actions'] = '
            <button class="btn btn-warning btn-sm editBtn mb-1" data-id="' . $row['product_id'] . '">Edit</button>
            <button class="btn btn-danger btn-sm deleteBtn mb-1" data-id="' . $row['product_id'] . '">Delete</button>
        ';
        $row['variant_button'] = '
            <button class="btn btn-info btn-sm variantBtn" data-id="' . $row['product_id'] . '">View Variants</button>
        ';

        $featureBtnText = $row['is_featured'] == 1 ? 'Remove Featured' : 'Make Featured';
        $trendingBtnText = $row['is_trending'] == 1 ? 'Remove Trending' : 'Make Trending';
        $bestSellerBtnText = $row['is_best_seller'] == 1 ? 'Remove Best Seller' : 'Make Best Seller';
        $specialOfferBtnText = $row['is_under_special_offer'] == 1 ? 'Remove Special Offer' : 'Make Special Offer';




        $row['extra_buttons'] = '
        <button class="btn btn-success btn-sm featureBtn mb-1" data-id="' . $row['product_id'] . '">' . $featureBtnText . '</button>
        <button class="btn btn-primary btn-sm trendingBtn mb-1" data-id="' . $row['product_id'] . '">' . $trendingBtnText . '</button>
        <button class="btn btn-dark btn-sm bestSellerBtn mb-1" data-id="' . $row['product_id'] . '">' . $bestSellerBtnText . '</button>
        <button class="btn btn-secondary btn-sm specialOfferBtn mb-1" data-id="' . $row['product_id'] . '">' . $specialOfferBtnText . '</button>
        ';

        $data[] = $row;
    }

    echo json_encode([
        "draw" => $_POST['draw'] ?? 1,
        "recordsTotal" => count($data),
        "recordsFiltered" => count($data),
        "data" => $data
    ]);

    exit();
}



if ($action == 'delete') {
    $product_id = $_POST['product_id'] ?? null;

    if ($product_id) {
        $product_id = intval($product_id);

        $fetchImagesQuery = "SELECT image_one, image_two, image_three FROM product WHERE product_id = $product_id";
        $result = $conn->query($fetchImagesQuery);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Delete images from storage if they exist
            $images = ['image_one', 'image_two', 'image_three'];

            foreach ($images as $imageField) {
                if (!empty($row[$imageField]) && file_exists($row[$imageField])) {
                    unlink($row[$imageField]); // Delete the file
                }
            }

            $query = "DELETE FROM product WHERE product_id = $product_id";

            if ($conn->query($query)) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Product deleted successfully."
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to delete the product."
                ]);
            }
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid product ID."
        ]);
    }
    exit();
}




if ($action == 'fetchSingle') {

    // Initialize the variables to avoid "undefined variable" warning
    $mainCategories = [];
    $subCategories = [];
    $sizes = [];
    $colors = [];
    $priceCombinations = [];

    $brands = [];

    $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $brands[] = $row;
        }
    }




    $sql = "SELECT * FROM main_category ORDER BY main_category_name ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $mainCategories[] = $row;
        }
    }


    // Fetch sizes and colors
    $sqlSizes = "SELECT * FROM `size` ORDER BY size_name ASC";
    $resultSizes = $conn->query($sqlSizes);
    if ($resultSizes->num_rows > 0) {
        while ($row = $resultSizes->fetch_assoc()) {
            $sizes[] = $row;
        }
    }

    $sqlColors = "SELECT * FROM colour ORDER BY colour_name ASC";
    $resultColors = $conn->query($sqlColors);
    if ($resultColors->num_rows > 0) {
        while ($row = $resultColors->fetch_assoc()) {
            $colors[] = $row;
        }
    }

    // Fetch price combinations
    $product_id = $_POST['product_id'];
    $sqlPriceCombinations = "SELECT * FROM price_combination WHERE product_id = $product_id";
    $resultPriceCombinations = $conn->query($sqlPriceCombinations);
    if ($resultPriceCombinations->num_rows > 0) {
        while ($row = $resultPriceCombinations->fetch_assoc()) {
            $priceCombinations[] = $row;
        }
    }

    // Fetch sub-categories if main_category_id is provided

    if (isset($_POST['main_category_id']) && $_POST['main_category_id'] !== '') {
        $mainCategoryId = intval($_POST['main_category_id']);

        $sql = "SELECT * FROM sub_category WHERE cid = $mainCategoryId ORDER BY sub_category_name ASC";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $subCategories[] = $row;
            }

        }
        echo json_encode([
            "status" => "success",
            "message" => "Sub Categories Fetched successfully.",
            "subCategories" => $subCategories    // Include sub categories
        ]);
        exit();
    }



    // Fetch product details if product_id is provided
    if (isset($_POST['product_id']) && $_POST['product_id'] !== '') {
        $product_id = intval($_POST['product_id']);

        $query = "
            SELECT 
                p.product_id, 
                p.product_name, 
                p.main_category_id, 
                p.sub_category_id, 
                mc.main_category_name, 
                sc.sub_category_name,
                p.description,
                p.image_one,
                p.image_two, 
                p.image_three ,
                p.brand_id,
                b.brand_name 
            FROM 
                product p
            JOIN 
                main_category mc ON p.main_category_id = mc.cid
            JOIN 
                sub_category sc ON p.sub_category_id = sc.sid
            JOIN 
                brands b ON p.brand_id = b.brand_id
            WHERE 
                p.product_id = $product_id
        ";

        $result = $conn->query($query);


        if ($result && $result->num_rows > 0) {
            $product = $result->fetch_assoc();

            // Fetch sub-categories if the product has a sub_category_id
            if ($product['main_category_id']) {
                $mainCategoryId = $product['main_category_id'];
                $sqlSubCategories = "SELECT * FROM sub_category WHERE cid = $mainCategoryId ORDER BY sub_category_name ASC";
                $subResult = $conn->query($sqlSubCategories);
                if ($subResult->num_rows > 0) {
                    while ($subRow = $subResult->fetch_assoc()) {
                        $subCategories[] = $subRow;
                    }
                }
            }


            echo json_encode([
                "status" => "success",
                "data" => $product,
                "mainCategories" => $mainCategories,  // Include main categories
                "subCategories" => $subCategories,    // Include sub categories
                "sizes" => $sizes,
                "colors" => $colors,
                "priceCombinations" => $priceCombinations,
                "brands" => $brands
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Product not found."
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid product ID."
        ]);
    }

    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateProduct') {

    $productId = $_POST['product_id'];
    $productName = sanitizeInput($conn, $_POST['product_name']);
    $mainCategoryId = $_POST['main_category'];
    $subCategoryId = $_POST['sub_category'];
    $description = $_POST['description'];
    $brandId = $_POST['brand'];

    /*  FETCH EXISTING PRODUCT DATA TO GET THE CURRENT IMAGE PATHS */
    $fetchSql = "SELECT image_one, image_two, image_three FROM product WHERE product_id = '$productId'";
    $result = $conn->query($fetchSql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }

    $uploadDir = 'uploads/';

    $product_url = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $productName)));
    $product_url .= '-' . time(); // Appending timestamp for uniqueness



    $sql = "UPDATE product SET 
    product_name = '$productName', 
    main_category_id = '$mainCategoryId', 
    sub_category_id = '$subCategoryId', 
    `description` = '$description',
    product_url = '$product_url',
    brand_id = '$brandId'";

    /* CHECK IF A NEW IMAGE FOR "IMAGE_ONE" IS UPLOADED */
    if (!empty($_FILES['image_one']['name'])) {
        /* EXTRACT THE FILE EXTENSION FROM THE ORIGINAL FILE NAME */
        $fileExtension = pathinfo($_FILES['image_one']['name'], PATHINFO_EXTENSION);

        /* GENERATE A UNIQUE NAME FOR THE FILE (E.G., USING TIME AND A RANDOM STRING) */
        $newFileName = uniqid('image_one_', true) . '.' . $fileExtension;

        /*  $targetFile = $uploadDir . basename($_FILES['image_one']['name']); */

        /* CONSTRUCT THE FULL PATH FOR THE UPLOADED FILE */
        $targetFile = $uploadDir . $newFileName;

        move_uploaded_file($_FILES['image_one']['tmp_name'], $targetFile);

        /* $image_one = 'uploads/' . $_FILES['image_one']['name']; */

        $image_one = $targetFile;
        $sql .= " , `image_one` = '$image_one'";
        if (!empty($row['image_one']) && file_exists($row['image_one'])) {
            unlink($row['image_one']);
        }
    }



    if (!empty($_FILES['image_two']['name'])) {
        $fileExtension = pathinfo($_FILES['image_two']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('image_two_', true) . '.' . $fileExtension;
        $targetFile = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['image_two']['tmp_name'], $targetFile)) {
            $image_two = $targetFile;
            $sql .= " , `image_two` = '$image_two'";
            if (!empty($row['image_two']) && file_exists($row['image_two'])) {
                unlink($row['image_two']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image two.']);
            exit();
        }
    }

    if (!empty($_FILES['image_three']['name'])) {
        $fileExtension = pathinfo($_FILES['image_three']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('image_three_', true) . '.' . $fileExtension;
        $targetFile = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['image_three']['tmp_name'], $targetFile)) {
            $image_three = $targetFile;
            $sql .= " , `image_three` = '$image_three'";
            if (!empty($row['image_three']) && file_exists($row['image_three'])) {
                unlink($row['image_three']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image three.']);
            exit();
        }
    }


    /* UPDATE PRODUCT IN THE DATABASE */
    $sql .= " WHERE product_id = '$productId'";


    if ($conn->query($sql) === TRUE) {

        $size_ids = $colour_ids = $combination_id = [];

        // Handle price combinations (new code for updating price combinations)
        if (isset($_POST['price_combinations'])) {
            $priceCombinations = json_decode($_POST['price_combinations'], true);

            foreach ($priceCombinations as $combination) {
                $combinationId = $combination['combination_id'];
                $sizeId = $combination['size_id'];
                $colorId = $combination['color_id'];
                $price = $combination['price'];

                $size_ids[] = $sizeId;
                $colour_ids[] = $colorId;


                if (intval($combinationId > 0)) {
                    $conn->query("
                    UPDATE price_combination SET
                    size_id = '$sizeId',
                    colour_id = '$colorId',
                    price = '$price'
                    WHERE combination_id = '$combinationId' AND product_id = '$productId'");

                    $combination_id[] = $combinationId;
                } else {

                    $conn->query(" INSERT INTO price_combination SET
                                    product_id = '$productId',
                                    size_id = '$sizeId',
                                    colour_id = '$colorId',
                                    price = '$price'");

                    $combination_id[] = $conn->insert_id;
                }
            }
            $combination_id_imploded = implode(',', $combination_id);

            $conn->query("DELETE FROM  price_combination WHERE product_id = '$productId' AND  combination_id NOT IN ($combination_id_imploded)");
            $conn->query("DELETE FROM  inventory  WHERE product_id = '$productId' AND  combination_id NOT IN ($combination_id_imploded)");



            $size_ids_imploded = implode(',', $size_ids);
            $colour_ids_imploded = implode(',', $colour_ids);
            $conn->query("UPDATE product SET  size_ids = '$size_ids_imploded', colour_ids = '$colour_ids_imploded' WHERE product_id = '$productId'");
        }


        echo json_encode(['status' => 'success', 'message' => 'Product Updated Successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed To Update Product']);
    }

    $conn->close();
    exit();
}

?>


<?php
if (isset($_POST['product_id']) && $_POST['action'] == 'fetchVariant') {

    $product_id = $_POST['product_id'];

    if ($product_id) {
        $query = "
            SELECT 
                sz.size_name, 
                cl.colour_name, 
                pc.price
            FROM 
                price_combination pc
            LEFT JOIN 
                size sz ON pc.size_id = sz.sid
            LEFT JOIN 
                colour cl ON pc.colour_id = cl.cid
            WHERE 
                pc.product_id = $product_id
        ";

        $result = $conn->query($query);

        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        echo json_encode([
            "data" => $data
        ]);
    } else {
        echo json_encode([
            "data" => []
        ]);
    }
    exit();

}
?>


<?php
if (isset($_POST['product_id']) && isset($_POST['action'])) {
    $product_id = (int) $_POST['product_id'];
    $action = $_POST['action'];

    $allowed_actions = ['featured', 'trending', 'bestseller', 'special'];

    if (!in_array($action, $allowed_actions)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
    }


    $column = '';
    switch ($action) {
        case 'featured':
            $column = 'is_featured';
            break;
        case 'trending':
            $column = 'is_trending';
            break;
        case 'bestseller':
            $column = 'is_best_seller';
            break;
        case 'special':
            $column = 'is_under_special_offer';
            break;
    }

    // Toggle the value (0 → 1 or 1 → 0)
    $sql = "UPDATE product SET $column = 1 - $column WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $status_result = mysqli_query($conn, "SELECT $column FROM product WHERE product_id = $product_id");
        $new_status = mysqli_fetch_assoc($status_result)[$column];

        echo json_encode(['status' => 'success', 'new_status' => $new_status]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update']);
    }

    exit();
}
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'include/style.php'; ?>
</head>

<body>
    <?php include 'include/header.php'; ?>

    <!-- Body: Body -->
    <div class="body d-flex py-3">
        <div class="container-xxl">
            <div class="row">
                <div class="col">


                    <div class="row">
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="filter-dropdown" class="form-label">Filter By Feature</label>
                                <select id="filter-dropdown" class="form-control">
                                    <option value="">Select Filter</option>
                                    <option value="is_featured">Featured</option>
                                    <option value="is_trending">Trending</option>
                                    <option value="is_best_seller">Best Seller</option>
                                    <option value="is_under_special_offer">Special Offer</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <table id="data-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Main Category</th>
                                <th>Sub Category</th>
                                <th>Brand Name</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Actions</th>
                                <th>Variants</th> <!-- Add a column for variants -->
                                <th>Extra Actions</th> <!-- Add a column for variants -->
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated here by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Variant Modal -->
    <div class="modal fade" id="variantModal" tabindex="-1" aria-labelledby="variantModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="variantModalLabel">Product Variants</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="variant-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Size</th>
                                <th>Colour</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody id="variant-table-body">
                            <!-- Variants will be populated here by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>


                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="edit-product-id">

                        <!-- Product Name -->
                        <div class="mb-3">
                            <label for="edit-product-name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="edit-product-name" required>
                        </div>

                        <!-- Main Category Dropdown -->
                        <div class="mb-3">
                            <label for="edit-main-category" class="form-label">Main Category:</label>
                            <select class="form-control" id="edit-main-category" name="main_category" required>
                                <option value="">Select Main Category</option>

                            </select>
                        </div>

                        <!-- Sub Category Dropdown -->
                        <div class="mb-3">
                            <label for="edit-sub-category" class="form-label">Sub Category:</label>
                            <select class="form-control" id="edit-sub-category" name="sub_category" required>
                                <option value="">Select Sub Category</option>
                                <!-- Sub-category options will be populated dynamically based on main category selection -->
                            </select>
                        </div>

                        <!-- Brand Dropdown -->
                        <div class="mb-3">
                            <label for="edit-brand" class="form-label">Brand:</label>
                            <select class="form-control" id="edit-brand" name="brand" required>
                                <option value="">Select Brand</option>
                                <!-- Brand options will be populated dynamically -->
                            </select>
                        </div>



                        <!-- Description -->
                        <div class="mb-3">
                            <label for="edit-description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit-description" rows="3" required></textarea>
                        </div>


                        <!-- Images -->
                        <div class="mb-3 text-center"">
                            <label class=" form-label">Current Images 1 </label><br>
                            <img id="image_one_preview" src="" alt="Image One"
                                style="max-width: 200px; margin-bottom: 5px;">
                            <br>
                            <label for="edit-image-one" class="form-label">Upload Image One</label>
                            <input type="file" class="form-control" id="edit-image-one" name="image_one">

                        </div>

                        <div class="mb-3 text-center">
                            <label class="form-label">Current Images 2 </label><br>
                            <img id="image_two_preview" src="" alt="Image Two"
                                style="max-width: 200px; margin-bottom: 5px;">
                            <br>
                            <label for="edit-image-two" class="form-label">Upload Image Two</label>
                            <input type="file" class="form-control" id="edit-image-two" name="image_two">

                        </div>

                        <div class="mb-3 text-center">
                            <label class="form-label">Current Images 3 </label><br>
                            <img id="image_three_preview" src="" alt="Image Three"
                                style="max-width: 200px; margin-bottom: 5px;">
                            <br>
                            <label for="edit-image-three" class="form-label">Upload Image Three</label>
                            <input type="file" class="form-control" id="edit-image-three" name="image_three">

                        </div>

                        <!-- Price Combinations Section -->
                        <div class="mb-3" id="edit-price-combinations">
                            <!-- Price combinations will be appended here dynamically -->
                        </div>

                        <div class="mb-3" id="combination-container">
                            <!-- Price combinations will be appended here dynamically -->
                        </div>




                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>







    <?php include 'include/footer.php'; ?>

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            var table = $('#data-table').DataTable({
                ajax: {
                    url: 'product-list.php',
                    type: 'POST',
                    data: function (d) {
                        var filter = $('#filter-dropdown').val();
                        if (filter) {
                            d.filter_by = filter;
                        }
                        d.action = 'fetch';
                    },
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'product_id' },
                    { data: 'product_name' },
                    { data: 'main_category' },
                    { data: 'sub_category' },
                    { data: 'brand_name' },
                    { data: 'description' },
                    { data: 'image', title: 'Image', orderable: false },
                    { data: 'actions' }, // Actions (Edit/Delete buttons)
                    { data: 'variant_button' }, // View Variants button
                    { data: 'extra_buttons' }
                ]
            });

            $('#filter-dropdown').change(function () {
                table.ajax.reload();
            });



            // Open modal and fetch variants
            $(document).on('click', '.variantBtn', function () {
                var productId = $(this).data('id');
                fetchVariants(productId);
                $('#variantModal').modal('show');
            });





            // Fetch variants for the product
            function fetchVariants(productId) {
                $.ajax({
                    url: 'product-list.php',  // A separate PHP file to fetch variants
                    type: 'POST',
                    data: { product_id: productId, action: 'fetchVariant' },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response)
                        var variants = response.data;
                        var tbody = $('#variant-table-body');
                        tbody.empty(); // Clear any previous rows
                        variants.forEach(function (variant) {
                            var row = '<tr>' +
                                '<td>' + variant.size_name + '</td>' +
                                '<td>' + variant.colour_name + '</td>' +
                                '<td>' + variant.price + '</td>' +
                                '</tr>';
                            tbody.append(row);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching variants:", error);
                        $('#variant-table-body').html('<tr><td colspan="3">Error fetching variants.</td></tr>');
                    }
                });
            }




            $(document).on('click', '.deleteBtn', function () {
                var productId = $(this).data('id');

                if (confirm('Are you sure you want to delete this product?')) {
                    $.ajax({
                        url: 'product-list.php',
                        type: 'POST',
                        data: { action: 'delete', product_id: productId },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                alert(response.message);
                                $('#data-table').DataTable().ajax.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error deleting product:", error);
                            alert("An error occurred while trying to delete the product.");
                        }
                    });
                }
            });


        });


        $(document).ready(function () {
            let colors;
            let sizes;

            $(document).on('click', '.editBtn', function () {
                var productId = $(this).data('id');

                $.ajax({
                    url: 'product-list.php',
                    type: 'POST',
                    data: { action: 'fetchSingle', product_id: productId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            colors = response.colors;
                            sizes = response.sizes;


                            var product = response.data;
                            var mainCategories = response.mainCategories;  // Main categories returned
                            var subCategories = response.subCategories;  // Sub categories returned


                            var priceCombinations = response.priceCombinations;
                            var brands = response.brands;




                            // Set product data in the modal
                            $('#edit-product-id').val(product.product_id);
                            $('#edit-product-name').val(product.product_name);
                            $('#edit-description').val(product.description);

                            // Set the Main Category dropdown options
                            $('#edit-main-category').empty();
                            $('#edit-main-category').append('<option value="">Select Main Category</option>');
                            $.each(mainCategories, function (index, mainCategory) {
                                var selected = product.main_category_id == mainCategory.cid ? 'selected' : '';
                                $('#edit-main-category').append('<option value="' + mainCategory.cid + '" ' + selected + '>' + mainCategory.main_category_name + '</option>');
                            });


                            // Set the Sub Category dropdown options
                            $('#edit-sub-category').empty();
                            $('#edit-sub-category').append('<option value="">Select Sub Category</option>');
                            $.each(subCategories, function (index, subCategory) {
                                var selected = product.sub_category_id == subCategory.sid ? 'selected' : '';
                                $('#edit-sub-category').append('<option value="' + subCategory.sid + '" ' + selected + '>' + subCategory.sub_category_name + '</option>');
                            });



                            $('#edit-brand').empty();
                            $('#edit-brand').append('<option value="">Select Brand</option>');

                            $.each(brands, function (index, brand) {
                                var selected = product.brand_id == brand.brand_id ? 'selected' : '';
                                $('#edit-brand').append('<option value="' + brand.brand_id + '" ' + selected + '>' + brand.brand_name + '</option>');
                            });



                            // Show the images in the modal
                            if (product.image_one) {
                                $('#image_one_preview').attr('src', product.image_one).show();
                            } else {
                                $('#image_one_preview').hide();
                            }

                            if (product.image_two) {
                                $('#image_two_preview').attr('src', product.image_two).show();
                            } else {
                                $('#image_two_preview').hide();
                            }

                            if (product.image_three) {
                                $('#image_three_preview').attr('src', product.image_three).show();
                            } else {
                                $('#image_three_preview').hide();
                            }




                            // Display price combinations dynamically
                            $('#edit-price-combinations').empty();
                            $('#combination-container').empty();
                            $.each(priceCombinations, function (index, combination) {
                                var sizeOption = sizes.find(size => size.sid == combination.size_id)?.size_name || '';
                                var colorOption = colors.find(color => color.cid == combination.colour_id)?.colour_name || '';

                                var combinationHtml = `
                                                    <div class="price-combination" data-id="${combination.combination_id}">
                                                        <input type="hidden" name="combinatio_id" value="${combination.combination_id}">
                                                        <div class="mb-3">
                                                            <label for="edit-size-${combination.combination_id}" class="form-label">Size:</label>
                                                            <select class="form-control edit-size" id="edit-size-${combination.combination_id}" name="size">
                                                                <option value="">Select Size</option>
                                                                ${sizes.map(size => `<option value="${size.sid}" ${size.sid == combination.size_id ? 'selected' : ''}>${size.size_name}</option>`).join('')}
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit-color-${combination.combination_id}" class="form-label">Color:</label>
                                                            <select class="form-control edit-color" id="edit-color-${combination.combination_id}" name="color">
                                                                <option value="">Select Color</option>
                                                                ${colors.map(color => `<option value="${color.cid}" ${color.cid == combination.colour_id ? 'selected' : ''}>${color.colour_name}</option>`).join('')}
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit-price-${combination.combination_id}" class="form-label">Price:</label>
                                                            <input type="text" class="form-control edit-price" id="edit-price-${combination.combination_id}" name="price" value="${combination.price}" required>
                                                        </div>


                                                         <button type="button" class="btn btn-danger remove-price-combination" data-id="${combination.combination_id}">Remove</button>
                                                    </div>
                                                `;
                                $('#edit-price-combinations').append(combinationHtml);
                            });
                            $('#combination-container').append(`<button type="button" class="btn btn-primary add-price-combination">Add Price Combination</button>`);

                            $('#editModal').modal('show');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching product details:", error);
                    }
                });
            });



            $(document).on('click', '.add-price-combination', function () {

                var newCombination = `<div class="price-combination">
                                       
                                        <div class="form-group">
                                            <label>Size:</label>
                                            <select name="size" class="form-control edit-size" required>
                                                <option value="">Select Size</option>
                                                ${sizes.map(size => `<option value="${size.sid}">${size.size_name}</option>`).join('')}
                                            </select>
                                        </div>
                                         <div class="form-group">
                                            <label>Colour:</label>
                                            <select name="colour" class="form-control edit-color" required>
                                                <option value="">Select Colour</option>
                                                ${colors.map(color => `<option value="${color.cid}">${color.colour_name}</option>`).join('')}
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Price:</label>
                                            <input type="number" step="0.01" name="price" class="form-control edit-price" required>
                                        </div>
                                        <button type="button" class="btn btn-danger remove-price-combination">Remove</button>
                                 </div> `;


                $('#edit-price-combinations').append(newCombination);
            });




            $(document).on('click', '.remove-price-combination', function () {
                $(this).closest('.price-combination').remove();
            });



            // Handle Main Category change (Dynamic Sub Category update)
            $('#edit-main-category').change(function () {
                var mainCategoryId = $(this).val();
                // var productId = $('#edit-product-id').val();


                if (mainCategoryId) {
                    $.ajax({
                        url: 'product-list.php',
                        type: 'POST',
                        data: { action: 'fetchSingle', main_category_id: mainCategoryId },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                var subCategories = response.subCategories;


                                var subCategoryDropdown = $('#edit-sub-category');

                                subCategoryDropdown.empty(); // Clear existing options
                                subCategoryDropdown.append('<option value="">Select Sub Category</option>');

                                $.each(response.subCategories, function (index, subCategory) {
                                    subCategoryDropdown.append('<option value="' + subCategory.sid + '">' + subCategory.sub_category_name + '</option>');
                                });

                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error fetching sub-categories:", error);
                        }
                    });
                }
            });


            // Handle Form Submission
            $('#editForm').on('submit', function (e) {
                e.preventDefault();

                // Create a new FormData object to hold form data including files
                var formData = new FormData(this);
                var productId = $('#edit-product-id').val();
                formData.append('action', 'updateProduct');
                formData.append('product_id', productId);
                formData.append('product_name', $('#edit-product-name').val());
                formData.append('description', $('#edit-description').val());



                // Collect price combinations data from the form (e.g., dynamically added size/colour/price inputs)
                var priceCombinations = [];
                $('.price-combination').each(function () {
                    var combinationId = $(this).data('id') || 0;
                    var sizeId = $(this).find('.edit-size').val();
                    var colorId = $(this).find('.edit-color').val();
                    var price = $(this).find('.edit-price').val();



                    if (sizeId && colorId && price) {
                        priceCombinations.push({
                            combination_id: combinationId,
                            size_id: sizeId,
                            color_id: colorId,
                            price: price
                        });
                    }
                });

                // Append the price combinations data to the FormData object
                formData.append('price_combinations', JSON.stringify(priceCombinations));





                $.ajax({
                    url: 'product-list.php',
                    type: 'POST',
                    data: formData,
                    processData: false,  // Prevent jQuery from automatically transforming the data
                    contentType: false,  // Let the browser handle the content-type header
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            $('#editModal').modal('hide');
                            $('#editForm')[0].reset();
                            $('#data-table').DataTable().ajax.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error updating product:", error);
                    }
                });
            });




        });








        $(document).ready(function () {
            $(document).on('click', '.featureBtn, .trendingBtn, .bestSellerBtn, .specialOfferBtn', function () {
                let button = $(this);
                let productId = button.data("id");
                let action = '';


                if (button.hasClass('featureBtn')) {
                    action = 'featured';
                } else if (button.hasClass('trendingBtn')) {
                    action = 'trending';
                } else if (button.hasClass('bestSellerBtn')) {
                    action = 'bestseller';
                } else if (button.hasClass('specialOfferBtn')) {
                    action = 'special';
                }


                $.ajax({
                    url: 'product-list.php',
                    type: 'POST',
                    data: { product_id: productId, action: action },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            if (action === 'featured') {
                                if (response.new_status == 1) {
                                    button.text("Remove Featured");
                                } else {
                                    button.text("Make Featured");
                                }
                            } else if (action === 'trending') {
                                if (response.new_status == 1) {
                                    button.text("Remove Trending");
                                } else {
                                    button.text("Make Trending");
                                }
                            } else if (action === 'bestseller') {
                                if (response.new_status == 1) {
                                    button.text("Remove Best Seller");
                                } else {
                                    button.text("Make Best Seller");
                                }
                            } else if (action === 'special') {
                                if (response.new_status == 1) {
                                    button.text("Remove Special Offer ");
                                } else {
                                    button.text("Make Special Offer ");
                                }
                            }
                        }
                    },
                    error: function () {
                        alert("Failed to update status");
                    }
                });
            });
        })


    </script>
</body>

</html>