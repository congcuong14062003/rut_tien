<?php include '../component/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../component/header.css">
    <link rel="stylesheet" href="../component/sidebar.css">
    <title>Danh sách thẻ</title>
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
                <h1 class="title">Danh sách thẻ</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Tên Chủ Tài Khoản</th>
                            <th>Số Thẻ</th>
                            <th>Ngày Hết Hạn</th>
                            <th>Trạng Thái</th>
                            <th>Tổng Tiền Đã Rút</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ví dụ về dữ liệu -->
                        <tr>
                            <td>Nguyễn Văn A</td>
                            <td>1234 5678 9123 4567</td>
                            <td>12/2025</td>
                            <td>Hoạt Động</td>
                            <td>10,000,000 VND</td>
                        </tr>
                        <tr>
                            <td>Trần Thị B</td>
                            <td>9876 5432 1098 7654</td>
                            <td>08/2023</td>
                            <td>Đã Khóa</td>
                            <td>5,000,000 VND</td>
                        </tr>
                        <!-- Thêm nhiều dòng hơn ở đây -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>