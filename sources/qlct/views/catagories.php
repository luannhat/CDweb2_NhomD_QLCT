<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Danh mục</title>
    <link rel="stylesheet" href="../public/css/khoanchi.css" />
    <style>
        tr.selected {
            background-color: #dff0d8 !important;
        }

        /* Modal styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            min-width: 320px;
            max-width: 400px;
            width: 90%;
            overflow: hidden;
        }

        .modal-header {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            background: white;
            border-bottom: 1px solid #e0e0e0;
        }

        .modal-icon {
            margin-right: 12px;
            width: 24px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .modal-icon::before {
            content: "";
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 18px solid #ffa500;
            position: relative;
        }

        .modal-icon::after {
            content: "!";
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            font-weight: bold;
            color: black;
            z-index: 1;
        }

        .modal-title {
            font-weight: bold;
            font-size: 16px;
            color: #333;
            margin: 0;
        }

        .modal-body {
            padding: 20px;
            background-color: #dff0d8;
            text-align: center;
        }

        .modal-message {
            margin: 0;
            font-size: 16px;
            color: #333;
            line-height: 1.4;
            font-weight: normal;
        }

        .modal-footer {
            padding: 16px 20px;
            background-color: #f5f5f5;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .modal-btn {
            padding: 10px 20px;
            border: 1px solid #c0c0c0;
            border-radius: 4px;
            background: #f0f0f0;
            color: #333;
            cursor: pointer;
            font-size: 14px;
            font-weight: normal;
            transition: all 0.2s;
            min-width: 80px;
        }

        .modal-btn:hover {
            background: #e0e0e0;
            border-color: #a0a0a0;
        }

        .modal-btn-cancel {
            background: #f0f0f0;
            color: #333;
            border-color: #c0c0c0;
        }

        .modal-btn-confirm {
            background: #f0f0f0;
            color: #333;
            border-color: #c0c0c0;
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
                <a href="khoanchi.php" >Khoản chi</a>
                <a href="catagories.php">Danh mục</a>
                <a href="ngansach.php">Ngân sách</a>
                <a href="baocao.php">Báo cáo</a>
                <a href="caidat.php">Cài đặt</a>
            </nav>
        </aside>

        <!-- Main -->
        <div class="main">
            <header class="header">
                <h1>Danh mục</h1>

                <div class="search" role="search">
                    <form method="get" action="catagories.php">
                        <input id="q" name="q" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" />
                        <button id="search-btn" type="submit">Tìm kiếm</button>
                    </form>
                </div>

                <div class="header-right">
                    <div class="avatar" title="Văn A">
                        <div class="circle">VA</div>
                        <div style="font-weight:600;color:#0b6b3f">Văn A</div>
                    </div>
                    <div class="bell" title="Thông báo">🔔</div>
                </div>
            </header>

            <main class="content">
                <div class="controls">
                    <button class="btn primary" id="addBtn" onclick="window.location.href='add_catagories.php'">Thêm danh mục mới</button>
                    <button class="btn danger" id="delete-btn">Xóa</button>
                </div>

                <section class="card" aria-labelledby="tableTitle">
                    <table id="expenseTable" aria-describedby="tableTitle">
                        <thead>
                            <tr>
                                <th class="col-date">Mã Danh mục</th>
                                <th class="col-content">Tên danh mục</th>
                                <th class="col-type">Loại</th>
                            </tr>
                        </thead>

                        <tbody id="tbody">
                            <?php
                            require_once __DIR__ . '/../controllers/CatagoryController.php';

                            // Khởi tạo controller
                            $controller = new DanhmucController();

                            // Lấy danh sách danh mục (ví dụ makh = 1)
                            $makh = 1; // sau này bạn có thể lấy từ session: $_SESSION['makh']
                            $danhmucs = $controller->index($makh);

                            // Hiển thị dữ liệu ra bảng
                            if (!empty($danhmucs)) {
                                foreach ($danhmucs as $row) {   
                                    echo "<tr data-madmchitieu='{$row['madmchitieu']}'>";
                                    echo "<td>" . htmlspecialchars($row['madmchitieu']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['tendanhmuc']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['loai']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' style='text-align:center;'>Chưa có danh mục nào</td></tr>";
                            }
                            ?>
                        </tbody>


<div id="confirmModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon"></div>
            <h3 class="modal-title">Xác nhận xóa</h3>
        </div>
        <div class="modal-body">
            <p class="modal-message">Bạn có chắc chắn muốn xóa danh mục này?</p>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-cancel" id="modalCancel">Hủy</button>
            <button class="modal-btn modal-btn-confirm" id="modalConfirm">Xóa</button>
        </div>
    </div>
</div>
<script>
    const table = document.getElementById('expenseTable');
let selectedRow = null;

// Chọn dòng
table.addEventListener('click', function(e) {
    const tr = e.target.closest('tr');
    if (!tr || tr.querySelector('th')) return;

    // Bỏ chọn dòng cũ
    if (selectedRow) selectedRow.classList.remove('selected');

    // Chọn dòng mới
    selectedRow = tr;
    selectedRow.classList.add('selected');
});

// Hiển thị modal khi click nút Xóa
const deleteBtn = document.getElementById('delete-btn');
const modal = document.getElementById('confirmModal');
const modalCancel = document.getElementById('modalCancel');
const modalConfirm = document.getElementById('modalConfirm');

deleteBtn.addEventListener('click', function() {
    if (!selectedRow) {
        alert('⚠️ Vui lòng chọn danh mục muốn xóa!');
        return;
    }
    modal.style.display = 'flex';
});

// Hủy modal
modalCancel.addEventListener('click', function() {
    modal.style.display = 'none';
});

// Xác nhận xóa
modalConfirm.addEventListener('click', function() {
    const madmchitieu = selectedRow.dataset.madmchitieu;
    window.location.href = `../controllers/CatagoryController.php?action=delete&madmchitieu=${madmchitieu}`;
});

</script>
