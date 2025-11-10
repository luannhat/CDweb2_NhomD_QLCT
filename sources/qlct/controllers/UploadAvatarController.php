<?php
require_once 'models/UploadAvatarModel.php';

class UploadAvatarController {
    private $model;

    public function __construct() {
        $this->model = new UploadAvatarModel();
    }

    // ✅ Hiển thị trang xem hoặc đổi avatar
    public function index() {
        session_start();

        $isLoggedIn = isset($_SESSION['makh']);
        $makh = $_SESSION['makh'] ?? null;

        if ($isLoggedIn) {
            $hinhanh = $this->model->getAvatarPath($makh);
        } else {
            $hinhanh = "public/Uploads/default-avatar.png"; // ảnh mặc định
        }

        include 'views/profile.php';
    }

    // ✅ Xử lý khi nhấn "Lưu Avatar"
    public function uploadAvatarSubmit() {
        session_start();

        if (!isset($_SESSION['makh'])) {
            echo "<script>
                    alert('⚠️ Bạn cần đăng nhập để thay đổi ảnh đại diện!');
                    window.location='index.php?action=login';
                  </script>";
            exit;
        }

        $makh = $_SESSION['makh'];

        // Kiểm tra file upload
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            echo "<script>alert('❌ File tải lên không hợp lệ!'); history.back();</script>";
            exit;
        }

        $uploadDir = "public/Uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmp = $_FILES['avatar']['tmp_name'];
        $fileName = uniqid("avatar_") . "_" . basename($_FILES['avatar']['name']);
        $targetPath = $uploadDir . $fileName;

        $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($fileType, $allowedTypes)) {
            echo "<script>alert('❌ Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF, WEBP)!'); history.back();</script>";
            exit;
        }

        // ✅ Upload file lên server
        if (move_uploaded_file($fileTmp, $targetPath)) {
            // ✅ Cập nhật DB
            $this->model->updateAvatar($makh, $targetPath);
            echo "<script>alert('✅ Cập nhật Avatar thành công!'); window.location='index.php?action=upload_avatar';</script>";
        } else {
            echo "<script>alert('❌ Lỗi khi tải ảnh lên.'); history.back();</script>";
        }
    }
}
