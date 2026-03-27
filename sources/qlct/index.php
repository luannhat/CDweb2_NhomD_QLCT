<?php
session_start();

// Nếu không có controller/action trong URL, trỏ thẳng tới home.php
if (!isset($_GET['controller']) && !isset($_GET['action'])) {
    require_once __DIR__ . '/views/user/home.php';
    exit;
}

// Tự động load class
spl_autoload_register(function ($className) {
    $paths = ['controllers', 'models', 'configs'];
    foreach ($paths as $path) {
        $file = __DIR__ . "/$path/$className.php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Lấy controller và action
$controllerName = $_GET['controller'] ?? 'home';
$actionName = $_GET['action'] ?? 'index';

// Map controller đặc biệt
$mapControllers = [
    'catagory' => ['class' => 'DanhmucController', 'file' => 'CatagoryController.php']
];

if (isset($mapControllers[$controllerName])) {
    $controllerClass = $mapControllers[$controllerName]['class'];
    $controllerFile  = __DIR__ . '/controllers/' . $mapControllers[$controllerName]['file'];
} else {
    $controllerClass = ucfirst($controllerName) . 'Controller';
    $controllerFile  = __DIR__ . "/controllers/{$controllerClass}.php";
}

// Kiểm tra tồn tại file controller
if (!file_exists($controllerFile)) {
    die("❌ Controller '{$controllerClass}' không tồn tại!");
}

require_once $controllerFile;

// Kiểm tra class tồn tại
if (!class_exists($controllerClass)) {
    die("❌ Lớp '{$controllerClass}' không tồn tại!");
}

// Khởi tạo controller
$controller = new $controllerClass();

// Kiểm tra action tồn tại
if (!method_exists($controller, $actionName)) {
    die("❌ Action '{$actionName}' không tồn tại trong controller '{$controllerClass}'!");
}

// Gọi action
$data = $controller->$actionName(); // action trả về dữ liệu (nếu có)

// Include view nếu có dữ liệu trả về
switch ($controllerName) {
    case 'khoanchi':
        if ($actionName == 'index') {
            $khoanchis = $data['khoanchis'] ?? [];
            $currentPage = $data['page'] ?? 1;
            $totalPages = $data['totalPages'] ?? 1;
            include __DIR__ . '/views/user/khoanchi.php';
        } elseif ($actionName == 'edit') {
            $expense = $data['expense'] ?? [];
            include __DIR__ . '/views/edit_expense.php';
        }
        break;

    case 'catagory':
        include __DIR__ . '/views/user/catagories.php';
        break;

    default:
        break;
}
