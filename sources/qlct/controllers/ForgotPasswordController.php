<?php
session_start();

class ForgotPasswordController {

    public function handleForgotPassword() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../views/Forgot_password.php?error=Phương thức không hợp lệ');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $captchaInput = $_POST['captcha_input'] ?? '';

        // Validate
        $errors = $this->validateForgotData($email, $captchaInput);

        if (!empty($errors)) {
            // Quay lại trang forgot_password kèm thông báo lỗi
            header('Location: ../views/Forgot_password.php?error=' . urlencode($errors[0]));
            exit;
        }

        // Captcha đúng → sang trang Reset
        $token = bin2hex(random_bytes(32));

        header('Location: ../views/Reset_password.php?token=' . urlencode($token));
        exit;
    }


    private function validateForgotData($email, $captchaInput) {
        $errors = [];

        if (empty($email)) {
            $errors[] = 'Email không được để trống';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        // check captcha
        if (empty($captchaInput)) {
            $errors[] = 'Mã captcha không được để trống';
        } else {
            if (!isset($_SESSION['captcha_code'])) {
                $errors[] = 'Captcha bị lỗi, hãy tải lại trang';
            } elseif (strtolower($captchaInput) !== strtolower($_SESSION['captcha_code'])) {
                $errors[] = 'Captcha sai, vui lòng nhập lại';
            }
        }

        return $errors;
    }
}

$controller = new ForgotPasswordController();
$controller->handleForgotPassword();