<?php
// Kiểm tra login
if (!isset($_SESSION['makh'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../controllers/CatagoryController.php';

$controller = new DanhmucController();
$danhmucs = $controller->index();
?>


<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Thêm danh mục</title>
    <link rel="stylesheet" href="../public/css/khoanchi.css" />
    <link rel="stylesheet" href="../public/css/themkhoanchi.css" />
    <style>
        .form-container {
            background: #fff;
            padding: 25px 35px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            width: 800px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-weight: 600;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s;
        }

        button:hover {
            background: #555;
        }

        .message {
            margin-top: 10px;
            text-align: center;
            color: green;
        }
    </style>
</head>

<body>
    <!-- Main -->
    <div class="form-container">
        <h2>Thêm danh mục</h2>
        <?php
        if (isset($_SESSION['error_msg'])) {
            echo "<p style='color:red; text-align:center;'>{$_SESSION['error_msg']}</p>";
            unset($_SESSION['error_msg']);
        }
        if (isset($_SESSION['success_msg'])) {
            echo "<p style='color:green; text-align:center;'>{$_SESSION['success_msg']}</p>";
            unset($_SESSION['success_msg']);
        }
        ?>

        <form action="/index.php?controller=catagory&action=store" method="POST">
            <div class="form-group">
                <label for="tendanhmuc">Tên danh mục</label>
                <input type="text" id="tendanhmuc" name="tendanhmuc" placeholder="Nhập tên danh mục..." required>
            </div>

            <div class="form-group">
                <label for="loaidanhmuc">Loại</label>
                <select id="loaidanhmuc" name="loaidanhmuc" required>
                    <option value="">-- Chọn loại --</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>

            <button type="submit">Thêm</button>
        </form>
    </div>
    </div>


</body>
</html>
