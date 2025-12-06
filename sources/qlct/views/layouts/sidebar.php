<?php
// Xác định base path: nếu đang ở trong views/ thì cần quay về root
$currentPath = $_SERVER['PHP_SELF'];
$isInViews = strpos($currentPath, '/views/') !== false || strpos($currentPath, '\\views\\') !== false;
$basePath = $isInViews ? '../' : '';
?>
<aside class="sidebar">
    <div class="logo">
        <div class="burger" aria-hidden="true"></div>
        <strong style="color:#222">Menu</strong>
    </div>

    <nav class="menu" aria-label="Main menu">
        <a href="<?= $basePath ?>index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' || basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : '' ?>">Trang chủ</a>
        <a href="<?= $basePath ?>views/khoanthu.php" class="<?= basename($_SERVER['PHP_SELF']) == 'khoanthu.php' ? 'active' : '' ?>">Khoản thu</a>
        <a href="<?= $basePath ?>views/khoanchi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'khoanchi.php' ? 'active' : '' ?>">Khoản chi</a>
        <a href="<?= $basePath ?>views/catagories.php" class="<?= basename($_SERVER['PHP_SELF']) == 'danhmuc.php' || basename($_SERVER['PHP_SELF']) == 'catagories.php' ? 'active' : '' ?>">Danh mục</a>
        <a href="<?= $basePath ?>views/ngansach.php" class="<?= basename($_SERVER['PHP_SELF']) == 'ngansach.php' ? 'active' : '' ?>">Ngân sách</a>
        <?php
            $reportPages = ['baocao.php', 'bieu_do_duong.php', 'bieu_do_cot.php'];
            $isReportActive = in_array(basename($_SERVER['PHP_SELF']), $reportPages, true)
                || (isset($_GET['controller']) && $_GET['controller'] == 'statistical' && isset($_GET['action']) && $_GET['action'] == 'annualStatistics');
        ?>
        <a href="<?= $basePath ?>views/baocao.php" class="<?= $isReportActive ? 'active' : '' ?>">Báo cáo</a>
        <a href="<?= $basePath ?>views/profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'caidat.php' || basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">Cài đặt</a>
    </nav>
</aside>
