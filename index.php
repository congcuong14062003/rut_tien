<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['user_id'])) {
    if(isset($_SESSION['role'])) {
        if ($_SESSION['role'] == 'user') {
            header("Location: /user/home");
            exit();
        }
    }
} else {
    header("Location: /login");
    exit();
}
?>