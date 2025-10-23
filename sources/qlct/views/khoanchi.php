<?php
session_start();
require_once __DIR__ . '/../controllers/KhoanchiController.php';

// =======================
// 🧠 Xử lý AJAX Delete
// =======================
if (isset($_GET['ajax']) && $_GET['ajax'] === 'delete') {
    header('Content-Type: application/json');

    try {
        $input = json_decode(file_get_contents('php://input'), true);
        error_log("AJAX Delete - Input received: " . print_r($input, true));

        if (isset($input['ids']) && is_array($input['ids']) && !empty($input['ids'])) {
            $staticIds = []; // dữ liệu mẫu (1–8)
            $realIds = [];   // dữ liệu thật từ DB (ID > 8)

            foreach ($input['ids'] as $id) {
                $intId = intval($id);
                if ($intId >= 1 && $intId <= 8) $staticIds[] = $id;
                else $realIds[] = $id;
            }

            $deletedCount = 0;
            $message = '';

            if (!empty($realIds)) {
                $_POST['magd_list'] = $realIds;
                $controller = new KhoanchiController();
                $result = $controller->deleteMultiple();

                if ($result['success']) {
                    $deletedCount = count($realIds);
                    $message = $result['message'];
                }
            }

            if (!empty($staticIds)) {
                $deletedCount += count($staticIds);
                $message .= ($message ? " và " : "Đã xóa ") . count($staticIds) . " dữ liệu mẫu";
            }

            $result = $deletedCount > 0
                ? ['success' => true, 'message' => $message, 'deleted_count' => $deletedCount]
                : ['success' => false, 'message' => 'Không có dữ liệu nào được xóa'];

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
?>

<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<!-- CSS riêng của trang Khoản chi -->
<?php $cssVersion = @filemtime(__DIR__ . '/../public/css/khoanchi.css') ?: time(); ?>
<link rel="stylesheet" href="../public/css/khoanchi.css?v=<?php echo $cssVersion; ?>" />

<div class="app">
    <!-- Sidebar -->
    <?php include __DIR__ . '/layouts/sidebar.php'; ?>

    <!-- Main -->
    <div class="main">
        <header class="header">
            <h1>Khoản chi</h1>

            <div class="search" role="search">
                <form method="get" action="khoanchi.php">
                    <input id="q" name="q" placeholder="Tìm kiếm..." 
                           value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" />
                    <button id="searchBtn" type="submit">Tìm kiếm</button>
                </form>
            </div>

            <div class="header-right">
                <div class="account" id="accountDropdown">
                    <?php 
                        $id = $_SESSION['id'] ?? '';
                        $displayName = $_SESSION['name'] ?? ($_SESSION['username'] ?? '');
                    ?>
                    <button class="account-btn <?php echo $id ? '' : 'just-icon'; ?>" aria-haspopup="true" aria-expanded="false" title="<?php echo $id ? htmlspecialchars($displayName) : 'Tài khoản'; ?>">
                        <img class="avatar-img" src="../public/images/user_profile.png" alt="User" />
                        <?php if ($id): ?>
                            <span class="account-name"><?php echo htmlspecialchars($displayName ?: 'Người dùng'); ?></span>
                        <?php endif; ?>
                        <span class="caret">▾</span>
                    </button>
                    <div class="dropdown-menu" role="menu" aria-hidden="true">
                        <?php if ($id): ?>
                            <a class="dropdown-item" href="view_user.php?id=<?php echo $id; ?>">Trang cá nhân</a>
                            <div class="dropdown-sep"></div>
                            <a class="dropdown-item" href="logout.php">Đăng xuất</a>
                        <?php else: ?>
                            <a class="dropdown-item" href="login.php">Đăng nhập</a>
                            <div class="dropdown-sep"></div>
                            <a class="dropdown-item" href="register.php">Đăng ký</a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="bell" title="Thông báo"><i class="fa-solid fa-bell"></i></div>
            </div>
        </header>

        <main class="content">
            <?php if (!empty($_SESSION['message'])): ?>
                <div class="inline-alert" role="alert">
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>
            <div class="controls">
                <button class="btn primary" id="addBtn" onclick="window.location.href='them_khoanchi.php'">
                    Thêm khoản chi tiêu
                </button>
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
                        // Gọi controller lấy dữ liệu
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
                                    <td colspan='4' style='text-align:center; padding:20px; color:#666;'>
                                        " . (empty($search) ? 'Chưa có khoản chi nào' : 'Không tìm thấy kết quả phù hợp') . "
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Phân trang -->
                <div class="pagination" style="margin-top:12px;">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?php echo $currentPage - 1; ?><?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?>" class="circle" id="prevBtn">&lt;</a>
                    <?php else: ?>
                        <span class="circle disabled">&lt;</span>
                    <?php endif; ?>

                    <div class="page-num" id="pageInfo">
                        <?php echo $currentPage; ?>/<?php echo $totalPages; ?>
                    </div>

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
<div id="deleteModal" class="modal-overlay" style="display:none;">
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

<!-- JS riêng -->
<script src="../public/js/khoanchi.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var account = document.getElementById('accountDropdown');
    if (!account) return;
    var btn = account.querySelector('.account-btn');
    var menu = account.querySelector('.dropdown-menu');

    function closeMenu() {
        menu.style.display = 'none';
        btn.setAttribute('aria-expanded', 'false');
        menu.setAttribute('aria-hidden', 'true');
    }

    function toggleMenu() {
        var isOpen = menu.style.display === 'block';
        if (isOpen) {
            closeMenu();
        } else {
            menu.style.display = 'block';
            btn.setAttribute('aria-expanded', 'true');
            menu.setAttribute('aria-hidden', 'false');
        }
    }

    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMenu();
    });

    document.addEventListener('click', function(e) {
        if (!account.contains(e.target)) {
            closeMenu();
        }
    });
});
</script>

