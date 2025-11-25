<?php
session_start();
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/ExpenseModel.php';


if (!isset($_SESSION['makh']) || empty($_SESSION['makh'])) {
    header("Location: login.php");
    exit;
}

$makh = (int)$_SESSION['makh'];
$model = new ExpenseModel();
$message = '';
$msgType = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $ngay = $_POST['ngay'] ?? '';
        $sotien = (float)($_POST['sotien'] ?? 0);
        $loai = $_POST['loai'] ?? 'chi';
        $ghichu = trim($_POST['ghichu'] ?? '');

     
        if ($sotien > 0 && in_array($loai, ['chi', 'thu'])) {
            $ok = $model->addRecurringLedger($makh, $ngay, $loai, $sotien, $ghichu);
            $message = $ok ? 'Thêm khoản định kỳ thành công!' : 'Lỗi! Không thể thêm dữ liệu.';
            $msgType = $ok ? 'success' : 'error';
        } else {
            $message = 'Dữ liệu không hợp lệ (loại hoặc số tiền).';
            $msgType = 'error';
        }
    }

    if ($action === 'update') {
        $mathuchi = (int)($_POST['mathuchi'] ?? 0);
        $ngay = $_POST['ngay'] ?? '';
        $sotien = (float)($_POST['sotien'] ?? 0);
        $loai = $_POST['loai'] ?? 'chi';
        $ghichu = trim($_POST['ghichu'] ?? '');

        if ($mathuchi > 0 && $sotien > 0 && in_array($loai, ['chi', 'thu'])) {
            $ok = $model->updateRecurringLedger($mathuchi, $makh, $ngay, $loai, $sotien, $ghichu);
            $message = $ok ? 'Cập nhật thành công!' : 'Lỗi! Không thể cập nhật dữ liệu.';
            $msgType = $ok ? 'success' : 'error';
        } else {
            $message = 'Dữ liệu cập nhật không hợp lệ.';
            $msgType = 'error';
        }
    }

    if ($action === 'delete') {
        $mathuchi = (int)($_POST['mathuchi'] ?? 0);
        if ($mathuchi > 0) {
            $ok = $model->deleteRecurringLedger($mathuchi, $makh);
            $message = $ok ? 'Xóa thành công!' : 'Không thể xóa khoản này!';
            $msgType = $ok ? 'success' : 'error';
        }
    }
}


$entries = $model->getRecurringLedgerList($makh, null, 100, 0);
$total_entries = $model->countRecurringLedger($makh);


