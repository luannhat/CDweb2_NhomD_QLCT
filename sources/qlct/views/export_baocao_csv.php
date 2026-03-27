<?php
// Bắt đầu output buffering ngay từ đầu để tránh lỗi header
ob_start();

session_start();
require_once "../models/KhoanthuModel.php";
require_once "../models/KhoanchiModel.php";

// Lấy makh từ session hoặc dùng giá trị mặc định
$makh = intval($_SESSION['id'] ?? 1);

// Nhận dữ liệu filter
$fromMonth = intval($_GET['from'] ?? 1);
$toMonth   = intval($_GET['to'] ?? 6);
$year      = intval($_GET['year'] ?? date("Y"));

// Model
$thuNhapModel = new KhoanthuModel();
$chiModel = new KhoanchiModel();

$data = [];
$tongThuTatCa = 0;
$tongChiTatCa = 0;

for ($m = $fromMonth; $m <= $toMonth; $m++) {
    $tongThu = $thuNhapModel->getTongThuTheoThang($makh, $m, $year);
    $tongChi = $chiModel->getTongChiTheoThang($makh, $m, $year);
    
    $tongThuTatCa += $tongThu;
    $tongChiTatCa += $tongChi;

    $data[] = [
        'thang' => sprintf("%02d/%d", $m, $year),
        'thu'   => $tongThu,
        'chi'   => $tongChi,
        'sodu'  => $tongThu - $tongChi
    ];
}

$tongSoDu = $tongThuTatCa - $tongChiTatCa;

// Tên file CSV
$filename = "BaoCaoTongHop_" . $fromMonth . "_" . $toMonth . "_" . $year . ".csv";

// Xóa output buffer trước khi gửi header
ob_clean();

// Set headers cho CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

// Mở output stream
$output = fopen('php://output', 'w');

// Thêm BOM UTF-8 để Excel hiển thị tiếng Việt đúng
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Ghi header CSV
fputcsv($output, ['Tháng', 'Tổng thu nhập (VNĐ)', 'Tổng chi tiêu (VNĐ)', 'Số dư (VNĐ)'], ';');

// Ghi dữ liệu
foreach ($data as $row) {
    fputcsv($output, [
        $row['thang'],
        number_format($row['thu'], 0, ',', '.'),
        number_format($row['chi'], 0, ',', '.'),
        number_format($row['sodu'], 0, ',', '.')
    ], ';');
}

// Ghi dòng tổng cộng
fputcsv($output, [
    'TỔNG CỘNG',
    number_format($tongThuTatCa, 0, ',', '.'),
    number_format($tongChiTatCa, 0, ',', '.'),
    number_format($tongSoDu, 0, ',', '.')
], ';');

// Ghi thông tin báo cáo
fputcsv($output, [], ';'); // Dòng trống
fputcsv($output, ['Khoảng thời gian:', 'Tháng ' . $fromMonth . ' đến Tháng ' . $toMonth . ' năm ' . $year], ';');
fputcsv($output, ['Ngày xuất báo cáo:', date('d/m/Y H:i:s')], ';');

// Đóng file
fclose($output);
exit;

