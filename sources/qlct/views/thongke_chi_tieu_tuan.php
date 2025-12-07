<?php
ob_start();
?>
<div class="thongke-chi-tieu-tuan">
    <h1>Thống kê chi tiêu trong tuần</h1>

    <!-- Form chọn năm và tháng -->
    <form method="GET" action="index.php" class="filter-form">
        <input type="hidden" name="controller" value="statistical">
        <input type="hidden" name="action" value="weeklyStatistics">

        <div class="form-group">
            <label for="year">Chọn năm:</label>
            <select name="year" id="year" onchange="this.form.submit()">
                <?php for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++): ?>
                    <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="month">Chọn tháng:</label>
            <select name="month" id="month" onchange="this.form.submit()">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= ($month == $m) ? 'selected' : '' ?>>
                        Tháng <?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <button type="submit" class="btn-filter">Xem thống kê</button>
    </form>

    <div class="stats-info">
        <h2>Tháng <?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>/<?= $year ?></h2>
    </div>

    <!-- Bảng thống kê chi tiêu tuần -->
    <div class="table-container">
        <table class="stats-table" border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th style="text-align: center; width: 20%;">Tuần</th>
                    <th style="text-align: right; width: 25%;">Thu nhập</th>
                    <th style="text-align: right; width: 25%;">Chi tiêu</th>
                    <th style="text-align: right; width: 25%;">Chênh lệch</th>
                    <th style="text-align: center; width: 5%;">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($totalByWeek)): ?>
                    <?php 
                    $totalIncome = 0;
                    $totalExpense = 0;
                    foreach ($totalByWeek as $index => $week): 
                        $difference = $week['income'] - $week['expense'];
                        $totalIncome += $week['income'];
                        $totalExpense += $week['expense'];
                    ?>
                        <tr class="week-row">
                            <td style="text-align: center; font-weight: bold;">
                                <?= htmlspecialchars($week['week']) ?>
                            </td>
                            <td style="text-align: right;">
                                <span class="income-amount">
                                    <?= number_format($week['income'], 0, ',', '.') ?> đ
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <span class="expense-amount" style="color: #dc3545;">
                                    <?= number_format($week['expense'], 0, ',', '.') ?> đ
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <span class="difference-amount <?= $difference >= 0 ? 'positive' : 'negative' ?>">
                                    <?= number_format(abs($difference), 0, ',', '.') ?> đ
                                    <?= $difference >= 0 ? '(tiết kiệm)' : '(thâu chi)' ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="index.php?controller=statistical&action=weeklyDetail&year=<?= $year ?>&month=<?= $month ?>&week=<?= ($index + 1) ?>" 
                                   class="btn-detail" title="Xem chi tiết">
                                    📋
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Tổng cộng -->
                    <tr class="total-row" style="background-color: #f9f9f9; font-weight: bold; border-top: 2px solid #333;">
                        <td style="text-align: center;">TỔNG CỘNG</td>
                        <td style="text-align: right;">
                            <span class="total-income">
                                <?= number_format($totalIncome, 0, ',', '.') ?> đ
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <span class="total-expense" style="color: #dc3545;">
                                <?= number_format($totalExpense, 0, ',', '.') ?> đ
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <span class="total-difference <?= ($totalIncome - $totalExpense) >= 0 ? 'positive' : 'negative' ?>">
                                <?= number_format(abs($totalIncome - $totalExpense), 0, ',', '.') ?> đ
                                <?= ($totalIncome - $totalExpense) >= 0 ? '(tiết kiệm)' : '(thâu chi)' ?>
                            </span>
                        </td>
                        <td></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #999;">
                            Không có dữ liệu chi tiêu trong tháng này.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Biểu đồ chi tiêu tuần -->
    <?php if (!empty($totalByWeek)): ?>
    <div class="chart-container" style="margin-top: 30px;">
        <h3>Biểu đồ chi tiêu tuần</h3>
        <canvas id="weeklyChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chuẩn bị dữ liệu cho biểu đồ
        const weekLabels = <?= json_encode(array_column($totalByWeek, 'week')) ?>;
        const expenseData = <?= json_encode(array_column($totalByWeek, 'expense')) ?>;
        const incomeData = <?= json_encode(array_column($totalByWeek, 'income')) ?>;

        const ctx = document.getElementById('weeklyChart').getContext('2d');
        const weeklyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: weekLabels,
                datasets: [
                    {
                        label: 'Thu nhập',
                        data: incomeData,
                        backgroundColor: '#28a745',
                        borderColor: '#1e7e34',
                        borderWidth: 1
                    },
                    {
                        label: 'Chi tiêu',
                        data: expenseData,
                        backgroundColor: '#dc3545',
                        borderColor: '#c82333',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'So sánh thu nhập và chi tiêu theo tuần'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                }
            }
        });
    </script>
    <?php endif; ?>

    <style>
        .thongke-chi-tieu-tuan {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
        }

        .thongke-chi-tieu-tuan h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-weight: bold;
            color: #555;
        }

        .form-group select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-filter {
            padding: 8px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-filter:hover {
            background-color: #0056b3;
        }

        .stats-info {
            margin-bottom: 20px;
        }

        .stats-info h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stats-table thead {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
        }

        .stats-table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #dee2e6;
        }

        .stats-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        .stats-table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .week-row td {
            vertical-align: middle;
        }

        .income-amount {
            color: #28a745;
            font-weight: 600;
        }

        .expense-amount {
            color: #dc3545;
            font-weight: 600;
        }

        .difference-amount {
            font-weight: 600;
            font-size: 14px;
        }

        .difference-amount.positive {
            color: #28a745;
        }

        .difference-amount.negative {
            color: #dc3545;
        }

        .total-row {
            background-color: #f0f0f0;
        }

        .total-income {
            color: #28a745;
            font-weight: bold;
        }

        .total-expense {
            color: #dc3545;
            font-weight: bold;
        }

        .total-difference {
            font-weight: bold;
        }

        .total-difference.positive {
            color: #28a745;
        }

        .total-difference.negative {
            color: #dc3545;
        }

        .btn-detail {
            display: inline-block;
            padding: 6px 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-detail:hover {
            background-color: #0056b3;
            text-decoration: none;
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
            .filter-form {
                flex-direction: column;
            }

            .form-group {
                width: 100%;
            }

            .stats-table {
                font-size: 12px;
            }

            .stats-table th,
            .stats-table td {
                padding: 8px;
            }

            .btn-detail {
                padding: 4px 8px;
                font-size: 12px;
            }
        }
    </style>
</div>
