<?php
session_start();

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user'])) {
    header("Location: ?controller=auth&action=login");
    exit;
}

// Lấy thông tin người dùng
$user = $_SESSION['user'];

// Hàm giả lập lấy dữ liệu (sau này bạn thay bằng truy vấn database)
function getTotalIncome($userId) {
    return 15000000; // ví dụ
}
function getTotalExpense($userId) {
    return 7500000; // ví dụ
}
function getExpenseCategories($userId) {
    return ["Ăn uống", "Di chuyển", "Giải trí", "Hóa đơn", "Khác"];
}
function getCategoryExpenses($userId) {
    return [2000000, 1000000, 1500000, 800000, 1200000];
}

// Chuẩn bị dữ liệu cho view
$totalIncome = getTotalIncome($user['id']);
$totalExpense = getTotalExpense($user['id']);
$balance = $totalIncome - $totalExpense;
$categories = getExpenseCategories($user['id']);
$categoryExpenses = getCategoryExpenses($user['id']);

// Gọi view
include 'views/user/dashboard.php';
?>
