<?php include '../component/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="../component/sidebar.css">
    <title>Lịch sử giao dịch</title>
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    table,
    th,
    td {
        border: 1px solid black;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    .container {
        margin: 20px;
    }

    .title {
        margin-bottom: 20px;
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="container_boby">
        <?php include '../component/sidebar.php'; ?>
        <div class="content_right">
            <div class="container">
                <h1 class="title">Lịch sử giao dịch</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Mã Giao Dịch</th>
                            <th>Loại Giao Dịch</th>
                            <th>Ngày Giao Dịch</th>
                            <th>Số Thẻ</th>
                            <th>Số Tiền Giao Dịch</th>
                            <th>Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>GD12345</td>
                            <td>Rút Tiền</td>
                            <td>2024-08-07</td>
                            <td>1234 5678 9012 3456</td>
                            <td>2,000,000 VND</td>
                            <td>Hoàn Thành</td>
                        </tr>
                        <tr>
                            <td>GD12346</td>
                            <td>Chuyển Khoản</td>
                            <td>2024-08-06</td>
                            <td>9876 5432 1098 7654</td>
                            <td>1,500,000 VND</td>
                            <td>Đang Xử Lý</td>
                        </tr>
                        <tr>
                            <td>GD12347</td>
                            <td>Rút Tiền</td>
                            <td>2024-08-05</td>
                            <td>1234 5678 9012 3456</td>
                            <td>3,000,000 VND</td>
                            <td>Hoàn Thành</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>