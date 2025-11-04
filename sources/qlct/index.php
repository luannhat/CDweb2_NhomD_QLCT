<?php
// index.php — file chính của ứng dụng PHP MVC

// require_once 'controllers/UploadAvatarController.php';

// // Lấy action trên URL
// $action = $_GET['action'] ?? 'upload_avatar';

// // Tạo controller
// $controller = new UploadAvatarController();

// // Điều hướng theo action
// switch ($action) {
//     case 'upload_avatar_submit':
//         $controller->uploadAvatarSubmit();
//         break;
//     case 'upload_avatar':
//     default:
//         $controller->index();
//         break;
// }

require_once 'controllers/StatisticalController.php';

$controller = new StatisticalController();
$controller->index();
