<?php
include 'include/db-config.php';
$action = 'Add';

if (isset($_REQUEST['draw'])) {
    $start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
    $length = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 5;
    $searchValue = isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value'] : '';
    $orderColumn = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column'] : 0;
    $orderDir = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : 'asc';

    $columns = ['brand_id', 'brand_name'];

    $orderBy = $columns[$orderColumn] . ' ' . $orderDir;

    $sql = "SELECT * FROM brands WHERE brand_name LIKE '%$searchValue%' ORDER BY $orderBy LIMIT $start, $length";
    $result = $conn->query($sql);

    $totalQuery = "SELECT COUNT(*) AS total FROM brands";
    $totalResult = $conn->query($totalQuery);
    $totalRecords = $totalResult->fetch_assoc()['total'];

    $filteredQuery = "SELECT COUNT(*) AS filtered FROM brands WHERE brand_name LIKE '%$searchValue%'";
    $filteredResult = $conn->query($filteredQuery);
    $filteredRecords = $filteredResult->fetch_assoc()['filtered'];

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $rowData = [
            'serial_no' => $start + 1,
            'brand_id' => $row['brand_id'],
            'brand_name' => $row['brand_name'],
            'action' => '<button class="btn btn-warning btn-edit" style="margin-right: 10px;" data-id="' . $row['brand_id'] . '">Edit</button>' .
                '<button class="btn btn-danger btn-delete" data-id="' . $row['brand_id'] . '">Delete</button>',
        ];
        $data[] = $rowData;
        $start++;
    }

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
    $brandName = sanitizeInput($conn, $_POST['brand_name']);


    $brandUrl = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $brandName));




    $oldImageQuery = "SELECT brand_image FROM brands WHERE brand_id = '$id'";
    $result = mysqli_query($conn, $oldImageQuery);
    $oldImage = '';

    $oldImage = '';
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $oldImage = $row['brand_image'];

    }

    $common_sql = "brand_name = '$brandName', brand_url = '$brandUrl'";



    if (intval($id) > 0) {
        if (!empty($_FILES['brand_image']['name']) && $_FILES['brand_image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "uploads/brands/";
            if (!is_dir($targetDir))
                mkdir($targetDir, 0777, true);

            $fileName = time() . '_' . basename($_FILES['brand_image']['name']);
            $targetFilePath = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($_FILES['brand_image']['tmp_name'], $targetFilePath)) {
                    if ($oldImage && file_exists($oldImage))
                        unlink($oldImage);
                    $common_sql .= ", brand_image = '$targetFilePath'";
                }
            }
        }

        $query = "UPDATE brands SET $common_sql WHERE brand_id = '$id'";
        $message = "Brand Updated Successfully";
    } else {
        $imagePath = null;
        if (!empty($_FILES['brand_image']['name']) && $_FILES['brand_image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "uploads/brands/";
            if (!is_dir($targetDir))
                mkdir($targetDir, 0777, true);

            $fileName = time() . '_' . basename($_FILES['brand_image']['name']);
            $targetFilePath = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($_FILES['brand_image']['tmp_name'], $targetFilePath)) {
                    $imagePath = $targetFilePath;
                }
            }
        }

        $query = "INSERT INTO brands (brand_name, brand_url, brand_image) VALUES ('$brandName', '$brandUrl', " . ($imagePath ? "'$imagePath'" : "NULL") . ")";
        $message = "Brand Added Successfully";
    }

    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
    exit();
}

$data = [];

if (isset($_REQUEST['eid'])) {
    $eid = $_GET['eid'];
    $sql = "SELECT * FROM brands WHERE brand_id = $eid";
    $result = $conn->query($sql);

    if ($result->num_rows) {
        $data = $result->fetch_assoc();
        $action = 'Update';
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = sanitizeInput($conn, $_POST['id']);

    $oldImageQuery = "SELECT brand_image FROM brands WHERE brand_id = '$id'";
    $result = mysqli_query($conn, $oldImageQuery);

    $oldImage = '';
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $oldImage = $row['brand_image'];

    }


    $query = "DELETE FROM brands WHERE brand_id = '$id'";

    if ($conn->query($query)) {

        if ($oldImage && file_exists($oldImage))
            unlink($oldImage);

        echo json_encode(['success' => true, 'message' => 'Brand Deleted Successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error Deleting Brand.']);
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

    <div id="message-container" style="display: none;"></div>

    <div class="body d-flex py-3">
        <div class="container-xxl">
            <div class="row">
                <div class="col-md-5 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><?= $action ?> Brand</h4>
                        </div>
                        <div class="card-body">
                            <form id="brand-form" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= $data['brand_id'] ?? '' ?>">

                                <div class="form-group">
                                    <label>Brand Name:</label>
                                    <input class="form-control" type="text" name="brand_name"
                                        value="<?= $data['brand_name'] ?? '' ?>" required>
                                </div>

                                <div class="form-group mt-3">
                                    <label>Brand Image:</label>
                                    <?php if (isset($data['brand_image']) && $data['brand_image']): ?>
                                        <img src="<?= $data['brand_image'] ?>" class="d-block mb-2"
                                            style="max-width: 200px;">
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="brand_image">
                                </div>

                                <button class="btn btn-primary mt-3" type="submit">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-7 col-12">
                    <table id="data-table" class="table table-bordered"></table>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>

    <script>
        $(document).ready(function () {
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: { url: "add-brand.php", type: "POST" },
                columns: [
                    { data: "serial_no", title: "Serial No" },
                    { data: "brand_id", visible: false },
                    { data: "brand_name", title: "Brand Name" },
                    { data: "action", title: "Actions" }
                ]
            });

            $('#data-table').on('click', '.btn-edit', function () {
                window.location.href = '?eid=' + $(this).data('id');
            });

            $('#data-table').on('click', '.btn-delete', function () {
                if (confirm("Delete this brand?")) {
                    $.ajax({
                        type: 'POST',
                        url: 'add-brand.php',
                        data: { action: 'delete', id: $(this).data('id') },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                $('#data-table').DataTable().ajax.reload();
                                window.location.href = 'add-brand.php';
                            } else {
                                alert(response.message);
                            }
                        }
                    });
                }
            });

            $('#brand-form').on('submit', function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                formData.append('submit', 'true');

                $.ajax({
                    type: 'POST',
                    url: 'add-brand.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            window.location.href = 'add-brand.php';
                        }
                        alert(response.message);
                    }
                });
            });
        });
    </script>
</body>

</html>