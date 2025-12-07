<?php
ob_start();
?>
<?php
$year = $year ?? date('Y');
$month = $month ?? null;
$data = $data ?? [];
$transactions = $transactions ?? [];
$availableMonths = $availableMonths ?? [];
?>

<h1>Thống kê chi tiêu theo tháng</h1>

<form method="GET" action="index.php">
    <input type="hidden" name="controller" value="user">
    <input type="hidden" name="action" value="stats">
    <input type="hidden" name="view" value="month">

    <label for="year">Chọn năm:</label>
    <select name="year" id="year">
        <?php for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++): ?>
            <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>><?= $y ?></option>
        <?php endfor; ?>
    </select>

    <label for="month">Chọn tháng:</label>
    <select name="month" id="month">
        <option value="">--Tất cả tháng--</option>
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= ($month == $m) ? 'selected' : '' ?>><?= $m ?></option>
        <?php endfor; ?>
    </select>


    <button type="submit">Xem thống kê</button>
</form>
<?php if (!empty($data)): ?>
    <h2>Năm <?= htmlspecialchars($year) ?> <?= $month ? "- Tháng $month" : "" ?></h2>

    <div class="stats-wrapper">
        <div class="table-container">
            <table>
                <tr>
                    <?php if ($month): ?>
                        <th>Mã GD</th>
                        <th>Nội dung</th>
                        <th>Số tiền</th>
                        <th>Ngày GD</th>
                        <th>Ghi chú</th>
                    <?php else: ?>
                        <th>Tháng</th>
                        <th>Tổng chi tiêu</th>
                    <?php endif; ?>
                </tr>

                <?php if ($month): ?>
                    <?php foreach ($transactions as $gd): ?>
                        <tr>
                            <td><?= $gd['magd'] ?></td>
                            <td><?= htmlspecialchars($gd['noidung']) ?></td>
                            <td><?= number_format($gd['sotien'], 0, ',', '.') ?> đ</td>
                            <td><?= $gd['ngaygiaodich'] ?></td>
                            <td><?= htmlspecialchars($gd['ghichu']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="font-weight:bold; background-color:#f2f2f2;">
                        <td colspan="4" style="text-align:right;">Tổng chi tiêu tháng <?= $month ?>:</td>
                        <td><?= number_format($data[$month] ?? 0, 0, ',', '.') ?> đ</td>
                    </tr>
                <?php else: ?>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <tr>
                            <td><?= $m ?></td>
                            <td><?= number_format($data[$m] ?? 0, 0, ',', '.') ?> đ</td>
                        </tr>
                    <?php endfor; ?>
                <?php endif; ?>
            </table>
        </div>

        <div class="chart-container">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
<?php else: ?>
    <p>Không có dữ liệu chi tiêu cho năm/tháng này</p>
<?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php
$labels = [];
$values = [];
if ($month) {
    foreach ($transactions as $gd) {
        $labels[] = htmlspecialchars($gd['noidung']);
        $values[] = $gd['sotien'];
    }
} else {
    foreach ($data as $m => $total) {
        $labels[] = "Tháng $m";
        $values[] = $total;
    }
}
?>
<script>
    const ctx = document.getElementById('pieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                data: <?= json_encode($values) ?>,
                backgroundColor: [
                    '#4a90e2', '#50e3c2', '#f5a623', '#e94e77', '#9013fe', '#7ed321',
                    '#d0021b', '#f8e71c', '#8b572a', '#417505', '#bd10e0', '#f57c00'
                ],
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw.toLocaleString() + ' đ';
                        }
                    }
                }
            }
        }
    });
</script>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, sans-serif;
        background: #f4f6fa;
        padding: 20px;
    }

    h1,
    h2 {
        text-align: center;
        color: #333;
    }

    form {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 30px;
        align-items: center;
    }

    form label {
        font-weight: bold;
        color: #555;
    }

    form select,
    form button {
        padding: 6px 12px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    form button {
        background: #4a90e2;
        color: #fff;
        font-weight: bold;
        cursor: pointer;
        border: none;
        transition: 0.2s;
    }

    form button:hover {
        background: #357ABD;
    }

    .table-container {
        display: flex;
        justify-content: center;
        overflow-x: auto;
        max-width: 90%;
    }

    table {
        border-collapse: collapse;
        min-width: 400px;
        text-align: center;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    th {
        background: #4a90e2;
        color: #fff;
        padding: 12px;
        border: 1px solid #ccc;
    }

    td {
        padding: 12px;
        border: 1px solid #ccc;
    }

    tr:nth-child(even) td {
        background: #f9f9f9;
    }

    tr:hover td {
        background: #e6f7ff;
        transition: 0.2s;
    }

    .no-data {
        text-align: center;
        color: #888;
        font-style: italic;
        margin-top: 20px;
    }

    .stats-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        justify-content: center;
        align-items: flex-start;
    }

    .table-container {
        flex: 1;
        min-width: 400px;
        overflow-x: auto;
    }

    .chart-container {
        flex: 0 0 500px;
        max-width: 500px;
    }
</style>
<?php
$content = ob_get_clean();
include __DIR__ . '/user/layout.php';
?>