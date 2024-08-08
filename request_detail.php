<?php
session_start();
include './db.php';

// Kiểm tra nếu người dùng chưa đăng nhập, chuyển hướng đến trang đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

// Kiểm tra vai trò của người dùng
$user_id = $_SESSION['user_id'];
$query = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role = $user['role']; // 'user' hoặc 'admin'

// Chỉ cho phép admin truy cập trang này
if ($role !== 'admin') {
    header("Location: /home");
    exit();
}

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
    <link rel="stylesheet" href="./detail.css">
    <title>Chi Tiết Yêu Cầu Rút Tiền</title>
</head>

<body>
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
</body>

</html>