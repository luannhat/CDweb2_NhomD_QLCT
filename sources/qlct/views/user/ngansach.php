<?php
require_once __DIR__ . '/../../controllers/NgansachController.php';

$id = $_SESSION['id'] ?? '';
$displayName = $_SESSION['name'] ?? ($_SESSION['username'] ?? '');

$controller = new NgansachController();
$mode = $_GET['mode'] ?? 'month';
$mode = $mode === 'week' ? 'week' : 'month';
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save_weekly') {
    $controller->saveWeeklyBudget();
    exit;
}

$data = $controller->index();
$budgets = $data['budgets'];
$categories = $data['categories'];
$filters = $data['filters'];

$statusLabels = [
    'under_budget' => ['icon' => 'fa-check', 'text' => 'Trong giới hạn', 'class' => 'status success'],
    'on_budget' => ['icon' => 'fa-check', 'text' => 'Đúng ngân sách', 'class' => 'status success'],
    'over_budget' => ['icon' => 'fa-triangle-exclamation', 'text' => 'Vượt mức', 'class' => 'status warning'],
];

$currentYear = (int)date('Y');
$defaultWeek = 1;
$defaultMonth = (int)date('n');
$defaultYear = (int)date('Y');

$cssVersion = @filemtime(__DIR__ . '/../public/css/ngansach.css') ?: time();

ob_start();
?>

