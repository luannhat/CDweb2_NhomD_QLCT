<?php
require_once __DIR__ . '/BaseModel.php';

class KhoanchiModel extends BaseModel
{
    // Lấy danh sách khoản chi của khách hàng
    public function getExpenses($makh, $search = '', $limit = 10, $offset = 0)
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
                    dm.tendanhmuc as loai,
                    g.sotien,
                    g.ghichu,
                    g.anhhoadon
                FROM GIAODICH g
                INNER JOIN DMCHITIEU dm ON g.machitieu = dm.machitieu
                WHERE g.makh = {$makh} 
                AND g.loai = 'expense'
                {$searchCondition}
                ORDER BY g.ngaygiaodich DESC
                LIMIT {$limit} OFFSET {$offset}";

        return $this->select($sql);
    }

    // Đếm tổng số khoản chi
    public function countExpenses($makh, $search = '')
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
                AND g.loai = 'expense'
                {$searchCondition}";

        $result = $this->select($sql);
        return $result[0]['total'] ?? 0;
    }

    // Lấy thông tin chi tiết một khoản chi
    public function getExpenseById($magd, $makh)
    {
        $sql = "SELECT 
                    g.magd,
                    g.ngaygiaodich,
                    g.noidung,
                    dm.tendanhmuc as loai,
                    g.sotien,
                    g.ghichu,
                    g.anhhoadon,
                    g.created_at
                FROM GIAODICH g
                INNER JOIN DMCHITIEU dm ON g.machitieu = dm.machitieu
                WHERE g.magd = {$magd} 
                AND g.makh = {$makh}
                AND g.loai = 'expense'";

        $result = $this->select($sql);
        return $result[0] ?? null;
    }

    // Thêm khoản chi mới
    public function addExpense($makh, $machitieu, $noidung, $sotien, $ngaygiaodich, $ghichu = '', $anhhoadon = '')
    {
        $noidung = $this->escape($noidung);
        $ghichu = $this->escape($ghichu);
        $anhhoadon = $this->escape($anhhoadon);

        $sql = "INSERT INTO GIAODICH (makh, machitieu, noidung, sotien, loai, ngaygiaodich, ghichu, anhhoadon)
                VALUES ({$makh}, {$machitieu}, '{$noidung}', {$sotien}, 'expense', '{$ngaygiaodich}', '{$ghichu}', '{$anhhoadon}')";

        return $this->insert($sql);
    }

    // Cập nhật khoản chi
    public function updateExpense($magd, $makh, $machitieu, $noidung, $sotien, $ngaygiaodich, $ghichu = '', $anhhoadon = '')
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
                AND loai = 'expense'";

        return $this->update($sql);
    }

    // Xóa khoản chi
    public function deleteExpense($magd, $makh)
    {
        $sql = "DELETE FROM GIAODICH 
                WHERE magd = {$magd} 
                AND makh = {$makh}
                AND loai = 'expense'";

        return $this->delete($sql);
    }

    // Xóa nhiều khoản chi
    public function deleteMultipleExpenses($magdList, $makh)
    {
        if (empty($magdList)) {
            return false;
        }

        $magdList = implode(',', array_map('intval', $magdList));
        $sql = "DELETE FROM GIAODICH 
                WHERE magd IN ({$magdList}) 
                AND makh = {$makh}
                AND loai = 'expense'";

        return $this->delete($sql);
    }

    // Lấy danh sách danh mục chi tiêu
    public function getExpenseCategories($makh)
    {
        $sql = "SELECT machitieu, tendanhmuc 
                FROM DMCHITIEU 
                WHERE makh = {$makh} 
                AND loai = 'expense'
                ORDER BY tendanhmuc";

        return $this->select($sql);
    }

    // Lấy thống kê tổng chi tiêu theo tháng
    public function getMonthlyExpenseStats($makh, $thang, $nam)
    {
        $sql = "SELECT 
                    SUM(sotien) as tong_chitieu,
                    COUNT(*) as so_giao_dich
                FROM GIAODICH 
                WHERE makh = {$makh} 
                AND loai = 'expense'
                AND MONTH(ngaygiaodich) = {$thang}
                AND YEAR(ngaygiaodich) = {$nam}";

        $result = $this->select($sql);
        return $result[0] ?? ['tong_chitieu' => 0, 'so_giao_dich' => 0];
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
