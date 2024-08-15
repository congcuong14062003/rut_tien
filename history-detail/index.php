<?php include '../component/header.php'; ?>
<?php include '../component/formatCardNumber.php'; ?>
<?php include '../component/formatAmount.php'; ?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="../component/sidebar.css">
    <link rel="stylesheet" href="./profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Chi tiết giao dịch</title>
</head>

<body>
    <!-- Header -->
    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right">
            <div class="container">
                <h1>Chi tiết giao dịch</h1>
                <?php
                // Lấy id giao dịch từ URL
                $id_history = $_GET['id'];

                // Kết nối cơ sở dữ liệu và lấy chi tiết giao dịch
                $query = "SELECT h.*, c.card_number FROM tbl_history h 
                          LEFT JOIN tbl_card c ON h.id_card = c.id_card 
                          WHERE h.id_history = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $id_history);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $formattedCardNumber = ($row['type'] === "Rút tiền từ thẻ" || $row['type'] === "Thêm thẻ") ? formatCardNumber($row['card_number']) : '';
                    $wallet_address = $user['wallet_address'];

                    function formatWalletAddress($address)
                    {
                        return str_repeat('*', strlen($address));
                    }

                    $formatted_wallet_address = formatWalletAddress($wallet_address);
                    ?>

                    <label for="type">Loại giao dịch:</label>
                    <input disabled type="text" id="type" name="type" value="<?php echo htmlspecialchars($row['type']); ?>">

                    <?php if ($row['type'] === "Thêm thẻ" || $row['type'] === "Rút tiền từ thẻ"): ?>
                        <label for="type">Số thẻ:</label>
                        <input disabled type="text" id="type" name=""
                            value="<?php echo htmlspecialchars($formattedCardNumber); ?>">
                    <?php endif; ?>

                    <?php if ($row['type'] === "Rút tiền từ thẻ" || $row['type'] === "Rút tiền về ví"): ?>
                        <label for="type">Số tiền giao dịch:</label>
                        <input disabled type="text" id="type" name=""
                            value="<?php echo htmlspecialchars(formatAmount($row['amount']) . ($row['amount'] ? " VND" : "")); ?>">
                    <?php endif; ?>
                    <?php if ($row['type'] === "Rút tiền về ví"): ?>
                        <label for="type">Ví nhận tiền:</label>
                        <input disabled type="text" id="type" name=""
                            value="<?php echo htmlspecialchars($formatted_wallet_address); ?>">
                    <?php endif; ?>
                    <label for="type">Thời gian giao dịch:</label>
                    <input disabled type="text" id="type" name=""
                        value="<?php echo htmlspecialchars($row['updated_at']); ?>">
                    <label for="type">Trạng thái:</label>
                    <input disabled type="text" id="type" name="" value="<?php echo htmlspecialchars($row['status']); ?>">
                    <?php
                } else {
                    echo "<p>Không tìm thấy giao dịch.</p>";
                }

                $stmt->close();
                ?>
            </div>
        </div>
    </div>
    <!-- Profile Form -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            // Optional: Add any JavaScript/jQuery code here
        });
    </script>
</body>

</html>