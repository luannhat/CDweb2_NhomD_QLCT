<?php
require_once __DIR__ . '/BaseModel.php';

class KhoanthuModel extends BaseModel
{
	// Lấy tất cả khoản thu từ bảng tổng hợp DSTHUNHAP (không phân trang)
	public function getAllFromDSTHUNHAP($makh)
	{
		 $conn = self::$_connection;
		$makh = intval($makh);

		$sql = "SELECT 
					ds.mathunhap,
					ds.ngaythunhap,
					ds.noidung,
					dm.tendanhmuc AS tendanhmuc,
					ds.sotien
				FROM DSTHUNHAP ds
				JOIN DMTHUNHAP dm ON ds.madmthunhap = dm.madmthunhap
				WHERE ds.makh = {$makh}
				ORDER BY ds.ngaythunhap DESC";

		$result = $conn->query($sql);
		$rows = [];
		if ($result && $result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
		}
		return $rows;
		
		}

	public function getKhoanthuById($mathunhap){
		$conn = self::$_connection;
		$mathunhap = intval($mathunhap);
		$sql = "SELECT * FROM DSTHUNHAP WHERE mathunhap = {$mathunhap}";
		$result = $conn->query($sql);

		return $result->fetch_assoc();
	}

	// Lấy khoản thu với phân trang
	public function getPagedIncomes($makh, $limit = 5, $offset = 0)
	{
		$conn = self::$_connection;
		$makh = intval($makh);

		$sql = "SELECT 
					ds.mathunhap,
					ds.ngaythunhap,
					ds.noidung,
					dm.tendanhmuc AS tendanhmuc,
					ds.sotien
				FROM DSTHUNHAP ds
				JOIN DMTHUNHAP dm ON ds.madmthunhap = dm.madmthunhap
				WHERE ds.makh = {$makh}
				ORDER BY ds.ngaythunhap DESC
				LIMIT {$limit} OFFSET {$offset}";

		$result = $conn->query($sql);
		$rows = [];
		if ($result && $result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
		}
		return $rows;
	}

	public function countTotalIncomes($makh)
	{
		$conn = self::$_connection;
		$makh = intval($makh);
		$sql = "SELECT COUNT(*) AS total FROM DSTHUNHAP WHERE makh = {$makh}";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		return $row['total'] ?? 0;
	}

	
	// Lấy danh sách khoản thu của khách hàng
	public function getIncomes($makh, $search = '', $limit = 10, $offset = 0)
	{
		$searchCondition = '';
		if (!empty($search)) {
			$search = $this->escape($search);
			$searchCondition = "AND (g.noidung LIKE '%{$search}%' OR dm.tendanhmuc LIKE '%{$search}%')";
		}

		$sql = "SELECT 
					g.magd,
					g.ngaygiaodich,
					g.noidung,
					dm.tendanhmuc as nguonthu,
					g.sotien,
					g.ghichu,
					g.anhhoadon
				FROM GIAODICH g
				INNER JOIN DMTHUNHAP dm ON g.mathunhap = dm.mathunhap
				WHERE g.makh = {$makh} 
				AND g.loai = {$loai}
				{$searchCondition}
				ORDER BY g.ngaygiaodich DESC
				LIMIT {$limit} OFFSET {$offset}";

		return $this->select($sql);
	}

	// Đếm tổng số khoản thu (để làm phân trang)
	public function countIncomes($makh, $search = '')
	{
		$searchCondition = '';
		if (!empty($search)) {
			$search = $this->escape($search);
			$searchCondition = "AND (g.noidung LIKE '%{$search}%' OR dm.tendanhmuc LIKE '%{$search}%')";
		}

		$sql = "SELECT COUNT(*) as total
				FROM GIAODICH g
				INNER JOIN DMCHITIEU dm ON g.machitieu = dm.machitieu
				WHERE g.makh = {$makh} 
				AND g.loai = {$loai} --Nếu mà để income nó chỉ kiếm kh có income xuất ra và bỏ qua expense
				{$searchCondition}";

		$result = $this->select($sql);
		return $result[0]['total'] ?? 0;
	}

