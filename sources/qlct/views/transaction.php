<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Giao dịch của <?= htmlspecialchars($tenkh) ?></title>
    <style>
        body {
            background-color: #c5eed0;
            font-family: Arial;
            padding: 20px;
        }

        .table-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        table {
            border-collapse: collapse;
            min-width: 800px;
            text-align: center;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background: #4a90e2;
            color: white;
            padding: 12px;
            border: 1px solid #ccc;
        }

        td {
            padding: 12px;
            border: 1px solid #ccc;
        }

        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        tr:hover td {
            background-color: #e6f7ff;
            transition: 0.2s;
        }

        input[type="text"] {
            padding: 6px;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 6px 12px;
            cursor: pointer;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            transition: 0.2s;
        }

        button:hover {
            background-color: #218838;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: green;
        }

        .add-btn {
            display: inline-block;
            padding: 10px 18px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
            transition: 0.2s;
        }

        .add-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php
    $tenkh = $tenkh ?? 'Khách hàng';
    ?>
    <h1>Giao dịch của <?= htmlspecialchars($tenkh, ENT_QUOTES, 'UTF-8') ?></h1>

    <div>
        <a href="index.php?controller=transaction&action=add" class="add-btn">Thêm giao dịch</a>
    </div>


    <div class="table-container">
        <?php if (!empty($giaodichs)): ?>
            <table>
                <tr>
                    <th>Mã GD</th>
                    <th>Mã KH</th>
                    <th>Nội dung</th>
                    <th>Số tiền</th>
                    <th>Ghi chú</th>
                    <th>Thay đổi ghi chú</th>
                </tr>
                <?php foreach ($giaodichs as $gd): ?>
                    <tr>
                        <td><?= htmlspecialchars($gd['magd']) ?></td>
                        <td><?= htmlspecialchars($gd['makh']) ?></td>
                        <td><?= htmlspecialchars($gd['noidung']) ?></td>
                        <td><?= number_format($gd['sotien']) ?> đ</td>
                        <td><?= htmlspecialchars($gd['ghichu']) ?></td>
                        <td>
                            <form method="POST" action="index.php?controller=transaction&action=updateNote">
                                <input type="hidden" name="magd" value="<?= htmlspecialchars($gd['magd']) ?>">
                                <input type="text" name="ghichu" placeholder="Nhập ghi chú">
                                <button type="submit">Lưu</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Chưa có giao dịch nào.</p>
        <?php endif; ?>
    </div>

</body>

</html>