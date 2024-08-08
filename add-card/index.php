<?php include '../component/header.php'; ?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];

    $sql = "INSERT INTO tbl_card (user_id, card_number, expDate, cvv, firstName, lastName) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $user_id, $card_number, $expiry_date, $cvv, $first_name, $last_name);

    if ($stmt->execute()) {
        $_SESSION['card_success'] = 'Thêm thẻ mới thành công';
        header("Location: /list-card");
        exit();
    } else {
        $_SESSION['card_error'] = 'Thêm thẻ mới thất bại';
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
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="../component/sidebar.css">
    <link rel="stylesheet" href="./addcard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Thông Tin Tài Khoản</title>
</head>

<body>
    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right">
            <div class="container">
                <h1 class="title">Thêm thẻ</h1>
                <form id="withdraw-form" method="post" action="">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                    <label for="card_number">Số thẻ:</label>
                    <input type="text" id="card_number" name="card_number" required>
                    <label for="expiry_date">Ngày hết hạn:</label>
                    <input type="month" id="expiry_date" name="expiry_date" required>
                    <label for="cvv">Số CVV:</label>
                    <input type="text" id="cvv" name="cvv" required>
                    <input type="submit" name="withdraw" value="Xác Nhận">
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['card_success'])) : ?>
                toastr.success("<?php echo $_SESSION['card_success']; ?>");
                <?php unset($_SESSION['card_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['card_error'])) : ?>
                toastr.error("<?php echo $_SESSION['card_error']; ?>");
                <?php unset($_SESSION['card_error']); ?>
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

            function validateForm() {
                if (!validateCardNumber()) return false;
                if (!validateCvv()) return false;
                if (!validateFirstName()) return false;
                if (!validateLastName()) return false;
                return true;
            }

            $('#withdraw-form').on('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return
                }
            });

            $('#card_number').on('change', validateCardNumber);
            $('#cvv').on('change', validateCvv);
            $('#first_name').on('change', validateFirstName);
            $('#last_name').on('change', validateLastName);

            // Ngăn chặn ký tự không phải là số trong ô nhập liệu số thẻ
            $('#card_number').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>

</html>