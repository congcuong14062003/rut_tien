<!-- header.php -->
<div class="header_container">
    <div class="btn_logout">
        <form method="post" action="../logout.php">
            <input type="submit" value="Đăng Xuất">
        </form>
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