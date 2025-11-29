<?php
session_start();
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/ExpenseModel.php';


if (!isset($_SESSION['makh']) || empty($_SESSION['makh'])) {
    header("Location: login.php");
    exit;
}

$makh = (int) $_SESSION['makh'];
$model = new ExpenseModel();


$thang = isset($_GET['thang']) ? (int)$_GET['thang'] : (int)date('n');
$nam = isset($_GET['nam']) ? (int)$_GET['nam'] : (int)date('Y');


if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $rows = $model->getLedgerByMonth($makh, $thang, $nam);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="lich_thu_chi_' . $nam . '_' . $thang . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Ngày', 'Loại', 'Số tiền', 'Ghi chú']);
    foreach ($rows as $r) {
        fputcsv($out, [
            date('Y-m-d H:i:s', strtotime($r['ngay'] ?? '')),
            $r['loai'] ?? '',
            $r['sotien'] ?? 0,
            $r['ghichu'] ?? ''
        ]);
    }
    fclose($out);
    exit;
}

$entries = $model->getLedgerByMonth($makh, $thang, $nam);
$totals = $model->getLedgerTotalsByMonth($makh, $thang, $nam);
$tong_thu = $totals['tong_thu'] ?? 0.0;
$tong_chi = $totals['tong_chi'] ?? 0.0;
$so_du = $tong_thu - $tong_chi;


?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Lịch Thu Chi Tháng</title>
<style>
body {font-family: Arial,sans-serif;background:#fff;color:#222;}
.container {max-width:900px;margin:24px auto;padding:0 12px;}
h2 {background:#a7f7b6;padding:12px;margin:0 0 12px;border-radius:4px;}
.controls {margin:12px 0;display:flex;gap:8px;align-items:center;}
select, button {padding:6px 8px;}
.summary {background:#f8f8f8;padding:10px;border-radius:6px;margin:12px 0;}
table {width:100%;border-collapse:collapse;margin-top:12px;}
th,td {padding:10px;border-bottom:1px solid #eee;text-align:left;}
.thu {color:green;font-weight:600;}
.chi {color:red;font-weight:600;}
.empty {text-align:center;padding:18px;color:#666;}
</style>
</head>
<body>
<div class="container">
    <h2>Lịch Thu Chi Tháng</h2>

    <form method="get" class="controls" style="margin-bottom:8px;">
        <label>Tháng:
            <select name="month">
                <?php for($i=1;$i<=12;$i++): ?>
                    <option value="<?= $i ?>" <?= $i===$thang?'selected':'' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </label>
        <label>Năm:
            <select name="year">
                <?php for($y=date('Y')-5;$y<=date('Y');$y++): ?>
                    <option value="<?= $y ?>" <?= $y===$nam?'selected':'' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </label>
        <button type="submit">Xem</button>
        <a style="margin-left:auto" href="?month=<?= $thang ?>&year=<?= $nam ?>&export=csv"><button type="button">Xuất CSV</button></a>
    </form>

    <div class="summary">
        <strong>Tổng thu:</strong> <?= number_format($tong_thu,0,',','.') ?> VND &nbsp;|
        <strong>Tổng chi:</strong> <?= number_format($tong_chi,0,',','.') ?> VND &nbsp;|
        <strong>Số dư:</strong> <?= number_format($so_du,0,',','.') ?> VND
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:160px">Ngày</th>
                <th>Loại</th>
                <th style="width:160px">Số tiền (VND)</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($entries)): ?>
                <?php foreach($entries as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($e['ngay']))) ?></td>
                        <td class="<?= $e['loai']=='thu'?'thu':'chi' ?>"><?= htmlspecialchars($e['loai']) ?></td>
                        <td><?= ($e['loai']=='thu'?'+ ':'- ').number_format($e['sotien'],0,',','.') ?></td>
                        <td><?= htmlspecialchars($e['ghichu']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="empty">Chưa có dữ liệu thu chi trong tháng này.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
