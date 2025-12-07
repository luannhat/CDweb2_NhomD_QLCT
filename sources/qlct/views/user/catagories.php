<?php
session_start();
ob_start();
// Kiểm tra đăng nhập user
if (!isset($_SESSION['makh'])) {
    header("Location: ../../login.php");
    exit();
}

$makh = $_SESSION['makh'];

// Include controller
require_once __DIR__ . '/../../controllers/CatagoryController.php';

// Khởi tạo controller
$controller = new DanhmucController();

// Lấy danh sách danh mục của user
$danhmucs = $controller->index();
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Danh mục</title>
    <link rel="stylesheet" href="../../public/css/khoanchi.css" />
    <style>
        tr.selected {
            background-color: #f0f8ff;
        }

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
            background: #fff;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            overflow: hidden;
            animation: fadeIn 0.3s ease;
        }

        .modal-header {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #eee;
        }

        .modal-icon {
            width: 30px;
            height: 30px;
            background: #ffa500;
            border-radius: 50%;
            color: white;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            margin-right: 10px;
            font-size: 18px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .modal-body {
            padding: 20px;
            text-align: center;
            font-size: 16px;
            color: #333;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 20px;
            background: #f9f9f9;
        }

        .modal-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }

        .modal-btn-cancel {
            background: #e0e0e0;
            color: #333;
        }

        .modal-btn-cancel:hover {
            background: #d0d0d0;
        }

        .modal-btn-confirm {
            background: #ff4d4f;
            color: white;
        }

        .modal-btn-confirm:hover {
            background: #e04444;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="main">
        <header class="header">
            <h1>Danh mục</h1>
        </header>

        <main class="content">
            <div class="controls">
                <button class="btn primary" onclick="window.location.href='/index.php?controller=catagory&action=add'">Thêm danh mục mới</button>
                <button class="btn danger" id="delete-btn">Xóa</button>
            </div>
            <?php
            if (isset($_SESSION['success_message'])) {
                echo "<div class='alert success'>{$_SESSION['success_message']}</div>";
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo "<div class='alert error'>{$_SESSION['error_message']}</div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <table id="expenseTable" border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>Mã Danh mục</th>
                        <th>Tên danh mục</th>
                        <th>Loại</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($danhmucs)) {
                        foreach ($danhmucs as $row) {
                            $id = htmlspecialchars($row['id']);
                            $name = htmlspecialchars($row['tendanhmuc']);
                            $loai = htmlspecialchars($row['loai']);

                            //loai để JS biết xóa bảng nào
                            echo "<tr data-madmchitieu='$id' data-loai='$loai'>";
                            echo "<td>$id</td>";
                            echo "<td>$name</td>";
                            echo "<td>$loai</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center;'>Chưa có danh mục nào</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Modal xác nhận xóa -->
            <div id="confirmModal" class="modal-overlay" style="display:none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-icon">!</div>
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
                    if (selectedRow) selectedRow.classList.remove('selected');
                    selectedRow = tr;
                    selectedRow.classList.add('selected');
                });

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

                modalCancel.addEventListener('click', function() {
                    modal.style.display = 'none';
                });

                modalConfirm.addEventListener('click', function() {
                    if (!selectedRow) return;
                    const madmchitieu = selectedRow.getAttribute('data-madmchitieu');
                    const loai = selectedRow.getAttribute('data-loai'); // Lấy loại
                    if (!madmchitieu || !loai) return alert('⚠️ Thông tin xóa trống!');
                    // Gửi sang controller
                    window.location.href = `/index.php?controller=catagory&action=delete&madmchitieu=${madmchitieu}&loai=${loai}`;
                });
            </script>
        </main>
    </div>
</body>

</html>
<?php
$content = ob_get_clean(); // Lấy toàn bộ HTML của view
require_once __DIR__ . '/layout.php';