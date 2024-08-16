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
    $new_wallet_address = $_POST['wallet_address'];
    $secondary_password = $_POST['secondary_password'];
    // Kiểm tra mật khẩu cấp hai
    if ($secondary_password == $user["second_password"]) {
        // Cập nhật địa chỉ ví
        $query = "UPDATE users SET wallet_address = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $new_wallet_address, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success_update'] = "Địa chỉ ví đã được cập nhật thành công!";
        } else {
            $_SESSION['error_update'] = "Có lỗi xảy ra khi cập nhật địa chỉ ví.";
        }
    } else {
        $_SESSION['error_update'] = "Mật khẩu cấp hai không chính xác.";
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
    <title>Đổi Địa Chỉ Ví</title>
</head>

<body>

    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right container_form">
            <div class="container">
                <h1>Đổi Địa Chỉ Ví</h1>
                <?php if (empty($user['second_password'])) : ?>
                    <!-- Hiển thị liên kết đến trang thiết lập mật khẩu cấp hai -->
                    <p>Vui lòng <a href="./change_password_second.php">thiết lập mật khẩu cấp hai</a> trước khi thay đổi địa
                        chỉ
                        ví.</p>
                <?php else : ?>
                    <!-- Form cập nhật địa chỉ ví -->
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="wallet_address">Địa chỉ ví mới:</label>
                            <input type="text" class="form-control" id="wallet_address" name="wallet_address" required>
                        </div>
                        <div class="form-group">
                            <label for="secondary_password">Mật khẩu cấp hai:</label>
                            <input type="password" class="form-control" id="secondary_password" name="secondary_password"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập Nhật</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>