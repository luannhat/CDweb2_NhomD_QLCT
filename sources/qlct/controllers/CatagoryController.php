<?php
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
        $makh = $_SESSION['makh'] ?? 1;
        $danhmucs = $this->model->getAllCatagory($makh);
        return $danhmucs; 
        
    }


    public function store()
    {
        $makh = $_SESSION['makh'] ?? 1;
        $tendanhmuc = $_POST['tendanhmuc'] ?? '';
        $loai = $_POST['loaidanhmuc'] ?? '';

        if (trim($tendanhmuc) === '' || trim($loai) === '') {
            echo "⚠️ Vui lòng nhập đầy đủ thông tin!";
            exit;
        }

        $result = $this->model->insertCatagories($makh, $tendanhmuc, $loai);
        if ($result['success']) {
            header("Location: ../views/catagories.php");
            exit;
        } else {
            echo "❌ Lỗi khi thêm danh mục: " . $result['message'];
        }
    }

    public function delete()
    {
        // Lấy mã danh mục từ GET
        $madmchitieu = $_GET['madmchitieu'] ?? 0;
        $madmchitieu = intval($madmchitieu);

        if ($madmchitieu <= 0) {
            echo "⚠️ Mã danh mục không hợp lệ!";
            exit;
        }

        // Gọi model để xóa
        $result = $this->model->deleteCatagories($madmchitieu);

        if ($result['success']) {
            // Xóa thành công → quay về trang danh sách
            header("Location: ../views/catagories.php");
            exit;
        } else {
            echo "❌ Lỗi khi xóa danh mục: " . $result['message'];
        }
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

        case 'index':
        default:
            $controller->index();
            break;
    }
}
