<?php
class TransactionController {
    private $model;

    public function __construct() {
        $this->model = new TransactionModel();
    }

    // Hiển thị danh sách giao dịch
    public function index() {
        $makh = $_SESSION['makh'] ?? 1;
        $giaodichs = $this->model->getAllTransaction($makh);
        $tenkh = $this->model->getCustomerName($makh);
        include __DIR__ . '/../views/transaction.php';
    }

    // Xử lý cập nhật ghi chú
    public function updateNote() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $magd = $_POST['magd'] ?? null;
            $ghichu = $_POST['ghichu'] ?? '';

            if ($magd) {
                $result = $this->model->updateGhichu($magd, $ghichu);
                if ($result) {
                    $_SESSION['message'] = "✅ Cập nhật ghi chú thành công!";
                } else {
                    $_SESSION['message'] = "⚠️ Cập nhật thất bại hoặc không có thay đổi.";
                }
            } else {
                $_SESSION['message'] = "❌ Thiếu mã giao dịch.";
            }
            header("Location: index.php?controller=transaction&action=index");
            exit;
            
        }
    }
}
?>
