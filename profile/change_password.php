<?php
session_start();
include '../db.php';

// Kiểm tra nếu người dùng chưa đăng nhập, chuyển hướng đến trang đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

// Xử lý form gửi dữ liệu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Lấy mật khẩu hiện tại từ cơ sở dữ liệu
    $user_id = $_SESSION['user_id'];
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

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
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Đổi Mật Khẩu Đăng Nhập</title>
</head>

<body>
    <a href="/profile" style="margin-left: 20px">
        <- Quay lại trang cá nhân</a>
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
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
            <script>
            $(document).ready(function() {
                <?php if (isset($_SESSION['success_update'])) : ?>
                toastr.success("<?php echo $_SESSION['success_update']; ?>");
                <?php unset($_SESSION['success_update']); ?>
                <?php elseif (isset($_SESSION['error_update'])) : ?>
                toastr.error("<?php echo $_SESSION['error_update']; ?>");
                <?php unset($_SESSION['error_update']); ?>
                <?php endif; ?>
            });
            </script>
</body>

</html>