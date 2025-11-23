<?php
session_start();
require_once __DIR__ . '/../models/StatisticalModel.php';

$model = new StatisticalModel();
$makh = $_SESSION['id'] ?? 1;
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-01-01');
$toDate = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-12-31');

$monthlyData = $model->getIncomeExpenseByDateRange($makh, $fromDate, $toDate);
$totals = $model->getTotalIncomeExpenseByDateRange($makh, $fromDate, $toDate);

// Chuẩn bị dữ liệu cho biểu đồ
$labels = [];
$incomeData = [];
$expenseData = [];

foreach ($monthlyData as $row) {
    $labels[] = date('m/Y', strtotime($row['thang'] . '-01'));
    $incomeData[] = $row['thu_nhap'];
    $expenseData[] = $row['chi_tieu'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Biểu đồ đường - Quản lý chi tiêu</title>

	<!-- Font Awesome 6 -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

	<!-- Chart.js -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<!-- CSS riêng của trang -->
	<?php $cssVersion = @filemtime(__DIR__ . '/../public/css/khoanchi.css') ?: time(); ?>
	<link rel="stylesheet" href="../public/css/khoanchi.css?v=<?php echo $cssVersion; ?>" />
</head>
<body>

<div class="app">
	<!-- Sidebar -->
	<?php include __DIR__ . '/layouts/sidebar.php'; ?>

	<!-- Main -->
	<div class="main">
		<header class="header">
			<h1>Biểu đồ đường</h1>

			<div class="search" role="search">
				<form method="get" action="bieu_do_duong.php">
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

			<!-- Form chọn khoảng thời gian -->
			<div class="date-range-selector">
				<form method="GET" action="bieu_do_duong.php" class="date-range-form">
					<div class="date-input-group">
						<label for="from_date">Từ:</label>
						<input type="date" name="from_date" id="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" required />
					</div>
					<div class="date-input-group">
						<label for="to_date">Đến:</label>
						<input type="date" name="to_date" id="to_date" value="<?php echo htmlspecialchars($toDate); ?>" required />
					</div>
					<button type="submit" class="btn primary">Xem</button>
				</form>
			</div>

			<!-- Legend -->
			<div class="chart-legend-inline">
				<div class="legend-item-inline">
					<div class="legend-color" style="background-color: #36A2EB;"></div>
					<span>Đường xanh: thu nhập</span>
				</div>
				<div class="legend-item-inline">
					<div class="legend-color" style="background-color: #FF6384;"></div>
					<span>Đường đỏ: chi tiêu</span>
				</div>
			</div>

			<!-- Biểu đồ -->
			<div class="line-chart-container">
				<canvas id="lineChart"></canvas>
			</div>

			<!-- Thống kê -->
			<div class="line-chart-summary">
				<div class="summary-item">
					<span class="summary-label">Tổng thu nhập:</span>
					<span class="summary-value"><?php echo number_format($totals['tong_thu_nhap'], 0, ',', '.'); ?> VNĐ</span>
				</div>
				<div class="summary-item">
					<span class="summary-label">Tổng chỉ tiêu:</span>
					<span class="summary-value"><?php echo number_format($totals['tong_chi_tieu'], 0, ',', '.'); ?> VNĐ</span>
				</div>
				<div class="summary-item">
					<span class="summary-label">Chênh lệch:</span>
					<span class="summary-value <?php echo $totals['chenh_lech'] >= 0 ? 'positive' : 'negative'; ?>">
						<?php echo $totals['chenh_lech'] >= 0 ? '+' : ''; ?><?php echo number_format($totals['chenh_lech'], 0, ',', '.'); ?> VNĐ
					</span>
				</div>
			</div>
		</main>
	</div>
</div>

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
			if (isOpen) closeMenu();
			else {
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

	// Vẽ biểu đồ đường
	const ctx = document.getElementById('lineChart');
	if (ctx) {
		const labels = <?php echo json_encode($labels); ?>;
		const incomeData = <?php echo json_encode($incomeData); ?>;
		const expenseData = <?php echo json_encode($expenseData); ?>;
		
		new Chart(ctx, {
			type: 'line',
			data: {
				labels: labels,
				datasets: [
					{
						label: 'Thu nhập',
						data: incomeData,
						borderColor: '#36A2EB',
						backgroundColor: 'rgba(54, 162, 235, 0.1)',
						tension: 0.4,
						fill: false
					},
					{
						label: 'Chi tiêu',
						data: expenseData,
						borderColor: '#FF6384',
						backgroundColor: 'rgba(255, 99, 132, 0.1)',
						tension: 0.4,
						fill: false
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							callback: function(value) {
								if (value >= 1000000) {
									return (value / 1000000).toFixed(0) + 'tr';
								}
								return value.toLocaleString('vi-VN');
							}
						},
						title: {
							display: true,
							text: 'VNĐ'
						}
					}
				},
				plugins: {
					legend: {
						display: false
					},
					tooltip: {
						callbacks: {
							label: function(context) {
								return context.dataset.label + ': ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' VNĐ';
							}
						}
					}
				}
			}
		});
	}
});
</script>

</body>
</html>

