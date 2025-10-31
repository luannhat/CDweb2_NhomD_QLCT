<?php
require_once __DIR__ . '/../models/KhoanchiModel.php';

class KhoanchiController
{
    private $khoanchiModel;
    private $makh; // ID khách hàng hiện tại (sẽ lấy từ session)

    public function __construct()
    {
        $this->khoanchiModel = new KhoanchiModel();
        // TODO: Lấy makh từ session khi có hệ thống đăng nhập
        $this->makh = 1; // Tạm thời hardcode cho demo
    }

    // Hiển thị danh sách khoản chi
    public function index()
    {
        $search = $_GET['q'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $expenses = $this->khoanchiModel->getExpenses($this->makh, $search, $limit, $offset);
            $totalExpenses = $this->khoanchiModel->countExpenses($this->makh, $search);
            $totalPages = ceil($totalExpenses / $limit);

            return [
                'expenses' => $expenses,
                'totalExpenses' => $totalExpenses,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'search' => $search
            ];
        } catch (Exception $e) {
            error_log("Lỗi khi lấy danh sách khoản chi: " . $e->getMessage());
            return [
                'expenses' => [],
                'totalExpenses' => 0,
                'currentPage' => 1,
                'totalPages' => 1,
                'search' => $search,
                'error' => 'Có lỗi xảy ra khi tải dữ liệu'
            ];
        }
    }

