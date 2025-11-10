<?php
if (empty($data)): ?>
    <p>Không có dữ liệu để hiển thị.</p>
<?php else: ?>
    <?php
    $total = array_sum(array_column($data, 'tongtien'));
    $percentages = [];
    $colors = [
        '#FF6384','#36A2EB','#FFCE56','#33CC99','#9966FF',
        '#FF9F40','#66CCFF','#FF6666','#33CC66','#FF99CC'
    ];

    foreach ($data as $index => $row) {
        $percentages[] = [
            'name' => $row['tendanhmuc'],
            'value' => $row['tongtien'],
            'percent' => $total > 0 ? round(($row['tongtien'] / $total) * 100, 2) : 0,
            'color' => $colors[$index % count($colors)]
        ];
    }
    ?>

    <div style="display: flex; align-items: flex-start; gap: 40px; margin-bottom: 20px;">
        <!-- PieChart -->
        <div style="width: 400px; text-align: center;">
            <canvas id="pieChart"></canvas>
            <!-- Legend -->
            <div style="margin-top: 10px; text-align: left;">
                <?php foreach ($percentages as $p): ?>
                    <div style="display: flex; align-items: center; margin-bottom: 4px;">
                        <div style="width: 20px; height: 20px; background-color: <?= $p['color'] ?>; margin-right: 8px;"></div>
                        <span><?= htmlspecialchars($p['name']) ?> (<?= $p['percent'] ?>%)</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Bảng chi tiêu -->
        <div>
            <table border="1" cellpadding="8">
                <tr><th>Danh mục</th><th>Tổng tiền</th><th>Tỷ lệ (%)</th></tr>
                <?php foreach ($percentages as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= number_format($p['value'], 0, ',', '.') ?> đ</td>
                        <td><?= $p['percent'] ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const ctx = document.getElementById('pieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_column($percentages, 'name')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($percentages, 'value')) ?>,
                backgroundColor: <?= json_encode(array_column($percentages, 'color')) ?>
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false // dùng legend tự tạo phía dưới biểu đồ
                }
            }
        }
    });
    </script>
<?php endif; ?>
