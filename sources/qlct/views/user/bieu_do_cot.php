<?php
session_start();
require_once __DIR__ . '/../models/StatisticalModel.php';

$model = new StatisticalModel();
$makh = $_SESSION['id'] ?? 1;

$currentYear = (int)date('Y');
$currentMonth = (int)date('n');

$selectedYear = isset($_GET['year']) ? max(2000, (int)$_GET['year']) : $currentYear;
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : $currentMonth;
if ($selectedMonth < 1 || $selectedMonth > 12) {
    $selectedMonth = $currentMonth;
}

$weeklyData = $model->getWeeklyIncomeExpenseByMonth($makh, $selectedYear, $selectedMonth);

$monthStart = sprintf('%04d-%02d-01', $selectedYear, $selectedMonth);
$monthEnd = date('Y-m-t', strtotime($monthStart));
$totals = $model->getTotalIncomeExpenseByDateRange($makh, $monthStart, $monthEnd);

$labels = array_column($weeklyData, 'label');
$incomeSeries = array_map('floatval', array_column($weeklyData, 'thu_nhap'));
$expenseSeries = array_map('floatval', array_column($weeklyData, 'chi_tieu'));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Biểu đồ cột - Quản lý chi tiêu</title>
	<link rel="icon" type="image/svg+xml" href="../public/favicon.svg">
	<link rel="alternate icon" href="../public/favicon.svg">

	<!-- Font Awesome 6 -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

	<!-- Chart.js -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<!-- CSS -->
	<?php $cssVersion = @filemtime(__DIR__ . '/../public/css/khoanchi.css') ?: time(); ?>
	<link rel="stylesheet" href="../public/css/khoanchi.css?v=<?php echo $cssVersion; ?>" />
</head>
<body>

<div class="app">
	<?php include __DIR__ . '/layouts/sidebar.php'; ?>

	<div class="main">
		<header class="header">
			<h1>Biểu đồ cột</h1>

			<div class="search" role="search">
				<form method="get" action="bieu_do_cot.php">
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

		<main class="content bar-chart-page">
			<?php if (!empty($_SESSION['message'])): ?>
				<div class="inline-alert" role="alert">
					<?php
					echo $_SESSION['message'];
					unset($_SESSION['message']);
					?>
				</div>
			<?php endif; ?>

			<section class="bar-chart-hero">
				
				<form class="bar-chart-filter" method="GET" action="bieu_do_cot.php">
					<label>
						Năm
						<select name="year">
							<?php for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++): ?>
								<option value="<?php echo $y; ?>" <?php echo $selectedYear == $y ? 'selected' : ''; ?>>
									<?php echo $y; ?>
								</option>
							<?php endfor; ?>
						</select>
					</label>
					<label>
						Tháng
						<select name="month">
							<?php for ($m = 1; $m <= 12; $m++): ?>
								<option value="<?php echo $m; ?>" <?php echo $selectedMonth == $m ? 'selected' : ''; ?>>
									<?php echo $m; ?>
								</option>
							<?php endfor; ?>
						</select>
					</label>
					<button type="submit" class="btn primary">Xem</button>
				</form>
			</section>

			<section class="bar-chart-card">
				<div class="bar-chart-card__chart">
					<canvas id="barChart"></canvas>
				</div>
				<div class="bar-chart-legend">
					<div class="legend-item-inline">
						<div class="legend-color legend-income"></div>
						<span>Thu nhập</span>
					</div>
					<div class="legend-item-inline">
						<div class="legend-color legend-expense"></div>
						<span>Chi tiêu</span>
					</div>
				</div>
				<div class="bar-chart-summary">
					<div>
						<span class="summary-label">Tổng thu nhập:</span>
						<span class="summary-value"><?php echo number_format($totals['tong_thu_nhap'], 0, ',', '.'); ?> VNĐ</span>
					</div>
					<div>
						<span class="summary-label">Tổng chi tiêu:</span>
						<span class="summary-value"><?php echo number_format($totals['tong_chi_tieu'], 0, ',', '.'); ?> VNĐ</span>
					</div>
				</div>
			</section>
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

	const ctx = document.getElementById('barChart');
	if (ctx) {
		const labels = <?php echo json_encode($labels); ?>;
		const incomeData = <?php echo json_encode($incomeSeries); ?>;
		const expenseData = <?php echo json_encode($expenseSeries); ?>;

		new Chart(ctx, {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [
					{
						label: 'Thu nhập',
						backgroundColor: '#00f16f',
						data: incomeData,
						borderRadius: 6,
						barThickness: 32
					},
					{
						label: 'Chi tiêu',
						backgroundColor: '#ff2a2a',
						data: expenseData,
						borderRadius: 6,
						barThickness: 32
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
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
						grid: {
							color: 'rgba(0,0,0,0.05)'
						}
					},
					x: {
						grid: {
							display: false
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

