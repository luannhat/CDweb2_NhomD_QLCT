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
}
