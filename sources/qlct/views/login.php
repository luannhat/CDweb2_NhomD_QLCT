<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/UserModel.php';
session_start();

$message = "";
$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $user = $userModel->login($email, $password);

    if ($user) {
        $_SESSION["user_id"] = $user["makh"];
        $_SESSION["user_name"] = $user["tenkh"];
        header("Location: trangchao.php");
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
    <style>
        /* CSS giống như bạn viết */
        body { font-family: Arial; margin: 0; padding: 0; background-color: #fff; }
        .header { background-color: #a5f5bb; height: 100px; text-align: center; padding-top: 40px; }
        .header img { padding-top: 10px; width: 140px; height: 140px; }
        .form-section { width: 400px; margin: 40px auto; text-align: center; padding-top: 40px; }
        h2 { margin-top: 10px; color: #000; text-shadow: 1px 1px 3px #aaa; }
        label { display: block; text-align: left; font-weight: bold; margin-bottom: 5px; color: #555; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .actions { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 20px; }
        .actions a { color: #000; text-decoration: none; }
        .actions a:hover { text-decoration: underline; }
        .btn-login { background-color: #a5f5bb; border: none; padding: 10px 20px; width: 60%; border-radius: 4px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-login:hover { background-color: #82e59a; }
        .message { text-align: center; color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="/../public/images/user.png" alt="user icon">
    </div>

    <div class="form-section">
        <h2>Đăng Nhập</h2>
        
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        
        <form action="" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Nhập email..." required>

            <label for="password">Mật Khẩu</label>
            <input type="password" id="password" name="password" placeholder="Nhập mật khẩu..." required>

            <div class="actions">
                <a href="#">Chưa có tài khoản</a>
                <a href="#">Quên Mật Khẩu</a>
            </div>

            <button type="submit" class="btn-login">Đăng Nhập</button>
        </form>
    </div>
</body>
</html>
