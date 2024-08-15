<?php include '../component/header.php'; ?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="../component/sidebar.css">
    <link rel="stylesheet" href="./listcard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Danh sách thẻ</title>
</head>

<body>
    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right">
            <div class="container">
                <h1 class="title">Danh sách thẻ</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Tên Chủ Tài Khoản</th>
                            <th>Số Thẻ</th>
                            <th>Ngày Hết Hạn</th>
                            <th>Trạng Thái</th>
                            <th>Tổng Tiền Đã Rút</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Kết nối cơ sở dữ liệu và lấy danh sách thẻ
                        $query = "SELECT * FROM tbl_card WHERE user_id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('i', $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Hàm định dạng số thẻ
                        function formatCardNumber($cardNumber) {
                            $firstFour = substr($cardNumber, 0, 4);
                            $lastFour = substr($cardNumber, -4);
                            $hiddenPart = str_repeat('*', strlen($cardNumber) - 8);
                            return $firstFour . $hiddenPart . $lastFour;
                        }

                        // Hàm chuyển đổi trạng thái ENUM
                        function getStatusText($status) {
                            switch ($status) {
                                case '0':
                                    return 'init';
                                case '1':
                                    return 'active';
                                case '2':
                                    return 'inactive';
                                default:
                                    return 'Không xác định';
                            }
                        }

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $formattedCardNumber = formatCardNumber($row['card_number']);
                                $formattedAmount = number_format($row['total_amount_success'], 0, ',', '.');
                                $statusText = getStatusText($row['status']);
                                echo "<tr>
                                        <td>{$row['firstName']} {$row['lastName']}</td>
                                        <td>{$formattedCardNumber}</td>
                                        <td>{$row['expDate']}</td>
                                        <td>{$statusText}</td>
                                        <td>{$formattedAmount} VND</td>";

                                if ($row['status'] == '1') { // Check if status is 'active'
                                    echo "<td><a href='/withdraw-visa?id_card={$row['id_card']}' class='btn-withdraw'>Rút tiền</a></td>";
                                } else {
                                    echo "<td></td>";
                                }

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Không có dữ liệu</td></tr>";
                        }

                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['card_success'])) : ?>
        toastr.success("<?php echo $_SESSION['card_success']; ?>");
        <?php unset($_SESSION['card_success']); ?>
        <?php endif; ?>
    });
     <?php if (isset($_SESSION['otp_success'])) : ?>
        toastr.success("<?php echo $_SESSION['otp_success']; ?>");
        <?php unset($_SESSION['otp_success']); ?>
        <?php endif; ?>
    </script>
</body>

</html>