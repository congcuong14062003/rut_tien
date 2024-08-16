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
    $current_secondary_password = $_POST['current_secondary_password'] ?? '';
    $new_secondary_password = $_POST['new_secondary_password'] ?? '';
    if (!empty($user['second_password'])) {
        // Kiểm tra mật khẩu cấp hai hiện tại
        if ($current_secondary_password == $user['second_password']) {
            // Cập nhật mật khẩu cấp hai mới
            $query = "UPDATE users SET second_password = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $new_secondary_password, $user_id);
            if ($stmt->execute()) {
                $_SESSION['success_update'] = "Mật khẩu cấp hai đã được cập nhật thành công!";
            } else {
                $_SESSION['error_update'] = "Có lỗi xảy ra khi cập nhật mật khẩu cấp hai.";
            }
        } else {
            $_SESSION['error_update'] = "Mật khẩu cấp hai hiện tại không chính xác.";
        }
    } else {
        // Cập nhật mật khẩu cấp hai mới
        $query = "UPDATE users SET second_password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $new_secondary_password, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success_update'] = "Mật khẩu cấp hai đã được thiết lập thành công!";
        } else {
            $_SESSION['error_update'] = "Có lỗi xảy ra khi thiết lập mật khẩu cấp hai.";
        }
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
    <title>Đổi Mật Khẩu Cấp Hai</title>
</head>

<body>
    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right container_form">
            <div class="container">
                <h1>Đổi Mật Khẩu Cấp Hai</h1>
                <?php if (empty($user['second_password'])) : ?>
                    <!-- Form thiết lập mật khẩu cấp hai -->
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="new_secondary_password">Mật khẩu cấp hai mới:</label>
                            <input type="password" class="form-control" id="new_secondary_password"
                                name="new_secondary_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Thiết Lập</button>
                    </form>
                <?php else : ?>
                    <!-- Form đổi mật khẩu cấp hai -->
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="current_secondary_password">Mật khẩu cấp hai hiện tại:</label>
                            <input type="password" class="form-control" id="current_secondary_password"
                                name="current_secondary_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_secondary_password">Mật khẩu cấp hai mới:</label>
                            <input type="password" class="form-control" id="new_secondary_password"
                                name="new_secondary_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập Nhật</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>