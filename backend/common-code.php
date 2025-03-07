<?php

include 'include/db-config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetchMainCategories') {

    $sql = "SELECT * FROM main_category ORDER BY main_category_name ASC";

    $result = $conn->query($sql);

    $mainCategories = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $mainCategories[] = $row;
        }
        echo json_encode(['status' => 'success', 'mainCategories' => $mainCategories]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No main categories found']);
    }

    $conn->close();

}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetchSubCategories' && isset($_POST['main_category_id'])) {

    $mainCategoryId = $_POST['main_category_id'];

    $sql = "SELECT * FROM sub_category WHERE cid = '$mainCategoryId'";

    $result = $conn->query($sql);

    $subCategories = [];

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            $subCategories[] = $row;
        }
        echo json_encode(['status' => 'success', 'subCategories' => $subCategories]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No subcategories found for the selected main category']);
    }

    $conn->close();
}





if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetchPriceCombinations' && isset($_POST['product_id'])) {

    $productId = $conn->real_escape_string($_POST['product_id']);

    $sql = "SELECT pc.combination_id, pc.size_id, pc.colour_id, pc.price, 
                   s.size_name, c.colour_name 
            FROM price_combination pc
            LEFT JOIN `size` s ON pc.size_id = s.sid
            LEFT JOIN colour c ON pc.colour_id = c.cid
            WHERE pc.product_id = '$productId'
            ORDER BY s.size_name ASC, c.colour_name ASC";

    $result = $conn->query($sql);

    $priceCombinations = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $priceCombinations[] = $row;
        }
        echo json_encode(['status' => 'success', 'priceCombinations' => $priceCombinations]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No price combinations found for the selected product']);
    }

    $conn->close();
}

?>