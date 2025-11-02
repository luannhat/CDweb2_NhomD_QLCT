<?php
// Xử lý AJAX request TRƯỚC khi output bất kỳ HTML nào
require_once __DIR__ . '/../controllers/KhoanchiController.php';

if (isset($_GET['ajax']) && $_GET['ajax'] === 'delete') {
    header('Content-Type: application/json');
    
    try {
        // Nhận JSON data từ JavaScript
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Debug log
        error_log("AJAX Delete - Input received: " . print_r($input, true));
        
        if (isset($input['ids']) && is_array($input['ids']) && !empty($input['ids'])) {
            // Phân loại ID: dữ liệu tĩnh (1-8) và dữ liệu thật (ID > 8)
            $staticIds = []; // ID 1-8 (dữ liệu tĩnh)
            $realIds = [];   // ID > 8 (dữ liệu thật từ database)
            
            foreach ($input['ids'] as $id) {
                $intId = intval($id);
                if ($intId >= 1 && $intId <= 8) {
                    $staticIds[] = $id;
                } else {
                    $realIds[] = $id;
                }
            }
            
            $deletedCount = 0;
            $message = '';
            
            // Xử lý dữ liệu thật từ database
            if (!empty($realIds)) {
                $_POST['magd_list'] = $realIds;
                $controller = new KhoanchiController();
                $result = $controller->deleteMultiple();
                
                if ($result['success']) {
                    $deletedCount = count($realIds);
                    $message = $result['message'];
                }
            }
            
            // Xử lý dữ liệu tĩnh (luôn thành công vì chỉ xóa khỏi UI)
            if (!empty($staticIds)) {
                $deletedCount += count($staticIds);
                if (!empty($message)) {
                    $message .= " và " . count($staticIds) . " dữ liệu mẫu";
                } else {
                    $message = "Đã xóa " . count($staticIds) . " dữ liệu mẫu";
                }
            }
            
            // Trả về kết quả
            if ($deletedCount > 0) {
                $result = ['success' => true, 'message' => $message, 'deleted_count' => $deletedCount];
            } else {
                $result = ['success' => false, 'message' => 'Không có dữ liệu nào được xóa'];
            }
            
            // Debug log
            error_log("AJAX Delete - Result: " . print_r($result, true));
        } else {
            $result = ['success' => false, 'message' => 'Không có dữ liệu để xóa hoặc định dạng không đúng'];
        }
        
        echo json_encode($result);
    } catch (Exception $e) {
        error_log("AJAX Delete Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
    }
    exit;
}

// Tiếp tục với HTML nếu không phải AJAX request
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Khoản chi</title>
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
                <a href="khoanchi.php" class="active">Khoản chi</a>
                <a href="catagories.php">Danh mục</a>
                <a href="ngansach.php">Ngân sách</a>
                <a href="baocao.php">Báo cáo</a>
                <a href="caidat.php">Cài đặt</a>
            </nav>
        </aside>

        <!-- Main -->
        <div class="main">
            <header class="header">
                <h1>Khoản chi</h1>

                <div class="search" role="search">
                    <form method="get" action="khoanchi.php">
                        <input id="q" name="q" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" />
                        <button id="searchBtn" type="submit">Tìm kiếm</button>
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
                    <button class="btn primary" id="addBtn" onclick="window.location.href='them_khoanchi.php'">Thêm khoản chi tiêu</button>
                    <button class="btn primary" id="editBtn" onclick="window.location.href='edit_expense.php'">Sửa khoản chi tiêu</button>
                    <button class="btn danger" id="deleteBtn">Xóa</button>
                </div>

                <section class="card" aria-labelledby="tableTitle">
                    <table id="expenseTable" aria-describedby="tableTitle">
                        <thead>
                            <tr>
                                <th class="col-date">Ngày</th>
                                <th class="col-content">Nội dung</th>
                                <th class="col-type">Loại</th>
                                <th class="col-money">Số tiền</th>
                            </tr>
                        </thead>

                        <tbody id="tbody">
<?php
// Load dữ liệu cho trang
$controller = new KhoanchiController();
$data = $controller->index();

$expenses = $data['expenses'];
$totalPages = $data['totalPages'];
$currentPage = $data['currentPage'];
$search = $data['search'];

if (!empty($expenses)) {
    foreach ($expenses as $expense) {
        $ngay = date('d/m/Y', strtotime($expense['ngaygiaodich']));
        $sotien = number_format($expense['sotien'], 0, ',', '.') . ' VNĐ';
        echo "<tr data-magd='{$expense['magd']}'>
            <td>{$ngay}</td>
            <td>{$expense['noidung']}</td>
            <td>{$expense['loai']}</td>
            <td>{$sotien}</td>
        </tr>";
    }
} else {
    echo "<tr>
        <td colspan='4' style='text-align: center; padding: 20px; color: #666;'>
            " . (empty($search) ? 'Chưa có khoản chi nào' : 'Không tìm thấy kết quả phù hợp') . "
        </td>
    </tr>";
}
?>
                        </tbody>
                    </table>

                    <div class="pagination" style="margin-top:12px;">
                        <?php if ($currentPage > 1): ?>
                            <a href="?page=<?php echo $currentPage - 1; ?><?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?>" class="circle" id="prevBtn">&lt;</a>
                        <?php else: ?>
                            <span class="circle disabled">&lt;</span>
                        <?php endif; ?>
                        
                        <div class="page-num" id="pageInfo"><?php echo $currentPage; ?>/<?php echo $totalPages; ?></div>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?page=<?php echo $currentPage + 1; ?><?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?>" class="circle" id="nextBtn">&gt;</a>
                        <?php else: ?>
                            <span class="circle disabled">&gt;</span>
                        <?php endif; ?>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Modal xác nhận xóa -->
    <div id="deleteModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon"></div>
                <span class="modal-title">Xóa chi tiêu</span>
            </div>
            <div class="modal-body">
                <p class="modal-message">Bạn có muốn xóa không?</p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-cancel">Hủy</button>
                <button class="modal-btn modal-btn-confirm">Xác nhận</button>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function showDeleteModal(count) {
            const modal = document.getElementById('deleteModal');
            const message = document.querySelector('.modal-message');
            message.textContent = count > 1 ? 
                `Bạn có muốn xóa ${count} khoản chi đã chọn không?` : 
                'Bạn có muốn xóa không?';
            modal.style.display = 'flex';
        }

        function hideDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'none';
        }

        // Modal event handlers
        document.querySelector('.modal-btn-cancel').addEventListener('click', hideDeleteModal);
        document.querySelector('.modal-btn-confirm').addEventListener('click', performDelete);
        
        // Close modal when clicking overlay
        document.getElementById('deleteModal').addEventListener('click', (e) => {
            if (e.target.id === 'deleteModal') {
                hideDeleteModal();
            }
        });

        // Variables for storing selected data
        let selectedRows = [];
        let selectedIds = [];

        // Chọn hàng bằng cách click
        const rows = document.querySelectorAll("#expenseTable tbody tr");
        rows.forEach(row => {
            row.addEventListener("click", () => {
                row.classList.toggle("selected");
            });
        });

        // Nút xóa
        const deleteBtn = document.getElementById("deleteBtn");
        deleteBtn.addEventListener("click", () => {
            const selected = document.querySelectorAll("tr.selected");
            if (selected.length === 0) {
                alert("Vui lòng chọn ít nhất một khoản chi để xóa!");
                return;
            }

            // Store selected data for later use
            selectedRows = Array.from(selected);
            selectedIds = selectedRows.map(tr => tr.dataset.magd);
            
            // Show modal
            showDeleteModal(selected.length);
        });

        // Function to perform actual delete
        async function performDelete() {
            hideDeleteModal();

            console.log("IDs to delete:", selectedIds);

            // Kiểm tra xem có ID hợp lệ không
            const validIds = selectedIds.filter(id => id && id !== 'undefined' && id !== 'null');
            if (validIds.length === 0) {
                alert("Không tìm thấy ID hợp lệ để xóa!");
                return;
            }

            try {
                // Gửi AJAX request xóa
                const response = await fetch('khoanchi.php?ajax=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids: validIds })
                });

                console.log("Response status:", response.status);
                console.log("Response headers:", response.headers.get('content-type'));

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const responseText = await response.text();
                console.log("Response text:", responseText);

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (jsonError) {
                    console.error("JSON parse error:", jsonError);
                    console.error("Response was not JSON:", responseText);
                    alert("Lỗi: Server trả về dữ liệu không hợp lệ. Vui lòng kiểm tra console để xem chi tiết.");
                    return;
                }

                console.log("Parsed data:", data);

                if (data.success) {
                    // Xóa tất cả các hàng đã chọn khỏi giao diện
                    selectedRows.forEach(tr => tr.remove());
                    alert(data.message || "Đã xóa thành công!");
                } else {
                    alert("Lỗi khi xóa: " + (data.message || 'Lỗi không xác định'));
                }
            } catch (error) {
                console.error("Delete error:", error);
                alert("Có lỗi xảy ra khi gửi yêu cầu xóa: " + error.message);
            }
        }
    </script>
</body>

</html>
