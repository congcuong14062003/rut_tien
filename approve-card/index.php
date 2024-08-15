<?php include '../component/header.php'; ?>
<?php
// Kiểm tra nếu người dùng chưa đăng nhập hoặc không phải admin, chuyển hướng đến trang đăng nhập hoặc trang không có quyền
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: /unauthorized");
    exit();
}

// Lấy danh sách yêu cầu rút tiền từ cơ sở dữ liệu
$query = "SELECT h.id_history, c.card_number, CONCAT(c.firstName, ' ', c.lastName) AS cardholder_name, c.expDate, c.cvv, h.amount, h.status
          FROM tbl_history h
          JOIN tbl_card c ON h.id_card = c.id_card
          WHERE h.type = 'Rút tiền từ thẻ'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách yêu cầu rút tiền</title>
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="../component/sidebar.css">
    <link rel="stylesheet" href="../history/history.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body>
    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right">
            <div class="container">
                <h1 style="text-align: center">Danh sách yêu cầu rút tiền</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Số thẻ</th>
                            <th>Tên chủ thẻ</th>
                            <th>Ngày hết hạn</th>
                            <th>CVV</th>
                            <th>Số tiền muốn rút</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['card_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['cardholder_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['expDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['cvv']); ?></td>
                                <td><?php echo number_format($row['amount'], 0, ',', '.'); ?> VND</td>
                                <td>
                                    <?php
                                    switch ($row['status']) {
                                        case '0':
                                            echo 'init';
                                            break;
                                        case '1':
                                            echo 'active';
                                            break;
                                        case '2':
                                            echo 'inactive';
                                            break;
                                        default:
                                            echo 'Unknown';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == '0') { ?>
                                        <form method="post" action="process_request.php">
                                            <input type="hidden" name="id_history"
                                                value="<?php echo htmlspecialchars($row['id_history']); ?>">
                                            <button type="submit" name="action" value="approve">Duyệt</button>
                                            <button type="submit" name="action" value="reject">Từ chối</button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Profile Form -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            <?php if (isset($_SESSION['success_update'])): ?>
                toastr.success("<?php echo $_SESSION['success_update']; ?>");
                <?php unset($_SESSION['success_update']); ?>
            <?php elseif (isset($_SESSION['error_update'])): ?>
                toastr.error("<?php echo $_SESSION['error_update']; ?>");
                <?php unset($_SESSION['error_update']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
