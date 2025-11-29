<?php
class HomeController
{
    public function index()
    {
        // Dữ liệu có thể truyền vào view
        $pageTitle = "Trang chủ";

        // Include view home
        include __DIR__ . '/../views/home.php';
    }
}
