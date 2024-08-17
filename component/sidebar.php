<?php
$current_page = basename($_SERVER['REQUEST_URI']);
?>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">

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

                    <form class="" method="post" action="/logout.php">
                        <input type="submit" class="logout" value="Đăng Xuất">
                    </form>
                </div>
            </div>
        </nav>
    </div>
</div>