    // Thêm khoản chi mới
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
        }

        $machitieu = intval($_POST['machitieu'] ?? 0);
        $noidung = trim($_POST['noidung'] ?? '');
        $sotien = floatval($_POST['sotien'] ?? 0);
        $ngaygiaodich = $_POST['ngaygiaodich'] ?? '';
        $ghichu = trim($_POST['ghichu'] ?? '');
        $anhhoadon = $_POST['anhhoadon'] ?? '';

        // Validate dữ liệu
        if (empty($noidung)) {
            return ['success' => false, 'message' => 'Nội dung không được để trống'];
        }

        if ($sotien <= 0) {
            return ['success' => false, 'message' => 'Số tiền phải lớn hơn 0'];
        }

        if (empty($ngaygiaodich)) {
            return ['success' => false, 'message' => 'Ngày giao dịch không được để trống'];
        }

        if ($machitieu <= 0) {
            return ['success' => false, 'message' => 'Vui lòng chọn danh mục chi tiêu'];
        }

        try {
            $result = $this->khoanchiModel->addExpense(
                $this->makh,
                $machitieu,
                $noidung,
                $sotien,
                $ngaygiaodich,
                $ghichu,
                $anhhoadon
            );

            if ($result) {
                return ['success' => true, 'message' => 'Thêm khoản chi thành công'];
            } else {
                return ['success' => false, 'message' => 'Có lỗi xảy ra khi thêm khoản chi'];
            }
        } catch (Exception $e) {
            error_log("Lỗi khi thêm khoản chi: " . $e->getMessage());
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi thêm khoản chi'];
        }
    }

    // Cập nhật khoản chi
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
        }

        $magd = intval($_POST['magd'] ?? 0);
        $machitieu = intval($_POST['machitieu'] ?? 0);
        $noidung = trim($_POST['noidung'] ?? '');
        $sotien = floatval($_POST['sotien'] ?? 0);
        $ngaygiaodich = $_POST['ngaygiaodich'] ?? '';
        $ghichu = trim($_POST['ghichu'] ?? '');
        $anhhoadon = $_POST['anhhoadon'] ?? '';

        // Validate dữ liệu
        if ($magd <= 0) {
            return ['success' => false, 'message' => 'ID giao dịch không hợp lệ'];
        }

        if (empty($noidung)) {
            return ['success' => false, 'message' => 'Nội dung không được để trống'];
        }

        if ($sotien <= 0) {
            return ['success' => false, 'message' => 'Số tiền phải lớn hơn 0'];
        }

        if (empty($ngaygiaodich)) {
            return ['success' => false, 'message' => 'Ngày giao dịch không được để trống'];
        }

        if ($machitieu <= 0) {
            return ['success' => false, 'message' => 'Vui lòng chọn danh mục chi tiêu'];
        }

        try {
            $result = $this->khoanchiModel->updateExpense(
                $magd,
                $this->makh,
                $machitieu,
                $noidung,
                $sotien,
                $ngaygiaodich,
                $ghichu,
                $anhhoadon
            );

            if ($result) {
                return ['success' => true, 'message' => 'Cập nhật khoản chi thành công'];
            } else {
                return ['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật khoản chi'];
            }
        } catch (Exception $e) {
            error_log("Lỗi khi cập nhật khoản chi: " . $e->getMessage());
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật khoản chi'];
        }
    }

    // Xóa khoản chi
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
        }

        $magd = intval($_POST['magd'] ?? 0);

        if ($magd <= 0) {
            return ['success' => false, 'message' => 'ID giao dịch không hợp lệ'];
        }

        try {
            $result = $this->khoanchiModel->deleteExpense($magd, $this->makh);

            if ($result) {
                return ['success' => true, 'message' => 'Xóa khoản chi thành công'];
            } else {
                return ['success' => false, 'message' => 'Có lỗi xảy ra khi xóa khoản chi'];
            }
        } catch (Exception $e) {
            error_log("Lỗi khi xóa khoản chi: " . $e->getMessage());
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi xóa khoản chi'];
        }
    }

    // Xóa nhiều khoản chi
    public function deleteMultiple()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
        }

        $magdList = $_POST['magd_list'] ?? [];

        if (empty($magdList) || !is_array($magdList)) {
            return ['success' => false, 'message' => 'Vui lòng chọn khoản chi cần xóa'];
        }

        // Validate tất cả ID
        $validIds = [];
        foreach ($magdList as $id) {
            $id = intval($id);
            if ($id > 0) {
                $validIds[] = $id;
            }
        }

        if (empty($validIds)) {
            return ['success' => false, 'message' => 'Không có ID hợp lệ để xóa'];
        }

        try {
            $result = $this->khoanchiModel->deleteMultipleExpenses($validIds, $this->makh);

            if ($result) {
                return ['success' => true, 'message' => 'Xóa ' . count($validIds) . ' khoản chi thành công'];
            } else {
                return ['success' => false, 'message' => 'Có lỗi xảy ra khi xóa khoản chi'];
            }
        } catch (Exception $e) {
            error_log("Lỗi khi xóa nhiều khoản chi: " . $e->getMessage());
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi xóa khoản chi'];
        }
    }

    // Lấy thông tin chi tiết khoản chi
    public function getDetail()
    {
        $magd = intval($_GET['magd'] ?? 0);

        if ($magd <= 0) {
            return ['success' => false, 'message' => 'ID giao dịch không hợp lệ'];
        }

        try {
            $expense = $this->khoanchiModel->getExpenseById($magd, $this->makh);

            if ($expense) {
                return ['success' => true, 'data' => $expense];
            } else {
                return ['success' => false, 'message' => 'Không tìm thấy khoản chi'];
            }
        } catch (Exception $e) {
            error_log("Lỗi khi lấy chi tiết khoản chi: " . $e->getMessage());
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi tải dữ liệu'];
        }
    }

    // Lấy danh sách danh mục chi tiêu
    public function getCategories()
    {
        try {
            $categories = $this->khoanchiModel->getExpenseCategories($this->makh);
            return ['success' => true, 'data' => $categories];
        } catch (Exception $e) {
            error_log("Lỗi khi lấy danh mục chi tiêu: " . $e->getMessage());
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi tải danh mục'];
        }
    }

    // Xử lý AJAX requests
    public function handleAjax()
    {
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'add':
                return $this->add();
            case 'update':
                return $this->update();
            case 'delete':
                return $this->delete();
            case 'delete_multiple':
                return $this->deleteMultiple();
            case 'get_detail':
                return $this->getDetail();
            case 'get_categories':
                return $this->getCategories();
            default:
                return ['success' => false, 'message' => 'Action không hợp lệ'];
        }
    }
}
