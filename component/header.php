<!-- header.php -->
<?php
session_start();
include '../db.php';
// Kiểm tra nếu người dùng chưa đăng nhập, chuyển hướng đến trang đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}
// Lấy thông tin người dùng từ cơ sở dữ liệu
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role = $user['role']; // 'user' hoặc 'admin'
$username = $user['username'];

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="header_container">
    <div class="side_bar_mobile">
        <div class="icon-mobile">
            <i class="fa-solid fa-bars"></i>
        </div>
        <div class="user_infor">
            <a href="/profile">
                <?php
            if (isset($_SESSION['user_id'])) {
                echo 'Xin chào, ' . htmlspecialchars($user['username']);
            }
            ?>
            </a>
        </div>
    </div>

    <div class="balance">
        <!-- <form method="post" action="../logout.php">
            <input type="submit" value="Đăng Xuất">
        </form> -->
        <?php
            if (isset($_SESSION['user_id'])) {
                echo 'Số dư: ' . htmlspecialchars($user['balance'] . ' đ');
            }
            ?>
    </div>
</div>