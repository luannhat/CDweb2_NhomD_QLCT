<?php
require_once __DIR__ . '/../../controllers/KhoanthuController.php';
$controller = new KhoanthuController();
$result = $controller->index();

$khoanthus   = $result['khoanthus'];
$page        = $result['page'];
$totalPages  = $result['totalPages'];

// Để highlight menu Khoản thu
$currentPage = 'income';

// BẮT ĐẦU GOM CONTENT
ob_start();
?>

<div class="app" style = "grid-template-columns: none;">

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
						onclick="location.href='index.php?controller=khoanthu&action=create'">
					Thêm khoản thu
				</button>
				<button class="btn danger" id="deleteBtn" disabled title="Sắp có">Xóa</button>
			</div>

			<section class="card" aria-labelledby="tableTitle">
				<table id="incomeTable" aria-describedby="tableTitle">
					<thead>
						<tr>
							<th class="col-date">Ngày</th>
							<th class="col-content">Nội dung</th>
							<th class="col-type">Danh mục</th>
							<th class="col-money">Số tiền</th>
						</tr>
					</thead>
					<tbody id="tbody">
						<?php if (!empty($khoanthus)): ?>
							<?php foreach ($khoanthus as $row): ?>
								<tr data-mathunhap="<?php echo htmlspecialchars($row['mathunhap']); ?>">
									<td><?php echo htmlspecialchars($row['ngaythunhap']); ?></td>
									<td><?php echo htmlspecialchars($row['noidung']); ?></td>
									<td><?php echo htmlspecialchars($row['tendanhmuc']); ?></td>
									<td><?php echo number_format($row['sotien']); ?> đ</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr><td colspan="4" style="text-align:center; padding:20px; color:#666;">Chưa có khoản thu nào</td></tr>
						<?php endif; ?>
					</tbody>
				</table>

				<!-- PHÂN TRANG -->
				<?php if ($totalPages > 1): ?>
				<div class="pagination">
					<?php if ($page > 1): ?>
						<a href="?page=<?php echo $page - 1; ?>" class="circle">&lt;</a>
					<?php else: ?>
						<span class="circle disabled">&lt;</span>
					<?php endif; ?>

					<div class="page-num"><?= $page ?>/<?= $totalPages ?></div>

					<?php if ($page < $totalPages): ?>
						<a href="?page=<?php echo $page + 1; ?>" class="circle">&gt;</a>
					<?php else: ?>
						<span class="circle disabled">&gt;</span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</section>
		</main>
	</div>
</div>

<script>
<?= file_get_contents(__DIR__ . '/khoanthu.js') ?? '' ?>
</script>

<?php
// KẾT THÚC CONTENT
$content = ob_get_clean();

// CSS riêng của trang này
$cssFiles = [
    '/public/css/khoanchi.css?v=' . time()
];

// GỌI LAYOUT
include __DIR__ . '/layout.php';
