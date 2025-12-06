<?php
session_start();
$randomCaptcha = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5);
$_SESSION['captcha_code'] = $randomCaptcha;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quên Mật Khẩu</title>
    <link rel="stylesheet" href="../../../public/css/forgot_password.css">
</head>

<body>

    <div class="container">
        <div class="form-box">

            <h2>QUÊN MẬT KHẨU</h2>

            <!-- CHỈ HIỆN LỖI NẾU CÓ THAM SỐ error -->
            <?php if (!empty($_GET['error'])): ?>
            <div class="error-message" style="color:red; font-weight:bold; margin-bottom:12px;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
            <?php endif; ?>

            <form action="../controllers/ForgotPasswordController.php" method="POST">

                <label for="email">Nhập địa chỉ email của bạn</label>
                <input type="email" name="email" id="email" placeholder="email@example.com" required>

                <div class="captcha-box">
                    <div class="captcha-code" id="captchaText">
                        <?= htmlspecialchars($randomCaptcha) ?>
                    </div>

                    <button type="button" class="captcha-btn" onclick="refreshCaptcha()">Làm mới</button>
                </div>

                <div class="captcha-input">
                    <input type="text" name="captcha_input" placeholder="Nhập mã captcha" required>
                </div>

                <button type="submit" class="submit-btn">GỬI</button>

            </form>
        </div>
    </div>

    <script>
    function refreshCaptcha() {
        location.reload();
    }
    </script>

</body>

</html>