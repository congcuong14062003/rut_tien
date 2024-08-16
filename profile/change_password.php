<?php include '../component/header.php'; ?>
<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    // Nếu không phải user, chuyển hướng đến trang thông báo không có quyền
    header("Location: /no-permission");
    exit();
}
?>
<?php
// Xử lý form gửi dữ liệu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Kiểm tra mật khẩu cũ
    if ($old_password == $user['password']) {
        // Cập nhật mật khẩu mới
        $query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $new_password, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success_update'] = "Mật khẩu đã được cập nhật thành công!";
        } else {
            $_SESSION['error_update'] = "Có lỗi xảy ra khi cập nhật mật khẩu.";
        }
    } else {
        $_SESSION['error_update'] = "Mật khẩu cũ không chính xác.";
    }
    header("Location: /profile");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="../component/sidebar.css">
    <link rel="stylesheet" href="./profile.css">
    <title>Đổi Mật Khẩu Đăng Nhập</title>
</head>

<body>
    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right container_form">
            <div class="container">
                <h1>Đổi Mật Khẩu Đăng Nhập</h1>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="old_password">Mật khẩu cũ:</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới:</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cập Nhật</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>