<?php
include 'include/db-config.php';


$brands = [];
$sql = "SELECT * FROM brands ORDER BY brand_name ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
}


$mainCategories = [];
$sql = "SELECT * FROM main_category ORDER BY main_category_name ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mainCategories[] = $row;
    }
}

$subCategories = [];

if (isset($_POST['main_category_id'])) {
    $mainCategoryId = intval($_POST['main_category_id']);

    $sql = "SELECT * FROM sub_category WHERE cid = $mainCategoryId ORDER BY sub_category_name ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $subCategories[] = $row;
        }
    }
    header('Content-Type: application/json');
    echo json_encode($subCategories);
    exit;
}



// Fetch Colours
$colours = [];
$sql = "SELECT * FROM colour ORDER BY colour_name ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $colours[] = $row;
    }
}

// Fetch Sizes
$sizes = [];
$sql = "SELECT * FROM `size` ORDER BY size_name ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sizes[] = $row;
    }
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    $product_name = $_POST['product_name'];
    $main_category_id = intval($_POST['main_category']);
    $sub_category_id = intval($_POST['sub_category']);
    $brand_id = intval($_POST['brand']);


    $description = $_POST['description'];
    $is_stock = isset($_POST['is_stock']) ? intval($_POST['is_stock']) : 0;

    $product_url = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product_name)));
    $product_url .= '-' . time();

    $size_ids = isset($_POST['size']) ? implode(',', $_POST['size']) : '';
    $colour_ids = isset($_POST['colour']) ? implode(',', $_POST['colour']) : '';



    // Handle image uploads
    $image_paths = [];
    $allowed_extensions = ['jpg', 'jpeg', 'png'];

    // Define the upload directory and full path.
    $upload_dir = 'uploads/';

    // Check if the directory exists, if not, create it
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create directory with proper permissions
    }

    for ($i = 1; $i <= 3; $i++) {
        $image_field = "product_image_{$i}";

        if (isset($_FILES[$image_field]) && $_FILES[$image_field]['error'] === 0) {

            if ($_FILES[$image_field]['size'] > 5000000) {  // 5MB
                $response['message'] = "Image {$i} exceeds the size limit of 5MB.";
                echo json_encode($response);
                exit;
            }

            // Get temporary file name, original file name, and file extension 
            $file_tmp = $_FILES[$image_field]['tmp_name'];
            $file_name = basename($_FILES[$image_field]['name']);
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            // Check if the file extension is allowed.
            if (in_array(strtolower($file_ext), $allowed_extensions)) {
                $new_file_name = uniqid() . '.' . $file_ext;

                $upload_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // If successful, add the file path to the array. 
                    $image_paths[] = $upload_path;
                } else {
                    $response['message'] = "Failed to upload image {$i}.";
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response['message'] = "Invalid file type for image {$i}.";
                echo json_encode($response);
                exit;
            }
        }
    }


    // Insert into product table 
    $query = "INSERT INTO product (product_name, main_category_id, sub_category_id, `description`, is_stock, image_one, image_two, image_three, product_url, brand_id, size_ids, colour_ids) 
              VALUES ('$product_name', $main_category_id, $sub_category_id, '$description', $is_stock, 
                      '" . (isset($image_paths[0]) ? $image_paths[0] : "") . "',
                      '" . (isset($image_paths[1]) ? $image_paths[1] : "") . "',
                      '" . (isset($image_paths[2]) ? $image_paths[2] : "") . "',
                     '$product_url', '$brand_id', '$size_ids', '$colour_ids')";



    if ($conn->query($query) === TRUE) {
        // If successful, get the product ID of the inserted product.
        $product_id = $conn->insert_id;

        // Insert combinations (sizes, colors, prices)
        if (isset($_POST['colour'], $_POST['size'], $_POST['price'])) {
            $colours = $_POST['colour'];
            $sizes = $_POST['size'];
            $prices = $_POST['price'];

            for ($i = 0; $i < count($colours); $i++) {
                $colour_id = intval($colours[$i]);
                $size_id = intval($sizes[$i]);
                $price = floatval($prices[$i]);

                // Insert into product_combinations table
                $comb_query = "INSERT INTO price_combination (product_id, size_id, colour_id, price) 
                               VALUES ($product_id, $size_id, $colour_id, $price)";
                $conn->query($comb_query);
            }
        }

        $response['success'] = true;
        $response['message'] = 'Product added successfully!';
    } else {
        $response['message'] = 'Database error: ' . $conn->error;
    }
    echo json_encode($response);
    exit();

}
?>

<html>

<head>
    <?php include 'include/style.php'; ?>
</head>

