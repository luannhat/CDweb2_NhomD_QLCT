<?php
class UserController {

    public function home() {
        $currentPage = 'home';
        include __DIR__ . '/../views/user/home.php';
    }

    public function history() {
        include __DIR__ . '/../views/user/history.php';
    }

    public function stats() {
        $this->requireLogin();
        include __DIR__ . '/../views/user/stats.php';
    }

    public function dashboard() {
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

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function requireLogin() {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Bạn cần đăng nhập để truy cập trang này!";
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
    }

    public function income() {
        $this->requireLogin();

        $currentPage = 'income';
        
        include __DIR__ . '/../views/user/khoanthu.php';
    }

    public function expense() {
        $this->requireLogin();

        $currentPage = 'expense';

        include __DIR__ . '/../views/user/khoanchi.php';
    }

    public function budget() {
        $this->requireLogin();

        $currentPage = 'budget';

        include __DIR__ . '/../views/user/ngansach.php';
    }
    
}
