<?php
session_start();
// Cấu hình tự động nạp class
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

// Lấy controller và action từ URL
$controllerName = $_GET['controller'] ?? 'transaction';  // controller mặc định
$actionName = $_GET['action'] ?? 'index';                 // action mặc định

// Chuyển controllerName về dạng PascalCase, thêm hậu tố "Controller"
$controllerClass = ucfirst($controllerName) . 'Controller';

// Đường dẫn tới file controller
$controllerFile = __DIR__ . "/controllers/{$controllerClass}.php";

// Kiểm tra tồn tại controller
if (!file_exists($controllerFile)) {
    die("❌ Controller '{$controllerClass}' không tồn tại!");
}

// Gọi file controller
require_once $controllerFile;

// Kiểm tra class có tồn tại không
if (!class_exists($controllerClass)) {
    die("❌ Lớp '{$controllerClass}' không tồn tại trong file controller!");
}

// Khởi tạo controller
$controller = new $controllerClass();

// Kiểm tra action có tồn tại không
if (!method_exists($controller, $actionName)) {
    die("❌ Action '{$actionName}' không tồn tại trong controller '{$controllerClass}'!");
}

// Gọi action tương ứng
$controller->$actionName();
