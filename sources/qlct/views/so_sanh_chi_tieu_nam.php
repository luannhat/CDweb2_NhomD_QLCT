<?php
session_start();
require_once __DIR__ . '/../models/StatisticalModel.php';

$model = new StatisticalModel();
$makh = $_SESSION['id'] ?? 1;

$currentYear = (int)date('Y');
$fromYear = isset($_GET['from_year']) ? max(2000, (int)$_GET['from_year']) : ($currentYear - 5);
$toYear = isset($_GET['to_year']) ? min(2100, (int)$_GET['to_year']) : $currentYear;

// Đảm bảo fromYear <= toYear
if ($fromYear > $toYear) {
    $temp = $fromYear;
    $fromYear = $toYear;
    $toYear = $temp;
}

$expenseData = $model->getExpenseByYearRange($makh, $fromYear, $toYear);

// Chuẩn bị dữ liệu cho biểu đồ
$years = array_keys($expenseData);
$amounts = array_values($expenseData);

// Màu sắc cho từng cột (giống như trong ảnh)
$colors = [
    '#4285F4', // Blue
    '#34A853', // Green  
    '#FBBC05', // Yellow
    '#9C27B0', // Purple
    '#FF5722', // Red/Orange
    '#00BCD4', // Cyan
    '#FF9800', // Orange
    '#E91E63', // Pink
    '#795548', // Brown
    '#607D8B'  // Blue Grey
];

