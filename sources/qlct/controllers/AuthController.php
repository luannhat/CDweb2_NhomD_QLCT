<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {

    public function login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $message = "";
    $userModel = new UserModel();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $user = $userModel->login($email, $password);

        if ($user) {

            // Lưu session đầy đủ
            $_SESSION['user'] = [
                'id'     => $user['makh'],
                'name'   => $user['tenkh'],
                'avatar' => $user['hinhanh'] ?? null,
                'email'  => $user['email'],
                'quyen'  => $user['quyen'] ?? 'user'   // nếu DB chưa có cột quyen thì mặc định user
            ];

            $_SESSION['makh'] = $user['makh'];

            // ⛔ Nếu là admin → chuyển vào trang admin
            if ($_SESSION['user']['quyen'] === 'admin' || 
                $_SESSION['user']['email'] === 'admin@gmail.com') 
            {
                header("Location: index.php?controller=admin&action=home");
                exit();
            }

            // ✔ User bình thường → vào dashboard
            header("Location: index.php?controller=user&action=dashboard");
            exit();
        } 
        else {
            $message = "Email hoặc mật khẩu không đúng!";
        }
    }

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


    // Form thay avatar
    public function changeAvatar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        include __DIR__ . '/../views/user/change_avatar.php';
    }

    // Lưu avatar
    public function updateAvatar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {

            $file = $_FILES['avatar'];
            $allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];

            // Validate
            if ($file['error'] !== 0) {
                die("Lỗi upload ảnh!");
            }

            if (!in_array($file['type'], $allowed)) {
                die("Chỉ hỗ trợ PNG, JPG, JPEG, WEBP");
            }

            // Tạo thư mục nếu chưa có
            $uploadDir = "uploads/avatar/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Tên file
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $avatarName = "user_" . $_SESSION['user']['id'] . "_avatar." . $ext;
            $avatarPath = $uploadDir . $avatarName;

            // Di chuyển file
            move_uploaded_file($file['tmp_name'], $avatarPath);

            // Cập nhật DB
            $userModel = new UserModel();
            $userModel->updateAvatar($_SESSION['user']['id'], $avatarPath);

            // Cập nhật lại session
            $_SESSION['user']['avatar'] = $avatarPath;

            //mess thông báo
            $_SESSION['success'] = "Cập nhật ảnh đại diện thành công!";

            header("Location: index.php?controller=user&action=dashboard");
            exit();
        }
    }
}
