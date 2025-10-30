<?php
require_once __DIR__ . '/BaseModel.php';

class KhoanthuModel extends BaseModel
{
	// Lấy tất cả khoản thu từ bảng tổng hợp DSTHUNHAP (không phân trang)
	public function getAllFromDSTHUNHAP($makh)
	{
		$conn = self::$_connection;
		$makh = intval($makh);
		$sql = "SELECT * FROM DSTHUNHAP WHERE makh = {$makh} ORDER BY created_at DESC";
		$result = $conn->query($sql);
		$rows = [];
		if ($result && $result ->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$row[]= $row;
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
				INNER JOIN DMCHITIEU dm ON g.machitieu = dm.machitieu
				WHERE g.makh = {$makh} 
				AND g.loai = 'income'
				{$searchCondition}
				ORDER BY g.ngaygiaodich DESC
				LIMIT {$limit} OFFSET {$offset}";

		return $this->select($sql);
	}

	// Đếm tổng số khoản thu
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
				AND g.loai = 'income'
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
				INNER JOIN DMCHITIEU dm ON g.machitieu = dm.machitieu
				WHERE g.magd = {$magd} 
				AND g.makh = {$makh}
				AND g.loai = 'income'";

		$result = $this->select($sql);
		return $result[0] ?? null;
	}

	// Thêm khoản thu mới
	public function addIncome($makh, $machitieu, $noidung, $sotien, $ngaythunhap)
	{
		$noidung = $this->escape($noidung);

		$sql = "INSERT INTO DSTHUNHAP (makh, machitieu, noidung, sotien, loai, ngaythunhap)
				VALUES ({$makh}, {$machitieu}, '{$noidung}', {$sotien}, 'income', '{$ngaythunhap}')";

		return $this->insert($sql);
	}

	// Cập nhật khoản thu
	public function updateIncome($magd, $makh, $machitieu, $noidung, $sotien, $ngaygiaodich, $ghichu = '', $anhhoadon = '')
	{
		$noidung = $this->escape($noidung);
		$ghichu = $this->escape($ghichu);
		$anhhoadon = $this->escape($anhhoadon);

		$sql = "UPDATE GIAODICH 
				SET machitieu = {$machitieu},
					noidung = '{$noidung}',
					sotien = {$sotien},
					ngaygiaodich = '{$ngaygiaodich}',
					ghichu = '{$ghichu}',
					anhhoadon = '{$anhhoadon}',
					updated_at = CURRENT_TIMESTAMP
				WHERE magd = {$magd} 
				AND makh = {$makh}
				AND loai = 'income'";

		return $this->update($sql);
	}

	// Xóa khoản thu
	public function deleteIncome($magd, $makh)
	{
		$sql = "DELETE FROM GIAODICH 
				WHERE magd = {$magd} 
				AND makh = {$makh}
				AND loai = 'income'";

		return $this->delete($sql);
	}

	// Xóa nhiều khoản thu
	public function deleteMultipleIncomes($magdList, $makh)
	{
		if (empty($magdList)) {
			return false;
		}

		$validIds = [];
		foreach ($magdList as $id) {
			$id = intval($id);
			if ($id > 0) {
				$validIds[] = $id;
			}
		}

		if (empty($validIds)) {
			return false;
		}

		$magdList = implode(',', $validIds);
		$sql = "DELETE FROM GIAODICH 
				WHERE magd IN ({$magdList}) 
				AND makh = " . intval($makh) . "
				AND loai = 'income'";

		$affectedRows = $this->delete($sql);
		return $affectedRows > 0;
	}

	// Lấy danh sách danh mục khoản thu
	public function getIncomeCategories($makh)
	{
		$sql = "SELECT machitieu, tendanhmuc 
				FROM DMCHITIEU 
				WHERE makh = {$makh} 
				AND loai = 'income'
				ORDER BY tendanhmuc";

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
}



