<?php
require_once __DIR__ . '/../models/KhoanthuModel.php';
require_once __DIR__ . '/../models/KhoanchiModel.php';
class DashboardController
{

    public function index()
    {
        $makh = $_SESSION['makh'];

        $khoanthuModel = new KhoanthuModel();
        $khoanchiModel = new KhoanchiModel();
        $totalIncome = $khoanthuModel->countTotalIncomes($makh);
        $totalExpence = $khoanchiModel->countTotalExpenses($makh);
        include 'views/user/dashboard.php';
    }
}
