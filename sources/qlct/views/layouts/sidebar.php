<aside class="sidebar">
    <div class="logo">
        <div class="burger" aria-hidden="true"></div>
        <strong style="color:#222">Menu</strong>
    </div>

    <nav class="menu" aria-label="Main menu">
        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Trang chủ</a>
        <a href="khoanthu.php" class="<?= basename($_SERVER['PHP_SELF']) == 'khoanthu.php' ? 'active' : '' ?>">Khoản thu</a>
        <a href="khoanchi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'khoanchi.php' ? 'active' : '' ?>">Khoản chi</a>
        <a href="danhmuc.php" class="<?= basename($_SERVER['PHP_SELF']) == 'danhmuc.php' ? 'active' : '' ?>">Danh mục</a>
        <a href="ngansach.php" class="<?= basename($_SERVER['PHP_SELF']) == 'ngansach.php' ? 'active' : '' ?>">Ngân sách</a>
        <a href="baocao.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'baocao.php' || (isset($_GET['controller']) && $_GET['controller'] == 'statistical' && isset($_GET['action']) && $_GET['action'] == 'annualStatistics')) ? 'active' : '' ?>">Báo cáo</a>
        <a href="caidat.php" class="<?= basename($_SERVER['PHP_SELF']) == 'caidat.php' ? 'active' : '' ?>">Cài đặt</a>
    </nav>
</aside>