$isEditing = false;
$editingEntry = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editingEntry = $model->getRecurringLedgerById($editId, $makh);
    if ($editingEntry) {
        $isEditing = true;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Khoản Tiền Định Kỳ</title>
    <style>
        * {box-sizing: border-box;}
        body {font-family: Arial, sans-serif; background:#fff; color:#222; margin:0}
        .container {max-width:1000px; margin:20px auto; padding:0 12px}
        .header {background:#a7f7b6; padding:15px; border-radius:4px; margin-bottom:20px}
        .header h2 {margin:0}
        .message {padding:10px 12px; margin-bottom:12px; border-radius:4px}
        .success {background:#d4edda; color:#155724; border:1px solid #c3e6cb}
        .error {background:#f8d7da; color:#721c24; border:1px solid #f5c6cb}
        .form-section {background:#f8f8f8; padding:15px; border-radius:4px; margin-bottom:20px}
        .form-group {margin-bottom:12px}
        label {display:block; font-weight:600; margin-bottom:4px}
        input, select, textarea {width:100%; max-width:400px; padding:8px; border:1px solid #ccc; border-radius:3px}
        .radio-group { display:flex; flex-direction:row; align-items:center; gap:12px; flex-wrap:wrap; }
        .radio-group label { display:flex; align-items:center; gap:8px; font-weight:400; white-space:nowrap; }
        button {padding:8px 16px; background:#a7f7b6; border:none; border-radius:3px; cursor:pointer; font-weight:600}
        button:hover {background:#90e89e}
        .btn-delete {background:#e74c3c; color:white; font-size:12px; padding:4px 8px}
        .btn-delete:hover {background:#c0392b}
        table {width:100%; border-collapse:collapse; margin-top:10px}
        th, td {padding:10px; border-bottom:1px solid #ddd}
        th {background:#a7f7b6}
        tr:nth-child(even) {background:#f9f9f9}
        .thu {color:green; font-weight:600}
        .chi {color:red; font-weight:600}
        .empty {text-align:center; padding:20px; color:#666}
        .form-row {display:grid; grid-template-columns:1fr 1fr; gap:12px}
        @media (max-width:600px) {.form-row {grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <h2>Quản Lý Khoản Tiền Định Kỳ</h2>
        <p>Tổng cộng: <strong><?= $total_entries ?></strong> khoản tiền</p>
    </div>

    <?php if ($message): ?>
        <div class="message <?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="form-section">
        <h3>Thêm Khoản Tiền Định Kỳ Mới</h3>
        <form method="POST">
           
            <?php if ($isEditing): ?>
                <input type="hidden" name="mathuchi" value="<?= (int)$editingEntry['mathuchi'] ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="ngay">Ngày bắt đầu:</label>
                    <input type="date" id="ngay" name="ngay" value="<?= htmlspecialchars($isEditing ? ($editingEntry['ngay'] ?? date('Y-m-d')) : date('Y-m-d')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="sotien">Số tiền (VND):</label>
                    <input type="number" id="sotien" name="sotien" min="0.01" step="0.01" required value="<?= htmlspecialchars($isEditing ? ($editingEntry['sotien'] ?? '') : '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Loại:</label>
                <div class="radio-group">
                    <label><input type="radio" name="loai" value="khonglaplai" <?= (!$isEditing || ($editingEntry['loai'] ?? '') === 'khonglaplai') ? 'checked' : '' ?>> Không lặp lại</label>
                    <label><input type="radio" name="loai" value="laplaitheongay" <?= ($isEditing && ($editingEntry['loai'] ?? '') === 'laplaitheongay') ? 'checked' : '' ?>> Lặp lại theo ngày</label>
                    <label><input type="radio" name="loai" value="laplaitheotuan" <?= ($isEditing && ($editingEntry['loai'] ?? '') === 'laplaitheotuan') ? 'checked' : '' ?>> Lặp lại theo tuần</label>
                    <label><input type="radio" name="loai" value="laplaitheothangs" <?= ($isEditing && ($editingEntry['loai'] ?? '') === 'laplaitheothang') ? 'checked' : '' ?>> Lặp lại theo tháng</label>
                </div>
            </div>

            <div class="form-group">
                <label for="ghichu">Ghi chú:</label>
                <textarea id="ghichu" name="ghichu" rows="2"><?= htmlspecialchars($isEditing ? ($editingEntry['ghichu'] ?? '') : '') ?></textarea>
            </div>

            <?php if ($isEditing): ?>
                <button type="submit" name="action" value="update">Lưu</button>
                <a href="<?= htmlspecialchars(basename(__FILE__)) ?>" style="margin-left:8px"><button type="button" style="background:#ccc">Hủy</button></a>
            <?php else: ?>
                <button type="submit" name="action" value="add">Lưu</button>
            <?php endif; ?>
            <button type="reset" style="background:#ccc">Xóa</button>
        </form>
    </div>

   
    <div class="form-section">
        <h3>Danh Sách Khoản Tiền Định Kỳ</h3>

        <?php if (!empty($entries)): ?>
            <table>
                <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Loại</th>
                    <th>Số tiền (VND)</th>
                    <th>Ghi chú</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($entries as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($e['ngay']))) ?></td>
                        <td class="<?= $e['loai'] ?>"><?= $e['loai'] === 'khonglaplai' ? 'Không lặp lại' : ($e['loai'] === 'laplaitheongay' ? 'Lặp lại theo ngày' : ($e['loai'] === 'laplaitheotuan' ? 'Lặp lại theo tuần' : 'Lặp lại theo tháng')) ?></td>
                        <td><?= number_format($e['sotien'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($e['ghichu']) ?></td>
                        <td>
                            <a href="?edit=<?= $e['mathuchi'] ?>" style="margin-right:8px; text-decoration:none;"><button type="button">Sửa</button></a>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="mathuchi" value="<?= $e['mathuchi'] ?>">
                                <button class="btn-delete" onclick="return confirm('Xóa khoản này?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="empty">Chưa có khoản định kỳ nào.</p>
        <?php endif; ?>

    </div>
</div>
</body>
</html>
