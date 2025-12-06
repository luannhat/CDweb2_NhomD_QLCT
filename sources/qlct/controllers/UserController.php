<?php
class UserController
{
    private $model;
    private $makh;

    public function home()
    {
        $currentPage = 'home';
        include __DIR__ . '/../views/user/home.php';
    }

    public function history()
    {
        include __DIR__ . '/../views/user/history.php';
    }

    public function stats()
    {
        $this->requireLogin();

        $view = $_GET['view'] ?? 'year';
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $month = isset($_GET['month']) ? intval($_GET['month']) : null;

        if ($view === 'month') {
            // Lấy dữ liệu chi tiêu theo tháng
            if ($month) {
                $transactions = $this->model->getTransactionsByMonth($this->makh, $year, $month);
                $data = [$month => array_sum(array_column($transactions, 'sotien'))];
                $availableMonths = $this->model->getMonthsWithTransactions($this->makh, $year);
            } else {
                $data = $this->model->getMonthlyTotals($this->makh, $year);
                $transactions = [];
                $availableMonths = array_keys($data);
            }

            include __DIR__ . '/../views/monthly_statistics.php';
        } else {
            // Theo năm
            $data = $this->model->getExpenseByCategoryAndYear($this->makh, $year);
            $totalExpense = $this->model->getTotalExpenseByYear($this->makh, $year);
            include __DIR__ . '/../views/thongke_nam.php';
        }
    }


    public function dashboard()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Kiểm tra xem user đã đăng nhập chưa
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $user = $_SESSION['user'];

        // Tính toán tổng thu nhập, chi tiêu, số dư
        $totalIncome = 0;   // bạn có thể lấy từ bảng thu nhập
        $totalExpense = 0;  // bạn có thể lấy từ bảng chi tiêu
        $balance = $totalIncome - $totalExpense;

        // Lấy dữ liệu biểu đồ chi tiêu theo danh mục
        $categories = [];          // tên danh mục
        $categoryExpenses = [];    // tổng chi tiêu theo danh mục

        // Load view dashboard
        include __DIR__ . '/../views/user/dashboard.php';
    }

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Bạn cần đăng nhập để truy cập trang này!";
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        $this->model = new StatisticalModel();
        $this->makh = $_SESSION['user']['id'];
    }

    private function requireLogin()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Bạn cần đăng nhập để truy cập trang này!";
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
    }

    public function income()
    {
        $this->requireLogin();

        $currentPage = 'income';

        // Nếu có id → load dữ liệu theo id để sửa
        $result = null;
        if (isset($_GET['id'])) {
            require_once __DIR__ . '/../models/KhoanthuModel.php';
            $model = new KhoanthuModel();
            $result = $model->findById($_GET['id']);
        }

        include __DIR__ . '/../views/user/khoanthu.php';
    }

    public function expense()
    {
        $this->requireLogin();

        require_once __DIR__ . '/KhoanchiController.php';
        $khoanchiController = new KhoanchiController();
        $result = $khoanchiController->index();

        $khoanchis = $result['khoanchis'] ?? [];
        $page = $result['page'] ?? 1;
        $totalPages = $result['totalPages'] ?? 1;

        include __DIR__ . '/../views/user/khoanchi.php';
    }

    public function budget()
    {
        $this->requireLogin();

        $currentPage = 'budget';

        include __DIR__ . '/../views/user/ngansach.php';
    }
}
