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
        $makh = $_SESSION['makh'] ?? 1;
        $giaodichs = $this->model->getAllTransaction($makh);
        $tenkh = $this->model->getCustomerName($makh) ?? 'Khách hàng';

        // Lấy message từ session nếu có
        $message = $_SESSION['message'] ?? null;
        unset($_SESSION['message']);

        // Truyền biến sang view
        include __DIR__ . '/../views/transaction.php';
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
    public function monthlyStatistics() {
        // Lấy năm/tháng từ GET
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $month = isset($_GET['month']) ? intval($_GET['month']) : '';

        // Lấy dữ liệu từ model
        $data = $this->model->getMonthlyStatistics($this->makh, $year, $month);
        $transactions = $month ? $this->model->getTransactionsByMonth($this->makh, $year, $month) : [];

        // Gửi sang view
        include './views/monthly_statistics.php';
    }
}
