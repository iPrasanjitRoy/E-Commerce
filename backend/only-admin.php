<?php
include 'include/db-config.php';

session_start();

if (!isset($_SESSION['admin_name']) || $_SESSION['admin_role'] !== 'Super Admin') {
    header("Location: index.php");
    exit();
}


?>