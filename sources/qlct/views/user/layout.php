<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý chi tiêu</title>
    <link rel="stylesheet" href="/public/css/user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

    <header>
        <div class="logo">QuanLyChiTieu</div>

        <nav>
            <a href="?controller=user&action=home" 
               class="<?= ($currentPage == 'home' ? 'active' : '') ?>">Trang chủ</a>

            <a href="?controller=user&action=income" 
               class="<?= ($currentPage == 'income' ? 'active' : '') ?>">Khoản thu</a>

            <a href="?controller=user&action=expense" 
               class="<?= ($currentPage == 'expense' ? 'active' : '') ?>">Khoản chi</a>

            <a href="?controller=user&action=budget" 
               class="<?= ($currentPage == 'budget' ? 'active' : '') ?>">Ngân sách</a>

            <a href="?controller=user&action=stats" 
               class="<?= ($currentPage == 'stats' ? 'active' : '') ?>">Thống kê / Báo cáo</a>
        </nav>

        <div class="auth-btns">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="?controller=auth&action=logout" class="btn-logout">Đăng xuất</a>
            <?php else: ?>
                <a href="?controller=auth&action=login" class="btn-primary">Đăng nhập</a>
                <a href="?controller=auth&action=register" class="btn-secondary">Đăng ký</a>
            <?php endif; ?>
        </div>

    </header>

    <div class="content">
        <?php echo $content; ?>
    </div>

    <footer>
        © 2025 Quản lý chi tiêu cá nhân
    </footer>

</body>
</html>
