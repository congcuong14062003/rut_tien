<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: /home");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng Ký</title>
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body>
    <div class="container">
        <h1>Đăng Ký</h1>
        <form method="post" action="register_action.php">
            <label for="username">Tên người dùng:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" required>
            <p>Đã có tài khoản <a style="color: red" href="/login">Đăng nhập</a></p>
            <input type="submit" value="Đăng ký">
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    $(document).ready(function() {
        <?php
            if (isset($_SESSION['error_register'])) {
                echo "toastr.error('" . $_SESSION['error_register'] . "');";
                unset($_SESSION['error_register']);
            } 
            ?>
    });
    </script>
</body>

</html>