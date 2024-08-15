<?php
session_start();
include '../db.php';

// Kiểm tra xem yêu cầu có phải là POST không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $captcha = $_POST['g-recaptcha-response'];

    // Xác minh CAPTCHA
    $secret_key = '6LcyFyAqAAAAAAM-TxbOu73aQKICNdVllKdhkgMr';
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$captcha");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        $_SESSION['error_login'] = "Vui lòng xác minh CAPTCHA.";
        header("Location: /login");
        exit();
    } else {
        // Truy vấn cơ sở dữ liệu để lấy thông tin người dùng
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['success_login'] = "Đăng nhập thành công";
                header("Location: /client/home");
                exit();
            } else {
                $_SESSION['error_login'] = "Sai mật khẩu!";
                header("Location: /login");
                exit();
            }
        } else {
            $_SESSION['error_login'] = "Người dùng không tồn tại!";
            header("Location: /login");
            exit();
        }
    }
}
