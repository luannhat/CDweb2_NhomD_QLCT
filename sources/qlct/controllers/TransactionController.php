<?php
require_once __DIR__ . '/../models/TransactionModel.php';

class TransactionController
{
    private $model;
    private $makh;
    public function __construct()
    {
        $this->model = new TransactionModel();
    }

    // Hiển thị danh sách giao dịch
    public function index()
    {
        $makh = $_SESSION['user']['id'];
        $giaodichs = $this->model->getAllTransaction($makh);
        $tenkh = $this->model->getCustomerName($makh) ?? 'Khách hàng';

        // Lấy message từ session nếu có
        $message = $_SESSION['message'] ?? null;
        unset($_SESSION['message']);

        ob_start();
        include __DIR__ . '/../views/transaction.php';
        $content = ob_get_clean();

        $currentPage = 'transaction';

        include __DIR__ . '/../views/user/layout.php';
    }

    public function add()
    {
        $makh = $_SESSION['user']['makh'];
        $expenseModell = new KhoanchiModel();
        $danhMucList = $expenseModell->getAllFromDSCHITIEU($makh);
        include __DIR__ . '/../views/add_transaction.php';
    }


    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $makh        = $_SESSION['user']['makh'];
            $machitieu   = $_POST['machitieu'];
            $noidung     = $_POST['noidung'];
            $sotien      = $_POST['sotien'];
            $loai        = $_POST['loai'];
            $ngaygiaodich = $_POST['ngaygiaodich'];
            $ghichu      = $_POST['ghichu'] ?? null;

            // Xử lý upload ảnh
            $anhhoadon = null;
            if (!empty($_FILES['anhhoadon']['name'])) {
                $filename = time() . "_" . basename($_FILES['anhhoadon']['name']);
                $path = "uploads/" . $filename;

                move_uploaded_file($_FILES['anhhoadon']['tmp_name'], $path);
                $anhhoadon = $filename;
            }

            $model = new TransactionModel();
            $ok = $model->addTransactions(
                $makh,
                $machitieu,
                $noidung,
                $sotien,
                $loai,
                $ngaygiaodich,
                $ghichu,
                $anhhoadon
            );

            if ($ok) {
                $_SESSION['success'] = "Thêm giao dịch thành công!";
            } else {
                $_SESSION['error'] = "Không thể thêm giao dịch!";
            }

            header("Location: index.php?controller=transaction&action=index");
            exit;
        }
    }

    // Xử lý cập nhật ghi chú
    public function updateNote()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $magd = $_POST['magd'] ?? null;
            $ghichu = $_POST['ghichu'] ?? '';

            if ($magd) {
                $result = $this->model->updateGhichu($magd, $ghichu);
                $_SESSION['message'] = $result ?
                    "✅ Cập nhật ghi chú thành công!" :
                    "⚠️ Cập nhật thất bại hoặc không có thay đổi.";
            } else {
                $_SESSION['message'] = "❌ Thiếu mã giao dịch!";
            }

            header("Location: index.php?controller=transaction&action=index");
            exit;
        }
    }
    public function monthlyStatistics()
    {
        // Lấy năm/tháng từ GET
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $month = isset($_GET['month']) && $_GET['month'] !== '' ? intval($_GET['month']) : null;

        // Lấy dữ liệu từ model
        $data = $this->model->getMonthlyStatistics($this->makh, $year, $month);
        $transactions = $month ? $this->model->getTransactionsByMonth($this->makh, $year, $month) : [];

        // Gửi sang view
        include __DIR__ . '/../views/monthly_statistics.php';
    }
}
