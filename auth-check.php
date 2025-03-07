<?php
include 'front-config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: auth-login.php");
    exit();
}

?>