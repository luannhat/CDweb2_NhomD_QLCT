<?php
require_once __DIR__ . '/../models/BaseModel.php';

class TransactionModel extends BaseModel
{
    private $model;
    private $makh;
    public function __construct()
    {
        parent::__construct();
    }

    // Lấy tất cả giao dịch của khách hàng
    public function getAllTransaction($makh)
    {
        $makh = intval($makh);
        $sql = "SELECT * FROM GIAODICH WHERE makh = $makh ORDER BY ngaygiaodich ASC";
        return $this->select($sql); // select trả về array
    }

    // Lấy tên khách hàng
    public function getCustomerName($makh)
    {
        $makh = intval($makh);
        $sql = "SELECT tenkh FROM KHACHHANG WHERE makh = $makh LIMIT 1";
        $rows = $this->select($sql);
        return !empty($rows) ? $rows[0]['tenkh'] : 'Khách hàng';
    }

    // Cập nhật ghi chú
    public function updateGhichu($magd, $ghichu)
    {
        $magd = intval($magd);
        $ghichu = self::$_connection->real_escape_string($ghichu);
        $sql = "UPDATE GIAODICH SET ghichu = '$ghichu' WHERE magd = $magd";
        $this->update($sql);
        return self::$_connection->affected_rows > 0;
    }

    //thóng kê chi tiêu theo tháng
    public function getMonthlySpending($makh, $year)
    {
        $rows = [];
        for ($m = 1; $m <= 12; $m++) {
            $rows[$m] = $this->getMonthlySpendingByMonth($makh, $year, $m);
        }
        return $rows;
    }

    // Lấy tổng chi tiêu của khách hàng theo tháng cụ thể
    public function getMonthlySpendingByMonth($makh, $year, $month)
    {
        $conn = self::$_connection;
        $stmt = $conn->prepare("
            SELECT SUM(sotien) AS total 
            FROM GIAODICH 
            WHERE makh = ? 
              AND YEAR(ngaygiaodich) = ? 
              AND MONTH(ngaygiaodich) = ?
              AND loai = 'expense'
        ");
        $stmt->bind_param("iii", $makh, $year, $month);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'] ?? 0;
    }
    //hàm lấy ds giao dịch theo tháng
    public function getTransactionsByMonth($makh, $year, $month)
    {
        $conn = self::$_connection;
        $stmt = $conn->prepare("
        SELECT * 
        FROM GIAODICH
        WHERE makh = ? 
          AND YEAR(ngaygiaodich) = ? 
          AND MONTH(ngaygiaodich) = ?
          AND loai = 'expense'
        ORDER BY ngaygiaodich ASC
    ");
        $stmt->bind_param("iii", $makh, $year, $month);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    public function monthlyStatistics()
    {
        // Lấy năm/tháng từ GET
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $month = isset($_GET['month']) ? intval($_GET['month']) : '';

        // Lấy dữ liệu từ model (gọi trực tiếp)
        $data = $this->getMonthlyStatistics($this->makh, $year, $month);
        $transactions = $month ? $this->getTransactionsByMonth($this->makh, $year, $month) : [];

        // Gửi sang view thống kê
        include __DIR__ . '/../views/monthly_statistics.php';
    }


    public function getMonthlyStatistics($makh, $year, $month = null)
    {
        $makh = intval($makh);
        $year = intval($year);
        $conn = self::$_connection;

        if (!empty($month)) {
            $month = intval($month);
            $stmt = $conn->prepare("
            SELECT SUM(sotien) AS total
            FROM GIAODICH
            WHERE makh = ? 
              AND loai = 'expense' 
              AND YEAR(ngaygiaodich) = ? 
              AND MONTH(ngaygiaodich) = ?
        ");
            $stmt->bind_param("iii", $makh, $year, $month);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return [$month => floatval($row['total'] ?? 0)];
        } else {
            // Tổng chi tiêu từng tháng
            $stmt = $conn->prepare("
            SELECT MONTH(ngaygiaodich) AS month, SUM(sotien) AS total
            FROM GIAODICH
            WHERE makh = ? AND loai = 'expense' AND YEAR(ngaygiaodich) = ?
            GROUP BY MONTH(ngaygiaodich)
        ");
            $stmt->bind_param("ii", $makh, $year);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[intval($row['month'])] = floatval($row['total']);
            }
            $stmt->close();

            // Đảm bảo tất cả các tháng 1-12 luôn có giá trị
            $allMonths = [];
            for ($m = 1; $m <= 12; $m++) {
                $allMonths[$m] = $rows[$m] ?? 0;
            }
            return $allMonths;
        }
    }

    // Lấy chi tiết giao dịch theo tháng (nếu có)
}
