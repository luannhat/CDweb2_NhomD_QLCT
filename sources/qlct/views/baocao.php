<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Báo cáo - Quản lý chi tiêu</title>
	<link rel="icon" type="image/svg+xml" href="../public/favicon.svg">
	<link rel="alternate icon" href="../public/favicon.svg">

	<!-- Font Awesome 6 -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

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
			<h1>Báo cáo</h1>

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

			<div class="report-container">
				<!-- Thống kê chi tiêu -->
				<div class="report-section">
					<h2 class="report-section-title">Thống kê chi tiêu</h2>
					<div class="report-buttons">
						<button class="report-btn" data-period="day">Trong ngày</button>
						<button class="report-btn" data-period="week">Trong tuần</button>
						<button class="report-btn" data-period="month">Trong tháng</button>
						<button class="report-btn" data-period="year">Trong năm</button>
					</div>
				</div>

				<!-- Xuất báo cáo -->
				<div class="report-section">
					<h2 class="report-section-title">Xuất báo cáo</h2>
					<div class="report-buttons">
						<button class="report-btn" data-export="pdf">PDF</button>
						<button class="report-btn" data-export="excel">Excel</button>
						<button class="report-btn" data-export="csv">CSV</button>
						<button class="report-btn" data-export="print">In trực tiếp</button>
					</div>
				</div>

				<!-- Báo cáo tổng hợp nhiều tháng -->
				<div class="report-section">
					<button class="report-btn" id="multiMonthBtn">Báo cáo tổng hợp nhiều tháng</button>
				</div>
			</div>

			<!-- Biểu đồ -->
			<div class="chart-selection-container">
				<div class="chart-grid">
					<button class="chart-btn" data-chart="line-compare">
						Biểu đồ đường so sánh thu nhập - chi tiêu
					</button>
					<button class="chart-btn" data-chart="trend">
						Biểu đồ xu hướng chi tiêu
					</button>
					<button class="chart-btn" data-chart="bar">
						Biểu đồ cột theo tháng/năm
					</button>
					<button class="chart-btn" data-chart="percentage">
						Biểu đồ phần trăm theo danh mục
					</button>
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

	// Xử lý các nút thống kê chi tiêu
	const statButtons = document.querySelectorAll('.report-btn[data-period]');
	statButtons.forEach(btn => {
		btn.addEventListener('click', function() {
			// Xóa active từ tất cả các nút trong cùng nhóm
			statButtons.forEach(b => b.classList.remove('active'));
			// Thêm active cho nút được click
			this.classList.add('active');
			
			const period = this.dataset.period;
			if (period === 'year') {
				// Chuyển đến trang thống kê theo năm
				window.location.href = 'thongke_nam.php';
			} else if (period === 'month') {
				// TODO: Xử lý thống kê theo tháng
				console.log('Selected period: month');
			} else if (period === 'week') {
				// TODO: Xử lý thống kê theo tuần
				console.log('Selected period: week');
			} else if (period === 'day') {
				// TODO: Xử lý thống kê theo ngày
				console.log('Selected period: day');
			}
		});
	});

	// Xử lý các nút xuất báo cáo
	const exportButtons = document.querySelectorAll('.report-btn[data-export]');
	exportButtons.forEach(btn => {
		btn.addEventListener('click', function() {
			// Xóa active từ tất cả các nút trong cùng nhóm
			exportButtons.forEach(b => b.classList.remove('active'));
			// Thêm active cho nút được click
			this.classList.add('active');
			
			const exportFormat = this.dataset.export;
			
			if (exportFormat === 'pdf') {
				// Chuyển sang màn hình báo cáo tổng hợp nhiều tháng với auto export PDF
				// Sử dụng giá trị mặc định: từ tháng 1 đến tháng 6, năm hiện tại
				const currentYear = new Date().getFullYear();
				window.location.href = 'bao_cao_tong_hop.php?from=1&to=6&year=' + currentYear + '&auto_export=pdf';
			} else if (exportFormat === 'excel') {
				// TODO: Xử lý xuất Excel
				console.log('Export format: Excel (chưa được triển khai)');
			} else if (exportFormat === 'csv') {
				// Chuyển sang màn hình báo cáo tổng hợp nhiều tháng với auto export CSV
				const currentYear = new Date().getFullYear();
				window.location.href = 'bao_cao_tong_hop.php?from=1&to=6&year=' + currentYear + '&auto_export=csv';
			} else if (exportFormat === 'print') {
				// In trực tiếp
				window.print();
			}
		});
	});

	// Xử lý nút báo cáo tổng hợp
	const multiMonthBtn = document.getElementById('multiMonthBtn');
	if (multiMonthBtn) {
		multiMonthBtn.addEventListener('click', function() {
			// TODO: Xử lý logic báo cáo tổng hợp nhiều tháng
			window.location.href = 'bao_cao_tong_hop.php';
		});
	}

	// Xử lý các nút biểu đồ
	const chartButtons = document.querySelectorAll('.chart-btn');
	chartButtons.forEach(btn => {
		btn.addEventListener('click', function() {
			// Xóa active từ tất cả các nút biểu đồ
			chartButtons.forEach(b => b.classList.remove('active'));
			// Thêm active cho nút được click
			this.classList.add('active');
			
			const chartType = this.dataset.chart;
			if (chartType === 'line-compare') {
				// Chuyển đến trang biểu đồ đường
				window.location.href = 'bieu_do_duong.php';
			} else if (chartType === 'trend') {
				// TODO: Xử lý biểu đồ xu hướng
				console.log('Selected chart: trend');
			} else if (chartType === 'bar') {
				window.location.href = 'bieu_do_cot.php';
			} else if (chartType === 'percentage') {
				// TODO: Xử lý biểu đồ phần trăm
				console.log('Selected chart: percentage');
			}
		});
	});
});
</script>

</body>
</html>

