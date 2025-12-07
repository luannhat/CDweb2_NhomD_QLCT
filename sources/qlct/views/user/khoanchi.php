<?php
require_once __DIR__ . '/../../controllers/KhoanchiController.php';
$controller = new KhoanchiController();
$result = $controller->index();

// Để highlight menu Khoản thu
$currentPage = 'expense';

// BẮT ĐẦU GOM CONTENT
ob_start();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Khoản chi - Quản lý chi tiêu</title>
	<link rel="icon" type="image/svg+xml" href="../public/favicon.svg">
	<link rel="alternate icon" href="../public/favicon.svg">

	<!-- Font Awesome 6 -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

	<!-- CSS riêng của trang -->
	<?php $cssVersion = @filemtime(__DIR__ . '/../public/css/khoanchi.css') ?: time(); ?>
	<link rel="stylesheet" href="../public/css/khoanchi.css?v=<?php echo $cssVersion; ?>" />
</head>

<body>

	<div class="app" style="grid-template-columns: none;">

		<!-- Main -->
		<div class="main">
			<header class="header">
				<h1>Khoản chi</h1>

				<div class="search" role="search">
					<form method="get" action="khoanchi.php">
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
					<button class="btn primary"
						onclick="location.href='index.php?controller=khoanchi&action=create'">
						Thêm khoản chi
					</button>
					<button class="btn primary" id="editBtn">
						Sửa khoản chi tiêu
					</button>

					<button class="btn danger" id="deleteBtn" disabled>Xóa</button>
				</div>

				<section class="card" aria-labelledby="tableTitle">
					<table id="expenseTable" aria-describedby="tableTitle">
						<thead>
							<tr>
								<th class="col-date">Ngày</th>
								<th class="col-content">Nội dung</th>
								<th class="col-type">Danh mục</th>
								<th class="col-money">Số tiền</th>
							</tr>
						</thead>
						<tbody id="tbody">
							<?php
							require_once __DIR__ . '/../../controllers/KhoanchiController.php';
							$controller = new KhoanchiController();
							$data = $controller->index(); // trả về ['khoanchis', 'page', 'totalPages']

							$khoanchis = $data['khoanchis'];
							$currentPage = $data['page'];
							$totalPages = $data['totalPages'];

							if (!empty($khoanchis)) {
								foreach ($khoanchis as $row) {
									$ngay = htmlspecialchars($row['ngaychitieu']);
									$noidung = htmlspecialchars($row['noidung']);
									$tendanhmuc = htmlspecialchars($row['tendanhmuc']);
									$sotien = number_format($row['sotien'], 0, ',', '.') . ' VNĐ';

									$machitieu = $row['machitieu'];
									echo "<tr class='row-selectable' data-machitieu='{$machitieu}'>
										<td>{$ngay}</td>
										<td>{$noidung}</td>
										<td>{$tendanhmuc}</td>
										<td>{$sotien}</td>
									  </tr>";
								}
							} else {
								echo "<tr><td colspan='4' style='text-align:center; padding:20px; color:#666;'>Chưa có khoản chi nào</td></tr>";
							}
							?>
						</tbody>
					</table>

					<!-- PHÂN TRANG -->
					<?php if ($totalPages > 1): ?>
						<div class="pagination-container">
							<div class="pagination">
								<?php if ($currentPage > 1): ?>
									<a href="?page=<?php echo $currentPage - 1; ?>" class="circle" id="prevBtn">&lt;</a>
								<?php else: ?>
									<span class="circle disabled">&lt;</span>
								<?php endif; ?>

								<div class="page-num" id="pageInfo"><?php echo $currentPage; ?>/<?php echo $totalPages; ?></div>

								<?php if ($currentPage < $totalPages): ?>
									<a href="?page=<?php echo $currentPage + 1; ?>" class="circle" id="nextBtn">&gt;</a>
								<?php else: ?>
									<span class="circle disabled">&gt;</span>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</section>
			</main>
		</div>
	</div>

	<!-- Hộp thoại xác nhận xóa -->
	<div id="deleteConfirmDialog" class="confirm-dialog-overlay" style="display: none;">
		<div class="confirm-dialog">
			<div class="confirm-dialog-header">
				<i class="fa-solid fa-triangle-exclamation" style="color: #666; margin-right: 8px;"></i>
				<span>Xóa chi tiêu</span>
			</div>
			<div class="confirm-dialog-body">
				<p>Bạn có muốn xóa không?</p>
			</div>
			<div class="confirm-dialog-footer">
				<button class="confirm-btn cancel-btn">Hủy</button>
				<button class="confirm-btn confirm-btn-primary">Xác nhận</button>
			</div>
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


			/** ========================
		ROW SELECTION & DELETE
	========================= */
			const deleteBtn = document.getElementById('deleteBtn');
			const tbody = document.getElementById('tbody');

			// Cập nhật trạng thái nút Xóa
			function updateDeleteBtn() {
				if (!deleteBtn) return;
				const selectedRows = document.querySelectorAll('.row-selectable.selected');
				deleteBtn.disabled = selectedRows.length === 0;
			}

			// Sử dụng event delegation trên tbody để xử lý click
			if (tbody) {
				tbody.addEventListener('click', function(e) {
					// Tìm dòng cha (tr) gần nhất có class row-selectable
					const row = e.target.closest('.row-selectable');

					if (!row) return;

					// Ngăn chặn sự kiện khi click vào các phần tử con (nếu có)
					if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A') {
						return;
					}

					// Toggle class selected
					row.classList.toggle('selected');
					updateDeleteBtn();
				});
			}

			// Thêm cursor pointer cho tất cả các dòng có thể chọn
			document.querySelectorAll('.row-selectable').forEach(row => {
				row.style.cursor = 'pointer';
			});

			// Khởi tạo trạng thái nút Xóa
			updateDeleteBtn();

			// Xử lý xóa với hộp thoại xác nhận tùy chỉnh
			const deleteDialog = document.getElementById('deleteConfirmDialog');
			const cancelBtn = deleteDialog?.querySelector('.cancel-btn');
			const confirmBtn = deleteDialog?.querySelector('.confirm-btn-primary');
			let deleteCallback = null;

			// Hàm hiển thị hộp thoại xác nhận
			function showDeleteConfirm(callback) {
				if (!deleteDialog) return;
				deleteCallback = callback;
				deleteDialog.style.display = 'flex';
			}

			// Hàm ẩn hộp thoại xác nhận
			function hideDeleteConfirm() {
				if (!deleteDialog) return;
				deleteDialog.style.display = 'none';
				deleteCallback = null;
			}

			// Xử lý nút Hủy
			if (cancelBtn) {
				cancelBtn.addEventListener('click', function() {
					hideDeleteConfirm();
				});
			}

			// Xử lý nút Xác nhận
			if (confirmBtn) {
				confirmBtn.addEventListener('click', function() {
					if (deleteCallback) {
						deleteCallback();
					}
					hideDeleteConfirm();
				});
			}

			// Đóng hộp thoại khi click ra ngoài
			if (deleteDialog) {
				deleteDialog.addEventListener('click', function(e) {
					if (e.target === deleteDialog) {
						hideDeleteConfirm();
					}
				});
			}

			// Xử lý xóa
			if (deleteBtn) {
				deleteBtn.addEventListener('click', function() {
					const selectedRows = document.querySelectorAll('.row-selectable.selected');
					if (selectedRows.length === 0) return;

					// Hiển thị hộp thoại xác nhận
					showDeleteConfirm(function() {
						const ids = [...selectedRows].map(row => row.dataset.machitieu);

						const formData = new FormData();
						const makh = <?php echo isset($_SESSION['id']) ? intval($_SESSION['id']) : 1; ?>;
						formData.append("makh", makh);
						ids.forEach(id => formData.append("machitieu_list[]", id));

						fetch('../controllers/KhoanchiController.php?action=deleteMultiple', {
								method: 'POST',
								body: formData
							})
							.then(res => res.json())
							.then(result => {
								alert(result.message);
								if (result.success) location.reload();
							})
							.catch(() => alert("Có lỗi xảy ra khi xóa."));
					});
				});
			}
			const editBtn = document.getElementById('editBtn');

			if (editBtn) {
				editBtn.addEventListener('click', function() {
					const selectedRow = document.querySelector('.row-selectable.selected');

					if (!selectedRow) {
						alert("Vui lòng chọn một khoản chi trước khi sửa!");
						return;
					}

					const machitieu = selectedRow.dataset.machitieu;

					window.location.href = '/views/edit_expense.php?machitieu=' + machitieu;
				});
			}
		});
	</script>

</body>

</html>

<?php
// KẾT THÚC CONTENT
$content = ob_get_clean();

// CSS riêng của trang này
$cssFiles = [
	'/public/css/khoanchi.css?v=' . time()
];

// GỌI LAYOUT
include __DIR__ . '/layout.php';
