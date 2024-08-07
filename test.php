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
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role = $user['role']; // 'user' hoặc 'admin'
$username = $user['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./home.css">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="../component/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Trang Chính</title>
</head>

<body>
    <?php include '../component/header.php'; ?>
    <?php include '../component/sidebar.php'; ?>
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
            var length = cardNumber.length;
            if (length > 20) {
                toastr.error('Số thẻ phải nhỏ hơn hoặc bằng 20 chữ số');
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















body {
font-family: Arial, sans-serif;
background-color: #f0f0f0;
color: #333;
margin: 0;
padding: 0;
}
/* .header_container {
display: flex;
align-items: center;
justify-content: space-between;
padding: 20px;
} */
.content_form {
display: flex;
padding: 20px;
padding-top: 0;
height: 82vh;
}
.btn_logout {
width: 100px;
}
.btn_logout input {
width: 100% !important;
margin: 0 !important;
}

.container {
width: 30%;
height: 100%;
padding: 20px;
background-color: #fff;
border-radius: 8px;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
.list_require {
padding: 20px;
border-radius: 8px;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
background-color: #fff;
margin-left: 20px;
width: 70%;
height: 100%;
overflow: auto;
}
.duyet_content {
padding: 20px;
padding-top: 0;
}
.duyet_container {
padding: 20px;
border-radius: 8px;
background-color: #fff;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);

}
h1, h2 {
text-align: center;
color: #333;
}

form {
display: flex;
flex-direction: column;
align-items: center;
}
form label {
width: 100%;
font-weight: bold;
text-align: left; /* Căn chữ trong thẻ label về bên trái */
}
.error-message {
color: red;
text-align: center;
}

form input[type="number"],
form input[type="text"],
form input[type="date"],
form input[type="month"],
form input[type="submit"] {
width: 100%;
padding: 10px;
margin: 10px 0;
border: 1px solid #ccc;
border-radius: 4px;
font-size: 16px;
}

form input[type="submit"] {
background-color: #4CAF50;
color: white;
cursor: pointer;
}

form input[type="submit"]:hover {
background-color: #45a049;
}

table {
width: 100%;
border-collapse: collapse;
margin-top: 20px;
}

table, th, td {
border: 1px solid #ddd;
}

th, td {
padding: 8px;
text-align: left;
}

th {
background-color: #f2f2f2;
}

tr:nth-child(even) {
background-color: #f9f9f9;
}

tr:hover {
background-color: #f1f1f1;
}

button {
padding: 10px;
margin: 5px;
border: none;
border-radius: 4px;
cursor: pointer;
}

button[type="submit"] {
background-color: #4CAF50;
color: white;
}

button[type="submit"]:hover {
background-color: #45a049;
}

button[name="action"][value="reject"] {
background-color: #f44336;
}

button[name="action"][value="reject"]:hover {
background-color: #e53935;
}



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
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role = $user['role']; // 'user' hoặc 'admin'
$username = $user['username'];
?>