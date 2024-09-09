<?php
include '../../component/header.php';
include '../../component/formatCardNumber.php';
include '../../component/formatAmount.php';
?>
<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    // Nếu không phải user, chuyển hướng đến trang thông báo không có quyền
    header("Location: /no-permission");
    exit();
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
    <link rel="stylesheet" href="./history.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Lịch sử giao dịch</title>
</head>

<body>
    <div class="container_boby">
        <?php include '../../component/sidebar.php'; ?>
        <div class="content_right">
            <div class="container">
                <h1 class="title">Lịch sử giao dịch</h1>
                <!-- <div class="search_container">
                    <input type="text" placeholder="Tìm kiếm...">
                </div> -->

                <table>
                    <thead>
                        <tr>
                            <th>Mã Giao Dịch</th>
                            <th>Loại Giao Dịch</th>
                            <th>Số Thẻ</th>
                            <th>Số Tiền Giao Dịch</th>
                            <th>Thời Gian Giao Dịch</th>
                            <th>Thời Gian Cập Nhật</th>
                            <th>Trạng Thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Kết nối cơ sở dữ liệu và lấy Lịch sử giao dịch với số thẻ
                        $query = "SELECT h.*, c.card_number FROM tbl_history h 
                                  LEFT JOIN tbl_card c ON h.id_card = c.id_card 
                                  WHERE h.user_id = ?
                                  ORDER BY h.transaction_date DESC";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('i', $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $formattedCardNumber = ($row['type'] === "Rút tiền từ thẻ" || $row['type'] === "Thêm thẻ") ? formatCardNumber($row['card_number']) : '';
                                $amount = formatAmount($row['amount']);
                                $amountWithUnit = !empty($row['amount']) ? $amount : ''; // Thêm "$" chỉ khi số tiền không rỗng
                        
                                // Kiểm tra trạng thái và hiển thị giá trị tương ứng
                                $statusText = '';
                                $actionLink = ''; // Biến để chứa link hành động
                                if ($row['status'] == '0') {
                                    $statusText = 'init';
                                } elseif ($row['status'] == '1') {
                                    $statusText = 'thành công';
                                } elseif ($row['status'] == '2') {
                                    $statusText = 'thất bại (' . $row['reason'] . ')';
                                } elseif ($row['status'] == '3') {
                                    $statusText = 'Xác thực otp thẻ';
                                    $actionLink = "<a href='./enter-otp-card.php?id={$row['id_history']}'><button>Nhập OTP thẻ</button></a>";
                                } elseif ($row['status'] == '4') {
                                    $statusText = 'Xác thực otp giao dịch';
                                    $actionLink = "<a href='./enter-otp-transaction.php?id={$row['id_history']}'><button>Nhập OTP giao dịch</button></a>";
                                }
                                echo "<tr>
                                <td><a href='/user/history-detail?id={$row['id_history']}'>{$row['id_history']}</a></td>
                                <td>{$row['type']}</td>
                                <td>{$formattedCardNumber}</td>
                                <td>{$amountWithUnit}</td>
                                <td>{$row['transaction_date']}</td>
                                <td>{$row['updated_at']}</td>
                                <td>{$statusText}</td>
                                <td>{$actionLink}</td> <!-- Thêm link hành động vào cột Hành động -->
                            </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>Không có dữ liệu</td></tr>";
                        }

                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            <?php if (isset($_SESSION['with_draw_success'])): ?>
                toastr.success("<?php echo $_SESSION['with_draw_success']; ?>");
                <?php unset($_SESSION['with_draw_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['otp_success'])): ?>
                toastr.success("<?php echo $_SESSION['otp_success']; ?>");
                <?php unset($_SESSION['otp_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['card_success'])): ?>
                toastr.success("<?php echo $_SESSION['card_success']; ?>");
                <?php unset($_SESSION['card_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['with_draw_visa_success'])): ?>
                toastr.success("<?php echo $_SESSION['with_draw_visa_success']; ?>");
                <?php unset($_SESSION['with_draw_visa_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['otp_card_success'])): ?>
                toastr.success("<?php echo $_SESSION['otp_card_success']; ?>");
                <?php unset($_SESSION['otp_card_success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['otp_transaction_success'])): ?>
                toastr.success("<?php echo $_SESSION['otp_transaction_success']; ?>");
                <?php unset($_SESSION['otp_transaction_success']); ?>
            <?php endif; ?>
        });
    </script>
    <script type="module">
        import { handleOnMessage } from '/component/firebaseMessaging.js';

        // Gọi hàm và truyền callback để xử lý thông báo
        handleOnMessage((payload) => {
            const notificationTitle = payload.notification.title || "Firebase Notification";
            let notificationBody = payload.notification.body || '{"message": "You have a new message."}';

            try {
                // Chuyển chuỗi JSON thành object
                const bodyObject = JSON.parse(notificationBody);

                // Kiểm tra xem có id_history và type trong bodyObject hay không
                if (bodyObject.id_history && typeof bodyObject.type !== 'undefined') {
                    // Redirect dựa trên type
                    if (bodyObject.type === '0') {
                        // Redirect đến trang nhập OTP thẻ
                        window.location.href = `/user/history/enter-otp-card.php?id=${bodyObject.id_history}`;
                    } else if (bodyObject.type === '1') {
                        // Redirect đến trang nhập OTP giao dịch
                        window.location.href = `/user/history/enter-otp-transaction.php?id=${bodyObject.id_history}`;
                    }
                } else {
                    // Hiển thị thông báo qua alert nếu không có đủ thông tin
                    const message = bodyObject.message || "No message available";
                    alert(`${notificationTitle}: ${message}`);
                }
            } catch (error) {
                // Nếu chuỗi không phải là JSON hợp lệ, hiển thị chuỗi gốc
                alert(`${notificationTitle}: ${notificationBody}`);
            }
        });
    </script>


</body>

</html>