<?php
include '../../component/header.php';
?>
<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    // Nếu không phải user, chuyển hướng đến trang thông báo không có quyền
    header("Location: /no-permission");
    exit();
}
?>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_card'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $card_number = $_POST['card_number'];
    $expiry_month = $_POST['expiry_month'];
    $expiry_year = $_POST['expiry_year'];
    $cvv = $_POST['cvv'];

    $expiry_date = $expiry_month . '/' . $expiry_year; // Kết hợp tháng và năm thành định dạng MM/YY

    $sql = "INSERT INTO tbl_card (user_id, card_number, expDate, cvv, firstName, lastName) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $user_id, $card_number, $expiry_date, $cvv, $first_name, $last_name);

    if ($stmt->execute()) {
        $_SESSION['card_success'] = 'Thêm thẻ mới thành công.';
        $_SESSION['new_card_id'] = $stmt->insert_id; // Lưu ID thẻ mới vào session để sử dụng sau
    } else {
        $_SESSION['card_error'] = 'Thêm thẻ mới thất bại';
    }

    $stmt->close();
    $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_otp'])) {
    $otp = $_POST['otp'];
    $card_id = $_SESSION['new_card_id'];
    $type = "Thêm thẻ";
    $sql = "INSERT INTO tbl_history (id_card, otp, user_id, type) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isis", $card_id, $otp, $user_id, $type);

    if ($stmt->execute()) {
        $_SESSION['otp_success'] = 'Xác nhận OTP thành công. Thêm thẻ mới thành công.';
        header("Location: /user/list-card");
    } else {
        $_SESSION['otp_error'] = 'Xác nhận OTP thất bại.';
        header("Location: /user/add-card");
    }

    $stmt->close();
    $conn->close();
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
    <link rel="stylesheet" href="./addcard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Thông Tin Tài Khoản</title>
</head>
<body>
    <div class="container_boby">
        <?php include '../../component/sidebar.php'; ?>
        <div class="content_right container_form">
            <div class="container">
                <h1 class="title">Thêm thẻ</h1>
                <form id="add-card-form" method="post" action="">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                    <label for="card_number">Số thẻ:</label>
                    <input type="text" id="card_number" name="card_number" required>
                    <label for="expiry_month">Tháng hết hạn (MM):</label>
                    <input type="text" id="expiry_month" name="expiry_month" required>
                    <label for="expiry_year">Năm hết hạn (YY):</label>
                    <input type="text" id="expiry_year" name="expiry_year" required>
                    <label for="cvv">Số CVV:</label>
                    <input type="text" id="cvv" name="cvv" required>
                    <input type="submit" name="add_card" value="Xác Nhận">
                </form>

                <!-- Form nhập OTP -->
                <form id="otp-form" method="post" action="" style="display:none;">
                    <label for="otp">Nhập mã OTP:</label>
                    <input type="password" id="otp" name="otp" required>
                    <input type="submit" name="confirm_otp" value="Xác Nhận OTP">
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['card_success'])) : ?>
        $('#otp-form').show(); // Hiển thị form nhập OTP
        $('#add-card-form').hide(); // Ẩn form thêm thẻ
        toastr.success("<?php echo $_SESSION['card_success']; ?>");
        <?php unset($_SESSION['card_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['card_error'])) : ?>
        toastr.error("<?php echo $_SESSION['card_error']; ?>");
        <?php unset($_SESSION['card_error']); ?>
        <?php endif; ?>

       

        <?php if (isset($_SESSION['otp_error'])) : ?>
        toastr.error("<?php echo $_SESSION['otp_error']; ?>");
        <?php unset($_SESSION['otp_error']); ?>
        <?php endif; ?>

        function validateCardNumber() {
            var cardNumber = $('#card_number').val();
            var length = cardNumber.length;
            if (length > 20 || length < 16) {
                toastr.error('Số thẻ phải từ 16 đến 20 chữ số');
                return false;
            }
            return true;
        }

        function validateCvv() {
            var cvv = $('#cvv').val();
            if (cvv.length != 3) {
                toastr.error('Số CVV phải là 3 ký tự');
                return false;
            }
            return true;
        }

        function validateFirstName() {
            var firstName = $('#first_name').val();
            var pattern = /^[A-Za-z\s]+$/; // Chỉ chứa chữ cái và dấu cách
            if (!pattern.test(firstName) || firstName.startsWith(' ')) {
                toastr.error('Tên phải không chứa dấu cách ở đầu và không có dấu');
                return false;
            }
            return true;
        }

        function validateLastName() {
            var lastName = $('#last_name').val();
            var pattern = /^[A-Za-z\s]+$/; // Chỉ chứa chữ cái và dấu cách
            if (!pattern.test(lastName) || lastName.startsWith(' ')) {
                toastr.error('Họ phải không chứa dấu cách ở đầu và không có dấu');
                return false;
            }
            return true;
        }

        function validateExpiryDate() {
            var month = $('#expiry_month').val();
            var year = $('#expiry_year').val();
            if (!/^(0[1-9]|1[0-2])$/.test(month)) {
                toastr.error('Tháng không hợp lệ. Chỉ nhập từ 01 đến 12');
                return false;
            }
            if (!/^\d{2}$/.test(year)) {
                toastr.error('Năm không hợp lệ. Chỉ nhập 2 số cuối của năm');
                return false;
            }
            return true;
        }

        function validateForm() {
            if (!validateCardNumber()) return false;
            if (!validateCvv()) return false;
            if (!validateFirstName()) return false;
            if (!validateLastName()) return false;
            if (!validateExpiryDate()) return false;
            return true;
        }

        $('#add-card-form').on('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return
            }
        });

        $('#card_number').on('change', validateCardNumber);
        $('#cvv').on('change', validateCvv);
        $('#first_name').on('change', validateFirstName);
        $('#last_name').on('change', validateLastName);
        $('#expiry_month').on('change', validateExpiryDate);
        $('#expiry_year').on('change', validateExpiryDate);

        // Ngăn chặn ký tự không phải là số trong ô nhập liệu số thẻ và CVV
        $('#card_number, #cvv, #expiry_month, #expiry_year').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
    </script>
</body>
</html>
