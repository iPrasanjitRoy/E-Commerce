<?php
include 'include/db-config.php';

/* FETCH CATEGORIES FROM THE MAIN_CATEGORY TABLE */
$mainCategories = [];
$sql = "SELECT * FROM main_category ORDER BY main_category_name ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mainCategories[] = $row;
    }
}



/* Fetch Sub-Categories with Main Category Name for DataTable */
if (isset($_REQUEST['draw'])) {
    // Pagination parameters
    $draw = isset($_REQUEST['draw']) ? $_REQUEST['draw'] : 1; // Default to 1 if not set
    $start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
    $length = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 5;
    $searchValue = isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value'] : '';
    $orderColumn = isset($_REQUEST['order'][0]['column']) ? intval($_REQUEST['order'][0]['column']) : 0;
    $orderDir = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : 'asc';

    // Columns to order by
    $columns = ['sub_category_name', 'main_category_name'];  // Order by sub_category_name or main_category_name

    // Sanitize search input
    $searchValue = $conn->real_escape_string($searchValue);  // To prevent SQL Injection

    // Build the WHERE clause for search
    $whereClause = '';
    if ($searchValue) {
        $whereClause = "WHERE sub_category.sub_category_name LIKE '%$searchValue%' OR main_category.main_category_name LIKE '%$searchValue%'";
    }

    // Query to fetch sub-categories with their main category and apply search and ordering
    $query = "SELECT sub_category.sid, sub_category.sub_category_name, main_category.main_category_name 
              FROM sub_category 
              JOIN main_category ON sub_category.cid = main_category.cid
              $whereClause 
              ORDER BY " . $columns[$orderColumn] . " $orderDir
              LIMIT $start, $length";

    $result = $conn->query($query);

    // Prepare data for DataTable
    $data = [];
    if ($result->num_rows > 0) {
        $serialNo = $start + 1;
        while ($row = $result->fetch_assoc()) {
            // Sanitize data output
            $data[] = [
                'serial_no' => $serialNo++,
                'sub_category_name' => htmlspecialchars($row['sub_category_name'], ENT_QUOTES, 'UTF-8'),
                'main_category_name' => htmlspecialchars($row['main_category_name'], ENT_QUOTES, 'UTF-8'),
                'actions' => ' <button class="btn btn-warning btn-sm edit-btn mb-2" data-id="' . $row['sid'] . '" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>
                               <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row['sid'] . '">Delete</button>'
            ];
        }
    }

    // Query to count total records for pagination (without WHERE clause for search)
    $totalRecordsQuery = "SELECT COUNT(*) AS total FROM sub_category";
    $totalRecordsResult = $conn->query($totalRecordsQuery);
    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

    /* GET THE FILTERED NUMBER OF RECORDS */
    $filteredQuery = "SELECT COUNT(*) AS filtered FROM sub_category 
    JOIN main_category ON sub_category.cid = main_category.cid
    $whereClause";

    $filteredResult = $conn->query($filteredQuery);
    $filteredRecords = $filteredResult->fetch_assoc()['filtered'];

    // Prepare the response for DataTable
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $data
    ]);
    exit();
}



