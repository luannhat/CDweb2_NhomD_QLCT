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
            <form action="../controllers/ForgotPasswordController.php" method="POST">
                <label for="email">Nhập địa chỉ email của bạn</label>
                <input type="email" name="email" id="email" placeholder="email@example.com" required>

                <div class="captcha-box">
                    <div class="captcha-code" id="captchaText">7gH5K</div>
                    <button type="button" class="captcha-btn" onclick="refreshCaptcha()">Captcha</button>
                </div>

                <div class="captcha-input">
                    <input type="text" name="captcha_input" placeholder="Nhập mã captcha" required>
                    <button type="button" class="refresh-btn" onclick="refreshCaptcha()">Làm mới mã Captcha</button>
                </div>

                <button type="submit" class="submit-btn">GỬI LIÊN KẾT ĐẶT LẠI</button>
            </form>
        </div>
    </div>

    <script>
        // Sinh ngẫu nhiên mã captcha
        function generateCaptcha() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let code = '';
            for (let i = 0; i < 5; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return code;
        }

        function refreshCaptcha() {
            document.getElementById('captchaText').textContent = generateCaptcha();
        }

        // Khởi tạo captcha khi tải trang
        window.onload = function () {
            refreshCaptcha();
        }
    </script>
</body>

</html>