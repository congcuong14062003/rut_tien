<?php include '../component/header.php'; ?>
<?php

$username = $user['username'];
$balance = $user['balance'];
$wallet_address = $user['wallet_address'];

// Định dạng địa chỉ ví thành toàn bộ dấu sao
function formatWalletAddress($address)
{
    return str_repeat('*', strlen($address));
}

$formatted_wallet_address = formatWalletAddress($wallet_address);

// Xử lý form rút tiền
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw'])) {
    $amount = $_POST['amount'];
    $secondary_password = $_POST['secondary_password'];


    if ( $user["second_password"] === $secondary_password) {
        if ($balance >= $amount) {
            // Trừ số dư
            $new_balance = $balance - $amount;
            $update_balance_query = "UPDATE users SET balance = ? WHERE id = ?";
            $stmt = $conn->prepare($update_balance_query);
            $stmt->bind_param('ii', $new_balance, $user_id);
            $stmt->execute();

            // Thêm giao dịch vào bảng lịch sử với ngày hiện tại
            $history_query = "INSERT INTO tbl_history (user_id, type, amount, transaction_date, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($history_query);
            $type = 'Rút tiền về ví';
            $stmt->bind_param('isi', $user_id, $type, $amount);
            $stmt->execute();
            $_SESSION['with_draw_success'] = "Rút tiền thành công!";
        } else {
            $_SESSION['with_draw_error'] = "Số dư không đủ!";
        }
    } else {
        $_SESSION['with_draw_error'] = "Mật khẩu cấp 2 không đúng!";
    }

    header("Location: /withdraw-money"); // Trở về trang rút tiền
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="../component/sidebar.css">
    <link rel="stylesheet" href="../add-card/addcard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Rút tiền</title>
    <style>
    input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }
    </style>
</head>

<body>
    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right">
            <div class="container">
                <h1 class="title">Rút tiền về tài khoản</h1>
                <form id="withdraw-form" method="post" action="">
                    <label for="balance">Số dư tài khoản:</label>
                    <input disabled type="text" id="balance" name="balance"
                        value="<?php echo htmlspecialchars($user['balance']); ?>">
                    <label for="wallet_address">Địa chỉ ví nhận:</label>
                    <input type="text" id="wallet_address" name="wallet_address"
                        value="<?php echo htmlspecialchars($formatted_wallet_address); ?>" disabled>
                    <label for="amount">Số tiền muốn rút:</label>
                    <input type="number" id="amount" name="amount" required>
                    <label for="secondary_password">Mật khẩu cấp 2:</label>
                    <input type="password" id="secondary_password" name="secondary_password" required>
                    <input type="submit" name="withdraw" value="Rút tiền">
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['with_draw_success'])) : ?>
        toastr.success("<?php echo $_SESSION['with_draw_success']; ?>");
        <?php unset($_SESSION['with_draw_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['with_draw_error'])) : ?>
        toastr.error("<?php echo $_SESSION['with_draw_error']; ?>");
        <?php unset($_SESSION['with_draw_error']); ?>
        <?php endif; ?>
    });
    </script>
</body>

</html>