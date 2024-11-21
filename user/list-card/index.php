<?php include '../../component/header.php';?>
<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    // Nếu không phải user, chuyển hướng đến trang thông báo không có quyền
    header("Location: /no-permission");
    exit();
}
?>
<?php
include '../../component/formatCardNumber.php';
include '../../component/formatAmount.php';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/index.css">
    <link rel="stylesheet" href="../../component/header.css">
    <link rel="stylesheet" href="../../component/sidebar.css">
    <link rel="stylesheet" href="./listcard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Danh sách thẻ</title>
</head>

<body>
    <div class="container_boby">
        <?php include '../../component/sidebar.php'; ?>
        <div class="content_right">
            <div class="container border_bottom">
                <h1 class="title">Danh sách thẻ</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Tên Chủ Tài Khoản</th>
                            <th>Số Thẻ</th>
                            <th>Ngày phát hành</th>
                            <th>Ngày Hết Hạn</th>
                            <th>Loại thẻ</th>
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

                        // Hàm chuyển đổi trạng thái ENUM
                        function getStatusText($status) {
                            switch ($status) {
                                case '0':
                                    return 'init';
                                case '1':
                                    return 'thành công';
                                case '2':
                                    return 'thất bại';
                                default:
                                    return 'Không xác định';
                            }
                        }

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $formattedCardNumber = formatCardNumber($row['card_number']);
                                $formattedAmount = formatAmount($row['total_amount_success']);
                                $statusText = getStatusText($row['status']);
                                echo "<tr>
                                        <td>{$row['card_name']}</td>
                                        <td>{$formattedCardNumber}</td>
                                        <td>{$row['issue_date']}</td>
                                        <td>{$row['expDate']}</td>
                                        <td>{$row['card_type']}</td>
                                        <td>{$statusText}</td>
                                        <td>{$formattedAmount}</td>";
                                    echo "<td><a href='/user/withdraw-visa?id_card={$row['id_card']}' class='btn-withdraw'>Rút tiền</a></td>";
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
    </script>
</body>

</html>