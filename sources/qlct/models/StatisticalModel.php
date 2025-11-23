<?php
require_once __DIR__ . '/../configs/database.php';

class StatisticalModel {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
        if ($this->conn->connect_errno) {
            die("Lỗi kết nối MySQL: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8mb4");
    }

    // Lấy tất cả danh mục + tổng chi tiêu
    public function getAllExpenseByCategory() {
        $sql = "SELECT d.tendanhmuc AS tendanhmuc, COALESCE(SUM(c.sotien),0) AS tongtien
                FROM DMCHITIEU d
                LEFT JOIN DSCHITIEU c ON d.machitieu = c.machitieu AND c.loai = 'expense'
                GROUP BY d.machitieu, d.tendanhmuc";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Lấy thống kê chi tiêu theo năm và danh mục
    public function getExpenseByCategoryAndYear($makh, $year) {
        $makh = intval($makh);
        $year = intval($year);
        
        $sql = "SELECT 
                    dm.tendanhmuc AS tendanhmuc, 
                    COALESCE(SUM(ds.sotien), 0) AS tongtien
                FROM DMCHITIEU dm
                LEFT JOIN DSCHITIEU ds ON dm.madmchitieu = ds.madmchitieu 
                    AND ds.loai = 'expense' 
                    AND ds.makh = {$makh}
                    AND YEAR(ds.ngaychitieu) = {$year}
                WHERE dm.makh = {$makh} AND dm.loai = 'expense'
                GROUP BY dm.madmchitieu, dm.tendanhmuc
                HAVING tongtien > 0
                ORDER BY tongtien DESC";
        
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Lấy tổng chi tiêu cả năm
    public function getTotalExpenseByYear($makh, $year) {
        $makh = intval($makh);
        $year = intval($year);
        
        $sql = "SELECT COALESCE(SUM(sotien), 0) AS tongtien
                FROM DSCHITIEU
                WHERE makh = {$makh}
                AND loai = 'expense'
                AND YEAR(ngaychitieu) = {$year}";
        
        $result = $this->conn->query($sql);
        $row = $result ? $result->fetch_assoc() : null;
        return $row ? floatval($row['tongtien']) : 0;
    }
}
