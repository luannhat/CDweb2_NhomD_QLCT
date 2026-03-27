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

// Tên file PDF
$filename = "BaoCaoTongHop_" . $fromMonth . "_" . $toMonth . "_" . $year . ".pdf";

// Tạo HTML cho PDF
$html = '<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo tổng hợp nhiều tháng</title>
    <style>
        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table td {
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-row {
            background-color: #e8f5e9 !important;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>BÁO CÁO TỔNG HỢP NHIỀU THÁNG</h1>
    
    <div class="info">
        <p><strong>Khoảng thời gian:</strong> Tháng ' . $fromMonth . ' đến Tháng ' . $toMonth . ' năm ' . $year . '</p>
        <p><strong>Ngày xuất báo cáo:</strong> ' . date('d/m/Y H:i:s') . '</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tháng</th>
                <th>Tổng thu nhập</th>
                <th>Tổng chi tiêu</th>
                <th>Số dư</th>
            </tr>
        </thead>
        <tbody>';

foreach($data as $row) {
    $html .= '<tr>
                <td>' . htmlspecialchars($row['thang']) . '</td>
                <td>' . number_format($row['thu'], 0, ',', '.') . ' VNĐ</td>
                <td>' . number_format($row['chi'], 0, ',', '.') . ' VNĐ</td>
                <td>' . number_format($row['sodu'], 0, ',', '.') . ' VNĐ</td>
            </tr>';
}

$html .= '<tr class="total-row">
                <td><strong>TỔNG CỘNG</strong></td>
                <td><strong>' . number_format($tongThuTatCa, 0, ',', '.') . ' VNĐ</strong></td>
                <td><strong>' . number_format($tongChiTatCa, 0, ',', '.') . ' VNĐ</strong></td>
                <td><strong>' . number_format($tongSoDu, 0, ',', '.') . ' VNĐ</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Báo cáo được tạo tự động từ hệ thống Quản lý chi tiêu</p>
    </div>
</body>
</html>';

// Kiểm tra và sử dụng các thư viện PDF có sẵn
// Tìm vendor ở các vị trí có thể
$vendor_paths = [
    '/var/www/html/vendor/autoload.php',        // Trong container: đường dẫn tuyệt đối (ưu tiên)
    __DIR__ . '/../vendor/autoload.php',        // Trong container: /var/www/html/vendor/autoload.php
    __DIR__ . '/../../../vendor/autoload.php',  // Local: từ sources/qlct/views/ đến thư mục gốc
    dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php', // Từ views -> qlct -> sources -> root
];
$mpdf_path = null;
foreach ($vendor_paths as $path) {
    if ($path && file_exists($path)) {
        $mpdf_path = $path;
        break;
    }
}

// Debug: Log đường dẫn đang kiểm tra (chỉ khi không tìm thấy)
if (!$mpdf_path) {
    error_log('PDF Export - Không tìm thấy vendor/autoload.php. Đã kiểm tra các đường dẫn:');
    foreach ($vendor_paths as $path) {
        error_log('  - ' . $path . ' : ' . (file_exists($path) ? 'EXISTS' : 'NOT FOUND'));
    }
    error_log('  - __DIR__ = ' . __DIR__);
}

$tcpdf_path = __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';

// Ưu tiên sử dụng mPDF
if ($mpdf_path && file_exists($mpdf_path)) {
    // Xóa output buffer trước khi load thư viện
    ob_clean();
    
    require_once($mpdf_path);
    
    // Kiểm tra xem class Mpdf có tồn tại không
    if (!class_exists('\Mpdf\Mpdf')) {
        error_log('PDF Export - Class \Mpdf\Mpdf không tồn tại sau khi require autoload.php');
        ob_clean();
    } else {
        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'tempDir' => sys_get_temp_dir(), // Thêm tempDir để tránh lỗi quyền truy cập
            ]);
            
            $mpdf->SetTitle('Báo cáo tổng hợp nhiều tháng');
            $mpdf->SetAuthor('Hệ thống QLCT');
            $mpdf->WriteHTML($html);
            $mpdf->Output($filename, 'D');
            exit;
        } catch (\Exception $e) {
            // Log lỗi để debug
            error_log('mPDF Error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            // Fallback nếu có lỗi - xóa buffer và tiếp tục
            ob_clean();
        }
    }
}

// Nếu không có mPDF, thử TCPDF
if ($tcpdf_path && file_exists($tcpdf_path)) {
    // Xóa output buffer trước khi load thư viện
    ob_clean();
    
    require_once($tcpdf_path);
    
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator('Quản lý chi tiêu');
    $pdf->SetAuthor('Hệ thống QLCT');
    $pdf->SetTitle('Báo cáo tổng hợp nhiều tháng');
    $pdf->SetSubject('Báo cáo tài chính');
    $pdf->SetKeywords('báo cáo, tổng hợp, thu chi');
    
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output($filename, 'D');
    exit;
}

// Fallback: Nếu không có thư viện PDF, thông báo lỗi rõ ràng
// Xóa output buffer trước khi gửi header
ob_clean();

// Thu thập thông tin debug
$debug_info = [];
$debug_info[] = "=== THÔNG TIN DEBUG ===";
$debug_info[] = "__DIR__ = " . __DIR__;
$debug_info[] = "";
$debug_info[] = "Đã kiểm tra các đường dẫn:";
foreach ($vendor_paths as $path) {
    $exists = file_exists($path);
    $debug_info[] = "  " . ($exists ? "✓" : "✗") . " $path";
}

// Để sử dụng chức năng xuất PDF, cần cài đặt thư viện mPDF:
header('Content-Type: text/plain; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . str_replace('.pdf', '.txt', $filename) . '"');
echo "LỖI: Hệ thống chưa được cài đặt thư viện PDF.\n\n";
echo implode("\n", $debug_info);
echo "\n\n";
echo "Vui lòng cài đặt mPDF bằng một trong các cách sau:\n\n";
echo "Cách 1: Sử dụng Composer (khuyến nghị)\n";
echo "  Trong Docker container:\n";
echo "    docker exec -it php-web bash\n";
echo "    cd /var/www/html\n";
echo "    composer require mpdf/mpdf\n\n";
echo "  Hoặc trên máy local:\n";
echo "    cd " . dirname(dirname(dirname(__DIR__))) . "\n";
echo "    composer require mpdf/mpdf\n\n";
echo "Cách 2: Kiểm tra mount volume\n";
echo "  Đảm bảo trong Docker-compose.yml có:\n";
echo "    volumes:\n";
echo "      - ./vendor:/var/www/html/vendor\n\n";
echo "Cách 3: Tải thư viện về thủ công\n";
echo "  - Tải mPDF từ: https://github.com/mpdf/mpdf\n";
echo "  - Đặt vào thư mục: " . dirname(dirname(dirname(__DIR__))) . "/vendor/\n\n";
echo "Sau khi cài đặt, chức năng xuất PDF sẽ hoạt động tự động.\n";
echo "\n";
echo "Để kiểm tra chi tiết, truy cập: test_vendor.php\n";
exit;
?>

