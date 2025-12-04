<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {

    public function login() {
        // Khởi tạo session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $message = "";
        $userModel = new UserModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            // Gọi phương thức login trong UserModel
            $user = $userModel->login($email, $password);

            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user['makh'],
                    'name' => $user['tenkh']
                ];
                // Chuyển hướng tới dashboard
                header("Location: index.php?controller=user&action=dashboard");
                exit();
            } else {
                $message = "Email hoặc mật khẩu không đúng!";
            }
        }

        // Load view login (không require model ở đây)
        include __DIR__ . '/../views/user/login.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header("Location: index.php?controller=user&action=home");
        exit();
    }
}
