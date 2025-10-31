<?php
// Tạm thời comment out database để demo
// require_once __DIR__ . '/../configs/database.php';
// require_once __DIR__ . '/../models/UserModel.php';

class ForgotPasswordController {
    // private $userModel;
    
    public function __construct() {
        // $this->userModel = new UserModel();
    }
    
    public function handleForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
           header('Location: ../views/ForgotPassword.php?error=Phương thức không hợp lệ');

            exit;
        }
        
        // Lấy dữ liệu từ form
        $email = $_POST['email'] ?? '';
        $captchaInput = $_POST['captcha_input'] ?? '';
        
        // Validate dữ liệu
        $errors = $this->validateForgotData($email, $captchaInput);
        
        if (!empty($errors)) {
           header('Location: ../views/ForgotPassword.php?error=' . urlencode(implode(', ', $errors)));

            exit;
        }
        
        // Demo version - không cần database
        try {
            // Tạo token giả để demo
            $token = bin2hex(random_bytes(32));
            
            // Luôn redirect đến trang reset password
            header('Location: ../views/Reset_password.php?token=' . urlencode($token));
            exit;
            
        } catch (Exception $e) {
            error_log("Forgot password error: " . $e->getMessage());
           header('Location: ../views/ForgotPassword.php?error=Có lỗi xảy ra, vui lòng thử lại');

            exit;
        }
    }
    
    private function validateForgotData($email, $captchaInput) {
        $errors = [];
        
        // Validate email
        if (empty($email)) {
            $errors[] = 'Email không được để trống';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }
        
        // Validate captcha (đơn giản - trong thực tế nên dùng session)
        if (empty($captchaInput)) {
            $errors[] = 'Mã captcha không được để trống';
        }
        
        return $errors;
    }
}

// Xử lý request
$controller = new ForgotPasswordController();
$controller->handleForgotPassword();
?>