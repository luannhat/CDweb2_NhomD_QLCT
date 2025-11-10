<?php
require_once 'models/StatisticalModel.php';

class StatisticalController {
    private $model;

    public function __construct() {
        $this->model = new StatisticalModel();
    }

    public function index() {
        $data = $this->model->getAllExpenseByCategory();

        // Nếu không có dữ liệu -> trả mảng rỗng
        if (!$data) $data = [];

        include './views/statistical.php';
    }
}
