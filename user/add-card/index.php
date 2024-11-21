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
    $card_number = $_POST['card_number'];
    $card_name = $_POST['card_name'];
    $expiry_month = $_POST['expiry_month'];
    $expiry_year = $_POST['expiry_year'];
    $issue_month = $_POST['issue_month'];
    $issue_year = $_POST['issue_year'];
    $cvv = !empty($_POST['cvv']) ? $_POST['cvv'] : null; // CVV không bắt buộc
    $country = $_POST['country'];
    $phone_number = $_POST['phone_number'];
    $postal_code = $_POST['postal_code'];
    $billing_address = $_POST['billing_address'];
    $card_type = $_POST['card_type']; // Bổ sung loại thẻ
    $expiry_date = $expiry_month . '/' . $expiry_year;
    $issue_date = $issue_month . '/' . $issue_year;

    // Thêm thẻ vào bảng tbl_card
    $status_card = '1';
    // Thêm thẻ vào bảng tbl_card
    $sql = "INSERT INTO tbl_card (user_id, card_number, card_name, expDate, issue_date, cvv, country, phone_number, postal_code, billing_address, status, card_type)
   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssssss", $user_id, $card_number, $card_name, $expiry_date, $issue_date, $cvv, $country, $phone_number, $postal_code, $billing_address, $status_card, $card_type);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <title>Thông Tin Tài Khoản</title>
</head>

<body>
    <div class="container_boby">
        <?php include '../../component/sidebar.php'; ?>
        <div class="content_right container_form">
            <div class="container">
                <h1 class="title">Thêm thẻ</h1>
                <form id="add-card-form" method="post" action="">
                    <label for="card_name">Card Name (<span style="color: red;"> *</span>):</label>
                    <input type="text" id="card_name" name="card_name" required>

                    <label for="card_number">Số thẻ (<span style="color: red;"> *</span>):</label>
                    <input type="text" id="card_number" name="card_number" required>

                    <label for="issue_month">Tháng phát hành (MM):</label>
                    <input type="text" id="issue_month" name="issue_month">

                    <label for="issue_year">Năm phát hành (YY):</label>
                    <input type="text" id="issue_year" name="issue_year">

                    <label for="expiry_month">Tháng hết hạn (MM) (<span style="color: red;"> *</span>):</label>
                    <input type="text" id="expiry_month" name="expiry_month" required>

                    <label for="expiry_year">Năm hết hạn (YY) (<span style="color: red;"> *</span>):</label>
                    <input type="text" id="expiry_year" name="expiry_year" required>

                    <label for="cvv">Số CVV:</label>
                    <input type="text" id="cvv" name="cvv">

                    <label for="card_type">Loại thẻ:</label>
                    <select id="card_type" name="card_type" required>
                        <option value="" disabled selected>Chọn loại thẻ</option>
                        <option value="ATM">ATM</option>
                        <option value="VISA nội địa">VISA nội địa</option>
                        <!-- <option selected value="VISA quốc tế">VISA quốc tế</option> -->
                    </select>

                    <label for="country">Quốc gia:</label>
                    <select id="country" name="country" required></select>

                    <!-- Sử dụng select thay vì input -->
                    <!-- <label for="phone_number">Số điện thoại:</label>
                    <input required type="tel" id="phone_number" name="phone_number"> -->


                    <!-- <label for="postal_code">Postal code:</label>
                    <input type="text" id="postal_code" name="postal_code" required> -->

                    <!-- <label for="billing_address">Địa chỉ thanh toán:</label>
                    <input type="text" id="billing_address" name="billing_address" required> -->

                    <input type="submit" name="add_card" value="Xác Nhận">
                </form>
            </div>
        </div>
    </div>
    <!-- Thêm thư viện cho countries.json -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countries/1.0.0/countries.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            // Khởi tạo intl-tel-input với quốc gia mặc định là Việt Nam
            // var input = document.querySelector("#phone_number");
            // var iti = window.intlTelInput(input, {
            //     initialCountry: "vn", // Quốc gia mặc định là Việt Nam
            //     utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
            // });

            // Tải danh sách quốc gia cho input country từ countries.json
            $.getJSON("https://restcountries.com/v3.1/all", function (data) {
                console.log(data); // Kiểm tra dữ liệu
                let countrySelect = $('#country');
                data.forEach(function (country) {
                    countrySelect.append(
                        $('<option>', {
                            value: country.name.common, // Lưu tên đầy đủ
                            text: country.name.common // Hiển thị tên đầy đủ
                        })
                    );
                });

                // Thiết lập quốc gia mặc định là Việt Nam
                countrySelect.val("Vietnam").change();
            });

            // Cập nhật mã điện thoại khi quốc gia thay đổi
            // $('#country').on('change', function () {
            //     var countryCode = $(this).val();
            //     iti.setCountry(countryCode);
            // });
        });
    </script>

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

            function validateMonthIssue() {
                var month = $('#issue_month').val();
                if (!month) return true; // Nếu để trống, bỏ qua validate
                if (!/^(0[1-9]|1[0-2])$/.test(month)) {
                    toastr.error('Tháng phát hành không hợp lệ. Chỉ nhập từ 01 đến 12');
                    return false;
                }
                return true;
            }

            function validateYeahIssue() {
                var year = $('#issue_year').val();
                if (!year) return true; // Nếu để trống, bỏ qua validate
                if (!/^\d{2}$/.test(year)) {
                    toastr.error('Năm phát hành không hợp lệ. Chỉ nhập 2 số cuối của năm');
                    return false;
                }
                return true;
            }

            function validateNamesLength() {
                // var cardname = $('#card_name').val().trim();
                // if (cardname > 0) {
                //     return true;
                // }
            }
            function validateForm() {
                // if (!validateCardNumber()) return false;
                if (!validateMonth()) return false;
                if (!validateYeah()) return false;
                if (!validateMonthIssue()) return false;
                if (!validateYeahIssue()) return false;
                return true;
            }


            $('#add-card-form').on('submit', function (e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return;
                }
            });

            // $('#card_number').on('change', validateCardNumber);
            // $('#cvv').on('change', validateCvv);
            // $('#first_name').on('change', validateFirstName);
            // $('#first_name').on('change', validateNamesLength);
            // $('#last_name').on('change', validateLastName);
            // $('#last_name').on('change', validateNamesLength);
            $('#expiry_month').on('change', validateMonth);
            $('#expiry_year').on('change', validateYeah);
            $('#issue_month').on('change', validateMonthIssue);
            $('#issue_year').on('change', validateYeahIssue);

            // Ngăn chặn ký tự không phải là số trong ô nhập liệu số thẻ và CVV
            $('#card_number, #expiry_month, #expiry_year, #issue_month, #issue_year, #phone_number').on('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>

</html>