<?php
require_once __DIR__ . '/../models/KhoanchiModel.php';

class KhoanchiController
{
    private $model;
    private $makh;

    public function __construct()
    {
        $this->model = new KhoanchiModel();
        $this->makh = $_SESSION['id'] ?? 1;
    }

    public function index()
    {

        $makh = $_SESSION['id'] ?? 1;

        $limit = 5;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        // Đếm số khoản chi
        $totalRecords = $this->model->countTotalExpenses($makh);
        $totalPages = ($totalRecords > 0)
        ? ceil($totalRecords / $limit)
        : 1;

        // Lấy danh sách khoản chi
        $khoanchis = $this->model->getPagedExpenses($makh, $limit, $offset);

        // Trả dữ liệu, không include view
        return [
            'khoanchis' => $khoanchis,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords
        ];
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
        }

        $madmchitieu = isset($_POST['madmchitieu']) ? intval($_POST['madmchitieu']) : 0;
        $ngaychitieu = trim($_POST['ngaychitieu'] ?? '');
        $noidung = trim($_POST['noidung'] ?? '');
        $sotienInput = trim($_POST['sotien'] ?? '');

        $loai = 'expense';

        // Validate các trường bắt buộc
        if (!$noidung) {
            return ['success' => false, 'message' => 'Lỗi: Vui lòng nhập tên khoản chi tiêu'];
        }

        if (!$madmchitieu) {
            return ['success' => false, 'message' => 'Lỗi: Vui lòng chọn danh mục'];
        }

        if (!$ngaychitieu) {
            return ['success' => false, 'message' => 'Lỗi: Vui lòng chọn ngày'];
        }

        // Validate số tiền với thông báo cụ thể
        if (!$sotienInput) {
            return ['success' => false, 'message' => 'Lỗi: Vui lòng nhập số tiền'];
        }

        // Kiểm tra nếu giá trị không phải là số
        if (!is_numeric($sotienInput)) {
            return ['success' => false, 'message' => 'Lỗi: Giá trị nhập vào không phải là số. Vui lòng chỉ nhập số (ví dụ: 100000)'];
        }

        $sotien = floatval($sotienInput);

        // Kiểm tra nếu số tiền <= 0
        if ($sotien <= 0) {
            return ['success' => false, 'message' => 'Lỗi: Số tiền phải lớn hơn 0. Vui lòng nhập số dương'];
        }

        // Kiểm tra nếu số tiền không phải là số nguyên
        // So sánh giá trị float với giá trị integer của nó
        if ((int)$sotien != $sotien) {
            return ['success' => false, 'message' => 'Lỗi: Số tiền phải là số nguyên. Vui lòng không nhập số thập phân'];
        }

        $sotien = (int)$sotien;

        $ok = $this->model->addExpense(
            $this->makh,
            $madmchitieu,
            $ngaychitieu,
            $noidung,
            $loai,
            $sotien
        );

        return $ok
            ? ['success' => true, 'message' => 'Thêm khoản chi thành công']
            : ['success' => false, 'message' => 'Không thể thêm khoản chi'];
    }

    /*public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
        }

        $machitieu = intval($_POST['machitieu'] ?? 0);
        $madmchitieu = intval($_POST['madmchitieu'] ?? 0);
        $noidung = trim($_POST['noidung'] ?? '');
        $sotien = floatval($_POST['sotien'] ?? 0);
        $ngaychitieu = $_POST['ngaychitieu'] ?? '';

        if ($machitieu <= 0 || !$madmchitieu || !$noidung || !$ngaychitieu || $sotien <= 0) {
            return ['success' => false, 'message' => 'Dữ liệu không hợp lệ'];
        }

        $ok = $this->model->updateExpense(
            $machitieu,
            $madmchitieu,
            $noidung,
            $ngaychitieu,
            $sotien
        );

        return $ok
            ? ['success' => true, 'message' => 'Cập nhật thành công']
            : ['success' => false, 'message' => 'Không thể cập nhật'];
    }*/

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
        }

        $machitieu = intval($_POST['machitieu'] ?? 0);
        if ($machitieu <= 0) {
            return ['success' => false, 'message' => 'ID không hợp lệ'];
        }

        $ok = $this->model->deleteExpense($machitieu, $this->makh);

        return $ok
            ? ['success' => true, 'message' => 'Đã xóa khoản chi']
            : ['success' => false, 'message' => 'Không thể xóa khoản chi'];
    }

    public function deleteMultiple()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
        }

        $list = $_POST['machitieu_list'] ?? [];
        if (empty($list) || !is_array($list)) {
            return ['success' => false, 'message' => 'Vui lòng chọn ít nhất một khoản chi'];
        }

        $valid = array_filter(array_map('intval', $list), fn($id) => $id > 0);

        $ok = $this->model->deleteMultipleExpenses($valid, $this->makh);

        return $ok
            ? ['success' => true, 'message' => 'Đã xóa ' . count($valid) . ' khoản chi']
            : ['success' => false, 'message' => 'Xóa không hợp lệ'];
    }

    public function getDetail()
    {
        $machitieu = intval($_GET['machitieu'] ?? 0);
        if ($machitieu <= 0) {
            return ['success' => false, 'message' => 'ID không hợp lệ'];
        }

        $data = $this->model->getExpenseById($machitieu);

        return $data
            ? ['success' => true, 'data' => $data]
            : ['success' => false, 'message' => 'Không tìm thấy khoản chi'];
    }

    public function create()
    {
        require_once __DIR__ . '/../models/KhoanchiModel.php';

		$model = new KhoanchiModel();
		$categories = $model->getCategories($makh);

		$currentPage = 'expense';

		ob_start();
		include __DIR__ . '/../views/user/them_khoanchi.php';
		$content = ob_get_clean();

		$cssFiles = [
			'/public/css/khoanchi.css',
			'/public/css/themkhoanchi.css'
		];

		include __DIR__ . '/../views/user/layout.php';
    }

}

if (__FILE__ === realpath($_SERVER['SCRIPT_FILENAME'])) {
    if (isset($_GET['action']) || isset($_POST['action'])) {
        header('Content-Type: application/json');
        $controller = new KhoanchiController();
        $action = $_GET['action'] ?? $_POST['action'];
        switch ($action) {
            case 'deleteMultiple':
                $result = $controller->deleteMultiple();
                echo json_encode($result);
                exit;
            case 'deleteSingle':
                $result = $controller->delete();
                echo json_encode($result);
                exit;
            default:
                echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
                exit;
        }
    }
}

