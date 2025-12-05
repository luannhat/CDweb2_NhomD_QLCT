<?php
require_once 'models/StatisticalModel.php';

class StatisticalController {
    private $model;
    private $makh;

    public function __construct() {
        $this->model = new StatisticalModel();
        $this->makh = $_SESSION['id'] ?? 1;
    }

    public function index() {
        $data = $this->model->getAllExpenseByCategory();

        include './views/statistical.php';
    }

    public function annualStatistics() {
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        
        $data = $this->model->getExpenseByCategoryAndYear($this->makh, $year);
        $totalExpense = $this->model->getTotalExpenseByYear($this->makh, $year);

        include './views/thongke_nam.php';
    }

    public function lineChart() {
        $fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-01-01');
        $toDate = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-12-31');
        
        $monthlyData = $this->model->getIncomeExpenseByDateRange($this->makh, $fromDate, $toDate);
        $totals = $this->model->getTotalIncomeExpenseByDateRange($this->makh, $fromDate, $toDate);

        include './views/bieu_do_duong.php';
    }

    public function compareYears() {
        $currentYear = date('Y');
        $fromYear = isset($_GET['from_year']) ? intval($_GET['from_year']) : ($currentYear - 5);
        $toYear = isset($_GET['to_year']) ? intval($_GET['to_year']) : $currentYear;
        
        // Đảm bảo fromYear <= toYear
        if ($fromYear > $toYear) {
            $temp = $fromYear;
            $fromYear = $toYear;
            $toYear = $temp;
        }
        
        $expenseData = $this->model->getExpenseByYearRange($this->makh, $fromYear, $toYear);
        
        include './views/so_sanh_chi_tieu_nam.php';
    }
}
