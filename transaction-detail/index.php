<?php include '../component/header.php'; ?>
<?php
$role = $user['role']; // 'user' hoặc 'admin'

// Chỉ cho phép admin truy cập trang này
// if ($role !== 'admin') {
//     header("Location: /home");
//     exit();
// }

// Lấy chi tiết yêu cầu rút tiền
$request_id = $_GET['id'];
$query = "SELECT * FROM payment_requests WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $request_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="../component/sidebar.css">
    <title>Thông Tin Tài Khoản</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .container {
            margin: 20px;
        }

        .title {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right">
            <!-- <div class="container">
                <h1 class="title">Thông Tin Tài Khoản</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Mã Giao Dịch</th>
                            <th>Loại Giao Dịch</th>
                            <th>Ngày Giao Dịch</th>
                            <th>Số Thẻ</th>
                            <th>Số Tiền Giao Dịch</th>
                            <th>Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>GD12345</td>
                            <td>Rút Tiền</td>
                            <td>2024-08-07</td>
                            <td>1234 5678 9012 3456</td>
                            <td>2,000,000 VND</td>
                            <td>Hoàn Thành</td>
                        </tr>
                        <tr>
                            <td>GD12346</td>
                            <td>Chuyển Khoản</td>
                            <td>2024-08-06</td>
                            <td>9876 5432 1098 7654</td>
                            <td>1,500,000 VND</td>
                            <td>Đang Xử Lý</td>
                        </tr>
                        <tr>
                            <td>GD12347</td>
                            <td>Rút Tiền</td>
                            <td>2024-08-05</td>
                            <td>1234 5678 9012 3456</td>
                            <td>3,000,000 VND</td>
                            <td>Hoàn Thành</td>
                        </tr>

                    </tbody>
                </table>
            </div> -->
            <div class="container">
                <h1>Chi Tiết Yêu Cầu Rút Tiền</h1>
                <table>
                    <tr>
                        <th>Số tiền</th>
                        <td><?php echo htmlspecialchars($request['amount']); ?></td>
                    </tr>
                    <tr>
                        <th>Số thẻ</th>
                        <td><?php echo htmlspecialchars(substr($request['card_number'], 0, 12) . "********"); ?></td>
                    </tr>
                    <tr>
                        <th>Số CVV</th>
                        <td><?php echo htmlspecialchars(str_repeat('*', strlen($request['cvv']))); ?></td>
                    </tr>
                    <tr>
                        <th>Firstname</th>
                        <td><?php echo htmlspecialchars($request['first_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Lastname</th>
                        <td><?php echo htmlspecialchars($request['last_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Ngày hết hạn</th>
                        <td>
                            <?php
                            $expiry_date = new DateTime($request['expiry_date']);
                            echo htmlspecialchars($expiry_date->format('m/Y'));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Tên tài khoản</th>
                        <td><?php echo htmlspecialchars($request['account_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td><?php echo htmlspecialchars($request['status']); ?></td>
                    </tr>
                </table>

                <?php if ($request['status'] == 99) : ?>
                    <div class="actions">
                        <form method="post" action="./home/home_action.php" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                            <button type="submit" name="action" value="approve">Duyệt</button>
                        </form>
                        <form method="post" action="./home/home_action.php" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                            <button type="submit" name="action" value="reject" class="reject">Từ chối</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>