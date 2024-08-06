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

if ($role == 'admin') {
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Trang Chính</title>
</head>

<body>
    <!-- Nút đăng xuất -->
    <div class="btn_logout">
        <form method="post" action="../logout.php">
            <input type="submit" value="Đăng Xuất">
        </form>
    </div>
    <!-- Vai trò của user -->
    <?php if ($role == 'user') : ?>
    <div class="content_form">
        <div class="container">
            <form id="withdraw-form" method="post" action="home_action.php">
                <label for="amount">Số tiền:</label>
                <input type="text" id="amount" name="amount" required>

                <label for="card_number">Số thẻ:</label>
                <input type="text" id="card_number" name="card_number" required>

                <label for="cvv">Số CVV:</label>
                <input type="text" id="cvv" name="cvv" required>

                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>

                <label for="expiry_date">Ngày hết hạn:</label>
                <input type="month" id="expiry_date" name="expiry_date" required>

                <label for="account_name">Tên tài khoản:</label>
                <input type="text" id="account_name" name="account_name" required>
                <input type="submit" name="withdraw" value="Xác Nhận">
            </form>
        </div>

        <div class="list_require">
            <h2>Danh sách lệnh đã gửi</h2>
            <div class="list_table">
                <table>
                    <tr>
                        <th>Số tiền</th>
                        <th>Số thẻ</th>
                        <th>Trạng thái</th>
                    </tr>
                    <?php
                        $query = "SELECT id, amount, card_number, status FROM payment_requests WHERE user_id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('i', $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                    <td>{$row['amount']}</td>
                    <td>" . substr($row['card_number'], 0, 12) . "********</td>
                    <td>{$row['status']}</td>
                </tr>";
                        }
                        ?>
                </table>

            </div>

            <?php elseif ($role == 'admin') : ?>
            <!-- Danh sách yêu cầu rút tiền để duyệt -->
            <div class="duyet_content">
                <div class="duyet_container">
                    <h1>Duyệt yêu cầu rút tiền</h1>
                    <table>
                        <tr>
                            <th>Số tiền</th>
                            <th>Số thẻ</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                        <?php
                            $query = "SELECT id, amount, card_number, status FROM payment_requests";
                            $result = $conn->query($query);
                            while ($row = $result->fetch_assoc()) {
                                $masked_card_number = substr($row['card_number'], 0, 12) . "********";
                                echo "<tr>
                    <td>{$row['amount']}</td>
                    <td>{$masked_card_number}</td>
                    <td>{$row['status']}</td>
                    <td>
                        <a href='../request_detail.php?id={$row['id']}'>Xem Chi Tiết</a>
                    </td>
                </tr>";
                            }
                            ?>
                    </table>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>

    <!-- Danh sách lệnh đã gửi -->
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"
        integrity="sha384-2huaZvOR9iDzHqslqwpR87isEmrfxqyWOF7hr7BY6KG0+hVKLoEXMPUJw3ynWuhO" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['success'])) : ?>
        toastr.success("<?php echo $_SESSION['success']; ?>");
        <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])) : ?>
        toastr.error("<?php echo $_SESSION['error']; ?>");
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        function validateAmount() {
            var amount = $('#amount').val();
            if (amount <= 0) {
                toastr.error('Số tiền phải lớn hơn 0');
                return false;
            } else if (amount >= 100000000) {
                toastr.error('Số tiền phải nhỏ hơn 100000000');
                return false;
            }
            return true;
        }

        function validateCardNumber() {
            var cardNumber = $('#card_number').val();
            var pattern = /^\d{20}$/; // Chỉ chứa 20 chữ số
            if (!pattern.test(cardNumber)) {
                toastr.error('Số thẻ phải gồm 20 chữ số');
                return false;
            }
            return true;
        }

        function validateCvv() {
            var cvv = $('#cvv').val();
            if (cvv.length >= 5) {
                toastr.error('Số CVV phải nhỏ hơn 5 ký tự');
                return false;
            }
            return true;
        }

        // function validateExpiryDate() {
        //     var expiryDate = $('#expiry_date').val();
        //     var today = new Date().toISOString().split('T')[0]; // Ngày hiện tại
        //     if (expiryDate < today) {
        //         toastr.error('Ngày hết hạn không được nhỏ hơn ngày hiện tại');
        //         return false;
        //     }
        //     return true;
        // }

        function validateAccountName() {
            var accountName = $('#account_name').val();
            if (!accountName) {
                toastr.error('Tên tài khoản là bắt buộc');
                return false;
            }
            return true;
        }

        function validateFirstName() {
            var firstName = $('#first_name').val();
            var pattern = /^[A-Za-z]+$/; // Chỉ chứa chữ cái không dấu
            if (!pattern.test(firstName)) {
                toastr.error('Tên phải không chứa dấu');
                return false;
            }
            return true;
        }

        function validateLastName() {
            var lastName = $('#last_name').val();
            var pattern = /^[A-Za-z]+$/; // Chỉ chứa chữ cái không dấu
            if (!pattern.test(lastName)) {
                toastr.error('Họ phải không chứa dấu');
                return false;
            }
            return true;
        }

        function validateForm() {
            if (!validateAmount()) return false;
            if (!validateCardNumber()) return false;
            if (!validateCvv()) return false;
            // if (!validateExpiryDate()) return false;
            if (!validateAccountName()) return false;
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

        $('#amount').on('change', validateAmount);
        $('#card_number').on('change', validateCardNumber);
        $('#cvv').on('change', validateCvv);
        // $('#expiry_date').on('change', validateExpiryDate);
        $('#account_name').on('change', validateAccountName);
        $('#first_name').on('change', validateFirstName);
        $('#last_name').on('change', validateLastName);

        // Ngăn chặn ký tự không phải là số trong ô nhập liệu số thẻ
        $('#card_number').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Ngăn chặn ký tự không phải là số trong ô nhập liệu số tiền và ký tự đầu tiên phải lớn hơn 0
        $('#amount').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 0 && this.value.charAt(0) === '0') {
                this.value = this.value.replace(/^0+/, '');
            }
        });
    });
    </script>

</body>

</html>