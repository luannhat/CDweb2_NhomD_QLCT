<?php
require_once __DIR__ . '/BaseModel.php';

class KhoanchiModel extends BaseModel
{
    // Lấy tất cả khoản thu từ bảng tổng hợp DSTHUNHAP (không phân trang)
	public function getAllFromDSCHITIEU($makh)
	{
		 $conn = self::$_connection;
		$makh = intval($makh);

		$sql = "SELECT 
					ds.machitieu,
					ds.ngaychitieu,
					ds.noidung,
					dm.tendanhmuc AS tendanhmuc,
					ds.sotien
				FROM DSCHITIEU ds
				JOIN DMCHITIEU dm ON ds.madmchitieu = dm.madmchitieu
				WHERE ds.makh = {$makh}
				ORDER BY ds.ngaychitieu DESC";

		$result = $conn->query($sql);
		$rows = [];
		if ($result && $result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
		}
		return $rows;
		
		}

	public function getKhoanchiById($machitieu){
		$conn = self::$_connection;
		$machitieu = intval($machitieu);
		$sql = "SELECT * FROM DSCHITIEU WHERE machitieu = {$machitieu}";
		$result = $conn->query($sql);

		return $result->fetch_assoc();
	}

    public function getPagedExpenses($makh, $limit = 5, $offset = 0, $search = '')
    {
        $makh = intval($makh);
        $limit = intval($limit);
        $offset = intval($offset);

        $searchCondition = '';
        if (!empty($search)) {
            $search = $this->escape($search);
            $searchCondition = "AND (g.noidung LIKE '%{$search}%' OR dm.tendanhmuc LIKE '%{$search}%')";
        }

        $sql = "SELECT 
                    g.machitieu,
                    g.ngaychitieu,
                    g.noidung,
                    dm.tendanhmuc AS tendanhmuc,
                    g.sotien
                FROM DSCHITIEU g
                INNER JOIN DMCHITIEU dm ON g.madmchitieu = dm.madmchitieu
                WHERE g.makh = {$makh}
                AND g.loai = 'expense'
                {$searchCondition}
                ORDER BY g.ngaychitieu DESC
                LIMIT {$limit} OFFSET {$offset}";

        return $this->select($sql);
    }


	// Lấy khoản chi với phân trang
	public function getPagedIncomes($makh, $limit = 5, $offset = 0)
	{
		$conn = self::$_connection;
		$makh = intval($makh);

		$sql = "SELECT 
					ds.machitieu,
					ds.ngaychitieu,
					ds.noidung,
					dm.tendanhmuc AS tendanhmuc,
					ds.sotien
				FROM DSCHITIEU ds
				JOIN DMCHITIEU dm ON ds.madmchitieu = dm.madmchitieu
				WHERE ds.makh = {$makh}
				ORDER BY ds.ngaychitieu DESC
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
		$sql = "SELECT COUNT(*) AS total FROM DSCHITIEU WHERE makh = {$makh}";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		return $row['total'] ?? 0;
	}

    /**
     * Đếm tổng số khoản chi (có tìm kiếm)
     */
    public function countExpenses($makh, $search = '')
    {
        $conn = self::$_connection;
        $makh = intval($makh);
        $searchCondition = '';

        if (!empty($search)) {
            $search = $this->escape($search);
            $searchCondition = "AND (g.noidung LIKE '%{$search}%' OR dm.tendanhmuc LIKE '%{$search}%')";
        }

        $sql = "SELECT COUNT(*) AS total
                FROM DSKHOANCHI g
                INNER JOIN DMCHITIEU dm ON g.machitieu = dm.machitieu
                WHERE g.makh = {$makh}
                AND g.loai = 'expense'
                {$searchCondition}";

        $result = $this->select($sql);
        return $result[0]['total'] ?? 0;
    }

    public function countTotalExpenses($makh)
    {
        $makh = intval($makh);

        $sql = "SELECT COUNT(*) AS total 
                FROM DSCHITIEU
                WHERE makh = $makh";

        $result = $this->select($sql);
        return $result[0]['total'] ?? 0;
    }



    /**
     * Lấy chi tiết một khoản chi theo ID
     */
    public function getExpenseById($madmchitieu, $makh)
    {
        $conn = self::$_connection;
        $madmchitieu = intval($madmchitieu);
        $makh = intval($makh);

        $sql = "SELECT 
                    g.madmchitieu = dm.madmchitieu,
                    g.ngaychitieu,
                    g.noidung,
                    dm.tendanhmuc AS loai,
                    g.sotien,
                    g.created_at
                FROM DSCHITIEU g
                INNER JOIN DMCHITIEU dm ON g.madmchitieu = dm.madmchitieu
                WHERE g.madmchitieu = {$madmchitieu}
                AND g.makh = {$makh}";

        $result = $this->select($sql);
        return $result[0] ?? null;
    }

