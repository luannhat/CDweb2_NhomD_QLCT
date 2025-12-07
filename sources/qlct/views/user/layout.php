<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý chi tiêu</title>
    <link rel="stylesheet" href="/public/css/user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php if (!empty($cssFiles)): ?>
        <?php foreach ($cssFiles as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>

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

            <div class="nav-dropdown">
                <input type="checkbox" id="stats-toggle" hidden>

                <label for="stats-toggle"
                    class="nav-link <?= ($currentPage == 'stats' ? 'active' : '') ?>">
                    Thống kê / Báo cáo <i class="fa-solid fa-caret-down"></i>
                </label>

                <div class="nav-dropdown-menu">
                    <a href="?controller=user&action=stats&view=month">Theo tháng</a>
                    <a href="?controller=user&action=stats&view=year">Theo năm</a>
                    <a href="?controller=user&action=stats&view=custom">Tùy chỉnh</a>
                </div>
            </div>

        </nav>


        <div class="auth-btns">
            <?php if (isset($_SESSION['user'])):
                $name = $_SESSION['user']['name'] ?? "User";
                $initial = strtoupper(substr($name, 0, 1));
                $avatar = $_SESSION['user']['avatar'] ?? null;
            ?>
                <div class="user-menu">
                    <input type="checkbox" id="menu-toggle" hidden>

                    <label for="menu-toggle" class="user-avatar">
                        <?php if ($avatar): ?>
                            <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar">
                        <?php else: ?>
                            <?= $initial ?>
                        <?php endif; ?>
                    </label>

                    <!-- DROPDOWN -->
                    <div class="dropdown-menu">

                        <!-- Header -->
                        <p class="dropdown-name"><?= htmlspecialchars($name) ?></p>
                        <div class="dropdown-divider"></div>

                        <!-- Hồ sơ của bạn -->
                        <div class="dropdown-item dropdown-profile">
                            Hồ sơ của bạn
                            <div class="profile-content">

                                <form action="index.php?controller=auth&action=updateAvatar"
                                    method="POST" enctype="multipart/form-data">
                                    <label class="avatar-change">
                                        Thay đổi ảnh đại diện
                                        <input type="file" name="avatar" hidden onchange="this.form.submit()">
                                    </label>
                                </form>

                            </div>
                        </div>

                        <!-- Cài đặt -->
                        <div class="dropdown-item">
                            Cài đặt
                        </div>

                        <div class="dropdown-divider"></div>

                        <!-- Đăng xuất -->
                        <a href="index.php?controller=auth&action=logout" class="dropdown-item logout">
                            Đăng xuất
                        </a>
                    </div>

                </div>
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