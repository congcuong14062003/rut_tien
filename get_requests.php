<?php
session_start();
include './db.php';

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

// Lấy thông tin người dùng từ cơ sở dữ liệu
$user_id = $_SESSION['user_id'];
$query = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role = $user['role'];

if ($role != 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Access denied']);
    exit();
}

// Lấy danh sách yêu cầu rút tiền
$query = "SELECT id, amount, card_number, status FROM payment_requests";
$result = $conn->query($query);

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = [
        'id' => $row['id'],
        'amount' => $row['amount'],
        'card_number' => substr($row['card_number'], 0, 12) . "********",
        'status' => $row['status']
    ];
}

header('Content-Type: application/json');
echo json_encode($requests);
