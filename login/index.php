<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: /user/home");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Đăng Nhập</title>
</head>

<body>
    <div class="container container_login">
        <h1>Đăng Nhập</h1>
        <form id="loginForm">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <p class="signup_option">Chưa có tài khoản? <a class="red_color" href="/register">Đăng ký ngay</a></p>
            <input type="submit" value="Đăng Nhập">
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {

            <?php
            if (isset($_SESSION['success_register'])) {
                echo "toastr.success('" . $_SESSION['success_register'] . "');";
                unset($_SESSION['success_register']);
            }
            ?>

        });
    </script>
    <script type="module">
        import { registerServiceWorker } from '../component/getToken.js'; // Đảm bảo đường dẫn đúng
        $(document).ready(function () {
            $('#loginForm').submit(async function (event) {
                event.preventDefault(); // Ngăn chặn hành vi gửi form mặc định
                var formData = $(this).serialize(); // Lấy dữ liệu từ form
                try {
                    if (typeof Notification === "undefined") {
                        toastr.error("Trình duyệt iphone không hỗ trợ thông báo đẩy. Vui lòng sử dụng laptop hoặc thiết bị android !");
                        return; // Dừng quá trình đăng nhập
                    }

                    // Yêu cầu quyền sử dụng thông báo
                    const permission = await Notification.requestPermission();
                    if (permission !== "granted") {
                        toastr.error("Bạn cần cấp quyền thông báo để đăng nhập.");
                        return; // Dừng quá trình đăng nhập
                    }
                    // Đăng ký Service Worker
                    await registerServiceWorker();

                    // Thực hiện đăng nhập
                    const response = await $.ajax({
                        type: 'POST',
                        url: 'login_action.php',
                        data: formData,
                        dataType: 'json'
                    });

                    if (response.status === 'success') {
                        window.location.href = '/index.php'; // Chuyển hướng về trang index.php
                    } else {
                        toastr.error(response.message);
                    }
                } catch (error) {
                    toastr.error('Đã xảy ra lỗi trong quá trình đăng nhập: ' + error.message);
                }
            });
        });
    </script>
</body>

</html>