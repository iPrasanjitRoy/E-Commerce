<?php
include 'include/db-config.php';

$query = "SELECT * FROM companies WHERE company_id = 1";
$result = mysqli_query($conn, $query);
$company = mysqli_fetch_assoc($result);

if (!$company) {
    /* SET DEFAULT EMPTY VALUES TO PREVENT ERRORS */
    $company = [
        'company_id' => 1,
        'title' => '',
        'logo' => '',
        'phone_number' => '',
        'email' => '',
        'website' => '',
        'address' => '',
        'facebook_link' => '',
        'instagram_link' => '',
        'twitter_link' => '',
        'footer_text' => '',
        'about_company' => '',
        'linked_link' => '',
        'youtube_link' => ''
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $company_id = $_POST['company_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $website = mysqli_real_escape_string($conn, $_POST['website']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $facebook_link = mysqli_real_escape_string($conn, $_POST['facebook_link']);
    $instagram_link = mysqli_real_escape_string($conn, $_POST['instagram_link']);
    $twitter_link = mysqli_real_escape_string($conn, $_POST['twitter_link']);
    $footer_text = mysqli_real_escape_string($conn, $_POST['footer_text']);
    $about_company = mysqli_real_escape_string($conn, $_POST['about_company']);
    $linked_link = mysqli_real_escape_string($conn, $_POST['linked_link']);
    $youtube_link = mysqli_real_escape_string($conn, $_POST['youtube_link']);



    $insert_query = "UPDATE companies SET `title` = '$title', ";

    if (!empty($_FILES['logo']['name'])) {

        $logo = $_FILES['logo']['name'];
        $target_dir = "uploads/";

        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed To Create Uploads Directory!'
                ]);
                exit();
            }
        }

        $target_file = $target_dir . uniqid() . "_" . basename($logo);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


        if ($_FILES['logo']['size'] > 1048576) {
            echo json_encode([
                'status' => 'error',
                'message' => 'File Size Should Not Exceed 1MB!'
            ]);
            exit();
        }

        if ($imageFileType != 'jpg' && $imageFileType != 'jpeg' && $imageFileType != 'png') {
            echo json_encode([
                'status' => 'error',
                'message' => 'Only JPG, JPEG, and PNG files are allowed!'
            ]);
            exit();
        }

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {

            $insert_query .= "`logo` = '$target_file', ";

            if (!empty($company['logo']) && file_exists($company['logo'])) {
                unlink($company['logo']);
            }


        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error uploading logo!'
            ]);
            exit();
        }


    }

    $insert_query .= "`phone_number` = '$phone_number', 
                    `email` = '$email', 
                    `website` = '$website', 
                    `address` = '$address',
                    `facebook_link` = '$facebook_link',
                    `instagram_link` = '$instagram_link',
                    `twitter_link` = '$twitter_link',
                    `footer_text` = '$footer_text',
                    `about_company` = '$about_company', 
                    `linked_link` = '$linked_link', 
                    `youtube_link` = '$youtube_link' 
              WHERE `company_id` = '$company_id'";


    if (mysqli_query($conn, $insert_query)) {
        echo json_encode([
            'status' => 'success',
            'message' => ' Company Details Updated Successfully!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error Updating Company Details'
        ]);
    }
    mysqli_close($conn);
    exit();
}


?>

<!doctype html>
<html class="no-js" lang="en" dir="ltr">

<head>
    <?php include 'include/style.php'; ?>
</head>

<body>
    <?php include 'include/header.php'; ?>

    <div class="body d-flex py-3">
        <div class="container-xxl">
            <div class="row">
                <div class="col-md-5">
                    <h4 class="mb-3">Update Company Details</h4>
                    <form id="updateCompanyForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="company_id" value="<?php echo $company['company_id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title"
                                value="<?php echo $company['title']; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Logo (Upload Image)</label>
                            <input type="file" class="form-control" name="logo">
                            <br>
                            <?php if ($company['logo']) { ?>
                                <img src="<?php echo $company['logo']; ?>" alt="Logo" width="100">
                            <?php } ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone_number"
                                value="<?php echo $company['phone_number']; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                value="<?php echo $company['email']; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input type="text" class="form-control" name="website"
                                value="<?php echo $company['website']; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address"><?php echo $company['address']; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Facebook Link</label>
                            <input type="text" class="form-control" name="facebook_link"
                                value="<?php echo $company['facebook_link']; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Instagram Link</label>
                            <input type="text" class="form-control" name="instagram_link"
                                value="<?php echo $company['instagram_link']; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Twitter Link</label>
                            <input type="text" class="form-control" name="twitter_link"
                                value="<?php echo $company['twitter_link']; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Footer Text</label>
                            <textarea class="form-control"
                                name="footer_text"><?php echo $company['footer_text']; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">About Company</label>
                            <textarea class="form-control"
                                name="about_company"><?php echo $company['about_company']; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Linked Link</label>
                            <input type="text" class="form-control" name="linked_link"
                                value="<?php echo $company['linked_link']; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">YouTube Link</label>
                            <input type="text" class="form-control" name="youtube_link"
                                value="<?php echo $company['youtube_link']; ?>">
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>

                    <div id="message"></div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>

    <script>
        $(document).ready(function () {
            $("#updateCompanyForm").submit(function (e) {
                e.preventDefault();

                var formData = new FormData(this);
                formData.append("action", "update");

                $.ajax({
                    url: "",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            window.location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert("Error updating company details");
                    }
                });
            });
        });
    </script>
</body>

</html>