    /**
     * Thêm khoản chi mới
     */
    public function addExpense($makh, $madmchitieu, $ngaychitieu, $noidung, $loai, $sotien)
    {
        $conn = self::$_connection;
        $makh = intval($makh);
        $madmchitieu = intval($madmchitieu);
        $ngaychitieu = $this->escape($ngaychitieu);
        $noidung = $this->escape($noidung);
        $loai = $this->escape($loai);
        $sotien = floatval($sotien);

        $sql = "INSERT INTO DSCHITIEU 
                    (makh, madmchitieu, ngaychitieu, noidung, loai, sotien)
                VALUES 
                    ('{$makh}', '{$madmchitieu}', '{$ngaychitieu}', '{$noidung}', '{$loai}', '{$sotien}')";

        return $this->insert($sql);
    }

    /**
     * Cập nhật khoản chi
     */
    public function updateExpense($magd, $makh, $machitieu, $noidung, $sotien, $ngaygiaodich, $ghichu = '', $anhhoadon = '')
    {
        $conn = self::$_connection;
        $magd = intval($magd);
        $makh = intval($makh);
        $machitieu = intval($machitieu);
        $sotien = floatval($sotien);

        $noidung = $this->escape($noidung);
        $ghichu = $this->escape($ghichu);
        $anhhoadon = $this->escape($anhhoadon);

        $sql = "UPDATE GIAODICH 
                SET 
                    machitieu = {$machitieu},
                    noidung = '{$noidung}',
                    sotien = {$sotien},
                    ngaygiaodich = '{$ngaygiaodich}',
                    ghichu = '{$ghichu}',
                    anhhoadon = '{$anhhoadon}',
                    updated_at = CURRENT_TIMESTAMP
                WHERE magd = {$magd}
                AND makh = {$makh}
                AND loai = 'expense'";

        return $this->update($sql);
    }

    /**
     * Xóa khoản chi đơn
     */
    public function deleteExpense($magd, $makh)
    {
        $magd = intval($magd);
        $makh = intval($makh);

        $sql = "DELETE FROM GIAODICH 
                WHERE magd = {$magd}
                AND makh = {$makh}
                AND loai = 'expense'";

        return $this->delete($sql);
    }

    /**
     * Xóa nhiều khoản chi
     */
    public function deleteMultipleExpenses($magdList, $makh)
    {
        if (empty($magdList)) {
            return false;
        }

        $validIds = array_filter(array_map('intval', $magdList), fn($id) => $id > 0);
        if (empty($validIds)) {
            return false;
        }

        $makh = intval($makh);
        $idString = implode(',', $validIds);

        $sql = "DELETE FROM GIAODICH 
                WHERE magd IN ({$idString})
                AND makh = {$makh}
                AND loai = 'expense'";

        $affectedRows = $this->delete($sql);
        return $affectedRows > 0;
    }

    /**
     * Lấy danh mục chi tiêu của khách hàng
     */
    public function getExpenseCategories($makh)
    {
        $makh = intval($makh);

        $sql = "SELECT madmchitieu, tendanhmuc 
                FROM DMCHITIEU 
                WHERE makh = {$makh}
                AND loai = 'expense'
                ORDER BY tendanhmuc ASC";

        return $this->select($sql);
    }

    /**
     * Thống kê chi tiêu theo tháng
     */
    public function getMonthlyExpenseStats($makh, $thang, $nam)
    {
        $makh = intval($makh);
        $thang = intval($thang);
        $nam = intval($nam);

        $sql = "SELECT 
                    SUM(sotien) AS tong_chitieu,
                    COUNT(*) AS so_giao_dich
                FROM GIAODICH 
                WHERE makh = {$makh}
                AND loai = 'expense'
                AND MONTH(ngaygiaodich) = {$thang}
                AND YEAR(ngaygiaodich) = {$nam}";

        $result = $this->select($sql);
        return $result[0] ?? ['tong_chitieu' => 0, 'so_giao_dich' => 0];
    }

    /**
     * Escape string để tránh SQL Injection
     */
    private function escape($string)
    {
        return self::$_connection 
            ? self::$_connection->real_escape_string($string)
            : addslashes($string);
    }
}
