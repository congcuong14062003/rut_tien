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
    $current_secondary_password = $_POST['current_secondary_password'] ?? '';
    $new_secondary_password = $_POST['new_secondary_password'] ?? '';

    // Lấy mật khẩu cấp hai hiện tại từ cơ sở dữ liệu
    $user_id = $_SESSION['user_id'];
    $query = "SELECT second_password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

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
    <title>Đổi Mật Khẩu Cấp Hai</title>
</head>

<body>
    <a href="/profile" style="margin-left: 20px">
        <- Quay lại trang cá nhân</a>
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