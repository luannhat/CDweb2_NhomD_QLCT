<?php
session_start();

if (!isset($_SESSION['makh'])) {
    echo "<h3 style='color:red'>⚠️ SESSION[makh] không tồn tại! Danh mục sẽ trống.</h3>";
}
require_once __DIR__ . '/../models/KhoanchiModel.php';
require_once __DIR__ . '/../controllers/KhoanchiController.php';

// Lấy mã khoản chi từ URL
$machitieu = $_GET['machitieu'] ?? null;

if (!$machitieu) {
    die("Thiếu mã khoản chi!");
}

// Lấy dữ liệu khoản chi từ database
$model = new KhoanchiModel();
$data = $model->getExpenseById($machitieu, $_SESSION['makh']);
if (!$data) {
    exit("❌ Không tìm thấy khoản chi hoặc không thuộc quyền của bạn!");
}
if (!$data || !is_array($data)) {
    echo "<h3 style='color:red'>❌ Không tìm thấy khoản chi!</h3>";
    exit; // QUAN TRỌNG — phải exit
}

// Xử lý khi submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new KhoanchiController();
    $result = $controller->update($machitieu, $_POST);
    header("Location: khoanchi.php");
    exit;
}

?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Sửa khoản chi</title>
    <link rel="stylesheet" href="/public/css/suakhoanchi.css">
</head>

<body>

    <div class="form-container">
        <h2>Sửa khoản chi</h2>

        <form method="POST">

            <!-- Mã khoản chi (readonly) -->
            <div class="form-row">
                <label>Mã khoản chi</label>
                <input type="text" value="<?= $data['machitieu']; ?>" readonly>
            </div>

            <!-- Tên khoản chi -->
            <div class="form-row">
                <label>Tên khoản chi</label>
                <input type="text" name="noidung" required value="<?= htmlspecialchars($data['noidung']); ?>">
            </div>

            <!-- Số tiền -->
            <div class="form-row">
                <label>Số tiền</label>
                <input type="number" name="sotien" required value="<?= $data['sotien']; ?>">
            </div>

            <!-- Ngày -->
            <div class="form-row">
                <label>Ngày</label>
                <input type="date" name="ngaygiaodich" required value="<?= $data['ngaychitieu']; ?>">
            </div>

            <!-- Danh mục -->
            <div class="form-row">
                <label>Danh mục</label>
                <select name="madmchitieu" required>
                    <?php
                    $makh_of_expense = $data['makh'];
                    $cats = $model->getDanhMucByMakh($makh_of_expense);
                    foreach ($cats as $cat) {
                        $sel = ($cat['madmchitieu'] == $data['madmchitieu']) ? "selected" : "";
                        echo "<option value='{$cat['madmchitieu']}' {$sel}>{$cat['tendanhmuc']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="btn-group">
                <a href="khoanchi.php" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>

        </form>
    </div>

</body>

</html>