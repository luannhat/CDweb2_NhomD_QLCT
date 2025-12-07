<?php
// ===== VIEW THUẦN – KHÔNG LOGIC, KHÔNG SIDEBAR =====
?>

<header class="header">
	<h1>Thêm khoản thu</h1>
</header>

<main class="content">
	<div class="form-container">

		<?php if (!empty($_SESSION['message'])): ?>
			<div class="alert <?= $_SESSION['success'] ? 'alert-success' : 'alert-error' ?>">
				<?= $_SESSION['message'] ?>
			</div>
			<?php unset($_SESSION['message'], $_SESSION['success']); ?>
		<?php endif; ?>

		<form method="POST" action="index.php?controller=khoanthu&action=add">

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
				<label for="ngaygiaodich">Ngày <span class="required">*</span></label>
				<input type="date" id="ngaygiaodich" name="ngaygiaodich"
					   value="<?= htmlspecialchars($_POST['ngaygiaodich'] ?? date('Y-m-d')) ?>">
			</div>

			<div class="form-row">
				<label for="madmthunhap">Loại <span class="required">*</span></label>
				<select id="madmthunhap" name="madmthunhap" required>
					<option value="">-- Chọn nguồn thu --</option>
					<?php foreach ($categories as $category): ?>
						<option value="<?= $category['madmthunhap'] ?>"
							<?= (($_POST['madmthunhap'] ?? '') == $category['madmthunhap']) ? 'selected' : '' ?>>
							<?= htmlspecialchars($category['tendanhmuc']) ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="btn-group">
				<a href="index.php?controller=khoanthu" class="btn btn-secondary">Hủy</a>
				<button type="submit" class="btn btn-primary">Lưu</button>
			</div>

		</form>
	</div>
</main>
