<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng Ký</title>
    <link rel="stylesheet" href="../../../public/css/styles.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Avatar">
        </div>

        <h2>ĐĂNG KÝ</h2>
        <form action="../controllers/RegisterController.php" method="POST" enctype="multipart/form-data">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="matkhau" placeholder="Mật Khẩu" required>

            <button type="submit" name="register">ĐĂNG KÝ</button>

            <p class="msg">
                <?php if (isset($_GET['error'])) echo htmlspecialchars($_GET['error']); ?>
                <?php if (isset($_GET['success'])) echo htmlspecialchars($_GET['success']); ?>
            </p>
        </form>
    </div>
</body>

</html>