<?php
include '../../component/header.php';
include '../../component/formatCardNumber.php';
include '../../component/configRate.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /no-permission");
    exit();
}

// Lấy thông tin từ URL
$id_card = isset($_GET['id_card']) ? $_GET['id_card'] : '';
// Lấy giá trị token từ cookie

// Khai báo biến danh sách thẻ
$cards = [];
$selected_card_id = $id_card; // ID thẻ sẽ được chọn tự động nếu có

// Lấy tất cả các thẻ của người dùng
$cards_query = "SELECT * FROM tbl_card WHERE user_id = ? AND status = '1'";
$stmt = $conn->prepare($cards_query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$cards_result = $stmt->get_result();
while ($card = $cards_result->fetch_assoc()) {
    $cards[] = $card;
}
$stmt->close();

// Lấy thông tin thẻ từ cơ sở dữ liệu nếu có id_card
$card_info = null;
if ($id_card) {
    $query = "SELECT * FROM tbl_card WHERE id_card = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_card);
    $stmt->execute();
    $result = $stmt->get_result();
    $card_info = $result->fetch_assoc();
    $stmt->close();
}

// Xử lý form rút tiền
// Lấy giá trị token từ SESSION
$token_user = isset($_SESSION['token_user']) ? $_SESSION['token_user'] : '';


// Xử lý form rút tiền
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw'])) {
    $amount = $_POST['hidden_amount'];
    $card_id = $_POST['card_id'];
    // $amount_after_rate = $amount - $RATE * $amount / 100;

    $history_query = "INSERT INTO tbl_history (user_id, type, amount, transaction_date, updated_at, id_card, token_user) VALUES (?, ?, ?, NOW(), NOW(), ?, ?)";
    $stmt = $conn->prepare($history_query);
    $type = 'Rút tiền từ thẻ';
    $stmt->bind_param('isiss', $_SESSION['user_id'], $type, $amount, $card_id, $token_user);

    if ($stmt->execute()) {


        $_SESSION['with_draw_visa_success'] = "Yêu cầu rút tiền thành công";
        header("Location: /user/history");
    } else {
        $_SESSION['with_draw_visa_error'] = "Yêu cầu rút tiền thất bại!";
    }
    $stmt->close();
}
$conn->close();
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
    <title>Rút tiền</title>
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
                <h1 class="title">Rút tiền từ thẻ</h1>
                <!-- Form rút tiền -->
                <form id="withdraw-form" method="post" action="">
                    <label for="card_id">Số thẻ:</label>
                    <select id="card_id" name="card_id" required>
                        <option value="" disabled selected>Chọn thẻ</option>
                        <?php foreach ($cards as $card): ?>
                            <option value="<?php echo $card['id_card']; ?>"
                                data-name="<?php echo htmlspecialchars($card['card_name']); ?>"
                                data-issue_date="<?php echo htmlspecialchars($card['issue_date']); ?>"
                                data-expiry_date="<?php echo htmlspecialchars($card['expDate']); ?>"
                                data-card_type="<?php echo htmlspecialchars($card['card_type']); ?>"
                                data-cvv="<?php echo htmlspecialchars($card['cvv']); ?>" <?php echo $selected_card_id == $card['id_card'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(formatCardNumber($card['card_number'])) . ' - ' . htmlspecialchars($card['card_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="name">Tên chủ thẻ:</label>
                    <input type="text" id="name" name="name" disabled
                        value="<?php echo $card_info ? htmlspecialchars($card_info['card_name']) : ''; ?>">

                    <label for="issue_date">Ngày phát hành:</label>
                    <input type="text" id="issue_date" name="issue_date" disabled
                        value="<?php echo $card_info ? htmlspecialchars($card_info['issue_date']) : ''; ?>">
                    <label for="expiry_date">Ngày hết hạn:</label>
                    <input type="text" id="expiry_date" name="expiry_date" disabled
                        value="<?php echo $card_info ? htmlspecialchars($card_info['expDate']) : ''; ?>">

                    <label for="card_type">Loại thẻ:</label>
                    <input type="text" id="card_type" name="card_type" disabled
                        value="<?php echo $card_info ? htmlspecialchars($card_info['card_type']) : ''; ?>">
                    <label for="cvv">CVV:</label>
                    <input type="text" id="cvv" name="cvv" disabled
                        value="<?php echo $card_info ? htmlspecialchars($card_info['cvv']) : ''; ?>">
                    <label for="amount">Số tiền muốn rút:</label>
                    <input type="text" id="amount" name="amount" required>
                    <input type="hidden" id="hidden_amount" name="hidden_amount">
                    <input type="submit" name="withdraw" value="Rút tiền">
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            <?php if (isset($_SESSION['with_draw_visa_error'])): ?>
                toastr.success("<?php echo $_SESSION['with_draw_visa_error']; ?>");
                <?php unset($_SESSION['with_draw_visa_error']); ?>
            <?php endif; ?>
            // Function to format numbers
            function formatNumber(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Function to validate amount
            function validateAmount(amount) {
                var numericValue = parseInt(amount.replace(/\./g, ''), 10);
                if (numericValue > 400 || numericValue <= 0) {
                    toastr.error("Vui lòng nhập số tiền hợp lệ (từ 1$ đến 400$)");
                    return false;
                }
                return true;
            }

            // Format the amount input field
            $('#amount').on('input', function () {
                var value = $(this).val().replace(/\./g, '');

                if (value.length > 1 && value[0] === '0') {
                    value = value.slice(1);
                }

                value = value.replace(/[^0-9]/g, '');

                var formattedValue = formatNumber(value);
                $(this).val(formattedValue);
                $('#hidden_amount').val(value);
                validateAmount(value);
            });

            // Validate the amount before form submission
            $('#withdraw-form').on('submit', function (event) {
                var amount = $('#hidden_amount').val();
                if (!validateAmount(amount)) {
                    event.preventDefault();
                }
            });

            // Auto-fill card information on card selection
            $('#card_id').on('change', function () {
                var selectedOption = $(this).find('option:selected');
                var name = selectedOption.data('name');
                var expiry_date = selectedOption.data('expiry_date');
                var issue_date = selectedOption.data('issue_date');
                var card_type = selectedOption.data('card_type');
                var cvv = selectedOption.data('cvv');

                $('#name').val(name);
                $('#expiry_date').val(expiry_date);
                $('#cvv').val(cvv);
                $('#issue_date').val(issue_date); // Thêm dòng này
                $('#card_type').val(card_type);   // Và dòng này
            });
        });
    </script>
</body>

</html>