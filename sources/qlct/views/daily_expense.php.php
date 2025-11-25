<?php
session_start();
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/ExpenseModel.php';


if (!isset($_SESSION['makh'])) {
    header('Location: login.php');
    exit();
}
    
$makh = (int)$_SESSION['makh'];

$model = new ExpenseModel();


$ngay = isset($_GET['ngay']) ? $_GET['ngay'] : date('Y-m-d');


$danhsach = $model->getExpensesByDate($makh, $ngay);
$tongchi = $model->getTotalExpensesByDate($makh, $ngay);
$bieudoRows = $model->getExpensesSummaryByDate($makh, $ngay);


$labels = array_map(fn($r) => $r['danhmuc'], $bieudoRows);
$values = array_map(fn($r) => (float)$r['tongtien'], $bieudoRows);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thống kê Chi Tiêu Trong Ngày</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body {
    background-color: #b6f7b6;
    font-family: Arial, sans-serif;
    text-align: center;
}
h2 {
    background-color: #a0ecaa;
    margin: 0;
    padding: 15px;
    text-shadow: 1px 1px 2px gray;
}
.container {
    background: white;
    margin: 20px auto;
    padding: 20px;
    width: 80%;
    border-radius: 10px;
}
.chart-container { width: 50%; margin: 20px auto; }
.list {
    background: #a5e7a5;
    padding: 15px;
    border-radius: 10px;
    margin-top: 20px;
    text-align: left;
}
p, label { font-size: 16px; }
</style>
</head>
<body>
    <h2>Thống kê Chi Tiêu Trong Ngày</h2>

    <div class="container">
        <!-- Form chọn ngày -->
        <form method="get">
            <label>Chọn ngày: </label>
            <input type="date" name="ngay" value="<?= htmlspecialchars($ngay) ?>">
            <button type="submit">Xem</button>
        </form>

        <hr style="margin:20px 0;">

        <?php if (empty($danhsach)): ?>
            <p><b>Chưa có dữ liệu!</b></p>
        <?php else: ?>
            <p><b>Tổng chi trong ngày:</b> <?= number_format($tongchi, 0, ',', '.') ?> VND</p>
            <p><b>Số lượng khoản chi:</b> <?= count($danhsach) ?></p>

            <?php if (!empty($values)): ?>
            <div class="chart-container">
                <canvas id="bieudo"></canvas>
            </div>
            <?php endif; ?>

            <div class="list">
                <b>Danh sách chi tiết:</b><br><br>
                <?php foreach ($danhsach as $i => $row): ?>
                    <?= ($i+1) . ". " . htmlspecialchars($row['noidung']) ?> -
                    <?= number_format($row['sotien'], 0, ',', '.') ?> VND -
                    <?= htmlspecialchars($row['tendanhmuc'] ?? 'Khác') ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

<?php if (!empty($values)): ?>
<script>
const ctx = document.getElementById('bieudo');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            data: <?= json_encode($values) ?>,
            backgroundColor: ['#4e79a7','#f28e2b','#e15759','#76b7b2','#59a14f','#9467bd','#ff9da7']
        }]
    },
    options: {
        plugins: {
            title: { display: true, text: 'Thống kê chi tiêu trong ngày' }
        }
    }
});
</script>
<?php endif; ?>
</body>
</html>
