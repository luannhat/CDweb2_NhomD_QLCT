<?php
session_start();
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/ExpenseModel.php';


if (!isset($_SESSION['makh']) || empty($_SESSION['makh'])) {
    header('Location: login.php');
    exit();
}

$makh = (int) $_SESSION['makh'];
$message = '';
$messageType = '';
$row = null;

$mathuanhap = (int) ($_GET['mathuanhap'] ?? 0);
if ($mathuanhap <= 0) {
    die('Không có mã khoản thu nhập được chọn.');
}

$expenseModel = new ExpenseModel();
$row = $expenseModel->getIncomeById($mathuanhap, $makh);
if (!$row) {
    die('Không tìm thấy khoản thu nhập hoặc bạn không có quyền truy cập.');
}

// CSRF token
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Yêu cầu không hợp lệ.';
        $messageType = 'error';
    } else {
        $tenkhoanthu = trim((string)($_POST['tenkhoanthu'] ?? ''));
        $sotien = (float)($_POST['sotien'] ?? 0);
        $ngaynhan_raw = $_POST['ngaynhan'] ?? '';
        $danhmuc = trim((string)($_POST['danhmuc'] ?? ''));
        $mota = trim((string)($_POST['mota'] ?? ''));

        $ngaynhan = '';
        if ($ngaynhan_raw !== '') {
            $ngaynhan = str_replace('T', ' ', $ngaynhan_raw);
            if (strlen($ngaynhan) === 16) $ngaynhan .= ':00';
        }

        if ($tenkhoanthu === '' || $sotien <= 0 || empty($ngaynhan)) {
            $message = 'Vui lòng điền đầy đủ thông tin bắt buộc và số tiền phải lớn hơn 0.';
            $messageType = 'error';
        } else {
            $ok = $chiModel->updateIncome($mathuanhap, $makh, $tenkhoanthu, $sotien, $ngaynhan, $danhmuc, $mota);
            if ($ok) {
                $message = '✓ Cập nhật khoản thu nhập thành công.';
                $messageType = 'success';
              
                $row = $chiModel->getIncomeById($mathuanhap, $makh);
            } else {
                $message = 'Lỗi khi cập nhật khoản thu nhập.';
                $messageType = 'error';
            }
        }
    }
}


$displayDate = '';
if (!empty($row['ngaynhan'])) {
    $ts = strtotime($row['ngaynhan']);
    if ($ts !== false) $displayDate = date('Y-m-d\TH:i', $ts);
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sửa Khoản Thu Nhập - Test</title>

<style>
    * { margin:0; padding:0; box-sizing:border-box; }

    body { 
        font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background:#8bf4a6;
        min-height:100vh;
    }

    .container { 
        width:100%;
        min-height:100vh;
        background:white;
    }

  
    header { 
        background:#7cd89b;
        color:white;
        padding:35px 20px;
        text-align:center;
    }

    header h1 { font-size:28px; margin-bottom:10px; }
    header p { opacity:0.9; font-size:14px; }

   
    .content { 
        padding:30px 20px; 
        max-width:900px; 
        margin:auto; 
    }

  
    .message { 
        padding:15px 20px; 
        margin-bottom:20px; 
        border-radius:8px; 
        display:flex; 
        align-items:center; 
        gap:10px; 
        font-weight:600; 
        animation:slideIn 0.3s ease-in-out;
    }

    @keyframes slideIn { 
        from { opacity:0; transform:translateY(-10px); } 
        to { opacity:1; transform:translateY(0); } 
    }

    .message.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .message.error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }

   
    .form-group { margin-bottom:20px; }
    label { display:block; margin-bottom:8px; font-weight:600; color:#333; }

    input, textarea, select {
        width:100%; 
        padding:12px 15px; 
        border:2px solid #ddd; 
        border-radius:8px; 
        font-size:14px; 
        font-family:inherit; 
        transition:border-color 0.3s;
    }

    input:focus, textarea:focus, select:focus {
        outline:none; 
        border-color:#7cd89b; 
        box-shadow:0 0 0 3px rgba(124,216,155,0.3);
    }

    textarea { resize:vertical; min-height:100px; }

 
    .info-box { 
        background:#f8f9fa; 
        padding:15px; 
        border-radius:8px; 
        margin-bottom:20px; 
        border-left:4px solid #7cd89b; 
    }

    .info-box p { margin:5px 0; color:#666; font-size:13px; }
    .info-box strong { color:#333; }

   
    .btn-group { 
        display:flex; 
        gap:10px; 
        justify-content:center; 
        margin-top:30px; 
        flex-wrap:wrap; 
    }

    button[type="submit"], button[type="button"] {
        background:#7cd89b; 
        color:white; 
        padding:12px 30px; 
        border:none; 
        border-radius:8px; 
        font-weight:600;
        cursor:pointer; 
        transition:0.3s;
    }

    button[type="submit"]:hover,
    button[type="button"]:hover {
        opacity:0.9;
        transform:translateY(-2px);
    }

    button[type="reset"] { 
        background:#e0e0e0; 
        color:#333; 
        padding:12px 30px; 
        border:none;
        border-radius:8px;
    }

    button[type="reset"]:hover { 
        background:#d5d5d5;
    }

    a { text-decoration:none; }
</style>
</head>
<body>

<div class="container">
    <header>
        <h1> Sửa Khoản Thu Nhập</h1>
        <p>Cập nhật thông tin khoản thu nhập của bạn</p>
    </header>

    <div class="content">

        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($messageType) ?>">
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <p><strong> Mã khoản thu:</strong> <?= htmlspecialchars($row['mathuanhap']) ?></p>
            <p><strong> Khách hàng:</strong> <?= htmlspecialchars($row['makh']) ?></p>
            <p><strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($row['ngaynhan'])) ?></p>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="action" value="update">

            <div class="form-group">
                <label for="tenkhoanthu"> Tên Khoản Thu Nhập *</label>
                <input type="text" id="tenkhoanthu" name="tenkhoanthu" 
                       value="<?= htmlspecialchars($row['tenkhoanthu']) ?>" required>
            </div>

            <div class="form-group">
                <label for="sotien"> Số Tiền (VND) *</label>
                <input type="number" id="sotien" name="sotien"
                       value="<?= htmlspecialchars($row['sotien']) ?>" min="0" required>
            </div>

            <div class="form-group">
                <label for="ngaynhan"> Ngày Nhận *</label>
                <input type="datetime-local" id="ngaynhan" name="ngaynhan"
                       value="<?= htmlspecialchars(str_replace(' ', 'T', $row['ngaynhan'])) ?>" required>
            </div>

            <div class="form-group">
                <label for="danhmuc"> Danh Mục</label>
                <input type="text" id="danhmuc" name="danhmuc" 
                       value="<?= htmlspecialchars($row['danhmuc']) ?>">
            </div>

            <div class="form-group">
                <label for="mota"> Mô Tả</label>
                <textarea id="mota" name="mota"><?= htmlspecialchars($row['mota']) ?></textarea>
            </div>

            <div class="btn-group">
                <button type="submit">Lưu Thay Đổi</button>
                <button type="reset">Hủy Bỏ</button>
                <a href="#">
                    <button type="button">Quay Lại</button>
                </a>
            </div>
        </form>

    </div>
</div>

</body>
</html>
