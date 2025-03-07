<?php
include 'include/db-config.php';
$action = 'Add';

if (isset($_REQUEST['draw'])) {
    $start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
    $length = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 5;
    $searchValue = isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value'] : '';
    $orderColumn = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column'] : 0;
    $orderDir = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : 'asc';

    $columns = ['cid', 'colour_name'];

    $orderBy = $columns[$orderColumn] . ' ' . $orderDir;

    $sql = "SELECT * FROM colour WHERE colour_name LIKE '%$searchValue%' ORDER BY $orderBy LIMIT $start, $length";
    $result = $conn->query($sql);

    /* GET THE TOTAL NUMBER OF RECORDS (NO FILTERS) */
    $totalQuery = "SELECT COUNT(*) AS total FROM colour";
    $totalResult = $conn->query($totalQuery);
    $totalRecords = $totalResult->fetch_assoc()['total'];

    /* GET THE FILTERED NUMBER OF RECORDS */
    $filteredQuery = "SELECT COUNT(*) AS filtered FROM colour WHERE colour_name LIKE '%$searchValue%'";
    $filteredResult = $conn->query($filteredQuery);
    $filteredRecords = $filteredResult->fetch_assoc()['filtered'];

    // Prepare data for DataTables
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $rowData = [
            'serial_no' => $start + 1, // Serial number starts from 1
            'cid' => $row['cid'], // Colour ID
            'colour_name' => $row['colour_name'],
            'action' => '<button class="btn btn-warning btn-edit" style="margin-right: 10px;" data-id="' . $row['cid'] . '" data-name="' . $row['colour_name'] . '">Edit</button>' .
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
    $colourName = sanitizeInput($conn, $_POST['colour_name']);

    /* IF $ID IS GREATER THAN 0, AN UPDATE QUERY IS EXECUTED TO MODIFY AN EXISTING RECORD WHERE THE CID EQUALS $ID */
    $common_sql = "`colour_name` = '$colourName'";
    if (intval($id) > 0) {
        $query = "UPDATE `colour` SET $common_sql WHERE `cid` = '$id'";
        $message = "Colour Updated Successfully";
    } else {
        $query = "INSERT INTO `colour` SET $common_sql";
        $message = "Colour Added Successfully";
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
    $sql = "SELECT * FROM colour WHERE cid = $eid";
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
    $query = "DELETE FROM colour WHERE cid = '$id'";

    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'message' => 'Colour Deleted Successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error Deleting Colour.']);
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
                            <h4 class="card-title"><?= $action ?> Colour</h4>
                        </div>

                        <div class="card-body">
                            <form id="colour-form" method="POST">
                                <input type="hidden" name="id" value="<?= isset($data['cid']) ? $data['cid'] : '' ?>">

                                <div class="form-group">
                                    <label for="colour-name">Colour Name:</label>
                                    <input class="form-control" type="text" id="colour-name" name="colour_name"
                                        placeholder="Enter Colour Name"
                                        value="<?= isset($data['colour_name']) ? $data['colour_name'] : '' ?>" required>
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

    <!-- Modal for Edit Colour -->
    <div class="modal fade" id="colour-modal" tabindex="-1" role="dialog" aria-labelledby="colour-modal-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="colour-modal-label">Edit Colour</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="colour-modal-form">
                        <input type="hidden" id="modal-id" name="id">
                        <div class="form-group">
                            <label for="modal-colour-name">Colour Name:</label>
                            <input type="text" class="form-control" id="modal-colour-name" name="colour_name" required>
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
                    "url": "add-colour.php",
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
                    { "data": "cid", "title": "ID", "visible": false },
                    { "data": "colour_name", "title": "Colour Name" },
                    { "data": "action", "title": "Actions" }
                ]
            });
        });

        $('#data-table').on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            var colourName = $(this).data('name');
            $('#modal-id').val(id);
            $('#modal-colour-name').val(colourName);
            $('#colour-modal').modal('show');
        });

        $('#colour-modal-form').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize() + '&submit=true';

            $.ajax({
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#message-container').text(response.message).css('color', 'green').show();
                        $('#colour-modal').modal('hide');
                        $('#data-table').DataTable().ajax.reload();
                    } else {
                        $('#message-container').text(response.message).css('color', 'red').show();
                    }
                }
            });
        });

        $('#data-table').on('click', '.btn-delete', function () {
            var id = $(this).data('id');
            if (confirm("Are you sure you want to delete this colour?")) {
                $.ajax({
                    type: 'POST',
                    url: 'add-colour.php',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert('Colour Deleted successfully!');
                            $('#data-table').DataTable().ajax.reload();
                        } else {
                            alert('Error Deleting Colour.');
                        }
                    },
                    error: function () {
                        alert('Error Communicating With The Server.');
                    }
                });
            }
        });

        $('#colour-form').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize() + '&submit=true';

            $.ajax({
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#message-container').text(response.message).css('color', 'green').show();
                        $('#colour-form').trigger('reset');
                        $('#data-table').DataTable().ajax.reload();
                    } else {
                        $('#message-container').text(response.message).css('color', 'red').show();
                    }
                }
            });
        });
    </script>
</body>

</html>