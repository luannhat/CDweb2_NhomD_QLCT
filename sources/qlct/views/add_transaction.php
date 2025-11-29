<?php

session_start();
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/ExpenseModel.php';
$expenseModel = new ExpenseModel();
$message = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenkhoanchi = trim($_POST['tenkhoanchi']);
    $sotien = floatval($_POST['sotien']);
    $madanhmuc = intval($_POST['danhmuc'] ?? 0);

 
    if (!empty($_POST['danhmuc_moi'])) {
        $madanhmuc = $expenseModell->addDanhMuc($_POST['danhmuc_moi']);
    }

   
    if ($expenseModell->addChiTieu($tenkhoanchi, $sotien, $madanhmuc)) {
        $message = " Đã lưu khoản chi tiêu thành công!";
    } else {
        $message = " Lỗi khi lưu khoản chi tiêu!";
    }
}


$danhmucList = $expenseModell->getDanhMucList();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm khoản chi tiêu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #fff;
            margin: 0; 
            padding: 0;
        }
        h2 {
            background-color: #a7f7b6;
            padding: 15px;
            margin: 0; 
        }
        form {
            width: 60%;
            margin: 40px auto;
            text-align: left;
        }
        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .form-row label {
            width: 150px;
            margin-right: 10px;
            text-align: right;
        }
        .form-row input[type="text"], .form-row input[type="number"] {
            flex: 1;
            padding: 6px;
        }
        .radio-group {
            margin-bottom: 20px;
        }
        button {
            padding: 8px 20px;
            margin: 10px;
            background-color: #a7f7b6;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #8feaa3;
        }
        .message {
            text-align: center;
            color: green;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Thêm khoản chi tiêu mới</h2>
    <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST">
        <div class="form-row">
            <label for="tenkhoanchi">Tên khoản chi:</label>
            <input type="text" id="tenkhoanchi" name="tenkhoanchi" required>
        </div>
        <div class="form-row">
            <label for="sotien">Số tiền (VNĐ):</label>
            <input type="number" id="sotien" name="sotien" required min="0">
        </div>
        <div class="form-row">
            <label>Danh mục:</label>
            <div style="flex: 1;">
                <div class="radio-group">
                    <?php foreach ($danhmucList as $row): ?>
                        <label>
                            <input type="radio" name="danhmuc" value="<?= htmlspecialchars($row['madanhmuc']) ?>" required>
                            <?= htmlspecialchars($row['tendanhmuc']) ?>
                        </label><br>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="form-row">
            <label for="danhmuc_moi">Thêm danh mục mới:</label>
            <input type="text" id="danhmuc_moi" name="danhmuc_moi" placeholder="Nhập danh mục mới nếu có">
        </div>
        <div style="text-align: center;">
            <button type="submit">Lưu</button>
            <button type="reset">Hủy bỏ</button>
        </div>
    </form>
</body>
</html>
