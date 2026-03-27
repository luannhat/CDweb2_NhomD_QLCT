<?php
require_once 'config/database.php';

class UploadAvatarModel {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }
    }

    // ✅ Lấy đường dẫn avatar
    public function getAvatarPath($makh) {
        $stmt = $this->conn->prepare("SELECT hinhanh FROM KHACHHANG WHERE makh = ?");
        $stmt->bind_param("i", $makh);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['hinhanh'] ?? "public/Uploads/default-avatar.png";
    }

    // ✅ Cập nhật avatar mới
    public function updateAvatar($makh, $path) {
        $stmt = $this->conn->prepare("UPDATE KHACHHANG SET hinhanh = ? WHERE makh = ?");
        $stmt->bind_param("si", $path, $makh);
        return $stmt->execute();
    }
}