$chartColors = [];
foreach ($years as $index => $year) {
    $chartColors[] = $colors[$index % count($colors)];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>So sánh chi tiêu giữa các năm - Quản lý chi tiêu</title>
	<link rel="icon" type="image/svg+xml" href="../public/favicon.svg">
	<link rel="alternate icon" href="../public/favicon.svg">

	<!-- Font Awesome 6 -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

	<!-- Chart.js -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<!-- CSS riêng của trang -->
	<?php $cssVersion = @filemtime(__DIR__ . '/../public/css/khoanchi.css') ?: time(); ?>
	<link rel="stylesheet" href="../public/css/khoanchi.css?v=<?php echo $cssVersion; ?>" />
	
	<style>
		.compare-years-container {
			max-width: 1200px;
			margin: 0 auto;
			padding: 20px;
		}
		
		.compare-years-header {
			margin-bottom: 30px;
		}
		
		.compare-years-header h1 {
			font-size: 28px;
			color: #333;
			margin-bottom: 10px;
		}
		
		.compare-years-description {
			background: #f8f9fa;
			padding: 20px;
			border-radius: 8px;
			margin-bottom: 30px;
			line-height: 1.6;
			color: #555;
		}
		
		.compare-years-description h2 {
			font-size: 20px;
			color: #333;
			margin-bottom: 15px;
		}
		
		.compare-years-description h3 {
			font-size: 18px;
			color: #444;
			margin-top: 20px;
			margin-bottom: 10px;
		}
		
		.compare-years-description ul {
			margin-left: 20px;
			margin-bottom: 15px;
		}
		
		.compare-years-description li {
			margin-bottom: 8px;
		}
		
		.year-selector-form {
			background: white;
			padding: 20px;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			margin-bottom: 30px;
		}
		
		.year-selector-form form {
			display: flex;
			align-items: center;
			gap: 15px;
			flex-wrap: wrap;
		}
		
		.year-selector-form label {
			font-weight: 600;
			color: #333;
		}
		
		.year-selector-form select {
			padding: 8px 12px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 14px;
			background: white;
		}
		
		.year-selector-form button {
			padding: 10px 20px;
			background: #5F9EA0;
			color: white;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			font-size: 14px;
			font-weight: 600;
		}
		
		.year-selector-form button:hover {
			background: #4a8a8c;
		}
		
		.chart-container-wrapper {
			background: white;
			padding: 30px;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			margin-bottom: 30px;
		}
		
		.chart-title {
			text-align: center;
			font-size: 20px;
			font-weight: 600;
			color: #333;
			margin-bottom: 20px;
		}
		
		.chart-wrapper {
			position: relative;
			height: 400px;
			margin-bottom: 20px;
		}
		
		.chart-figure-label {
			text-align: center;
			color: #666;
			font-size: 14px;
			margin-top: 10px;
			font-style: italic;
		}
		
		.no-data-message {
			text-align: center;
			padding: 40px;
			color: #999;
			font-size: 16px;
		}
		
		.functionality-section {
			background: #f8f9fa;
			padding: 25px;
			border-radius: 8px;
			margin-top: 30px;
		}
		
		.functionality-section h2 {
			font-size: 20px;
			color: #333;
			margin-bottom: 15px;
		}
		
		.functionality-section h3 {
			font-size: 16px;
			color: #444;
			margin-top: 15px;
			margin-bottom: 8px;
		}
		
		.functionality-section ul {
			margin-left: 20px;
		}
		
		.functionality-section li {
			margin-bottom: 6px;
		}
	</style>
</head>
<body>

<div class="app">
	<!-- Sidebar -->
	<?php include __DIR__ . '/layouts/sidebar.php'; ?>

	<!-- Main -->
	<div class="main">
		<header class="header">
			<h1>So sánh chi tiêu giữa các năm</h1>

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

			<div class="compare-years-container">
				<!-- Mô tả chức năng -->
				<div class="compare-years-description">
					<h2>So sánh chi tiêu giữa các năm</h2>
					<p>Hệ thống thu thập dữ liệu chi tiêu do người dùng nhập vào (từ Excel/CSV hoặc nhập thủ công). Dữ liệu này được tổng hợp theo năm, hiển thị dưới dạng biểu đồ cột, đường hoặc bảng dữ liệu để so sánh. Người dùng có thể chọn các năm cụ thể hoặc xem toàn bộ xu hướng.</p>
				</div>

				<!-- Form chọn khoảng năm -->
				<div class="year-selector-form">
					<form method="GET" action="../index.php">
						<input type="hidden" name="controller" value="statistical">
						<input type="hidden" name="action" value="compareYears">
						<label for="from_year">Từ năm:</label>
						<select name="from_year" id="from_year">
							<?php 
							for ($y = $currentYear - 10; $y <= $currentYear; $y++): 
							?>
								<option value="<?php echo $y; ?>" <?php echo ($fromYear == $y) ? 'selected' : ''; ?>>
									<?php echo $y; ?>
								</option>
							<?php endfor; ?>
						</select>
						
						<label for="to_year">Đến năm:</label>
						<select name="to_year" id="to_year">
							<?php 
							for ($y = $currentYear - 10; $y <= $currentYear + 1; $y++): 
							?>
								<option value="<?php echo $y; ?>" <?php echo ($toYear == $y) ? 'selected' : ''; ?>>
									<?php echo $y; ?>
								</option>
							<?php endfor; ?>
						</select>
						
						<button type="submit">Xem báo cáo</button>
					</form>
				</div>

				<!-- Biểu đồ -->
				<?php if (!empty($expenseData)): ?>
				<div class="chart-container-wrapper">
					<div class="chart-title">So sánh chi tiêu <?php echo $fromYear; ?>-<?php echo $toYear; ?></div>
					<div class="chart-wrapper">
						<canvas id="yearComparisonChart"></canvas>
					</div>
					<div class="chart-figure-label">Hình 40. So sánh chi tiêu giữa các năm</div>
				</div>
				<?php else: ?>
					<div class="no-data-message">
						<p>Không có dữ liệu chi tiêu cho khoảng năm <?php echo $fromYear; ?> - <?php echo $toYear; ?></p>
					</div>
				<?php endif; ?>

				<!-- Mô tả chi tiết chức năng -->
				<div class="functionality-section">
					<h2>Chức năng: So sánh chi tiêu giữa các năm</h2>
					
					<h3>Mục đích:</h3>
					<p>Giúp người dùng dễ dàng theo dõi sự thay đổi chi tiêu qua nhiều năm, từ đó đưa ra quyết định tài chính hợp lý.</p>
					
					<h3>Cách hoạt động:</h3>
					<ul>
						<li>Người dùng chọn khoảng năm muốn so sánh (ví dụ: <?php echo $fromYear; ?> – <?php echo $toYear; ?>).</li>
						<li>Hệ thống hiển thị báo cáo trực quan bằng biểu đồ cột, đường hoặc bảng dữ liệu để so sánh tổng chi tiêu và các danh mục chi tiêu.</li>
						<li>Có thể xem chi tiết theo tháng/quý/năm trong từng giai đoạn để phân tích sâu hơn.</li>
					</ul>
					
					<h3>Lợi ích:</h3>
					<ul>
						<li>Phát hiện xu hướng tăng/giảm chi tiêu.</li>
						<li>Xác định giai đoạn có mức chi tiêu bất thường.</li>
						<li>Hỗ trợ lập kế hoạch tài chính cho năm tiếp theo.</li>
					</ul>
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

	// Vẽ biểu đồ so sánh chi tiêu giữa các năm
	<?php if (!empty($expenseData)): ?>
	const ctx = document.getElementById('yearComparisonChart');
	if (ctx) {
		const years = <?php echo json_encode($years); ?>;
		const amounts = <?php echo json_encode($amounts); ?>;
		const colors = <?php echo json_encode($chartColors); ?>;
		
		// Tính max value để set scale phù hợp
		const maxValue = Math.max(...amounts);
		const maxScale = Math.ceil(maxValue / 2000) * 2000; // Làm tròn lên theo bước 2000
		
		new Chart(ctx, {
			type: 'bar',
			data: {
				labels: years,
				datasets: [{
					label: 'Tổng chi tiêu (VNĐ)',
					data: amounts,
					backgroundColor: colors,
					borderColor: colors.map(c => c.replace('0.8', '1')),
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						display: false
					},
					tooltip: {
						callbacks: {
							label: function(context) {
								const value = context.parsed.y;
								return 'Tổng chi tiêu: ' + new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						title: {
							display: true,
							text: 'Tổng chi tiêu (VNĐ)'
						},
						ticks: {
							stepSize: 2000,
							callback: function(value) {
								if (value >= 1000000) {
									return (value / 1000000).toFixed(1) + 'tr';
								}
								return new Intl.NumberFormat('vi-VN').format(value);
							}
						},
						max: maxScale > 0 ? maxScale : undefined,
						grid: {
							color: 'rgba(0,0,0,0.1)'
						}
					},
					x: {
						title: {
							display: true,
							text: 'Năm'
						},
						grid: {
							display: false
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

