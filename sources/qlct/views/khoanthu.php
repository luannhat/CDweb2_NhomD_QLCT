<?php
session_start();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Khoản thu - Quản lý chi tiêu</title>

	<!-- Font Awesome 6 -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

	<!-- CSS riêng của trang Khoản thu (tái sử dụng khoanchi.css) -->
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
			<h1>Khoản thu</h1>

			<div class="search" role="search">
				<form method="get" action="khoanthu.php">
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
			<div class="controls">
				<button class="btn primary" id="addBtn" onclick="window.location.href='them_khoanthu.php'">
					Thêm khoản thu
				</button>
				<button class="btn danger" id="deleteBtn" disabled title="Sắp có">Xóa</button>
			</div>

			<section class="card" aria-labelledby="tableTitle">
				<table id="incomeTable" aria-describedby="tableTitle">
					<thead>
						<tr>
							<th class="col-select">
								<input type="checkbox" id="selectAll" title="Chọn tất cả" />
							</th>
							<th class="col-date">Ngày</th>
							<th class="col-content">Nội dung</th>
							<th class="col-type">Loại</th>
							<th class="col-money">Số tiền</th>
						</tr>
					</thead>
					<tbody id="tbody">
						<?php
						require_once __DIR__ . '/../controllers/KhoanthuController.php';
						$controller = new KhoanthuController();
						$makh = 1;
						$khoanthus = $controller->index($makh);
						$currentPage;

						if (!empty($khoanthus)) {
    foreach ($khoanthus as $row) {
        echo "<tr data-mathunhap='{$row['mathunhap']}'>";
		echo "<td><input type='checkbox' class='row-select' /></td>";
        echo "<td>" . htmlspecialchars($row['ngaythunhap'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($row['noidung'] ?? '') . "</td>";
		echo "<td>" . htmlspecialchars($row['loai'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($row['sotien'] ?? '') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5' style='text-align:center; padding:20px; color:#666;'>Chưa có khoản thu nào</td></tr>";
}
						?>
					</tbody>
				</table>

				<!-- Phân trang
				<div class="pagination" style="margin-top:12px;">
					<?php if ($currentPage > 1): ?>
						<a href="?page=<?php echo $currentPage - 1; ?><?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?>" class="circle" id="prevBtn">&lt;</a>
					<?php else: ?>
						<span class="circle disabled">&lt;</span>
					<?php endif; ?>

					<div class="page-num" id="pageInfo"><?php echo $currentPage; ?>/<?php echo $totalPages; ?></div>

					<?php if ($currentPage < $totalPages): ?>
						<a href="?page=<?php echo $currentPage + 1; ?><?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?>" class="circle" id="nextBtn">&gt;</a>
					<?php else: ?>
						<span class="circle disabled">&gt;</span>
					<?php endif; ?>
				</div> -->
			</section>
		</main>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var account = document.getElementById('accountDropdown');
	if (!account) return;
	var btn = account.querySelector('.account-btn');
	var menu = account.querySelector('.dropdown-menu');

	function closeMenu() {
		menu.style.display = 'none';
		btn.setAttribute('aria-expanded', 'false');
		menu.setAttribute('aria-hidden', 'true');
	}

	function toggleMenu() {
		var isOpen = menu.style.display === 'block';
		if (isOpen) {
			closeMenu();
		} else {
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
});
</script>

</body>
</html>