<div class="main">
    <header class="header">
        <h1><?= $mode === 'week' ? 'Lập ngân sách theo tuần' : 'Ngân sách'; ?></h1>
    </header>

    <main class="content">
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="inline-alert" role="alert">
                <?= $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <section class="budget-mode">
            <button class="mode-btn"
                    data-target-mode="week"
                    type="button"
                    aria-pressed="<?= $mode === 'week' ? 'true' : 'false'; ?>">
                Lập ngân sách theo tuần
            </button>
            <button class="mode-btn"
                    type="button"
                    aria-pressed="<?= $mode === 'month' ? 'true' : 'false'; ?>">
                Lập ngân sách theo tháng
            </button>
        </section>

        <?php if ($mode === 'week'): ?>
            <section class="weekly-form-card">
                <form method="post" action="ngansach.php?mode=week&action=save_weekly" class="weekly-form">
                    <div class="form-row">
                        <label for="weekSelect">Chọn tuần:</label>
                        <select id="weekSelect" name="week" required>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <option value="<?= $i; ?>" <?= $i === $defaultWeek ? 'selected' : ''; ?>>
                                    <?= $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>

                        <label for="weekMonthSelect">Chọn tháng:</label>
                        <select id="weekMonthSelect" name="month" required>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m; ?>" <?= $m === $defaultMonth ? 'selected' : ''; ?>>
                                    <?= $m; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="budgetAmount">Tổng ngân sách:</label>
                        <input id="budgetAmount"
                               name="budget_amount"
                               inputmode="numeric"
                               placeholder="500.000"
                               required />
                    </div>

                    <div class="form-group">
                        <label for="categorySelect">Danh mục:</label>
                        <select id="categorySelect" name="category_id" required>
                            <option value="">Chọn danh mục</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['madmchitieu']; ?>">
                                    <?= htmlspecialchars($category['tendanhmuc']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row form-actions">
                        <button class="form-btn primary" type="submit">Lưu</button>
                        <button class="form-btn ghost" type="button" data-target-mode="month">Hủy bỏ</button>
                    </div>
                </form>
            </section>

        <?php else: ?>
            <form class="filters" method="get" action="ngansach.php">
                <input type="hidden" name="mode" value="month">
                <label class="filter-field">
                    Tuần
                    <select name="week" aria-label="Tuần">
                        <option value="">Chọn tuần</option>
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <option value="<?= $i; ?>" <?= ($filters['week'] ?? null) === $i ? 'selected' : ''; ?>>
                                Tuần <?= $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </label>

                <label class="filter-field">
                    Tháng
                    <select name="month" aria-label="Tháng">
                        <option value="">Chọn tháng</option>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m; ?>" <?= ($filters['month'] ?? null) === $m ? 'selected' : ''; ?>>
                                Tháng <?= $m; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </label>

                <label class="filter-field">
                    Năm
                    <select name="year" aria-label="Năm">
                        <option value="">Chọn năm</option>
                        <?php for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++): ?>
                            <option value="<?= $y; ?>" <?= ($filters['year'] ?? null) === $y ? 'selected' : ''; ?>>
                                <?= $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </label>

                <label class="filter-field">
                    &nbsp;
                    <button type="submit">Lọc</button>
                </label>
            </form>

            <section class="table-card" aria-labelledby="budgetTableTitle">
                <table>
                    <thead>
                    <tr>
                        <th>Ngày</th>
                        <th>Danh mục</th>
                        <th>Ngân sách</th>
                        <th>Đã chi</th>
                        <th>Chênh lệch</th>
                        <th>Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($budgets)): ?>
                        <?php foreach ($budgets as $budget): ?>
                            <?php
                            $diffValue = floatval($budget['chenhlech_value']);
                            $diffPrefix = $diffValue >= 0 ? '+' : '-';
                            $diffAmount = number_format(abs($diffValue), 0, ',', '.');
                            $diffDisplay = (abs($diffValue) < 0.005) ? '0' : $diffPrefix . $diffAmount;
                            ?>
                            <tr>
                                <td>
                                    <div class="date-label">
                                        <?= date('d/m/Y', strtotime($budget['ngay'])); ?>
                                    </div>
                                    <div class="date-sub">
                                        Tuần <?= $budget['week_in_month']; ?> • Tháng <?= $budget['month']; ?>/<?= $budget['year']; ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($budget['tendanhmuc'] ?? 'Không xác định'); ?></td>
                                <td><?= number_format($budget['ngansach'], 0, ',', '.'); ?></td>
                                <td><?= number_format($budget['dachi'], 0, ',', '.'); ?></td>
                                <td><?= $diffDisplay; ?></td>
                                <td>
                                    <?php
                                    $status = $statusLabels[$budget['trangthai']] ?? $statusLabels['on_budget'];
                                    ?>
                                    <span class="<?= $status['class']; ?>">
                                        <i class="fa-solid <?= $status['icon']; ?>"></i>
                                        <?= $status['text']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="empty-row">
                            <td colspan="6">Chưa có ngân sách nào</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <div class="legend">
                <div class="legend-item">
                    <span class="legend-icon success"><i class="fa-solid fa-check"></i></span>
                    <span>Trong giới hạn</span>
                </div>
                <div class="legend-item">
                    <span class="legend-icon warning"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    <span>Vượt mức</span>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var account = document.getElementById('accountDropdown');
    if (account) {
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
    }

    document.querySelectorAll('.mode-btn[data-target-mode]').forEach(function(button) {
        button.addEventListener('click', function() {
            var targetMode = button.getAttribute('data-target-mode');
            if (!targetMode) return;
            window.location.href = 'views/ngansach.php?mode=' + targetMode;
        });
    });

    var inactiveMonthBtn = document.querySelector('.mode-btn:not([data-target-mode])');
    if (inactiveMonthBtn) {
        inactiveMonthBtn.addEventListener('click', function(e) {
            e.preventDefault();
            return false;
        });
    }

    var amountInput = document.getElementById('budgetAmount');
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            var digits = amountInput.value.replace(/\D/g, '');
            if (!digits) {
                amountInput.value = '';
                return;
            }
            amountInput.value = digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });
    }

    var cancelBtn = document.querySelector('.form-btn.ghost[data-target-mode]');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var targetMode = cancelBtn.getAttribute('data-target-mode');
            if (targetMode) {
                window.location.href = 'ngansach.php?mode=' + targetMode;
            }
        });
    }
});
</script>

<?php
$content = ob_get_clean();
$title = $mode === 'week' ? 'Lập ngân sách theo tuần' : 'Ngân sách';
$cssFiles = [
    "../public/css/ngansach.css?v=" . ($cssVersion ?? time())
];
include __DIR__ . '/layout.php';
