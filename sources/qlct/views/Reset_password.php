<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Đặt Lại Mật Khẩu</title>
  <link rel="stylesheet" href="../public/css/reset_password.css">
</head>

<body>
  <div class="header">ĐẶT LẠI MẬT KHẨU</div>

  <div class="container">
    <form action="../controllers/ResetPasswordController.php" method="POST">
      <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">
      
      <div class="form-group">
        <label for="new_password">Mật khẩu mới</label>
        <input type="password" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới" required>
      </div>

      <div class="form-group">
        <label for="confirm_password">Xác nhận mật khẩu mới</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
      </div>

      <button type="submit" class="save-btn">LƯU MẬT KHẨU MỚI</button>

      <p class="msg">
        <?php if (isset($_GET['error'])) echo htmlspecialchars($_GET['error']); ?>
      </p>
    </form>
  </div>

  <script>
    // Validate form trước khi submit
    document.querySelector('form').addEventListener('submit', function(e) {
      const password = document.getElementById('new_password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      
      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Mật khẩu không khớp!');
        return false;
      }
      
      if (password.length < 8) {
        e.preventDefault();
        alert('Mật khẩu phải có ít nhất 8 ký tự!');
        return false;
      }
    });
  </script>
</body>

</html>