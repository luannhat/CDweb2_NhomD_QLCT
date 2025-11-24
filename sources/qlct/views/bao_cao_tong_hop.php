<?php
session_start();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Báo cáo tổng hợp nhiều tháng</title>

	<!-- Font Awesome 6 -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

	<!-- Chart.js -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<!-- CSS riêng của trang -->
	<?php $cssVersion = @filemtime(__DIR__ . '/../public/css/baocao.css') ?: time(); ?>
	<link rel="stylesheet" href="../public/css/baocao.css?v=<?php echo $cssVersion; ?>" />
	<link rel="stylesheet" href="../public/css/khoanchi.css?v=<?php echo $cssVersion; ?>" />
</head>
<body>

<div class="app">
	<!-- Sidebar -->
	<?php include __DIR__ . '/layouts/sidebar.php'; ?>

	<!-- Main -->
	<div class="main">
		<header class="header">
			<h1>Báo cáo tổng hợp nhiều tháng</h1>

			<div class="search" role="search">
				<form method="get" action="bieu_do_duong.php">
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
            <?php 
            require_once "../models/KhoanthuModel.php";
            require_once "../models/KhoanchiModel.php";

            // Lấy makh từ session hoặc dùng giá trị mặc định
            $makh = intval($_SESSION['id'] ?? 1);
            
            // Nhận dữ liệu filter
            $fromMonth = intval($_GET['from'] ?? 1);
            $toMonth   = intval($_GET['to'] ?? 6);
            $year      = intval($_GET['year'] ?? date("Y"));

            // Model
            $thuNhapModel = new KhoanthuModel();
            $chiModel = new KhoanchiModel();

            $data = [];

            for ($m = $fromMonth; $m <= $toMonth; $m++) {
                $tongThu = $thuNhapModel->getTongThuTheoThang($makh, $m, $year);
                $tongChi = $chiModel->getTongChiTheoThang($makh, $m, $year);

                $data[] = [
                    'thang' => sprintf("%02d/%d", $m, $year),
                    'thu'   => $tongThu,
                    'chi'   => $tongChi,
                    'sodu'  => $tongThu - $tongChi
                ];
            }
            ?>

            <form method="GET" class="filter-row">
                <label>Từ:</label>
                <select name="from" class="select-input">
                    <?php for($i=1;$i<=12;$i++): ?>
                    <option value="<?= $i ?>" <?= ($i==$fromMonth?'selected':'') ?>>Tháng <?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <label>Đến:</label>
                <select name="to" class="select-input">
                    <?php for($i=1;$i<=12;$i++): ?>
                    <option value="<?= $i ?>" <?= ($i==$toMonth?'selected':'') ?>>Tháng <?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <label>Năm:</label>
                <select name="year" class="select-input">
                    <?php for($y=2020;$y<=2030;$y++): ?>
                    <option value="<?= $y ?>" <?= ($y==$year?'selected':'') ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>

                <div class="export-pdf-dropdown">
                    <button type="button" class="export-pdf-btn" id="exportPdfBtn">
                        Xuất PDF <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="export-pdf-menu" id="exportPdfMenu" style="display: none;">
                        <a href="#" class="export-option" data-format="pdf">PDF</a>
                        <a href="#" class="export-option" data-format="excel">Excel</a>
                        <a href="#" class="export-option" data-format="csv">CSV</a>
                    </div>
                </div>
            </form>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>Tháng</th>
                        <th>Tổng thu nhập</th>
                        <th>Tổng chi tiêu</th>
                        <th>Số dư</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data as $row): ?>
                    <tr>
                        <td><?= $row['thang'] ?></td>
                        <td><?= number_format($row['thu'], 0, ',', '.') ?></td>
                        <td><?= number_format($row['chi'], 0, ',', '.') ?></td>
                        <td><?= number_format($row['sodu'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="export-btn-wrapper">
                <button class="btn-export">Xuất</button>
            </div>

        </main>

	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Account dropdown
    var account = document.getElementById('accountDropdown');
    if (account) {
        var btn = account.querySelector('.account-btn');
        var menu = account.querySelector('.dropdown-menu');

        function closeAccountMenu() {
            menu.style.display = 'none';
            btn.setAttribute('aria-expanded', 'false');
            menu.setAttribute('aria-hidden', 'true');
        }

        function toggleAccountMenu() {
            var isOpen = menu.style.display === 'block';
            if (isOpen) closeAccountMenu();
            else {
                menu.style.display = 'block';
                btn.setAttribute('aria-expanded', 'true');
                menu.setAttribute('aria-hidden', 'false');
            }
        }

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleAccountMenu();
        });

        document.addEventListener('click', function(e) {
            if (!account.contains(e.target)) {
                closeAccountMenu();
            }
        });
    }

    // Export PDF dropdown
    var exportPdfBtn = document.getElementById('exportPdfBtn');
    var exportPdfMenu = document.getElementById('exportPdfMenu');
    
    if (exportPdfBtn && exportPdfMenu) {
        function closeExportMenu() {
            exportPdfMenu.style.display = 'none';
        }

        function toggleExportMenu() {
            var isOpen = exportPdfMenu.style.display === 'block';
            if (isOpen) {
                closeExportMenu();
            } else {
                exportPdfMenu.style.display = 'block';
            }
        }

        exportPdfBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleExportMenu();
        });

        document.addEventListener('click', function(e) {
            if (!exportPdfBtn.contains(e.target) && !exportPdfMenu.contains(e.target)) {
                closeExportMenu();
            }
        });

        // Handle export options
        var exportOptions = document.querySelectorAll('.export-option');
        exportOptions.forEach(function(option) {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                var format = this.getAttribute('data-format');
                // TODO: Implement export functionality
                console.log('Export as:', format);
                closeExportMenu();
            });
        });
    }

    // Auto submit form when filter changes
    var filterForm = document.querySelector('.filter-row');
    if (filterForm) {
        var selects = filterForm.querySelectorAll('select');
        selects.forEach(function(select) {
            select.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    }

    // Export button at bottom
    var btnExport = document.querySelector('.btn-export');
    if (btnExport) {
        btnExport.addEventListener('click', function() {
            // TODO: Implement export functionality
            console.log('Export button clicked');
        });
    }
});
</script>

</body>
</html>

