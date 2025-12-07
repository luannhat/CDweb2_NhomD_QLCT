<?php
require_once 'models/StatisticalModel.php';

class StatisticalController
{
    private $model;
    private $makh;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Bắt buộc user phải đăng nhập
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $this->makh = $_SESSION['user']['makh'];
        $this->model = new StatisticalModel();
    }

    public function index()
    {
        $data = $this->model->getAllExpenseByCategory($this->makh);
        include './views/statistical.php';
    }

    public function monthlyStatistics()
    {
         $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $month = isset($_GET['month']) ? intval($_GET['month']) : null;

        if ($month) {
            $transactions = $this->model->getTransactionsByMonth($this->makh, $year, $month);
            $data = [$month => array_sum(array_column($transactions, 'sotien'))];
            $availableMonths = $this->model->getMonthsWithTransactions($this->makh, $year);
        } else {
            $data = $this->model->getMonthlyTotals($this->makh, $year);
            $transactions = [];
            $availableMonths = array_keys($data);
        }

        include './views/monthly_statistics.php';
    }
    public function annualStatistics()
    {
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

        $data = $this->model->getExpenseByCategoryAndYear($this->makh, $year);
        $totalExpense = $this->model->getTotalExpenseByYear($this->makh, $year);

        include './views/thongke_nam.php';
    }

    public function lineChart()
    {
        $fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-01-01');
        $toDate = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-12-31');

        $monthlyData = $this->model->getIncomeExpenseByDateRange($this->makh, $fromDate, $toDate);
        $totals = $this->model->getTotalIncomeExpenseByDateRange($this->makh, $fromDate, $toDate);

        include './views/bieu_do_duong.php';
    }

    public function weeklyStatistics()
    {
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));

        $weeklyData = $this->model->getWeeklyIncomeExpenseByMonth($this->makh, $year, $month);
        
        // Tính tổng chi tiêu theo tuần
        $totalByWeek = [];
        $overallTotal = 0;
        foreach ($weeklyData as $week) {
            $total = $week['chi_tieu'];
            $totalByWeek[] = [
                'week' => $week['label'],
                'total' => $total,
                'income' => $week['thu_nhap'],
                'expense' => $week['chi_tieu']
            ];
            $overallTotal += $total;
        }

        $availableMonths = $this->model->getMonthsWithTransactions($this->makh, $year);

        include './views/thongke_chi_tieu_tuan.php';
    }

    public function weeklyDetail()
    {
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
        $week = isset($_GET['week']) ? intval($_GET['week']) : 1;

        // Lấy dữ liệu chi tiết của tuần
        $expenses = $this->model->getWeeklyExpenseDetails($this->makh, $year, $month, $week);
        
        // Tính tổng chi tiêu tuần
        $totalWeekExpense = array_sum(array_column($expenses, 'sotien'));

        // Lấy thông tin danh mục
        $weeklyData = $this->model->getWeeklyIncomeExpenseByMonth($this->makh, $year, $month);
        $weekInfo = $weeklyData[$week - 1] ?? ['label' => 'Tuần ' . $week];

        include './views/thongke_chi_tieu_tuan_detail.php';
    }
}
