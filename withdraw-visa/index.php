<?php include '../component/header.php'; ?>

<?php
// Kết nối cơ sở dữ liệu

// Lấy thông tin từ URL
$id_card = isset($_GET['id_card']) ? $_GET['id_card'] : '';

// Khai báo biến danh sách thẻ
$cards = [];
$selected_card_id = $id_card; // ID thẻ sẽ được chọn tự động nếu có

// Lấy thông tin thẻ từ cơ sở dữ liệu nếu có id_card
if ($id_card) {
    $query = "SELECT * FROM tbl_card WHERE id_card = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_card);
    $stmt->execute();
    $result = $stmt->get_result();
    $card = $result->fetch_assoc();
    $stmt->close();
} else {
    // Nếu không có id_card, lấy tất cả các thẻ của người dùng
    $cards_query = "SELECT * FROM tbl_card WHERE user_id = ?";
    $stmt = $conn->prepare($cards_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $cards_result = $stmt->get_result();
    while ($card = $cards_result->fetch_assoc()) {
        $cards[] = $card;
    }
    $stmt->close();
}

// Xử lý form rút tiền
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw'])) {
    $amount = $_POST['amount'];
    $card_id = $_POST['card_id'];
    $otp = $_POST['otp'];

    // Thêm giao dịch vào bảng lịch sử với ngày hiện tại
    $history_query = "INSERT INTO tbl_history (user_id, type, amount, transaction_date, updated_at, id_card, otp) VALUES (?, ?, ?, NOW(), NOW(), ?, ?)";
    $stmt = $conn->prepare($history_query);
    $type = 'Rút tiền từ thẻ';
    $stmt->bind_param('isiis', $user_id, $type, $amount, $card_id, $otp);
    if ($stmt->execute()) {
        // Cập nhật tổng số tiền đã rút trong bảng tbl_card
        $update_query = "UPDATE tbl_card SET total_amount_success = total_amount_success + ? WHERE id_card = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('ii', $amount, $card_id);
        $update_stmt->execute();
        $update_stmt->close();

        $_SESSION['with_draw_visa_success'] = "Rút tiền thành công!";
        header('Location: /history');
    } else {
        $_SESSION['with_draw_error'] = "Rút tiền thất bại!";
    }
    $stmt->close();
}

// Đóng kết nối
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
    input,
    select {
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
                <h1 class="title">Rút tiền từ thẻ</h1>
                <form id="withdraw-form" method="post" action="">
                    <label for="card_id">Số thẻ:</label>
                    <select id="card_id" name="card_id" required>
                        <option value="" disabled selected>Chọn thẻ</option>
                        <?php if ($id_card): ?>
                        <option value="<?php echo $card['id_card']; ?>"
                            data-name="<?php echo htmlspecialchars($card['firstName'] . ' ' . $card['lastName']); ?>"
                            data-expiry_date="<?php echo htmlspecialchars($card['expDate']); ?>"
                            data-cvv="<?php echo htmlspecialchars($card['cvv']); ?>" selected>
                            <?php echo htmlspecialchars($card['card_number']); ?>
                        </option>
                        <?php else: ?>
                        <?php foreach ($cards as $card): ?>
                        <option value="<?php echo $card['id_card']; ?>"
                            data-name="<?php echo htmlspecialchars($card['firstName'] . ' ' . $card['lastName']); ?>"
                            data-expiry_date="<?php echo htmlspecialchars($card['expDate']); ?>"
                            data-cvv="<?php echo htmlspecialchars($card['cvv']); ?>">
                            <?php echo htmlspecialchars($card['card_number']); ?>
                        </option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <label for="name">Tên chủ thẻ:</label>
                    <input type="text" id="name" name="name" disabled
                        <?php echo $id_card ? 'value="' . htmlspecialchars($card['firstName'] . ' ' . $card['lastName']) . '"' : ''; ?>>
                    <label for="expiry_date">Ngày hết hạn:</label>
                    <input type="text" id="expiry_date" name="expiry_date" disabled
                        <?php echo $id_card ? 'value="' . htmlspecialchars($card['expDate']) . '"' : ''; ?>>
                    <label for="cvv">CVV:</label>
                    <input type="text" id="cvv" name="cvv" disabled
                        <?php echo $id_card ? 'value="' . htmlspecialchars($card['cvv']) . '"' : ''; ?>>
                    <label for="amount">Số tiền muốn rút:</label>
                    <input type="number" id="amount" name="amount" required>
                    <label for="otp">Nhập mã OTP:</label>
                    <input type="password" id="otp" name="otp" required>
                    <input type="submit" name="withdraw" value="Rút tiền">
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#card_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var name = selectedOption.data('name');
            var expiry_date = selectedOption.data('expiry_date');
            var cvv = selectedOption.data('cvv');

            $('#name').val(name);
            $('#expiry_date').val(expiry_date);
            $('#cvv').val(cvv);
        });


        <?php if (isset($_SESSION['with_draw_error'])) : ?>
        toastr.error("<?php echo $_SESSION['with_draw_error']; ?>");
        <?php unset($_SESSION['with_draw_error']); ?>
        <?php endif; ?>
    });
    </script>
</body>

</html>