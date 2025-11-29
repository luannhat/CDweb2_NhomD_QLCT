<?php
require_once __DIR__ . '/BaseModel.php';

class SpendingModel extends BaseModel
{
    private $table = 'KHOANCHI';

    // Lấy danh sách khoản chi, hỗ trợ tìm kiếm và phân trang
    public function getExpenses($search = '', $limit = 10, $offset = 0)
    {
        $searchCondition = '';
        if (!empty($search)) {
            $search = $this->escape($search);
            $searchCondition = "AND tenkhoanchi LIKE '%{$search}%'";
        }

        $sql = "SELECT machi, tenkhoanchi, sotien, madanhmuc, ngaybatdau, lapphieu
                FROM {$this->table}
                WHERE 1=1 {$searchCondition}
                ORDER BY ngaybatdau DESC
                LIMIT {$limit} OFFSET {$offset}";

        return $this->select($sql);
    }

    // Đếm tổng số khoản chi (có thể tìm kiếm)
    public function countExpenses($search = '')
    {
        $searchCondition = '';
        if (!empty($search)) {
            $search = $this->escape($search);
            $searchCondition = "AND tenkhoanchi LIKE '%{$search}%'";
        }

        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE 1=1 {$searchCondition}";

        $result = $this->select($sql);
        return $result[0]['total'] ?? 0;
    }

    // Lấy chi tiết một khoản chi theo ID
    public function getExpenseById($machi)
    {
        $sql = "SELECT * FROM {$this->table} WHERE machi = {$machi} LIMIT 1";
        $result = $this->select($sql);
        return $result[0] ?? null;
    }

    // Thêm khoản chi mới
    public function addExpense($tenkhoanchi, $sotien, $madanhmuc, $ngaybatdau, $lapphieu = 'Không lặp lại')
    {
        // Use prepared insert to avoid SQL injection
        $sql = "INSERT INTO {$this->table} (tenkhoanchi, sotien, madanhmuc, ngaybatdau, lapphieu)
            VALUES (?, ?, ?, ?, ?)";
        return $this->insertPrepared($sql, 'sdiss', [$tenkhoanchi, $sotien, $madanhmuc, $ngaybatdau, $lapphieu]);
    }

    // Cập nhật khoản chi
    public function updateExpense($machi, $tenkhoanchi, $sotien, $madanhmuc, $ngaybatdau, $lapphieu)
    {
        $tenkhoanchi = $this->escape($tenkhoanchi);
        $lapphieu = $this->escape($lapphieu);

        $sql = "UPDATE {$this->table}
                SET tenkhoanchi = '{$tenkhoanchi}',
                    sotien = {$sotien},
                    madanhmuc = {$madanhmuc},
                    ngaybatdau = '{$ngaybatdau}',
                    lapphieu = '{$lapphieu}'
                WHERE machi = {$machi}";

        return $this->update($sql);
    }

    // Xóa khoản chi
    public function deleteExpense($machi)
    {
        $sql = "DELETE FROM {$this->table} WHERE machi = {$machi}";
        return $this->delete($sql);
    }

    // Xóa nhiều khoản chi
    public function deleteMultipleExpenses(array $ids)
    {
        $validIds = array_filter(array_map('intval', $ids), fn($id) => $id > 0);
        if (empty($validIds)) return false;

        $idList = implode(',', $validIds);
        $sql = "DELETE FROM {$this->table} WHERE machi IN ({$idList})";
        return $this->delete($sql);
    }

    // Lấy danh sách danh mục chi tiêu của người dùng (DMCHITIEU)
    public function getExpenseCategories($makh)
    {
        $sql = "SELECT machitieu, tendanhmuc FROM DMCHITIEU WHERE makh = ? AND loai = 'expense' ORDER BY tendanhmuc ASC";
        if (!self::$_connection) return [];
        $stmt = self::$_connection->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('i', $makh);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($row = $res->fetch_assoc()) {
            $out[] = $row;
        }
        $stmt->close();
        return $out;
    }

    // Prepared insert helper (safer than building SQL strings)
    public function insertPrepared($sql, $types, $params)
    {
        if (!self::$_connection) return false;
        $stmt = self::$_connection->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param($types, ...$params);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function updatePrepared($sql, $types, $params)
    {
        return $this->insertPrepared($sql, $types, $params);
    }

    /**
     * Tìm kiếm khoản chi theo từ khóa (sử dụng prepared statements)
     * Tìm trong tenkhoanchi
     */
    public function searchExpenses($keyword, $limit = 20, $offset = 0) {
        $sql = "SELECT machi, tenkhoanchi, sotien, madanhmuc, ngaybatdau, lapphieu
                FROM {$this->table}
                WHERE tenkhoanchi LIKE ?
                ORDER BY ngaybatdau DESC
                LIMIT ? OFFSET ?";
        if (!self::$_connection) return [];
        $stmt = self::$_connection->prepare($sql);
        if (!$stmt) return [];
        $keyword_param = "%" . $keyword . "%";
        $stmt->bind_param('sii', $keyword_param, $limit, $offset);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($row = $res->fetch_assoc()) {
            $out[] = $row;
        }
        $stmt->close();
        return $out;
    }

    /**
     * Đếm khoản chi theo từ khóa (sử dụng prepared statements)
     */
    public function countSearchExpenses($keyword) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE tenkhoanchi LIKE ?";
        if (!self::$_connection) return 0;
        $stmt = self::$_connection->prepare($sql);
        if (!$stmt) return 0;
        $keyword_param = "%" . $keyword . "%";
        $stmt->bind_param('s', $keyword_param);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return (int)($row['total'] ?? 0);
    }

    // Escape string để tránh SQL Injection
    private function escape($string)
    {
        if (self::$_connection) {
            return self::$_connection->real_escape_string($string);
        }
        return addslashes($string);
    }
}
