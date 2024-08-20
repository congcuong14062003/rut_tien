<?php include '../../component/header.php'; ?>
<?php include '../../component/formatCardNumber.php'; ?>
<?php include '../../component/formatAmount.php'; ?>
<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    // Nếu không phải user, chuyển hướng đến trang thông báo không có quyền
    header("Location: /no-permission");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/index.css">
    <link rel="stylesheet" href="../../component/header.css">
    <link rel="stylesheet" href="../../component/sidebar.css">
    <link rel="stylesheet" href="./history.css">
    <title>Lịch sử biến động số dư</title>
</head>

<body>
    <div class="container_boby">
        <?php include '../../component/sidebar.php'; ?>
        <div class="content_right">
            <div class="container border_bottom">
                <h1 class="title">Lịch sử biến động số dư</h1>
                <div class="table_container">
                    <table>
                        <thead>
                            <tr>
                                <th>Số Tiền Giao Dịch</th>
                                <th>Số Dư Trước Giao Dịch</th>
                                <th>Số Dư Sau Giao Dịch</th>
                                <th>Thời Gian Giao Dịch</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $user_id = $user['id'];
                            $history_balance_query = "SELECT hb.balance_fluctuation, hb.balance_before, hb.balance_after, h.type, hb.transaction_date 
                                                      FROM tbl_history_balance hb 
                                                      JOIN tbl_history h ON hb.id_history = h.id_history 
                                                      WHERE hb.user_id = ? 
                                                      ORDER BY hb.transaction_date DESC";
                            $stmt = $conn->prepare($history_balance_query);
                            $stmt->bind_param('i', $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $balance_fluctuation = $row['balance_fluctuation'];
                                    $balance_before = $row['balance_before'];
                                    $balance_after = $row['balance_after'];
                                    $type = $row['type'];
                                    $transaction_date = $row['transaction_date'];

                                    // Format số tiền giao dịch
                                    if ($type == "Rút tiền về ví") {
                                        $formatted_balance_fluctuation = "- " . formatAmount($balance_fluctuation);
                                    } elseif ($type == "Rút tiền từ thẻ") {
                                        $formatted_balance_fluctuation = "+ " . formatAmount($balance_fluctuation);
                                    } else {
                                        $formatted_balance_fluctuation = number_format($balance_fluctuation);
                                    }

                                    // Format số dư trước và sau giao dịch
                                    $formatted_balance_before = formatAmount($balance_before);
                                    $formatted_balance_after = formatAmount($balance_after);

                                    echo "<tr>";
                                    echo "<td>{$formatted_balance_fluctuation}</td>";
                                    echo "<td>{$formatted_balance_before}</td>";
                                    echo "<td>{$formatted_balance_after}</td>";
                                    echo "<td>{$transaction_date}</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr>
                                    <td colspan='4'>Chưa có dữ liệu</td>
                                </tr>";
                            }
                            $stmt->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
