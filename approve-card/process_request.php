<?php
session_start();
include '../db.php';

// Kiểm tra nếu người dùng chưa đăng nhập hoặc không phải admin, chuyển hướng đến trang đăng nhập hoặc trang không có quyền
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: /unauthorized");
    exit();
}

// Xử lý duyệt hoặc từ chối yêu cầu rút tiền
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_history = intval($_POST['id_history']);
    $action = $_POST['action'];

    if ($action == 'approve') {
        $status = '1';
    } elseif ($action == 'reject') {
        $status = '2';
    } else {
        // Hành động không hợp lệ
        header("Location: /unauthorized");
        exit();
    }

    // Cập nhật trạng thái yêu cầu
    $query = "UPDATE tbl_history SET status = ? WHERE id_history = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $id_history);
    if ($stmt->execute()) {
        header("Location: /withdrawal_requests.php"); // Quay lại trang danh sách yêu cầu
    } else {
        echo "Có lỗi xảy ra khi cập nhật yêu cầu.";
    }
}
?>
