<?php
require_once __DIR__ . '/../../models/BaseModel.php';
require_once __DIR__ . '/../../models/UserModel.php';


$message = "";
$userModel = new UserModel();

// Nếu người dùng đã login, chuyển thẳng sang dashboard
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $user = $userModel->login($email, $password);

    if ($user) {
        // Lưu session theo chuẩn dashboard.php
        $_SESSION['user'] = [
            'id' => $user['makh'],
            'name' => $user['tenkh']
        ];

        // Redirect sang dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Email hoặc mật khẩu không đúng!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        /* CSS đơn giản cho login form */
        body { font-family: Arial; background: #f7f9fc; margin:0; padding:0; }
        .login-container { max-width: 400px; margin: 80px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow:0 3px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        input[type=email], input[type=password] { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px; }
        .btn-login { width:100%; padding:10px; background:#1a73e8; color:#fff; border:none; border-radius:5px; cursor:pointer; font-weight:bold; }
        .btn-login:hover { background:#155bc1; }
        .message { color:red; text-align:center; margin-bottom:10px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Đăng Nhập</h2>

        <?php if ($message): ?>
            <p class="message"><?= $message ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit" class="btn-login">Đăng Nhập</button>
        </form>
    </div>
</body>
</html>
