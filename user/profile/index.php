<?php include '../../component/header.php'; ?>
<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    // Nếu không phải user, chuyển hướng đến trang thông báo không có quyền
    header("Location: /no-permission");
    exit();
}
?>
<?php include '../../component/formatSecutiry.php' ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/index.css">
    <link rel="stylesheet" href="../../component/header.css">
    <link rel="stylesheet" href="../../component/sidebar.css">
    <link rel="stylesheet" href="./profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Trang Cá Nhân</title>
</head>

<body>
    <!-- Header -->
    <div class="container_boby">
        <?php include '../../component/sidebar.php'; ?>
        <div class="content_right container_form">
            <div class=" container">
                <h1>Thông tin cá nhân</h1>
                <label for="username">Tên người dùng:</label>
                <input disabled type="text" id="username" name="username"
                    value="<?php echo htmlspecialchars($user['username']); ?>">

                    <label for="wallet_address">Địa chỉ ví:</label>
                <input disabled type="text" id="wallet_address" name="wallet_address"
                    value="<?php echo htmlspecialchars(formatSecutiry($user['wallet_address'])); ?>">

                <label for="username">Số dư hiện tại:</label>
                <input disabled type="text" id="username" name="username"
                    value="<?php echo htmlspecialchars($user['balance']); ?>">
                <div class="action_button">
                    <a href="./change_wallet_address.php"><button type="button" class="btn btn-primary">Đổi địa
                            chỉ
                            ví</button></a>
                    <a href="./change_password.php"><button type="button" class="btn btn-primary">Đổi mật khẩu
                            đăng
                            nhập</button></a>
                    <a href="./change_password_second.php"><button type="button" class="btn btn-primary">Đổi mật
                            khẩu
                            cấp
                            hai</button></a>
                </div>

            </div>
        </div>
    </div>
    <!-- Profile Form -->
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