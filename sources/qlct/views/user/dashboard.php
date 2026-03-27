<?php
ob_start();

$currentPage = 'home';

// Bắt đầu session nếu chưa active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu chưa login, redirect về home
if (!isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}

$user = $_SESSION['user'];

require_once 'models/KhoanthuModel.php';
require_once 'models/KhoanchiModel.php';

$makh = intval($user['id'] ?? 0); // id user là makh

$khoanthuModel = new KhoanthuModel();
$khoanchiModel = new KhoanchiModel();

// --- Tổng thu chi tiêu ---
$totalExpense = floatval($khoanchiModel->countTotalExpenses($makh));
$totalIncome = floatval($khoanthuModel->countTotalIncomes($makh));
// --- Số dư ---
$balance = $totalIncome - $totalExpense;

// --- Dữ liệu biểu đồ ---
$categories = $khoanchiModel->getCategories($makh);
$categoryExpenses = $khoanchiModel->getExpenseByCategory($makh);

// Đảm bảo dữ liệu mảng
$categories = is_array($categories) ? $categories : [];
$categoryExpenses = is_array($categoryExpenses) ? $categoryExpenses : [];

?>

<section class="dashboard-hero">
    <h1>Xin chào, <?= htmlspecialchars($user['name'] ?? 'Người dùng') ?>!</h1>
    <p>Chào mừng bạn quay lại. Đây là tổng quan chi tiêu của bạn.</p>
</section>

<section class="dashboard-summary">
    <div class="summary-card">
        <h3>Tổng thu nhập</h3>
        <p><?= number_format($totalIncome, 0, ',', '.') ?> VND</p>
    </div>
    <div class="summary-card">
        <h3>Tổng chi tiêu</h3>
        <p><?= number_format($totalExpense, 0, ',', '.') ?> VND</p>
    </div>
    <div class="summary-card">
        <h3>Số dư hiện tại</h3>
        <p><?= number_format($balance, 0, ',', '.') ?> VND</p>
    </div>
</section>

<section class="dashboard-actions">
    <a href="?controller=transaction&action=index" class="btn-primary">Giao dịch</a>
    <a href="?controller=report&action=view" class="btn-secondary">Xem báo cáo</a>
</section>

<section class="dashboard-charts">
    <h2>Biểu đồ chi tiêu</h2>
    <canvas id="expenseChart" width="400" height="200"></canvas>
    <div style="margin-top: 10px;">
        <a href="/index.php?controller=catagory&action=index" class="btn-primary">Xem mục danh mục</a>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    

const ctx = document.getElementById('expenseChart').getContext('2d');
const expenseChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($categories) ?>,
        datasets: [{
            label: 'Chi tiêu theo danh mục',
            data: <?= json_encode($categoryExpenses) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: { y: { beginAtZero: true } }
    }
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
