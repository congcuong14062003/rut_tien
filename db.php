<?php
$servername = "localhost";
$username = "Admin";
$password = "MyNewPass";
$dbname = "payment_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>