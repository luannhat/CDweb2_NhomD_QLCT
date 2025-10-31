<?php
session_start();
require_once '../models/BaseModel.php'; // nạp lớp BaseModel

// 🔒 Kiểm tra user đăng nhập
$makh = $_SESSION['makh'] ?? null;
if (!$makh) {
    die("⚠️ Bạn chưa đăng nhập!");
}

// 🧩 Kiểm tra có file upload không
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $file_name = $_FILES['avatar']['name'];
    $tmp_name  = $_FILES['avatar']['tmp_name'];
    $file_size = $_FILES['avatar']['size'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // 🧱 Kiểm tra định dạng
    if (!in_array($file_ext, $allowed_ext)) {
        die("❌ Chỉ chấp nhận JPG, JPEG, PNG, GIF, WEBP!");
    }

    // 🧱 Giới hạn kích thước (2MB)
    if ($file_size > 2 * 1024 * 1024) {
        die("❌ File quá lớn (tối đa 2MB)!");
    }

    // 🟢 Đường dẫn upload
    $upload_dir = __DIR__ . '/../public/images/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // 🧩 Tạo tên file duy nhất
    $new_name = "avatar_" . $makh . "_" . time() . "." . $file_ext;
    $target_path = $upload_dir . $new_name;

    // 📦 Di chuyển file upload
    if (move_uploaded_file($tmp_name, $target_path)) {

        // ✅ Tạo một instance ẩn danh của BaseModel để đảm bảo connection được khởi tạo
        new class extends BaseModel {};

        // 🧩 Lấy kết nối static
        $conn = BaseModel::$_connection;

        // 🧱 Cập nhật avatar trong DB
        $safe_name = $conn->real_escape_string($new_name);
        $makh_int  = intval($makh);

        $sql = "UPDATE KHACHHANG SET hinhanh = '$safe_name' WHERE makh = $makh_int";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "✅ Cập nhật ảnh đại diện thành công!";
            header("Location: /views/profile.php");
            exit;
        } else {
            die("❌ Lỗi truy vấn: " . $conn->error);
        }

    } else {
        die("❌ Không thể lưu file.");
    }
} else {
    die("❌ Không có file được tải lên.");
}
