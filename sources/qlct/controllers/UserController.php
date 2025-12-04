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
}
