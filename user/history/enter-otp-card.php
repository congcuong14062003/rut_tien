<?php
include '../../component/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /no-permission");
    exit();
}

// Kiểm tra xem người dùng đã gửi biểu mẫu chưa
if (isset($_POST['confirm_otp'])) {
    $otp = $_POST['otp'];
    $id_history = $_GET['id'];  // Lấy id_history từ URL
    
    // Kiểm tra OTP có hợp lệ không (bạn có thể thêm logic kiểm tra tùy ý)
    if (!empty($otp)) {
        // Cập nhật OTP trong bảng tbl_history
        $query = "UPDATE tbl_history SET otp_card = ? WHERE id_history = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si',$otp, $id_history);

        if ($stmt->execute()) {
            // Nếu cập nhật thành công
            $_SESSION['otp_success'] = "Nhập OTP thẻ thành công!";
            header("Location: /user/history");
            exit();
        } else {
            // Nếu cập nhật thất bại
            $_SESSION['otp_error'] = "Nhập OTP thất bại. Vui lòng thử lại.";
        }
        
        $stmt->close();
    } else {
        $_SESSION['otp_error'] = "Vui lòng nhập OTP hợp lệ.";
    }
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
    <link rel="stylesheet" href="../add-card/addcard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>OTP thẻ</title>
    <style>
    input,
    select {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }
    </style>
</head>

<body>
    <div class="container_boby">
        <?php include '../../component/sidebar.php'; ?>
        <div class="content_right container_form">
            <div class="container">
                <h1 class="title">Nhập OTP thẻ</h1>
                <!-- Form nhập OTP -->
                <form id="otp-card-form" method="post" action="">
                    <label for="otp">Nhập mã OTP:</label>
                    <input type="password" id="otp" name="otp" required>
                    <input type="submit" name="confirm_otp" value="Xác Nhận OTP">
                </form>
                <?php
                if (isset($_SESSION['otp_error'])) {
                    echo "<div class='error'>" . $_SESSION['otp_error'] . "</div>";
                    unset($_SESSION['otp_error']);
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['otp_card_error'])): ?>
                toastr.error("<?php echo $_SESSION['otp_card_error']; ?>");
                <?php unset($_SESSION['otp_card_error']); ?>
            <?php endif; ?>
        });
    </script>
</body>

</html>