if (isset($_POST['submit'])) {
    $id = sanitizeInput($conn, $_POST['id']);
    $subCategoryName = sanitizeInput($conn, $_POST['sub_category_name']);
    $mainCategoryId = sanitizeInput($conn, $_POST['main_category']);

    $subCategoryUrl = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $subCategoryName));

    $oldImageQuery = "SELECT `sub_category_image` FROM `sub_category` WHERE `sid` = '$id'";
    $result = mysqli_query($conn, $oldImageQuery);

    if ($result && mysqli_num_rows($result) > 0) {
        $oldImage = mysqli_fetch_assoc($result)['sub_category_image'];
    } else {
        $oldImage = null;
    }



    $common_sql = "`sub_category_name` = '$subCategoryName', `cid` = '$mainCategoryId', `sub_category_url` = '$subCategoryUrl'";

    if (intval($id) > 0) {

        if (!empty($_FILES['edit_sub_category_image']['name']) && $_FILES['edit_sub_category_image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "uploads/sub_categories/";

            // Check if the directory exists, create if not
            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0777, true)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create directories']);
                    exit();
                }
            }

            $fileName = time() . '_' . basename($_FILES['edit_sub_category_image']['name']);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array(strtolower($fileType), $allowedTypes)) {
                if (move_uploaded_file($_FILES['edit_sub_category_image']['tmp_name'], $targetFilePath)) {

                    if (!empty($oldImage) && file_exists($oldImage)) {
                        unlink($oldImage);
                    }

                    $imagePath = $targetFilePath;
                    $common_sql .= ", `sub_category_image` = '$imagePath'";
                } else {
                    echo json_encode(['success' => false, 'message' => 'File upload failed']);
                    exit();
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid file type']);
                exit();
            }
        }

        $query = "UPDATE `sub_category` SET $common_sql WHERE `sid` = '$id'";
        $message = "Sub-category Updated Successfully";
    } else {
        $imagePath = null;

        if (isset($_FILES['sub_category_image']['name']) && $_FILES['sub_category_image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "uploads/sub_categories/";

            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0777, true)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create directories']);
                    exit();
                }
            }

            $fileName = time() . '_' . basename($_FILES['sub_category_image']['name']);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($fileType), $allowedTypes)) {
                if (move_uploaded_file($_FILES['sub_category_image']['tmp_name'], $targetFilePath)) {
                    $imagePath = $targetFilePath;
                } else {
                    echo json_encode(['success' => false, 'message' => 'File upload failed']);
                    exit();
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid file type']);
                exit();
            }
        }

        $query = "INSERT INTO `sub_category` (`sub_category_name`, `sub_category_url`,  `sub_category_image`, `cid`) 
                  VALUES ('$subCategoryName', '$subCategoryUrl', '$imagePath', '$mainCategoryId')";
        $message = "Sub-category Added Successfully";
    }


    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error']);
    }
    exit();
}




if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $subCategoryId = intval($_POST['id']);

    // Delete query to remove the sub-category
    $deleteQuery = "DELETE FROM `sub_category` WHERE `sid` = $subCategoryId";

    if ($conn->query($deleteQuery)) {
        echo json_encode(['success' => true, 'message' => 'Sub-Category Deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting sub-category.']);
    }
    exit();
}


if (isset($_POST['action']) && $_POST['action'] == 'fetch') {
    $subCategoryId = intval($_POST['id']);

    $fetchQuery = "SELECT * FROM sub_category WHERE sid = $subCategoryId";
    $result = $conn->query($fetchQuery);

    if ($result->num_rows > 0) {
        $subCategoryData = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $subCategoryData]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sub-category not found.']);
    }
    exit();
}



?>

<html>

<head>
    <?php include 'include/style.php'; ?>
</head>

