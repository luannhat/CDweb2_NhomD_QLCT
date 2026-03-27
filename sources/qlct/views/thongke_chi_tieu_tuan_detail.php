<?php
ob_start();
?>
<div class="thongke-chi-tieu-tuan-detail">
    <h1>Chi tiết chi tiêu - <?= htmlspecialchars($weekInfo['label']) ?></h1>

    <!-- Nút quay lại -->
    <div class="back-button">
        <a href="index.php?controller=statistical&action=weeklyStatistics&year=<?= $year ?>&month=<?= $month ?>" 
           class="btn-back">← Quay lại</a>
    </div>

    <!-- Thông tin tóm tắt -->
    <div class="summary-info">
        <div class="info-card">
            <div class="info-label">Tháng</div>
            <div class="info-value"><?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>/<?= $year ?></div>
        </div>
        <div class="info-card">
            <div class="info-label">Tuần</div>
            <div class="info-value"><?= htmlspecialchars($weekInfo['label']) ?></div>
        </div>
        <div class="info-card">
            <div class="info-label">Thu nhập</div>
            <div class="info-value income-color">
                <?= number_format($weekInfo['thu_nhap'], 0, ',', '.') ?> đ
            </div>
        </div>
        <div class="info-card">
            <div class="info-label">Chi tiêu</div>
            <div class="info-value expense-color">
                <?= number_format($weekInfo['chi_tieu'], 0, ',', '.') ?> đ
            </div>
        </div>
        <div class="info-card">
            <div class="info-label">Chênh lệch</div>
            <div class="info-value <?= ($weekInfo['thu_nhap'] - $weekInfo['chi_tieu']) >= 0 ? 'positive-color' : 'negative-color' ?>">
                <?= number_format(abs($weekInfo['thu_nhap'] - $weekInfo['chi_tieu']), 0, ',', '.') ?> đ
                <br>
                <small><?= ($weekInfo['thu_nhap'] - $weekInfo['chi_tieu']) >= 0 ? '(tiết kiệm)' : '(thâu chi)' ?></small>
            </div>
        </div>
    </div>

    <!-- Danh sách chi tiêu chi tiết -->
    <h2>Danh sách chi tiêu chi tiết</h2>
    
    <?php if (!empty($expenses)): ?>
        <div class="table-container">
            <table class="detail-table" border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr style="background-color: #f0f0f0;">
                        <th style="text-align: center; width: 15%;">Ngày giao dịch</th>
                        <th style="text-align: left; width: 30%;">Danh mục</th>
                        <th style="text-align: right; width: 20%;">Số tiền</th>
                        <th style="text-align: left; width: 35%;">Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $groupedByCategory = [];
                    foreach ($expenses as $expense) {
                        $category = $expense['machitieu'] ?? 'Khác';
                        if (!isset($groupedByCategory[$category])) {
                            $groupedByCategory[$category] = [];
                        }
                        $groupedByCategory[$category][] = $expense;
                    }
                    ?>
                    
                    <?php foreach ($groupedByCategory as $category => $items): ?>
                        <?php 
                        $categoryTotal = array_sum(array_column($items, 'sotien'));
                        $isFirstRow = true;
                        ?>
                        <?php foreach ($items as $expense): ?>
                            <tr class="expense-row">
                                <td style="text-align: center; padding: 10px;">
                                    <?= date('d/m/Y', strtotime($expense['ngaychitieu'])) ?>
                                </td>
                                <td style="padding: 10px;">
                                    <strong><?= htmlspecialchars($category) ?></strong>
                                </td>
                                <td style="text-align: right; padding: 10px; color: #dc3545; font-weight: 600;">
                                    <?= number_format($expense['sotien'], 0, ',', '.') ?> đ
                                </td>
                                <td style="padding: 10px;">
                                    <?= htmlspecialchars($expense['ghichu'] ?? 'N/A') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <!-- Tổng theo danh mục -->
                        <tr class="category-total">
                            <td colspan="2" style="text-align: right; font-weight: bold;">
                                Tổng "<?= htmlspecialchars($category) ?>"
                            </td>
                            <td style="text-align: right; font-weight: bold; color: #dc3545;">
                                <?= number_format($categoryTotal, 0, ',', '.') ?> đ
                            </td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Tổng cộng -->
                    <tr class="total-row" style="background-color: #f9f9f9; font-weight: bold; border-top: 2px solid #333;">
                        <td colspan="2" style="text-align: right;">TỔNG CHI TIÊU</td>
                        <td style="text-align: right; color: #dc3545; font-size: 16px;">
                            <?= number_format($totalWeekExpense, 0, ',', '.') ?> đ
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-data">
            <p>Không có dữ liệu chi tiêu trong tuần này.</p>
        </div>
    <?php endif; ?>

    <!-- Biểu đồ phân bố chi tiêu theo danh mục -->
    <?php if (!empty($groupedByCategory)): ?>
    <div class="chart-container" style="margin-top: 30px;">
        <h3>Phân bố chi tiêu theo danh mục</h3>
        <canvas id="categoryChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chuẩn bị dữ liệu cho biểu đồ
        const categoryLabels = <?= json_encode(array_keys($groupedByCategory)) ?>;
        const categoryTotals = <?= json_encode(array_values(array_map(function($items) {
            return array_sum(array_column($items, 'sotien'));
        }, array_values($groupedByCategory)))) ?>;

        const colors = [
            '#FF6384','#36A2EB','#FFCE56','#33CC99','#9966FF',
            '#FF9F40','#66CCFF','#FF6666','#33CC66','#FF99CC'
        ];

        const ctx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryTotals,
                    backgroundColor: colors.slice(0, categoryLabels.length),
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return value.toLocaleString('vi-VN') + ' đ (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    </script>
    <?php endif; ?>

    <style>
        .thongke-chi-tieu-tuan-detail {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
        }

        .thongke-chi-tieu-tuan-detail h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .thongke-chi-tieu-tuan-detail h2 {
            color: #555;
            margin-top: 30px;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .back-button {
            margin-bottom: 20px;
        }

        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-back:hover {
            background-color: #5a6268;
            text-decoration: none;
        }

        .summary-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .info-card {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .info-label {
            font-size: 12px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .income-color {
            color: #28a745 !important;
        }

        .expense-color {
            color: #dc3545 !important;
        }

        .positive-color {
            color: #28a745 !important;
        }

        .negative-color {
            color: #dc3545 !important;
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .detail-table thead {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
        }

        .detail-table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #dee2e6;
        }

        .detail-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .expense-row {
            background-color: #ffffff;
        }

        .category-total {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .category-total td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
        }

        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
            border-top: 2px solid #333;
        }

        .total-row td {
            padding: 15px 12px;
        }

        .no-data {
            padding: 40px;
            text-align: center;
            color: #999;
            background-color: #f5f5f5;
            border-radius: 8px;
        }

        .chart-container {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .chart-container h3 {
            margin-top: 0;
            color: #333;
        }

        @media (max-width: 768px) {
            .summary-info {
                grid-template-columns: repeat(2, 1fr);
            }

            .detail-table {
                font-size: 12px;
            }

            .detail-table th,
            .detail-table td {
                padding: 8px;
            }
        }
    </style>
</div>