<body>
    <?php include 'include/header.php'; ?>

    <div class="container mt-5">
        <h3>Add New Product</h3>

        <form id="product-form" enctype="multipart/form-data" method="POST">

            <!-- Product Name -->
            <div class="form-group">
                <label for="product-name">Product Name:</label>
                <input type="text" id="product-name" name="product_name" class="form-control" required>
            </div>

            <!-- Main Category Dropdown -->
            <div class="form-group">
                <label for="main-category">Main Category:</label>
                <select id="main-category" name="main_category" class="form-control" required>
                    <option value="">Select Main Category</option>
                    <?php foreach ($mainCategories as $category) { ?>
                        <option value="<?= $category['cid'] ?>"><?= $category['main_category_name'] ?></option>
                    <?php } ?>
                </select>
            </div>


            <!-- Sub Category Dropdown -->
            <div class="form-group">
                <label for="sub-category">Sub Category:</label>
                <select id="sub-category" name="sub_category" class="form-control" required>
                    <option value="">Select Sub Category</option>
                </select>
            </div>

            <!-- Brand Dropdown -->
            <div class="form-group">
                <label for="brand">Brand:</label>
                <select id="brand" name="brand" class="form-control" required>
                    <option value="">Select Brand</option>
                    <?php foreach ($brands as $brand) { ?>
                        <option value="<?= $brand['brand_id'] ?>"><?= $brand['brand_name'] ?></option>
                    <?php } ?>
                </select>
            </div>



            <!-- Description -->
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" class="form-control" rows="4"></textarea>
            </div>

            <!-- <div class="form-group">
                <label for="is_stock"> Stock Availability : </label>
                <select id="is_stock" name="is_stock" class="form-control">
                    <option value=""> Select Stock Availability </option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div> -->

            <div class="form-group">
                <label>Stock Availability:</label>
                <div>
                    <input type="radio" id="stock_yes" name="is_stock" value="1" class="form-check-input">
                    <label for="stock_yes" class="form-check-label">Yes</label>

                    <input type="radio" id="stock_no" name="is_stock" value="0" class="form-check-input">
                    <label for="stock_no" class="form-check-label">No</label>
                </div>
            </div>


            <!-- Dynamic Size & Colour Combinations -->
            <div id="combination-container">

                <div class="form-group">
                    <label for="colour">Colour:</label>
                    <select id="colour" name="colour[]" class="form-control" required>
                        <option value="">Select Colour</option>
                        <?php foreach ($colours as $colour) { ?>
                            <option value="<?= $colour['cid'] ?>"><?= $colour['colour_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>


                <div class="form-group">
                    <label for="size">Size:</label>
                    <select id="size" name="size[]" class="form-control" required>
                        <option value="">Select Size</option>
                        <?php foreach ($sizes as $size) { ?>
                            <option value="<?= $size['sid'] ?>"><?= $size['size_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>


                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" step="0.01" name="price[]" class="form-control" required>
                </div>

            </div>

            <!-- Add More Combinations Button -->
            <button type="button" id="add-more" class="btn btn-secondary">Add More Combinations</button>


            <!-- Image Upload -->
            <div class="form-group">
                <label for="product-image">Product Image:</label>
                <input type="file" id="product-image-1" name="product_image_1" class="form-control" accept="image/*">
            </div>

            <!-- Image Upload -->
            <div class="form-group">
                <label for="product-image">Product Image:</label>
                <input type="file" id="product-image-2" name="product_image_2" class="form-control" accept="image/*">
            </div>

            <!-- Image Upload -->
            <div class="form-group">
                <label for="product-image">Product Image:</label>
                <input type="file" id="product-image-3" name="product_image_3" class="form-control" accept="image/*">
            </div>


            <button type="submit" name="submit" class="btn btn-primary mt-3">Add Product</button>
        </form>
    </div>

    <script>
        /* POPULATE SUB CATEGORY BASED ON MAIN CATEGORY SELECTION */
        $('#main-category').change(function (e) {
            e.preventDefault();
            var mainCategoryId = $(this).val();


            $('#sub-category').empty().append('<option value="">Select Sub Category</option>');

            if (mainCategoryId) {
                $.ajax({
                    url: 'product-entry-page.php',
                    type: 'POST',
                    data: { main_category_id: mainCategoryId },
                    dataType: 'json',
                    success: function (response) {
                        var subCategories = response;
                        subCategories.forEach(function (subCategory) {
                            $('#sub-category').append('<option value="' + subCategory.sid + '">' + subCategory.sub_category_name + '</option>');
                        });
                    }
                });
            }
        });





        // Add More Size and Colour Combinations Dynamically
        $('#add-more').click(function () {
            var newCombination = `
            <div class="form-group">
                <label for="colour">Colour:</label>
                <select name="colour[]" class="form-control" required>
                    <option value="">Select Colour</option>
                    <?php foreach ($colours as $colour) { ?>
                                                                                                                                                                                                                                                                                                                    <option value="<?= $colour['cid'] ?>"><?= $colour['colour_name'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="size">Size:</label>
                <select name="size[]" class="form-control" required>
                    <option value="">Select Size</option>
                    <?php foreach ($sizes as $size) { ?>
                                                                                                                                                                                                                                                                                                                    <option value="<?= $size['sid'] ?>"><?= $size['size_name'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" step="0.01" name="price[]" class="form-control" required>
            </div>
            `;
            $('#combination-container').append(newCombination);
        });


        // Handle Form Submission via AJAX
        $('#product-form').submit(function (e) {
            e.preventDefault();

            var formData = new FormData(this); // Collect form data, including files
            /* THE SERIALIZE() METHOD ONLY SERIALIZES FORM DATA (LIKE TEXT FIELDS, CHECKBOXES, AND SELECT OPTIONS), BUT IT DOES NOT HANDLE FILE UPLOADS DIRECTLY. */
            // var serializedData = $('#product-form').serialize();

            /* BY DEFAULT, WHEN YOU SEND DATA WITH $.AJAX(), JQUERY AUTOMATICALLY CONVERTS THE DATA INTO A URL-ENCODED QUERY STRING (E.G., KEY1=VALUE1&KEY2=VALUE2).  */
            $.ajax({
                url: 'product-entry-page',
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from converting the data into a query string
                contentType: false, // Prevent jQuery from setting the content type
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Product Added successfully!');
                        $('#product-form')[0].reset();
                        $('#sub-category').empty().append('<option value="">Select Sub Category</option>');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);  // Log the response to inspect it
                    alert('Something went wrong. Please try again.');
                }
            });
        });


    </script>


</body>

</html>