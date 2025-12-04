<?php
class UserController {

    public function home() {
        $currentPage = 'home';
        include __DIR__ . '/../views/user/home.php';
    }

    public function history() {
        include __DIR__ . '/../views/user/history.php';
    }

    public function stats() {
        include __DIR__ . '/../views/user/stats.php';
    }
}
