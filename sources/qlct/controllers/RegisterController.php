<?php
require_once __DIR__ . '/../config/database.php'; // Gọi file chứa thông tin database

// Kết nối MySQL
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("❌ Kết nối cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $tenkh   = trim($_POST['tenkh']);
    $email   = trim($_POST['email']);
    $matkhau = trim($_POST['matkhau']);
    $repass  = trim($_POST['repass']);

    // Kiểm tra mật khẩu nhập lại
    if ($matkhau !== $repass) {
        header("Location: ../views/Logout.php?error=Mật khẩu không khớp!");
        exit();
    }

    // Kiểm tra email đã tồn tại chưa
    $check = $conn->prepare("SELECT * FROM KHACHHANG WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../views/Logout.php?error=Email đã tồn tại!");
        exit();
    }

    // Hash mật khẩu
    $hashedPass = password_hash($matkhau, PASSWORD_DEFAULT);

    // Xử lý upload ảnh nếu có
    $uploadPath = null;
    if (!empty($_FILES['hinhanh']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['hinhanh']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['hinhanh']['tmp_name'], $targetFile)) {
            $uploadPath = $fileName;
        }
    }

    // Thêm người dùng mới
    $stmt = $conn->prepare("INSERT INTO KHACHHANG (tenkh, email, matkhau, hinhanh) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $tenkh, $email, $hashedPass, $uploadPath);

    if ($stmt->execute()) {
        header("Location: ../views/Logout.php?success=Đăng ký thành công!");
    } else {
        header("Location: ../views/Logout.php?error=Lỗi khi đăng ký!");
    }

    $stmt->close();
    $check->close();
    $conn->close();
} else {
    header("Location: ../views/Logout.php");
    exit();
}
?>