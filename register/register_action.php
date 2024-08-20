<?php
session_start();
include '../db.php';

// Kiểm tra xem yêu cầu có phải là POST không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Không mã hóa mật khẩu

    // Kiểm tra xem username có tồn tại không
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? and role = 'user'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error_register'] = "Tên người dùng đã tồn tại!";
        header("Location: /register");
        exit();
    } else {
        // Thêm người dùng mới vào cơ sở dữ liệu
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            $_SESSION['success_register'] = "Đăng ký thành công!";
            header("Location: /login");
            exit();
        } else {
            $_SESSION['error_register'] = "Có lỗi xảy ra. Vui lòng thử lại!";
            header("Location: /register");
            exit();
        }
    }
}
