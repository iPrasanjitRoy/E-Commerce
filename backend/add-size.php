<?php
include 'include/db-config.php';
$action = 'Add';

if (isset($_REQUEST['draw'])) {
    $start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
    $length = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 5;
    $searchValue = isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value'] : '';
    $orderColumn = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column'] : 0;
    $orderDir = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : 'asc';

    $columns = ['sid', 'size_name'];

    $orderBy = $columns[$orderColumn] . ' ' . $orderDir;

    $sql = "SELECT * FROM size WHERE size_name LIKE '%$searchValue%' ORDER BY $orderBy LIMIT $start, $length";
    $result = $conn->query($sql);

    /* GET THE TOTAL NUMBER OF RECORDS (NO FILTERS) */
    $totalQuery = "SELECT COUNT(*) AS total FROM size";
    $totalResult = $conn->query($totalQuery);
    $totalRecords = $totalResult->fetch_assoc()['total'];

    /* GET THE FILTERED NUMBER OF RECORDS */
    $filteredQuery = "SELECT COUNT(*) AS filtered FROM size WHERE size_name LIKE '%$searchValue%'";
    $filteredResult = $conn->query($filteredQuery);
    $filteredRecords = $filteredResult->fetch_assoc()['filtered'];

    // Prepare data for DataTables
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $rowData = [
            'serial_no' => $start + 1, // Serial number starts from 1
            'sid' => $row['sid'], // Size ID
            'size_name' => $row['size_name'],
            'action' => '<button class="btn btn-warning btn-edit" style="margin-right: 10px;" data-id="' . $row['sid'] . '" data-name="' . $row['size_name'] . '">Edit</button>' .
                '<button class="btn btn-danger btn-delete" data-id="' . $row['sid'] . '">Delete</button>',
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
    $sizeName = sanitizeInput($conn, $_POST['size_name']);

    /* IF $ID IS GREATER THAN 0, AN UPDATE QUERY IS EXECUTED TO MODIFY AN EXISTING RECORD WHERE THE sid EQUALS $ID */
    $common_sql = "`size_name` = '$sizeName'";
    if (intval($id) > 0) {
        $query = "UPDATE `size` SET $common_sql WHERE `sid` = '$id'";
        $message = "Size Updated Successfully";
    } else {
        $query = "INSERT INTO `size` SET $common_sql";
        $message = "Size Added Successfully";
    }

    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error']);
    }
    exit();
}

if (isset($_REQUEST['eid'])) {
    $eid = $_GET['eid'];
    $sql = "SELECT * FROM size WHERE sid = $eid";
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
    $query = "DELETE FROM size WHERE sid = '$id'";

    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'message' => 'Size Deleted Successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error Deleting Size.']);
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
                            <h4 class="card-title"><?= $action ?> Size</h4>
                        </div>

                        <div class="card-body">
                            <form id="size-form" method="POST">
                                <input type="hidden" name="id" value="<?= isset($data['sid']) ? $data['sid'] : '' ?>">

                                <div class="form-group">
                                    <label for="size-name">Size Name:</label>
                                    <input class="form-control" type="text" id="size-name" name="size_name"
                                        placeholder="Enter Size Name"
                                        value="<?= isset($data['size_name']) ? $data['size_name'] : '' ?>" required>
                                </div>

                                <br><br>
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-7 col-12">
                    <table id="data-table"></table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Edit Size -->
    <div class="modal fade" id="size-modal" tabindex="-1" role="dialog" aria-labelledby="size-modal-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="size-modal-label">Edit Size</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="size-modal-form">
                        <input type="hidden" id="modal-id" name="id">
                        <div class="form-group">
                            <label for="modal-size-name">Size Name:</label>
                            <input type="text" class="form-control" id="modal-size-name" name="size_name" required>
                        </div>
                        <br><br>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>

    <script>
        $(document).ready(function () {
            $('#data-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "add-size.php",
                    "type": "POST",
                    "data": function (d) {
                        d.draw = d.draw;
                        d.start = d.start;
                        d.length = d.length;
                        d.search = d.search;
                        d.order = d.order;
                    }
                },
                "columns": [
                    { "data": "serial_no", "title": "Serial No" },
                    { "data": "sid", "title": "ID", "visible": false },
                    { "data": "size_name", "title": "Size Name" },
                    { "data": "action", "title": "Actions" }
                ]
            });
        });

        $('#size-form').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize() + '&submit=true'; // Append submit=true for AJAX handling

            $.ajax({
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#message-container').text(response.message).css('color', 'green').show();
                        $('#data-table').DataTable().ajax.reload();
                        $('#size-name').val(''); // Clear the input field after submission
                    } else {
                        $('#message-container').text(response.message).css('color', 'red').show();
                    }
                },
                error: function () {
                    $('#message-container').text('Error occurred during submission.').css('color', 'red').show();
                }
            });
        });



        $('#data-table').on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            var sizeName = $(this).data('name');
            $('#modal-id').val(id);
            $('#modal-size-name').val(sizeName);
            $('#size-modal').modal('show');
        });

        $('#size-modal-form').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize() + '&submit=true';

            $.ajax({
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#message-container').text(response.message).css('color', 'green').show();
                        $('#size-modal').modal('hide');
                        $('#data-table').DataTable().ajax.reload();
                    } else {
                        $('#message-container').text(response.message).css('color', 'red').show();
                    }
                }
            });
        });

        $('#data-table').on('click', '.btn-delete', function () {
            if (confirm("Are you sure you want to delete this size?")) {
                var id = $(this).data('id');

                $.ajax({
                    type: 'POST',
                    url: 'add-size.php',
                    data: { action: 'delete', id: id },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#message-container').text(response.message).css('color', 'green').show();
                            $('#data-table').DataTable().ajax.reload();
                        } else {
                            $('#message-container').text(response.message).css('color', 'red').show();
                        }
                    }
                });
            }
        });
    </script>

</body>

</html>