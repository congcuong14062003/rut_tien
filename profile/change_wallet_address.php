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
    $new_wallet_address = $_POST['wallet_address'];
    $secondary_password = $_POST['secondary_password'];

    // Lấy mật khẩu cấp hai từ cơ sở dữ liệu
    $user_id = $_SESSION['user_id'];
    $query = "SELECT second_password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

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

// Lấy mật khẩu cấp hai hiện tại từ cơ sở dữ liệu
$user_id = $_SESSION['user_id'];
$query = "SELECT second_password FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Đổi Địa Chỉ Ví</title>
</head>

<body>
    <a href="/profile" style="margin-left: 20px">
        <- Quay lại trang cá nhân</a>
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
            <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
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