<?php

require_once __DIR__ . '/../models/NgansachModel.php';

class NgansachController
{
    private $model;
    private $makh;

    public function __construct()
    {
        $this->model = new NgansachModel();
        $this->makh = $_SESSION['id'] ?? ($_SESSION['makh'] ?? 1);
    }

    public function index(): array
    {
        $filters = [
            'week' => isset($_GET['week']) ? intval($_GET['week']) : null,
            'month' => isset($_GET['month']) ? intval($_GET['month']) : null,
            'year' => isset($_GET['year']) ? intval($_GET['year']) : null,
        ];

        foreach ($filters as $key => $value) {
            if (empty($value)) {
                $filters[$key] = null;
            }
        }

        $budgets = $this->model->getBudgets($this->makh, $filters);
        $categories = $this->model->getExpenseCategories($this->makh);

        return [
            'budgets' => $budgets,
            'categories' => $categories,
            'filters' => $filters,
        ];
    }

    public function saveWeeklyBudget(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ngansach.php?mode=week');
            exit;
        }

        $week = intval($_POST['week'] ?? 0);
        $month = intval($_POST['month'] ?? 0);
        $year = intval($_POST['year'] ?? date('Y'));
        $categoryId = intval($_POST['category_id'] ?? 0);
        $rawAmount = str_replace(['.', ',', ' '], '', $_POST['budget_amount'] ?? '');
        $amount = floatval($rawAmount);

        // Chỉ có 4 tuần trong tháng: tuần 1 (1-7), tuần 2 (8-14), tuần 3 (15-21), tuần 4 (22-cuối tháng)
        if ($week < 1 || $week > 4 || $month < 1 || $month > 12 || $year < 2000 || $year > 2100) {
            $_SESSION['message'] = 'Vui lòng chọn tuần, tháng, năm hợp lệ.';
            header('Location: ngansach.php?mode=week');
            exit;
        }

        if ($categoryId <= 0) {
            $_SESSION['message'] = 'Vui lòng chọn danh mục.';
            header('Location: ngansach.php?mode=week');
            exit;
        }

        if ($amount <= 0) {
            $_SESSION['message'] = 'Tổng ngân sách phải lớn hơn 0.';
            header('Location: ngansach.php?mode=week');
            exit;
        }

        $result = $this->model->createWeeklyBudget($this->makh, $categoryId, $week, $month, $year, $amount);

        if ($result['success']) {
            $_SESSION['message'] = 'Đã lưu ngân sách tuần thành công.';
            header('Location: ngansach.php?mode=month');
        } else {
            $_SESSION['message'] = 'Không thể lưu ngân sách: ' . $result['message'];
            header('Location: ngansach.php?mode=week');
        }

        exit;
    }
}

