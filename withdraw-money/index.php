<?php include '../component/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="../component/sidebar.css">
    <link rel="stylesheet" href="../add-card/addcard.css">
    <title>Rút tiền</title>
</head>

<body>
    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right">
            <div class="container">
                <h1 class="title">Rút tiền</h1>
                <form id="withdraw-form" method="post" action="home_action.php">
                    <label for="first_name">Số dư tài khoản:</label>
                    <input type="text" id="first_name" name="first_name" required>
                    <label for="last_name">Địa chỉ ví nhận:</label>
                    <input type="text" id="last_name" name="last_name" required>
                    <label for="card_number">Loại ví:</label>
                    <input type="text" id="card_number" name="card_number" required>
                    <label for="expiry_date">Số tiền muốn rút:</label>
                    <input type="text" id="expiry_date" name="expiry_date" required>
                    <input type="submit" name="withdraw" value="Rút tiền">
                </form>
            </div>
        </div>
    </div>

</body>

</html>