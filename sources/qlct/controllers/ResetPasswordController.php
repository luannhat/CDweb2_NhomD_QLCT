<?php
// Tạm thời comment out database để demo
// require_once __DIR__ . '/../configs/database.php';
// require_once __DIR__ . '/../models/UserModel.php';

class ResetPasswordController {
    // private $userModel;
    
    public function __construct() {
        // $this->userModel = new UserModel();
    }
    
    public function handleResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../views/Reset_password.php?error=Phương thức không hợp lệ');
            exit;
        }
        
        // Lấy dữ liệu từ form
        $token = $_POST['token'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate dữ liệu
        $errors = $this->validateResetData($token, $newPassword, $confirmPassword);
        
        if (!empty($errors)) {
            header('Location: ../views/Reset_password.php?token=' . urlencode($token) . '&error=' . urlencode(implode(', ', $errors)));
            exit;
        }
        
        // Demo version - không cần database
        try {
            // Hiển thị thông báo thành công trước khi redirect
            header('Location: ../views/Reset_password.php?token=' . urlencode($token) . '&success=Lưu mật khẩu mới thành công');
            exit;
            
        } catch (Exception $e) {
            error_log("Reset password error: " . $e->getMessage());
            header('Location: ../views/Reset_password.php?token=' . urlencode($token) . '&error=Có lỗi xảy ra, vui lòng thử lại');
            exit;
        }
    }
    
    private function validateResetData($token, $newPassword, $confirmPassword) {
        $errors = [];
        
        // Validate token
        if (empty($token)) {
            $errors[] = 'Token không được để trống';
        }
        
        // Validate password
        if (empty($newPassword)) {
            $errors[] = 'Mật khẩu mới không được để trống';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'Mật khẩu phải có ít nhất 8 ký tự';
        }
        
        // Validate confirm password
        if (empty($confirmPassword)) {
            $errors[] = 'Xác nhận mật khẩu không được để trống';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'Mật khẩu xác nhận không khớp';
        }
        
        return $errors;
    }
}

// Xử lý request
$controller = new ResetPasswordController();
$controller->handleResetPassword();
?>

