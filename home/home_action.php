<?php
session_start();
include '../db.php';

// Kiểm tra nếu người dùng chưa đăng nhập, chuyển hướng đến trang đăng nhập
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
$role = $user['role']; // 'user' hoặc 'admin'

// Xử lý yêu cầu rút tiền của người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw']) && $role == 'user') {
    $amount = $_POST['amount'];
    $card_number = $_POST['card_number'];
    $cvv = $_POST['cvv'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $expiry_date = $_POST['expiry_date'];
    $account_name = $_POST['account_name'];


    // Chuyển đổi ngày hết hạn sang định dạng DATETIME nếu cần
    $expiry_datetime = $expiry_date; // Giả sử bạn muốn lưu ngày cuối cùng của ngày đó

    // Thêm yêu cầu rút tiền vào cơ sở dữ liệu
    $stmt = $conn->prepare("INSERT INTO payment_requests (user_id, amount, card_number, cvv,  first_name, last_name, expiry_date, account_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iissssss', $user_id, $amount,  $card_number, $cvv, $first_name, $last_name,  $expiry_datetime, $account_name);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Yêu cầu rút tiền đã được gửi!";
        header("Location: /home");
    } else {
        $_SESSION['error'] = "Lỗi khi gửi yêu cầu: " . $stmt->error;
        header("Location: /home");
        exit();
    }
}

// Xử lý hành động duyệt hoặc từ chối yêu cầu của người duyệt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['request_id'])) {
        $request_id = $_POST['request_id'];
        $status = $_POST['action'] === 'approve' ? '200' : '201';

        $stmt = $conn->prepare("UPDATE payment_requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $request_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Thiết lập thông báo vào session
            $_SESSION['success'] = ($status === '200') ? "Yêu cầu đã được duyệt!" : "Yêu cầu đã bị từ chối!";
        } else {
            $_SESSION['error'] = "Lỗi khi cập nhật yêu cầu: " . $stmt->error;
        }

        header("Location: /home"); // Chuyển hướng về trang home
        exit();
    }
}