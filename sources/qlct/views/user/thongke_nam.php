<?php
require_once __DIR__ . '/../../models/StatisticalModel.php';

$model = new StatisticalModel();
$makh = $_SESSION['id'] ?? 1;
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$data = $model->getExpenseByCategoryAndYear($makh, $year);
$totalExpense = $model->getTotalExpenseByYear($makh, $year);

// Chuẩn bị dữ liệu cho pie chart
$colors = ['#36A2EB', '#FF6384', '#33CC99', '#9966FF', '#FF9F40', '#66CCFF', '#FF6666', '#33CC66', '#FF99CC', '#FFCE56'];
$chartData = [];
$total = array_sum(array_column($data, 'tongtien'));

foreach ($data as $index => $row) {
    $chartData[] = [
        'name' => $row['tendanhmuc'],
        'value' => floatval($row['tongtien']),
        'percent' => $total > 0 ? round(($row['tongtien'] / $total) * 100, 2) : 0,
        'color' => $colors[$index % count($colors)]
    ];
}

ob_start(); // ✅ BẮT BUFFER
?>

<header class="header">
    <h1>Thống kê chi tiêu trong năm</h1>

    <div class="search" role="search">
        <form method="get" action="baocao.php">
            <input id="q" name="q" placeholder="Tìm kiếm..."
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button id="searchBtn" type="submit">Tìm kiếm</button>
        </form>
    </div>
</header>

<main class="content">

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="inline-alert">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Form chọn năm -->
    <div class="year-selector">
        <form method="GET" action="index.php" class="year-form">

			<input type="hidden" name="controller" value="user">
			<input type="hidden" name="action" value="stats">
			<input type="hidden" name="view" value="year">

            <label for="year">Chọn năm:</label>
            <select name="year" id="year">
                <?php
                $currentYear = date('Y');
                for ($y = $currentYear - 5; $y <= $currentYear + 1; $y++):
                ?>
                    <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>>
                        <?= $y ?>
                    </option>
                <?php endfor; ?>
            </select>
            <button class="btn primary">Xem thống kê</button>
        </form>
    </div>

    <?php if (!empty($chartData)): ?>
        <div class="chart-container">
            <div class="chart-wrapper">
                <canvas id="pieChart"></canvas>
            </div>
            <div class="chart-legend">
                <?php foreach ($chartData as $item): ?>
                    <div class="legend-item">
                        <div class="legend-color" style="background: <?= $item['color'] ?>"></div>
                        <span><?= htmlspecialchars($item['name']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <p>Không có dữ liệu năm <?= $year ?></p>
    <?php endif; ?>

    <?php if ($totalExpense > 0): ?>
        <div class="total-expense">
            Tổng chi tiêu: <strong><?= number_format($totalExpense, 0, ',', '.') ?> VNĐ</strong>
        </div>
    <?php endif; ?>

</main>

<script>
<?php if (!empty($chartData)): ?>
new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($chartData, 'name')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($chartData, 'value')) ?>,
            backgroundColor: <?= json_encode(array_column($chartData, 'color')) ?>,
        }]
    },
    options: { plugins: { legend: { display: false } } }
});
<?php endif; ?>
</script>

<?php
$content = ob_get_clean();

$pageTitle = 'Thống kê chi tiêu trong năm';
$cssFiles = ['/public/css/khoanchi.css'];

require_once __DIR__ . '/layout.php';
