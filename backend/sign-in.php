<?php
session_start();
include 'include/db-config.php';

$response = array('status' => 'error', 'message' => '');
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'signin') {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5(mysqli_real_escape_string($conn, $_POST['password']));

    $sql = "SELECT * FROM admin WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['first_name'] . " " . $user['last_name'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['admin_email'] = $user['email'];

        $response['status'] = 'success';
        $response['message'] = 'Login successful!';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid Email or Password.';
    }

    mysqli_close($conn);
    echo json_encode($response);
    exit();
}
?>

<!doctype html>
<html class="no-js" lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Signin</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon"> <!-- Favicon-->

    <!-- project css file  -->
    <link rel="stylesheet" href="css/ebazar.style.min.css">
</head>

<body>
    <div id="ebazar-layout" class="theme-blue">

        <!-- main body area -->
        <div class="main p-2 py-3 p-xl-5 ">

            <!-- Body: Body -->
            <div class="body d-flex p-0 p-xl-5">
                <div class="container-xxl">

                    <div class="row">
                        <div
                            class="col-lg-12 d-flex justify-content-center align-items-center border-0 rounded-lg auth-h100">
                            <div class="w-100 p-3 p-md-5 card border-0 shadow-sm" style="max-width: 32rem;">
                                <!-- Form -->
                                <form class="row g-1 p-3 p-md-4" id="loginForm" method="POST">
                                    <div class="col-12 text-center mb-5">
                                        <h1>Sign in</h1>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-2">
                                            <label class="form-label">Email address</label>
                                            <input type="email" id="email" name="email"
                                                class="form-control form-control-lg" placeholder="name@example.com"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-2">
                                            <div class="form-label">
                                                <span class="d-flex justify-content-between align-items-center">
                                                    Password
                                                    <a class="text-secondary" href="auth-password-reset.html">Forgot
                                                        Password?</a>
                                                </span>
                                            </div>
                                            <input type="password" id="password" name="password"
                                                class="form-control form-control-lg" placeholder="***************"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-12 text-center mt-4">
                                        <button type="submit"
                                            class="btn btn-lg btn-block btn-light lift text-uppercase">SIGN IN</button>
                                    </div>
                                </form>
                                <!-- End Form -->

                            </div>
                        </div>
                    </div> <!-- End Row -->

                </div>
            </div>

        </div>

    </div>

    <?php
    include 'include/footer.php';
    ?>



    <script>
        $(document).ready(function () {
            $("#loginForm").submit(function (e) {
                e.preventDefault();

                var email = $("input[name='email']").val();
                var password = $("input[name='password']").val();


                $.ajax({
                    url: "sign-in.php",
                    type: "POST",
                    data: { email: email, password: password, action: 'signin' },
                    dataType: "json",
                    success: function (response) {
                        if (response.status == "success") {
                            window.location.href = "index.php";
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert("An error occurred. Please try again.");
                    }
                });
            });
        });
    </script>

</body>

</html>