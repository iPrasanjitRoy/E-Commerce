<?php
include 'include/db-config.php';
$action = 'Add';

if (isset($_REQUEST['draw'])) {
    $start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
    $length = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 5;
    $searchValue = isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value'] : '';
    $orderColumn = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column'] : 0;
    $orderDir = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : 'asc';

    $columns = ['cid', 'main_category_name'];

    $orderBy = $columns[$orderColumn] . ' ' . $orderDir;

    $sql = "SELECT * FROM main_category WHERE main_category_name LIKE '%$searchValue%' ORDER BY $orderBy LIMIT $start,
            $length";
    $result = $conn->query($sql);

    /*  GET THE TOTAL NUMBER OF RECORDS (NO FILTERS) */
    $totalQuery = "SELECT COUNT(*) AS total FROM main_category";
    $totalResult = $conn->query($totalQuery);
    $totalRecords = $totalResult->fetch_assoc()['total'];

    /* GET THE FILTERED NUMBER OF RECORDS */
    $filteredQuery = "SELECT COUNT(*) AS filtered FROM main_category WHERE main_category_name LIKE '%$searchValue%'";
    $filteredResult = $conn->query($filteredQuery);
    $filteredRecords = $filteredResult->fetch_assoc()['filtered'];

    // Prepare data for DataTables
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $rowData = [
            'serial_no' => $start + 1, // Serial number starts from 1
            'cid' => $row['cid'], // Category ID
            'main_category_name' => $row['main_category_name'],
            'action' => '<button class="btn btn-warning btn-edit" style="margin-right: 10px;" data-id="' . $row['cid'] . '">Edit</button>' .
                '<button class="btn btn-danger btn-delete" data-id="' . $row['cid'] . '">Delete</button>',
        ];


        $data[] = $rowData;
        $start++; // Increment serial number for the next record
    }

    // Return data as JSON
    echo json_encode([
        "draw" => intval($_REQUEST['draw']),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $data
    ]);
    exit();
}


if (isset($_POST['submit'])) {
    $id = sanitizeInput($conn, $_POST['id']);
    $mainCategory = sanitizeInput($conn, $_POST['main_category']);

    $mainCategoryUrl = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $mainCategory));


    $oldImageQuery = "SELECT `main_category_image` FROM `main_category` WHERE `cid` = '$id'";
    $result = mysqli_query($conn, $oldImageQuery);
    $oldImage = mysqli_fetch_assoc($result)['main_category_image'];


    /*  IF $ID IS GREATER THAN 0, AN UPDATE QUERY IS EXECUTED TO MODIFY AN EXISTING RECORD WHERE THE CID EQUALS $ID */
    $common_sql = "`main_category_name` = '$mainCategory', `main_category_url` = '$mainCategoryUrl'";



    if (intval($id) > 0) {

        if (!empty($_FILES['main_category_image']['name']) && $_FILES['main_category_image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "uploads/categories/";

            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0777, true)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create directories']);
                    exit();
                }
            }

            $fileName = time() . '_' . basename($_FILES['main_category_image']['name']);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array(strtolower($fileType), $allowedTypes)) {
                if (move_uploaded_file($_FILES['main_category_image']['tmp_name'], $targetFilePath)) {

                    if (!empty($oldImage) && file_exists($oldImage)) {
                        unlink($oldImage);
                    }

                    $imagePath = $targetFilePath;
                    $common_sql .= ", `main_category_image` = '$imagePath'";
                } else {
                    echo json_encode(['success' => false, 'message' => 'File upload failed']);
                    exit();
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid file type']);
                exit();
            }
        }

        $query = "UPDATE `main_category` SET $common_sql WHERE `cid` = '$id'";
        $message = "Category Update Successfully";
    } else {
        $imagePath = null;

        if (isset($_FILES['main_category_image']['name']) && $_FILES['main_category_image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "uploads/categories/";

            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0777, true)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create directories']);
                    exit();
                }
            }

            $fileName = time() . '_' . basename($_FILES['main_category_image']['name']);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($fileType), $allowedTypes)) {
                if (move_uploaded_file($_FILES['main_category_image']['tmp_name'], $targetFilePath)) {
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


        $mainCategoryUrl = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $mainCategory));

        $query = "INSERT INTO `main_category` (`main_category_name`, `main_category_url`, `main_category_image`) VALUES ('$mainCategory', '$mainCategoryUrl', '$imagePath')";

        $message = "Category Added Successfully";
    }


    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        echo json_encode(['success' => true, 'message' => 'Error']);
    }
    exit();
}




if (isset($_REQUEST['eid'])) {
    $eid = $_GET['eid'];
    $sql = "SELECT * FROM main_category WHERE cid = $eid";
    $sql = $conn->query($sql);

    if ($sql->num_rows) {
        $data = $sql->fetch_assoc();
        $action = 'Update';

    } else {
        echo "No Data Found";
        exit();
    }

}