	// Lấy thông tin chi tiết một khoản thu
	public function getIncomeById($magd, $makh)
	{
		$sql = "SELECT 
					g.magd,
					g.ngaygiaodich,
					g.noidung,
					dm.tendanhmuc as nguonthu,
					g.sotien,
					g.ghichu,
					g.anhhoadon,
					g.created_at
				FROM GIAODICH g
				INNER JOIN DMTHUNHAP dm ON g.mathunhap = dm.mathunhap
				WHERE g.magd = {$magd} 
				AND g.makh = {$makh}
				AND g.loai = {$loai}";

		$result = $this->select($sql);
		return $result[0] ?? null;
	}

	// Thêm khoản thu mới
	public function addIncome($makh, $madmthunhap, $noidung, $sotien, $loai, $ngaythunhap)
	{
		$noidung = $this->escape($noidung);
		$loai = $this->escape($loai);

		$sql = "INSERT INTO DSTHUNHAP (makh, madmthunhap, noidung, sotien, loai, ngaythunhap)
				VALUES ({$makh}, {$madmthunhap}, '{$noidung}', {$sotien}, '{$loai}', '{$ngaythunhap}')";

		// Ghi log câu SQL để xem thực tế chèn gì
		error_log("SQL addIncome: " . $sql);

		$result = $this->insert($sql);

		// Nếu lỗi SQL, ghi log chi tiết lỗi MySQL
		if (!$result && self::$_connection->error) {
			error_log("MySQL error: " . self::$_connection->error);
		}

		return $result;
	}



	// // Xóa khoản thu
	// public function deleteIncome($magd, $makh)
	// {
	// 	$sql = "DELETE FROM GIAODICH 
	// 			WHERE magd = {$magd} 
	// 			AND makh = {$makh}
	// 			AND loai = 'income'";

	// 	return $this->delete($sql);
	// }

	// // Xóa nhiều khoản thu
	// public function deleteMultipleIncomes($magdList, $makh)
	// {
	// 	if (empty($magdList)) {
	// 		return false;
	// 	}

	// 	$validIds = [];
	// 	foreach ($magdList as $id) {
	// 		$id = intval($id);
	// 		if ($id > 0) {
	// 			$validIds[] = $id;
	// 		}
	// 	}

	// 	if (empty($validIds)) {
	// 		return false;
	// 	}

	// 	$magdList = implode(',', $validIds);
	// 	$sql = "DELETE FROM GIAODICH 
	// 			WHERE magd IN ({$magdList}) 
	// 			AND makh = " . intval($makh) . "
	// 			AND loai = 'income'";

	// 	$affectedRows = $this->delete($sql);
	// 	return $affectedRows > 0;
	// }

	// Lấy danh sách danh mục khoản thu
	// public function getIncomeCategories($makh)
	// {
	// 	$sql = "SELECT mathunhap, tendanhmuc
	// 			FROM DMTHUNHAP 
	// 			WHERE makh = {$makh} 
	// 			AND loai = 'income'
	// 			ORDER BY tendanhmuc";

	// 	return $this->select($sql);
	// }

	// Lấy tất cả danh mục không trùng lặp
	public function getAllCategoriesDistinct()
	{
		$sql = "SELECT 
					MIN(madmthunhap) AS madmthunhap, 
					tendanhmuc
				FROM DMTHUNHAP
				GROUP BY tendanhmuc
				ORDER BY tendanhmuc ASC";

		return $this->select($sql);
	}



	// Escape string để tránh SQL injection
	private function escape($string)
	{
		if (self::$_connection) {
			return self::$_connection->real_escape_string($string);
		}
		return addslashes($string);
	}

	//lay ten danh muc theo bang dm thu nhap theo makh
	public function getCategoryByIncome($mathunhap, $makh)
	{
		$sql = "SELECT 
					ds.mathunhap,
					ds.noidung,
					ds.ngaythunhap,
					ds.sotien,
					dm.tendanhmuc AS tendanhmuc
				FROM DSTHUNHAP ds
				JOIN DMTHUNHAP dm ON ds.madmthunhap = dm.madmthunhap
				WHERE ds.mathunhap = ? AND ds.makh = ?";

		$stmt = self::$_connection->prepare($sql);
		$stmt->bind_param("ii", $mathunhap, $makh);
		$stmt->execute();
		$result = $stmt->get_result();

		return $result->fetch_assoc();
	}

}



