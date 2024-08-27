<?php
include '../../component/header.php';
include '../../component/validateCardNumber.php';

// Kiểm tra quyền truy cập
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /no-permission");
    exit();
}

// Xử lý thêm thẻ mới
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_card'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $card_number = $_POST['card_number'];
    $expiry_month = $_POST['expiry_month'];
    $expiry_year = $_POST['expiry_year'];
    $cvv = $_POST['cvv'];
    $country = $_POST['country'];
    $phone_number = $_POST['phone_number'];
    $postal_code = $_POST['postal_code'];
    $billing_address = $_POST['billing_address'];
    $expiry_date = $expiry_month . '/' . $expiry_year; // Kết hợp tháng và năm thành định dạng MM/YY
    // Thêm thẻ vào bảng tbl_card
    $status_card = '1';
    $sql = "INSERT INTO tbl_card (user_id, card_number, expDate, cvv, firstName, lastName, country, phone_number, postal_code, billing_address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssssss", $user_id, $card_number, $expiry_date, $cvv, $first_name, $last_name, $country, $phone_number, $postal_code, $billing_address, $status_card);
    if ($stmt->execute()) {
        $_SESSION['new_card_id'] = $stmt->insert_id; // Lưu ID thẻ mới vào session để sử dụng sau
        $stmt->close();
        // Thêm bản ghi vào bảng tbl_history
        $type = "Thêm thẻ";
        $status = '1'; // Status = 1 cho thành công
        $sql = "INSERT INTO tbl_history (id_card, user_id, type, status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $_SESSION['new_card_id'], $user_id, $type, $status);
        if ($stmt->execute()) {
            $_SESSION['card_success'] = 'Thêm thẻ mới thành công.';
            header("Location: /user/history");
        } else {
            $_SESSION['card_error'] = 'Thêm thẻ mới thất bại';
        }
        $stmt->close();
    } else {
        $_SESSION['card_error'] = 'Thêm thẻ mới thất bại';
        $stmt->close();
    }
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
                    <label for="country">Quốc gia:</label>
                    <input type="text" id="country" name="country" required>
                    <label for="phone_number">Số điện thoại:</label>
                    <input type="text" id="phone_number" name="phone_number" required>
                    <label for="postal_code">Postal code:</label>
                    <input type="text" id="postal_code" name="postal_code" required>
                    <label for="billing_address">Địa chỉ thanh toán:</label>
                    <input type="text" id="billing_address" name="billing_address" required>
                    <input type="submit" name="add_card" value="Xác Nhận">
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            <?php if (isset($_SESSION['card_error'])): ?>
                toastr.error("<?php echo $_SESSION['card_error']; ?>");
                <?php unset($_SESSION['card_error']); ?>
            <?php endif; ?>

            function isValidLuhn(number) {
                let sum = 0;
                let shouldDouble = false;

                for (let i = number.length - 1; i >= 0; i--) {
                    let digit = parseInt(number.charAt(i), 10);

                    if (shouldDouble) {
                        digit *= 2;
                        if (digit > 9) {
                            digit -= 9;
                        }
                    }
                    sum += digit;
                    shouldDouble = !shouldDouble;
                }
                return (sum % 10 === 0);
            }
            function validateCardNumber() {
                var cardNumber = $('#card_number').val().replace(/\s+/g, ''); // Loại bỏ khoảng trắng
                var length = cardNumber.length;
                if (length > 20 || length < 16) {
                    toastr.error('Số thẻ phải từ 16 đến 20 chữ số');
                    return false;
                } else if (!isValidLuhn(cardNumber)) {
                    toastr.error('Số thẻ không hợp lệ');
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
            function validateMonth() {
                var month = $('#expiry_month').val();
                if (!/^(0[1-9]|1[0-2])$/.test(month)) {
                    toastr.error('Tháng không hợp lệ. Chỉ nhập từ 01 đến 12');
                    return false;
                }
                return true;
            }
            function validateYeah() {
                var year = $('#expiry_year').val();
                if (!/^\d{2}$/.test(year)) {
                    toastr.error('Năm không hợp lệ. Chỉ nhập 2 số cuối của năm');
                    return false;
                }
                return true;
            }
            function validateNamesLength() {
                var firstName = $('#first_name').val().trim();
                var lastName = $('#last_name').val().trim();
                var totalLength = firstName.length + lastName.length;
                if (firstName && lastName) {
                    if (totalLength < 5) {
                        toastr.error('Họ và tên cộng lại phải có ít nhất 5 ký tự');
                        return false;
                    }
                }
                return true;
            }
            function validateForm() {
                if (!validateFirstName()) return false;
                if (!validateLastName()) return false;
                if (!validateNamesLength()) return false;
                if (!validateCardNumber()) return false;
                if (!validateMonth()) return false;
                if (!validateYeah()) return false;
                if (!validateCvv()) return false;
                return true;
            }

            $('#add-card-form').on('submit', function (e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return;
                }
            });

            $('#card_number').on('change', validateCardNumber);
            $('#cvv').on('change', validateCvv);
            $('#first_name').on('change', validateFirstName);
            $('#first_name').on('change', validateNamesLength);
            $('#last_name').on('change', validateLastName);
            $('#last_name').on('change', validateNamesLength);
            $('#expiry_month').on('change', validateMonth);
            $('#expiry_year').on('change', validateYeah);

            // Ngăn chặn ký tự không phải là số trong ô nhập liệu số thẻ và CVV
            $('#card_number, #expiry_month, #expiry_year, #phone_number').on('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>

</html>