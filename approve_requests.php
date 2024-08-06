<?php
session_start();
include 'db.php';

// Kiểm tra quyền người dùng
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'approver' && $_SESSION['role'] !== 'admin')) {
    header("Location: /login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['request_id'])) {
        $request_id = $_POST['request_id'];
        $status = $_POST['action'] === 'approve' ? '200' : '201';
        
        $stmt = $conn->prepare("UPDATE payment_requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $request_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo ($status === '200') ? "Yêu cầu đã được duyệt!" : "Yêu cầu đã bị từ chối!";
        } else {
            echo "Lỗi khi cập nhật yêu cầu: " . $stmt->error;
        }
    }
}

// Hiển thị danh sách yêu cầu đang chờ xử lý
$result = $conn->query("SELECT id, amount, card_number, status FROM payment_requests WHERE status = '99'");
?>
<table>
    <tr>
        <th>Số tiền</th>
        <th>Số thẻ</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
    </tr>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['amount']}</td>
            <td>" . substr($row['card_number'], 0, 8) . "****</td>
            <td>{$row['status']}</td>
            <td>
                <a href='approve_requests.php?id={$row['id']}'>Xem chi tiết</a>
            </td>
        </tr>";
    }
    ?>
</table>

<?php
// Hiển thị chi tiết yêu cầu nếu có ID trong URL
if (isset($_GET['id'])) {
    $request_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM payment_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();
    ?>
<h2>Chi tiết yêu cầu</h2>
<form method="post">
    Số tiền: <?php echo htmlspecialchars($request['amount']); ?><br>
    Số thẻ: <?php echo htmlspecialchars(substr($request['card_number'], 0, 8) . "****"); ?><br>
    Firstname: <?php echo htmlspecialchars($request['first_name']); ?><br>
    Lastname: <?php echo htmlspecialchars($request['last_name']); ?><br>
    Ngày hết hạn: <?php echo htmlspecialchars($request['expiry_date']); ?><br>
    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request_id); ?>">
    <input type="submit" name="action" value="approve">
    <input type="submit" name="action" value="reject">
</form>
<?php
}
?>