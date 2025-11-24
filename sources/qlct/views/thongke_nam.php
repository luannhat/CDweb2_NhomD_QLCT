<?php
session_start();
require_once __DIR__ . '/../models/StatisticalModel.php';

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
?>

<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Thống kê chi tiêu trong năm - Quản lý chi tiêu</title>
	<link rel="icon" type="image/svg+xml" href="../public/favicon.svg">
	<link rel="alternate icon" href="../public/favicon.svg">

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
			<h1>Thống kê chi tiêu trong năm</h1>

			<div class="search" role="search">
				<form method="get" action="baocao.php">
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

			<!-- Form chọn năm -->
			<div class="year-selector">
				<form method="GET" action="thongke_nam.php" class="year-form">
					<label for="year">Chọn năm:</label>
					<select name="year" id="year">
						<?php 
						$currentYear = date('Y');
						for ($y = $currentYear - 5; $y <= $currentYear + 1; $y++): 
						?>
							<option value="<?php echo $y; ?>" <?php echo ($year == $y) ? 'selected' : ''; ?>>
								<?php echo $y; ?>
							</option>
						<?php endfor; ?>
					</select>
					<button type="submit" class="btn primary">Xem thống kê</button>
				</form>
			</div>

			<!-- Biểu đồ và legend -->
			<?php if (!empty($chartData)): ?>
			<div class="chart-container">
				<div class="chart-wrapper">
					<canvas id="pieChart"></canvas>
				</div>
				<div class="chart-legend">
					<?php foreach ($chartData as $item): ?>
						<div class="legend-item">
							<div class="legend-color" style="background-color: <?php echo $item['color']; ?>;"></div>
							<span class="legend-text"><?php echo htmlspecialchars($item['name']); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php else: ?>
				<div class="no-data">
					<p>Không có dữ liệu chi tiêu cho năm <?php echo $year; ?></p>
				</div>
			<?php endif; ?>

			<!-- Tổng chi tiêu cả năm -->
			<?php if ($totalExpense > 0): ?>
			<div class="total-expense">
				<p>Tổng chỉ tiêu cả năm: <strong><?php echo number_format($totalExpense, 0, ',', '.'); ?> VNĐ</strong></p>
			</div>
			<?php endif; ?>
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

	// Vẽ pie chart
	<?php if (!empty($chartData)): ?>
	const ctx = document.getElementById('pieChart');
	if (ctx) {
		const chartData = <?php echo json_encode($chartData); ?>;
		
		new Chart(ctx, {
			type: 'pie',
			data: {
				labels: chartData.map(item => item.name),
				datasets: [{
					data: chartData.map(item => item.value),
					backgroundColor: chartData.map(item => item.color),
					borderWidth: 2,
					borderColor: '#fff'
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: {
						display: false
					},
					tooltip: {
						callbacks: {
							label: function(context) {
								const label = context.label || '';
								const value = context.parsed || 0;
								const total = context.dataset.data.reduce((a, b) => a + b, 0);
								const percent = ((value / total) * 100).toFixed(2);
								return label + ': ' + new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ (' + percent + '%)';
							}
						}
					}
				}
			}
		});
	}
	<?php endif; ?>
});
</script>

</body>
</html>

