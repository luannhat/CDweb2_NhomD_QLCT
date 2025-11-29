

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
			<h1>Trang chủ</h1>

			<div class="search" role="search">
				<form method="get" action="home.php">
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
	</div>
</div>

<script>

</script>

</body>
</html>

