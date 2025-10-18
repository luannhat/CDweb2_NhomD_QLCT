<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Khoản chi</title>
    <link rel="stylesheet" href="../public/css/khoanchi.css" />
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
                <a href="danhmuc.php">Danh mục</a>
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
                    <button class="btn danger" id="deleteBtn">Xóa</button>
                </div>

                <section class="card" aria-labelledby="tableTitle">
                    <table id="expenseTable" aria-describedby="tableTitle">
                        <thead>
                            <tr>
                                <th class="col-select"><input id="selectAll" type="checkbox" /></th>
                                <th class="col-date">Ngày</th>
                                <th class="col-content">Nội dung</th>
                                <th class="col-type">Loại</th>
                                <th class="col-money">Số tiền</th>
                            </tr>
                        </thead>

                        <tbody id="tbody">
<?php
// Xử lý AJAX requests
if (isset($_GET['ajax'])) {
    require_once __DIR__ . '/../controllers/KhoanchiController.php';
    $controller = new KhoanchiController();
    header('Content-Type: application/json');
    echo json_encode($controller->handleAjax());
    exit;
}

// Lấy dữ liệu từ controller
require_once __DIR__ . '/../controllers/KhoanchiController.php';
$controller = new KhoanchiController();
$data = $controller->index();

$expenses = $data['expenses'];
$totalPages = $data['totalPages'];
$currentPage = $data['currentPage'];
$search = $data['search'];

// Hiển thị dữ liệu từ database
if (!empty($expenses)) {
    foreach ($expenses as $expense) {
        $ngay = date('d/m/Y', strtotime($expense['ngaygiaodich']));
        $sotien = number_format($expense['sotien'], 0, ',', '.') . ' VNĐ';
        echo "<tr data-magd='{$expense['magd']}'>
            <td><input type='checkbox' class='expense-checkbox' value='{$expense['magd']}' /></td>
            <td>{$ngay}</td>
            <td>{$expense['noidung']}</td>
            <td>{$expense['loai']}</td>
            <td>{$sotien}</td>
        </tr>";
    }
} else {
    echo "<tr>
        <td colspan='5' style='text-align: center; padding: 20px; color: #666;'>
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

    <script src="../public/js/khoanchi.js"></script>
</body>

</html>
