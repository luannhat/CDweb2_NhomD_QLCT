<?php
require_once __DIR__ . '/../models/KhoanthuModel.php';

class KhoanthuController
{
	private $khoanthuModel;
	private $makh; // ID khách hàng hiện tại (tạm thời hardcode)

	public function __construct()
	{
		$this->khoanthuModel = new KhoanthuModel();
		$this->makh = 1; // TODO: lấy từ session khi có đăng nhập
	}

	// Hiển thị danh sách khoản thu
	public function index()
	{
		$makh = $_SESSION['makh'] ?? 1;

		// --- Thiết lập phân trang ---
		$limit = 5; // số bản ghi mỗi trang
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		if ($page < 1) $page = 1;
		$offset = ($page - 1) * $limit;

		// --- Lấy dữ liệu ---
		$totalRecords = $this->khoanthuModel->countTotalIncomes($makh);
		$totalPages = ceil($totalRecords / $limit);
		$khoanthus = $this->khoanthuModel->getPagedIncomes($makh, $limit, $offset);

		// --- Trả về dữ liệu view ---
		$result = [
			'khoanthus' => $khoanthus,
			'page' => $page,
			'totalPages' => $totalPages
		];
		include './views/khoanthu.php';
	}
	// Thêm khoản thu mới
	public function add()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
		}

		$madmthunhap = intval($_POST['madmthunhap'] ?? 0);
		$noidung = trim($_POST['noidung'] ?? '');
		$sotien = floatval($_POST['sotien'] ?? 0);
		$ngaygiaodich = $_POST['ngaygiaodich'] ?? '';


		if (empty($noidung)) {
			return ['success' => false, 'message' => 'Nội dung không được để trống'];
		}
		if ($sotien <= 0) {
			return ['success' => false, 'message' => 'Số tiền phải lớn hơn 0'];
		}
		if (empty($ngaygiaodich)) {
			return ['success' => false, 'message' => 'Ngày giao dịch không được để trống'];
		}
		if ($madmthunhap <= 0) {
			return ['success' => false, 'message' => 'Vui lòng chọn danh mục khoản thu'];
		}

		try {
			$result = $this->khoanthuModel->addIncome(
				$this->makh,
				$madmthunhap,
				$noidung,
				$sotien,
				'income', //loai
				$ngaygiaodich //ngay thu
			);

			if ($result) {
				return ['success' => true, 'message' => 'Thêm khoản thu thành công'];
			} else {
				return ['success' => false, 'message' => 'Có lỗi xảy ra khi thêm khoản thu'];
			}
		} catch (Exception $e) {
			error_log("Lỗi khi thêm khoản thu: " . $e->getMessage());
			return ['success' => false, 'message' => 'Có lỗi xảy ra khi thêm khoản thu'];
		}
	}

	// Lấy danh sách danh mục khoản thu
	public function getCategories()
	{
		try {
			$categories = $this->khoanthuModel->getAllCategoriesDistinct($this->makh);
			return ['success' => true, 'data' => $categories];
		} catch (Exception $e) {
			error_log("Lỗi khi lấy danh mục khoản thu: " . $e->getMessage());
			return ['success' => false, 'message' => 'Có lỗi xảy ra khi tải danh mục'];
		}
	}
}
