<?php
session_start();

$id = $_SESSION['id'] ?? '';
$keyword = $_GET['keyword'] ?? '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý chi tiêu</title>
    <link rel="icon" type="image/svg+xml" href="../public/favicon.svg">
    <link rel="alternate icon" href="../public/favicon.svg">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f7f8fa;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        .navbar {
            background-color: #007bff;
            border-radius: 0;
            margin-bottom: 0;
        }
        .navbar .navbar-brand,
        .navbar-nav > li > a {
            color: #fff !important;
        }
        .navbar-nav > li > a:hover {
            background-color: #0056b3 !important;
        }
        .navbar-form .form-control {
            border-radius: 20px;
        }
        .alert {
            margin: 15px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="list_users.php">💰 Quản Lý Chi Tiêu</a>
            </div>

            <!-- Menu -->
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="form_user.php"><i class="fa fa-plus-circle"></i> Thêm người dùng</a></li>
                </ul>

                <!-- Search -->
                <form class="navbar-form navbar-left" method="get" action="">
                    <div class="form-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm người dùng..."
                               value="<?php echo htmlspecialchars($keyword); ?>">
                    </div>
                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                </form>

                <!-- Account -->
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
                            <i class="fa fa-user-circle-o"></i> Tài khoản <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if ($id): ?>
                                <li><a href="view_user.php?id=<?php echo $id; ?>">Trang cá nhân</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="logout.php">Đăng xuất</a></li>
                            <?php else: ?>
                                <li><a href="login.php">Đăng nhập</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hiển thị thông báo -->
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-warning text-center" role="alert">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS + jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
