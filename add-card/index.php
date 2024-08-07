<?php include '../component/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="../component/sidebar.css">
    <link rel="stylesheet" href="./addcard.css">
    <title>Thông Tin Tài Khoản</title>
</head>

<body>
    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right">
            <div class="container">
                <h1 class="title">Thêm thẻ</h1>
                <form id="withdraw-form" method="post" action="home_action.php">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                    <label for="card_number">Số thẻ:</label>
                    <input type="text" id="card_number" name="card_number" required>
                    <label for="expiry_date">Ngày hết hạn:</label>
                    <input type="month" id="expiry_date" name="expiry_date" required>
                    <label for="cvv">Số CVV:</label>
                    <input type="text" id="cvv" name="cvv" required>
                    <input type="submit" name="withdraw" value="Xác Nhận">
                </form>
            </div>
        </div>
    </div>

</body>

</html>