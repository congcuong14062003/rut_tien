<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: /home");
    exit();
} else {
    header("Location: /login");
    exit();
}
?>