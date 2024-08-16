<?php
include '../component/header.php';

// Xử lý hành động chấp nhận hoặc từ chối
if (isset($_GET['action']) && isset($_GET['id_card'])) {
    $action = $_GET['action'];
    $id_card = $_GET['id_card'];

    // Kết nối cơ sở dữ liệu và cập nhật trạng thái
    $status = ($action === 'approve') ? '1' : '2';

    $query = "UPDATE tbl_card SET status = ? WHERE id_card = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $status, $id_card);

    if ($stmt->execute()) {
        $message = ($action === 'approve') ? "Chấp nhận thẻ thành công." : "Từ chối thẻ thành công.";
        $_SESSION['card_success'] = $message;
    } else {
        $_SESSION['card_error'] = "Đã xảy ra lỗi khi cập nhật trạng thái thẻ.";
    }

    $stmt->close();
    $conn->close();

    // Chuyển hướng về trang danh sách thẻ
    header('Location: /manager-card-user');
    exit();
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
    <link rel="stylesheet" href="./listcard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Danh sách thẻ</title>
</head>

<body>
    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right">
            <div class="container border_bottom">
                <h1 class="title">Danh sách thẻ</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Tên Chủ Tài Khoản</th>
                            <th>Số Thẻ</th>
                            <th>Ngày Hết Hạn</th>
                            <th>Trạng Thái</th>
                            <th>Tổng Tiền Đã Rút</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Kết nối cơ sở dữ liệu và lấy danh sách thẻ
                        $query = "SELECT * FROM tbl_card";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Hàm định dạng số thẻ
                        function formatCardNumber($cardNumber) {
                            $firstFour = substr($cardNumber, 0, 4);
                            $lastFour = substr($cardNumber, -4);
                            $hiddenPart = str_repeat('*', strlen($cardNumber) - 8);
                            return $firstFour . $hiddenPart . $lastFour;
                        }

                        // Hàm chuyển đổi trạng thái ENUM
                        function getStatusText($status) {
                            switch ($status) {
                                case '0':
                                    return 'init';
                                case '1':
                                    return 'active';
                                case '2':
                                    return 'inactive';
                                default:
                                    return 'Không xác định';
                            }
                        }

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $formattedCardNumber = formatCardNumber($row['card_number']);
                                $formattedAmount = number_format($row['total_amount_success'], 0, ',', '.');
                                $statusText = getStatusText($row['status']);
                                echo "<tr>
                                        <td>{$row['firstName']} {$row['lastName']}</td>
                                        <td>{$formattedCardNumber}</td>
                                        <td>{$row['expDate']}</td>
                                        <td>{$statusText}</td>
                                        <td>{$formattedAmount} VND</td>";

                                // Hiển thị nút chấp nhận và từ chối chỉ khi trạng thái là '0'
                                if ($row['status'] == '0') {
                                    echo "<td>
                                            <button><a href='?action=approve&id_card={$row['id_card']}' class='btn-accept'>Chấp Nhận</a></button>
                                            <button class='btn-decline'><a href='?action=decline&id_card={$row['id_card']}' >Từ Chối</a></button>
                                          </td>";
                                } else {
                                    echo "<td></td>";
                                }

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Không có dữ liệu</td></tr>";
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
    $(document).ready(function() {
        <?php if (isset($_SESSION['card_success'])) : ?>
        toastr.success("<?php echo $_SESSION['card_success']; ?>");
        <?php unset($_SESSION['card_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['card_error'])) : ?>
        toastr.error("<?php echo $_SESSION['card_error']; ?>");
        <?php unset($_SESSION['card_error']); ?>
        <?php endif; ?>
    });
    </script>
</body>

</html>
