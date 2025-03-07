<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myshop";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed :" . $conn->connect_error);
}

include_once 'function.php';

$script_ver = '1.0.0';

?>