if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = sanitizeInput($conn, $_POST['id']);
    $query = "DELETE FROM main_category WHERE cid = '$id'";

    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'message' => 'Category Deleted Successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error Deleting Category.']);
    }
    exit();
}


?>


<html>

<head>
    <?php
    include 'include/style.php';
    ?>
</head>

<body>
    <?php
    include 'include/header.php';
    ?>

    <!-- Message container for success and error -->
    <div id="message-container" style="display: none;"></div>

    <!-- Body: Body -->
    <div class="body d-flex py-3">
        <div class="container-xxl">
            <div class="row">
                <div class="col-md-5 col-12">
                    <div class="card">

                        <div class="card-header">
                            <h4 class="card-title"><?= $action ?> Main Category</h4>
                        </div>

                        <div class="card-body">
                            <form id="main-category-form" method="POST">
                                <input type="hidden" name="id" value="<?= isset($data['cid']) ? $data['cid'] : '' ?> ">

                                <div class="form-group">
                                    <label for="main-category">Main Category:</label>
                                    <input class="form-control" type="text" id="main-category" name="main_category"
                                        placeholder="Enter Main Category"
                                        value="<?= isset($data['main_category_name']) ? $data['main_category_name'] : '' ?> "
                                        required>
                                </div>


                                <div class="form-group">
                                    <label for="main_category_image">Category Image:</label>
                                    <br><br>
                                    <?php if (isset($data['main_category_image']) && !empty($data['main_category_image'])): ?>
                                        <!-- Display existing image if available -->
                                        <img src="<?= $data['main_category_image'] ?>" alt="Category Image"
                                            style="max-width: 200px; margin-bottom: 10px;">
                                    <?php endif; ?>

                                    <!-- File input for new image upload -->
                                    <input type="file" class="form-control" name="main_category_image">
                                </div>

                                <br><br>
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </form>
                        </div>

                    </div>
                </div>


                <div class="col-md-7 col-12">
                    <table id="data-table" class="table table-bordered">


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
            $('#data-table').DataTable({
                "processing": true,  // Shows the processing indicator
                "serverSide": true,  // Enables server-side processing
                "ajax": {
                    "url": "add-main-category.php",  // Current page URL
                    "type": "POST",
                    "data": function (d) {
                        // Send extra parameters (like draw, start, length) to the server
                        d.draw = d.draw;
                        d.start = d.start;
                        d.length = d.length;
                        d.search = d.search;
                        d.order = d.order;
                    }
                },
                "columns": [
                    // Serial No Column
                    {
                        "data": "serial_no",
                        "title": "Serial No"

                    },
                    {
                        "data": "cid",
                        "title": "ID",
                        "visible": false
                    },
                    {
                        "data": "main_category_name",
                        "title": "Main Category"
                    },
                    {
                        "data": "action",
                        "title": "Actions"
                    }
                ]
            });
        });



        // Handle Edit Button Click
        $('#data-table').on('click', '.btn-edit', function () {
            var id = $(this).data('id');



            window.location.href = '?eid=' + id;
        });






        // Handle Delete Button Click
        $('#data-table').on('click', '.btn-delete', function () {
            var id = $(this).data('id');
            if (confirm("Are you sure you want to delete this category?")) {
                $.ajax({
                    type: 'POST',
                    url: 'add-main-category.php',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    dataType: 'json', /*  EXPECT JSON RESPONSE FROM THE SERVER */
                    success: function (response) {
                        console.log(response);
                        if (response.success) {
                            alert('Category Deleted successfully!');
                            $('#data-table').DataTable().ajax.reload();
                        } else {
                            alert('Error Deleting Category.');
                        }
                    },
                    error: function () {
                        alert('Error Communicating With The Server.');
                    }
                });
            }
        });



        $('#main-category-form').on('submit', function (e) {
            e.preventDefault();

            // Create FormData object to include file uploads
            const formData = new FormData(this);
            formData.append('submit', 'true'); // Ensure the PHP script processes the form

            $.ajax({
                type: 'POST',
                data: formData,
                contentType: false, // Required for file uploads
                processData: false, // Required for FormData
                dataType: 'json', /*  EXPECT JSON RESPONSE FROM THE SERVER */
                success: function (response) {
                    if (response.success) {


                        $('input[name="main_category_name"]').val('');
                        $('img').remove();


                        $('#message-container').text(response.message).css('color', 'green').show();
                        $('#data-table').DataTable().ajax.reload();

                        window.location.href = 'add-main-category.php';



                    } else {
                        $('#message-container').text(response.message).css('color', 'red').show();
                    }
                }

            });
        });





    </script>
</body>

</html>