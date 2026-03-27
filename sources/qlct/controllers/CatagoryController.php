<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/CatagoryModel.php';

class DanhmucController
{
    private $model;
    public function __construct()
    {
        $this->model = new DanhmucModel();
    }

    public function index()
    {
        if (!isset($_SESSION['makh'])) {
            die(" Chưa đăng nhập!");
        }

        $makh = $_SESSION['makh'];
        $danhmucs = $this->model->getAllCatagory($makh);
        return $danhmucs;
        ob_start();
        require __DIR__ . '/../views/user/catagories.php'; // view danh mục
        $content = ob_get_clean();

        require __DIR__ . '/../views/user/layout.php';
    }


    public function store()
    {
        if (!isset($_SESSION['makh'])) {
            die(" Chưa đăng nhập!");
        }

        $makh = $_SESSION['makh'];
        $tendanhmuc = $_POST['tendanhmuc'] ?? '';
        $loai = $_POST['loaidanhmuc'] ?? '';

        if (trim($tendanhmuc) === '' || trim($loai) === '') {
            $_SESSION['error_msg'] = "⚠️ Vui lòng nhập đầy đủ thông tin!";
            header("Location: /index.php?controller=catagory&action=add"); // quay lại trang thêm
            exit;
        }

        $result = $this->model->insertCatagories($makh, $tendanhmuc, $loai);

        if ($result['success']) {
            $_SESSION['success_msg'] = " Thêm danh mục thành công!";
            header("Location: /index.php?controller=catagory&action=add"); // quay về danh sách
            exit;
        } else {
            $_SESSION['error_msg'] = " Lỗi khi thêm danh mục: " . $result['message'];
            header("Location: /index.php?controller=catagory&action=add"); // quay lại trang thêm
            exit;
        }
    }
    public function add()
    {
        if (!isset($_SESSION['makh'])) {
            header("Location: ../../login.php");
            exit;
        }

        $makh = $_SESSION['makh'];
        $danhmucs = $this->model->getAllCatagory($makh);

        ob_start();
        require __DIR__ . '/../views/user/add_catagories.php';
        $content = ob_get_clean();

        require __DIR__ . '/../views/user/layout.php';
    }

    public function delete()
    {
        if (!isset($_SESSION['makh'])) die(" Chưa đăng nhập!");

        $madmchitieu = intval($_GET['madmchitieu'] ?? 0);
        $loai = $_GET['loai'] ?? '';

        if ($madmchitieu <= 0 || ($loai !== 'income' && $loai !== 'expense')) {
            die("⚠️ Thông tin xóa không hợp lệ!");
        }

        $result = $this->model->deleteCatagories($madmchitieu, $loai);

        if ($result['success']) {
            $_SESSION['success_message'] = " Xóa danh mục thành công!";
        } else {
            $_SESSION['error_message'] = " Lỗi xóa: " . $result['message'];
        }

        // Redirect trực tiếp về file catagories.php
        header("Location: /views/user/catagories.php");
        exit;
    }
}
if (isset($_GET['action'])) {
    $controller = new DanhmucController();

    switch ($_GET['action']) {
        case 'store':
            $controller->store();
            break;

        case 'delete':
            $controller->delete();
            break;

        default:
            $controller->index();
            break;
    }
}
