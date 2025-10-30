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
    <div class="app">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <div class="burger" aria-hidden="true"></div>
                <strong style="color:#222">Menu</strong>
            </div>

            <nav class="menu" aria-label="Main menu">
                <a href="index.php">Trang chủ</a>
                <a href="khoanthu.php">Khoản thu</a>
                <a href="khoanchi.php" class="active">Khoản chi</a>
                <a href="catagories.php">Danh mục</a>
                <a href="ngansach.php">Ngân sách</a>
                <a href="baocao.php">Báo cáo</a>
                <a href="caidat.php">Cài đặt</a>
            </nav>
        </aside>
        <!-- Main -->
        <div class="form-container">
            <h2>Thêm danh mục</h2>
            <form action="../controllers/CatagoryController.php?action=store" method="POST">
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