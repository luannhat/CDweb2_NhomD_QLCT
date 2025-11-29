<?php
session_start();
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/UserModel.php';


if (!isset($_SESSION['makh'])) {
    header("Location: login.php");
    exit();
}

$makh = (int)$_SESSION['makh'];
$userModel = new UserModel();
$message = "";


$user = $userModel->layKhachHangTheoId($makh);
if (!$user) {
    $_SESSION['error'] = "Không tìm thấy thông tin khách hàng.";
    header("Location: login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenkh = trim($_POST['tenkh']);
    $email = trim($_POST['email']);
    $sodienthoai = trim($_POST['sodienthoai']);
    $ngaysinh = trim($_POST['ngaysinh']);
    $gioitinh = trim($_POST['gioitinh']);
    $diachi = trim($_POST['diachi']);
    $matkhau = !empty($_POST['matkhau']) ? password_hash($_POST['matkhau'], PASSWORD_BCRYPT) : $user['matkhau'];
    $hinhanh = $user['hinhanh'];


    if (!empty($_FILES['hinhanh']['name'])) {
        $targetDir = __DIR__ . "/uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $filename = time() . "_" . basename($_FILES['hinhanh']['name']);
        $filepath = $targetDir . $filename;
        if (move_uploaded_file($_FILES['hinhanh']['tmp_name'], $filepath)) {
            $hinhanh = 'views/uploads/' . $filename; // đường dẫn hiển thị web
        }
    }

  
    $updateData = [
        'tenkh' => $tenkh,
        'email' => $email,
        'matkhau' => $matkhau,
        'hinhanh' => $hinhanh,
        'sodienthoai' => $sodienthoai,
        'ngaysinh' => $ngaysinh,
        'gioitinh' => $gioitinh,
        'diachi' => $diachi,
        'makh' => $makh
    ];

    $userModel->capNhatHoSo($updateData);
    $message = "Cập nhật hồ sơ thành công!";
    $user = $userModel->layKhachHangTheoId($makh); 
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ Sơ Cá Nhân</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fff8;
            margin: 0;
            padding: 0;
        }
        .profile-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            background-color: #9effb7;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
        }
        h2 {
            margin: 10px 0 0 0;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        td {
            padding: 8px;
        }
        td:first-child {
            font-weight: bold;
            width: 30%;
        }
        input, select {
            width: 100%;
            padding: 6px;
            box-sizing: border-box;
        }
        .btn-group {
            text-align: center;
            margin: 20px 0;
        }
        .btn {
            background-color: #8bf4a6;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #6de68f;
        }
        .message {
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
            color: green;
        }
        @media (max-width: 600px) {
            .header img { width: 80px; height: 80px; }
            td:first-child { width: 40%; }
            .btn { padding: 8px 12px; font-size: 14px; }
        }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="header">
        <img src="<?= htmlspecialchars($user['hinhanh'] ?: 'sources/qlct/public/images/hoso.jpg') ?>" alt="Avatar">
        <h2>Hồ Sơ Cá Nhân</h2>
    </div>

  <?php if ($message): ?>
    <div class="message"><?= $message ?></div>
<?php endif; ?>


    <form method="POST" enctype="multipart/form-data">
        <table>
            <tr><td>Ảnh đại diện:</td><td><input type="file" name="hinhanh"></td></tr>
            <tr><td>Họ và tên:</td><td><input type="text" name="tenkh" value="<?= htmlspecialchars($user['tenkh']) ?>"></td></tr>
            <tr><td>Email:</td><td><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"></td></tr>
            <tr><td>Số điện thoại:</td><td><input type="text" name="sodienthoai" value="<?= htmlspecialchars($user['sodienthoai']) ?>"></td></tr>
            <tr><td>Ngày sinh:</td><td><input type="date" name="ngaysinh" value="<?= htmlspecialchars($user['ngaysinh']) ?>"></td></tr>
            <tr><td>Giới tính:</td>
                <td>
                    <select name="gioitinh">
                        <option value="Nam" <?= ($user['gioitinh']=='Nam')?'selected':'' ?>>Nam</option>
                        <option value="Nữ" <?= ($user['gioitinh']=='Nữ')?'selected':'' ?>>Nữ</option>
                        <option value="Khác" <?= ($user['gioitinh']=='Khác')?'selected':'' ?>>Khác</option>
                    </select>
                </td>
            </tr>
              <tr>
            <td>Địa chỉ:</td>
            <td><input type="text" name="diachi" value="<?= htmlspecialchars($user['diachi']) ?>"></td>
        </tr>
        <tr>
            <td>Mật khẩu mới:</td>
            <td><input type="password" name="matkhau" placeholder="Để trống nếu không đổi"></td>
        </tr>
        </table>

        <div class="btn-group">
            <button class="btn" type="submit">Lưu hồ sơ</button>
            <button class="btn" type="button" onclick="window.location.href='change_password.php'">Đổi mật khẩu</button>
        </div>
    </form>
</div>

</body>
</html>
