<?php
// ===== VIEW THUẦN – KHÔNG LOGIC, KHÔNG SIDEBAR =====
?>

<header class="header">
	<h1>Thêm khoản chi</h1>
</header>

<main class="content">
	<div class="form-container">

		<?php if (!empty($_SESSION['message'])): ?>
			<div class="alert <?= $_SESSION['success'] ? 'alert-success' : 'alert-error' ?>">
				<?= $_SESSION['message'] ?>
			</div>
			<?php unset($_SESSION['message'], $_SESSION['success']); ?>
		<?php endif; ?>

		<form method="POST" action="index.php?controller=khoanchi&action=add">

			<div class="form-row">
				<label for="noidung">Nội dung <span class="required">*</span></label>
				<input type="text" id="noidung" name="noidung" required
					   value="<?= htmlspecialchars($_POST['noidung'] ?? '') ?>">
			</div>

			<div class="form-row">
				<label for="sotien">Số tiền <span class="required">*</span></label>
				<input type="number" id="sotien" name="sotien" required min="1"
					   value="<?= htmlspecialchars($_POST['sotien'] ?? '') ?>">
			</div>

			<div class="form-row">
				<label for="ngaychitieu">Ngày <span class="required">*</span></label>
				<input type="date" id="ngaychitieu" name="ngaychitieu"
					   value="<?= htmlspecialchars($_POST['ngaychitieu'] ?? date('Y-m-d')) ?>">
			</div>

			<div class="form-row">
				<label for="madmchitieu">Danh mục <span class="required">*</span></label>
				<select id="madmchitieu" name="madmchitieu" required>
					<option value="">-- Chọn danh mục chi --</option>
					<?php foreach ($categories as $category): ?>
						<option value="<?= $category['madmchitieu'] ?>"
							<?= (($_POST['madmchitieu'] ?? '') == $category['madmchitieu']) ? 'selected' : '' ?>>
							<?= htmlspecialchars($category['tendanhmuc']) ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="btn-group">
				<a href="index.php?controller=khoanchi" class="btn btn-secondary">Hủy</a>
				<button type="submit" class="btn btn-primary">Lưu</button>
			</div>

		</form>
	</div>
</main>
