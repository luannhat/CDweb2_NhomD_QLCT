<style>
    /* Toàn màn hình */
    body {
        background-color: #f4f6fa;
        /* màu nền nhẹ nhàng */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 20px;
    }

    /* Tiêu đề */
    h1,
    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    /* Form chọn năm/tháng */
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

    form select {
        padding: 6px 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    form button {
        padding: 6px 16px;
        background-color: #4a90e2;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.2s;
    }

    form button:hover {
        background-color: #357ABD;
    }

    /* Bảng container */
    .table-container {
        display: flex;
        justify-content: center;
        overflow-x: auto;
        max-width: 90%;
    }

    /* Bảng thống kê */
    table {
        border-collapse: collapse;
        min-width: 800px;
        text-align: center;
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Header bảng */
    th {
        background-color: #4a90e2;
        color: white;
        padding: 14px 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid #ccc;
        /* giữ đường dọc */
    }

    /* Dòng dữ liệu */
    td {
        padding: 12px 15px;
        border: 1px solid #ccc;
        /* giữ đường dọc */
    }

    /* Dòng chẵn */
    tr:nth-child(even) td {
        background-color: #f9f9f9;
    }

    /* Hover trên cả dòng */
    tr:hover td {
        background-color: #e6f7ff;
        transition: 0.2s;
    }

    /* Thông báo khi không có dữ liệu */
    .no-data {
        text-align: center;
        color: #888;
        font-style: italic;
        margin-top: 20px;
    }
</style>

<?php
$year = $year ?? date('Y');
$month = $month ?? '';
$data = $data ?? [];
$transactions = $transactions ?? []; // chi tiết giao dịch nếu có
?>

<h1>Thống kê chi tiêu</h1>

<!-- Form chọn năm/tháng -->
<form method="GET" action="index.php">
    <input type="hidden" name="controller" value="transaction">
    <input type="hidden" name="action" value="monthlyStatistics">

    <label for="year">Chọn năm:</label>
    <select name="year" id="year">
        <?php for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++): ?>
            <option value="<?= $y ?>" <?= (isset($_GET['year']) && $_GET['year']==$y) ? 'selected' : '' ?>>
                <?= $y ?>
            </option>
        <?php endfor; ?>
    </select>

    <label for="month">Chọn tháng:</label>
    <select name="month" id="month">
        <option value="">--Tất cả tháng--</option>
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= (isset($_GET['month']) && $_GET['month']==$m) ? 'selected' : '' ?>>
                <?= $m ?>
            </option>
        <?php endfor; ?>
    </select>

    <button type="submit">Xem thống kê</button>
</form>

<!-- Bảng thống kê -->
<?php if (!empty($data)): ?>
    <h2>Năm <?= htmlspecialchars($year) ?> <?= $month ? "- Tháng $month" : "" ?></h2>
    <div style="display:flex; justify-content:center; align-items:flex-start; gap:30px; flex-wrap:wrap;">

        <div class="table-container" style="flex:1; min-width:400px; overflow-x:auto;">
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
                    <!-- Chi tiết các giao dịch -->
                    <?php foreach ($transactions as $gd): ?>
                        <tr>
                            <td><?= $gd['magd'] ?></td>
                            <td><?= htmlspecialchars($gd['noidung']) ?></td>
                            <td><?= number_format($gd['sotien'], 0, ',', '.') ?> đ</td>
                            <td><?= $gd['ngaygiaodich'] ?></td>
                            <td><?= htmlspecialchars($gd['ghichu']) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Tổng chi tiêu nằm dưới cùng -->
                    <tr style="font-weight:bold; background-color:#f2f2f2;">
                        <td colspan="4" style="text-align:right;">Tổng chi tiêu tháng <?= $month ?>:</td>
                        <td><?= number_format($data[$month] ?? 0, 0, ',', '.') ?> đ</td>
                    </tr>



                <?php else: ?>
                    <!-- Tổng chi tiêu theo tất cả tháng -->
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <tr>
                            <td><?= $m ?></td>
                            <td><?= number_format($data[$m] ?? 0, 0, ',', '.') ?> đ</td>
                        </tr>
                    <?php endfor; ?>
                <?php endif; ?>
            </table>
        </div>
        <div style="flex:0 0 500px; max-width:500px;">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
<?php else: ?>
    <p class="no-data">Không có dữ liệu chi tiêu cho năm/tháng này</p>
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
    foreach ($data as $monthNum => $total) {
        $labels[] = "Tháng $monthNum";
        $values[] = $total;
    }
}

?>

<script>
    const ctx = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Chi tiêu (đ)',
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
                    position: 'right',
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