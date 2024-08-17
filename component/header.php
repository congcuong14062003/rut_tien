<?php
ob_start(); // Bật bộ đệm đầu ra
session_start();

$servername = "localhost";
$username = "root";
$password = "MyNewPass";
$dbname = "payment_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

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
$formattedBalance = number_format($user['balance'], 0, ',', '.');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="./sidebar.css">

<div class="header_container">
    <!-- Overlay -->
    <div id="overlay"></div>

    <div class="side_bar_mobile">
        <div class="icon-mobile">
            <i class="fa-solid fa-bars"></i>
        </div>
        <div class="user_infor">
            <?php if ($role != 'admin') { ?>
                <a href="/profile">
                    <?php
                    if (isset($_SESSION['user_id'])) {
                        echo 'Xin chào, ' . htmlspecialchars($user['username']);
                    }
                    ?>
                </a>
            <?php } else { ?>
                <span>Xin chào, <?php echo htmlspecialchars($user['username']); ?></span>
            <?php } ?>
        </div>
    </div>
    <?php if ($role != 'admin') { ?>
        <div class="balance">
            <?php
            if (isset($_SESSION['user_id'])) {
                echo 'Số dư: ' . htmlspecialchars($formattedBalance . ' VND');
            }
            ?>
        </div>
    <?php } ?>
    <?php
    $current_page = basename($_SERVER['REQUEST_URI']);
    ?>

    <div id="user" class="side_bar_active">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>" href="../home">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-house"></i></div>
                            Trang chủ
                        </a>
                        <?php if ($role != 'admin') { ?>
                            <a class="nav-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>" href="/user/home">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-house"></i></div>
                                Trang chủ
                            </a>
                            <a class="nav-link <?php echo ($current_page == 'list-card') ? 'active' : ''; ?>"
                                href="/user/list-card">
                                <div class="sb-nav-link-icon"><i class="fas fa-id-card"></i></div>
                                Danh sách thẻ
                            </a>
                            <a class="nav-link <?php echo ($current_page == 'add-card') ? 'active' : ''; ?>"
                                href="/user/add-card">
                                <div class="sb-nav-link-icon"><i class="fas fa-plus-circle"></i></div>
                                Add thẻ
                            </a>
                            <a class="nav-link <?php echo ($current_page == 'history') ? 'active' : ''; ?>"
                                href="/user/history">
                                <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                                Lịch sử giao dịch
                            </a>
                            <a class="nav-link <?php echo ($current_page == 'withdraw-money') ? 'active' : ''; ?>"
                                href="/user/withdraw-money">
                                <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                                Rút tiền từ tài khoản về ví
                            </a>
                            <a class="nav-link <?php echo ($current_page == 'withdraw-visa') ? 'active' : ''; ?>"
                                href="/user/withdraw-visa">
                                <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                                Rút tiền từ thẻ về tài khoản
                            </a>
                            <a class="nav-link <?php echo ($current_page == 'history-balance') ? 'active' : ''; ?>"
                                href="/user/history-balance">
                                <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                                Lịch sử biến động số dư
                            </a>

                            <a class="nav-link <?php echo ($current_page == 'profile') ? 'active' : ''; ?>"
                                href="/user/profile">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                Trang cá nhân
                            </a>
                        <?php } else { ?>
                            <a class="nav-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>" href="/admin/home">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-house"></i></div>
                                Trang chủ
                            </a>
                            <a class="nav-link <?php echo ($current_page == 'manager-user') ? 'active' : ''; ?>"
                                href="/admin/manager-user">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                Quản lý user
                            </a>
                            <a class="nav-link <?php echo ($current_page == 'manager-card-withdraw') ? 'active' : ''; ?>"
                                href="/admin/manager-card-withdraw">
                                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                                Duyệt lệnh rút tiền từ thẻ về tài khoản
                            </a>
                            <a class="nav-link <?php echo ($current_page == 'manager-account-withdraw') ? 'active' : ''; ?>"
                                href="/admin/manager-account-withdraw">
                                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                                Duyệt lệnh rút tiền từ tài khoản về ví
                            </a>
                            <a class="nav-link <?php echo ($current_page == 'manager-card-user') ? 'active' : ''; ?>"
                                href="/admin/manager-card-user">
                                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                                Duyệt add thẻ vào tài khoản
                            </a>
                        <?php } ?>

                        <form class="logout" method="post" action="/logout.php">
                            <input type="submit" value="Đăng Xuất">
                        </form>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuIcon = document.querySelector('.icon-mobile');
        const sidebar = document.querySelector('.side_bar_active');
        const overlay = document.getElementById('overlay');

        menuIcon.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    });
</script>