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

        // Nếu không có dữ liệu -> trả mảng rỗng
        if (!$data) $data = [];

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
}