<body>
    <?php include 'include/header.php'; ?>

    <!-- Message container for success and error -->
    <div id="message-container" style="display: none;"></div>

    <!-- Body: Body -->
    <div class="body d-flex py-3">
        <div class="container-xxl">
            <div class="row">
                <div class="col-md-5 col-12">

                    <div class="card">

                        <div class="card-header">
                            <h4 class="card-title">Add Sub-Category</h4>
                        </div>

                        <div class="card-body">

                            <form id="sub-category-form" method="POST">

                                <!-- Main Category Dropdown -->
                                <div class="form-group mb-3">
                                    <label for="main_category">Select Main Category:</label>
                                    <select class="form-select" id="main_category" name="main_category" required>
                                        <option value="">Select Main Category</option>
                                        <?php foreach ($mainCategories as $mainCategory) { ?>
                                            <option value="<?= $mainCategory['cid'] ?>">
                                                <?= $mainCategory['main_category_name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- Hidden ID Field (for Edit purposes) -->
                                <input type="hidden" id="sub_category_id" name="id" value="">

                                <!-- Sub-category Form initially hidden -->
                                <div id="sub-category-form-container" style="display: none;">

                                    <div class="form-group">
                                        <label for="sub-category-name">Sub-Category Name:</label>
                                        <input class="form-control" type="text" id="sub-category-name"
                                            name="sub_category_name" placeholder="Enter Sub-Category Name" required>
                                    </div>

                                    <br>

                                    <div class="form-group">
                                        <label for="subCategoryImage">Sub-Category Image</label>
                                        <input type="file" id="subCategoryImage" class="form-control"
                                            name="sub_category_image" accept="image/*">
                                    </div>

                                    <br>


                                    <button class="btn btn-primary" type="submit" name="submit">Add
                                        Sub-Category</button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>


                <div class="col-md-7 col-12">
                    <table id="data-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Sub Category Name</th>
                                <th>Main Category Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be inserted here by DataTable -->
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
                    <h5 class="modal-title" id="editModalLabel">Edit Sub-Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="edit-sub-category-form">

                    <div class="modal-body">
                        <!-- Hidden ID Field -->
                        <input type="hidden" id="edit_sub_category_id" name="id">

                        <!-- Main Category Dropdown -->
                        <div class="form-group">
                            <label for="edit_main_category">Select Main Category:</label>

                            <select class="form-control" id="edit_main_category" name="main_category" required>
                                <option value="">Select Main Category</option>
                                <?php foreach ($mainCategories as $mainCategory) { ?>
                                    <option value="<?= $mainCategory['cid'] ?>">
                                        <?= $mainCategory['main_category_name'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Sub-Category Name Input -->
                        <div class="form-group mt-3">
                            <label for="edit_sub_category_name">Sub-Category Name:</label>
                            <input class="form-control" type="text" id="edit_sub_category_name" name="sub_category_name"
                                required>
                        </div>

                        <!-- Display Sub-Category Image -->
                        <div class="form-group mt-3">
                            <label>Current Image:</label>
                            <div class="d-flex justify-content-center">
                                <img id="edit_sub_category_image_preview" src="" alt="Sub-Category Image"
                                    style="max-width: 200px;">
                            </div>
                        </div>
                        <br>

                        <div class="form-group">
                            <label for="editSubCategoryImage">Sub-Category Image</label>
                            <input type="file" id="editSubCategoryImage" class="form-control"
                                name="edit_sub_category_image" accept="image/*">
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>



    <?php include 'include/footer.php'; ?>

    <script>
        /* SHOW SUB-CATEGORY FORM WHEN A MAIN CATEGORY IS SELECTED */
        $('#main_category').on('change', function () {
            var mainCategoryId = $(this).val();

            if (mainCategoryId) {
                $('#sub-category-form-container').show();
            } else {
                $('#sub-category-form-container').hide();
            }
        });





        $(document).ready(function () {
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                "ajax": {
                    "type": "POST",
                },
                columns: [
                    { data: 'serial_no' },
                    { data: 'sub_category_name' },
                    { data: 'main_category_name' },
                    { data: 'actions', orderable: false, searchable: false }
                ],
            });
        });




        $('#sub-category-form').on('submit', function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('submit', 'true');

            $.ajax({
                type: 'POST',
                data: formData,
                processData: false, // Don't process the data
                contentType: false, // Don't set contentType
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#message-container').text(response.message).css('color', 'green').show();
                        $('#sub-category-form').trigger('reset');
                        $('#data-table').DataTable().ajax.reload();
                    } else {
                        $('#message-container').text(response.message).css('color', 'red').show();
                    }
                },
                error: function () {
                    $('#message-container').text('An error occurred while processing the request.').css('color', 'red').show();
                }
            });
        });

        $(document).ready(function () {
            $('#data-table').on('click', '.delete-btn', function () {
                const subCategoryId = $(this).data('id');

                if (confirm('Are you sure you want to delete this sub-category?')) {
                    $.ajax({
                        type: 'POST',
                        data: { action: 'delete', id: subCategoryId },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                alert(response.message);
                                $('#data-table').DataTable().ajax.reload();
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function () {
                            alert('An error occurred while processing the request.');
                        }
                    });
                }
            });
        });



        $('#data-table').on('click', '.edit-btn', function () {
            const subCategoryId = $(this).data('id');

            $.ajax({
                type: 'POST',
                data: { action: 'fetch', id: subCategoryId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#edit_sub_category_id').val(response.data.sid);
                        $('#edit_sub_category_name').val(response.data.sub_category_name);
                        $('#edit_main_category').val(response.data.cid).change();

                        if (response.data.sub_category_image) {
                            $('#edit_sub_category_image_preview').attr('src', response.data.sub_category_image);
                        } else {
                            $('#edit_sub_category_image_preview').attr('src', '');
                        }




                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('An error occurred while fetching sub-category data.');
                }
            });
        });





        $('#edit-sub-category-form').on('submit', function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('submit', 'true');


            $.ajax({
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#editModal').modal('hide');
                        $('#message-container').text(response.message).css('color', 'green').show();
                        $('#data-table').DataTable().ajax.reload();
                    } else {
                        $('#message-container').text(response.message).css('color', 'red').show();
                    }
                },
                error: function () {
                    $('#message-container').text('An error occurred while updating the sub-category.').css('color', 'red').show();
                }
            });
        });







    </script>

</body>

</html>