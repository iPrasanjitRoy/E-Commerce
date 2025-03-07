<?php
include 'only-admin.php';
$action = isset($_POST['action']) ? $_POST['action'] : '';


if ($action == 'add') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $password = md5($_POST['password']);

    $sql = "INSERT INTO `admin` (first_name, last_name, email, phone, `role`, `password`) 
            VALUES ('$first_name', '$last_name', '$email', '$phone', '$role', '$password')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success", "message" => "Employee Added Successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed To Add Employee "]);
    }

    exit();
}


if ($action == 'fetch') {
    $sql = "SELECT id, first_name, last_name, email, phone, `role`  FROM `admin` ORDER BY id ASC";
    $result = mysqli_query($conn, $sql);

    $employees = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['name'] = $row['first_name'] . ' ' . $row['last_name'];

        $row['actions'] = '<button class="btn btn-warning btn-sm edit-btn mb-2" data-id="' . $row['id'] . '">Edit</button>
                           <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row['id'] . '">Delete</button>';
        $employees[] = $row;
    }

    echo json_encode(["data" => $employees]);
    exit();
}


if ($action == 'edit_fetch') {
    $id = $_POST['id'];
    $sql = "SELECT id, first_name, last_name, email, phone, `role` FROM `admin` WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    $data = [];


    if ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
        echo json_encode(["status" => "success", "data" => $data]);

    } else {
        echo json_encode(["status" => "error", "message" => "Error"]);
    }
    exit();
}



if ($action == 'update') {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $password = md5($password);
        $sql = "UPDATE `admin` SET first_name='$first_name', last_name='$last_name', email='$email', phone='$phone', `role`='$role', `password`='$password' WHERE id=$id";
    } else {
        $sql = "UPDATE `admin` SET first_name='$first_name', last_name='$last_name', email='$email', phone='$phone', `role`='$role' WHERE id=$id";
    }

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success", "message" => "Employee Updated Successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed To update Employee"]);
    }

    exit();
}


if ($action == 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM `admin` WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success", "message" => "Employee Deleted Successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed To Delete Employee"]);
    }

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
                <div class="col-md-5">
                    <h3 class="mb-3">Employee Management</h3>
                    <!-- Add Employee Form -->
                    <form id="addEmployeeForm">

                        <div class="mb-3">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select class="form-control" name="role" required>
                                <option value="">Select</option>
                                <option value="Super Admin">Super Admin</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Add Employee</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-7">
                    <!-- Employee Table -->
                    <table id="employeeTable" class="table table-striped mt-4">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editEmployeeForm">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
                        </div>
                        <div class="mb-3">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" class="form-control" name="phone" id="edit_phone" required>
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select class="form-control" name="role" id="edit_role" required>
                                <option value="Super Admin">Super Admin</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Password </label>
                            <input type="password" class="form-control" name="password" id="edit_password"
                                placeholder="********">
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update Employee</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>





    <?php
    include 'include/footer.php';
    ?>

    <script>
        $(document).ready(function () {

            $('#employeeTable').DataTable({
                ajax: {
                    url: "add-employee.php",
                    type: 'POST',
                    "data": { action: "fetch" },
                    dataSrc: 'data'
                },
                columns: [
                    { "data": "id" },
                    { "data": "name" },
                    { "data": "email" },
                    { "data": "phone" },
                    { "data": "role" },
                    { "data": "actions" }
                ]
            });



            $(document).on('click', '.edit-btn', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: 'add-employee.php',
                    type: 'POST',
                    data: { action: 'edit_fetch', id: id },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#edit_id').val(response.data[0].id);
                            $('#edit_first_name').val(response.data[0].first_name);
                            $('#edit_last_name').val(response.data[0].last_name);
                            $('#edit_email').val(response.data[0].email);
                            $('#edit_phone').val(response.data[0].phone);
                            $('#edit_role').val(response.data[0].role);
                            $('#edit_password').val('');
                            $('#editEmployeeModal').modal('show');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                });
            });

            $('#editEmployeeForm').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'add-employee.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=update',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            $('#editEmployeeModal').modal('hide');
                            $('#employeeTable').DataTable().ajax.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                });
            });


            $('#addEmployeeForm').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'add-employee.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=add',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            $('#addEmployeeForm')[0].reset();
                            $('#employeeTable').DataTable().ajax.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                });

            });


            $(document).on('click', '.delete-btn', function () {
                var id = $(this).data('id');
                if (confirm("Are you sure you want to delete this employee?")) {
                    $.ajax({
                        url: 'add-employee.php',
                        type: 'POST',
                        data: { action: 'delete', id: id },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                alert(response.message);
                                $('#employeeTable').DataTable().ajax.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>