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
}
