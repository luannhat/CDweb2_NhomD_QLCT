<?php
require_once 'configs/database.php';

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
}
