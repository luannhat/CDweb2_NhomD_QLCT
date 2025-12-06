<?php
require_once __DIR__ . '/../models/BaseModel.php';

class TransactionModel extends BaseModel
{
    private $conn;
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

    // Lấy tổng chi tiêu theo tháng cụ thể
    public function getMonthlySpendingByMonth($year, $month)
    {
        $conn = self::$_connection;
        $stmt = $conn->prepare("
            SELECT SUM(sotien) AS total
            FROM GIAODICH
            WHERE makh=? AND loai='expense' AND YEAR(ngaygiaodich)=? AND MONTH(ngaygiaodich)=?
        ");
        $stmt->bind_param("iii", $this->makh, $year, $month);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return floatval($row['total'] ?? 0);
    }

    // Lấy danh sách các tháng có dữ liệu chi tiêu trong năm
    public function getMonthsWithExpenses($year)
    {
        $conn = self::$_connection;
        $stmt = $conn->prepare("
            SELECT DISTINCT MONTH(ngaygiaodich) AS month
            FROM GIAODICH
            WHERE makh=? AND loai='expense' AND YEAR(ngaygiaodich)=?
            ORDER BY month
        ");
        $stmt->bind_param("ii", $this->makh, $year);
        $stmt->execute();
        $result = $stmt->get_result();
        $months = [];
        while ($row = $result->fetch_assoc()) {
            $months[] = intval($row['month']);
        }
        $stmt->close();
        return $months;
    }

    // Lấy danh sách giao dịch theo tháng
    public function getTransactionsByMonth($year, $month)
    {
        $conn = self::$_connection;
        $stmt = $conn->prepare("
            SELECT * FROM GIAODICH
            WHERE makh=? AND loai='expense' AND YEAR(ngaygiaodich)=? AND MONTH(ngaygiaodich)=?
            ORDER BY ngaygiaodich ASC
        ");
        $stmt->bind_param("iii", $this->makh, $year, $month);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    // Lấy tổng chi tiêu từng tháng trong năm
    public function getMonthlyStatistics($year, $month = null)
    {
        $conn = self::$_connection;

        if ($month) {
            $total = $this->getMonthlySpendingByMonth($year, $month);
            return [$month => $total];
        } else {
            // Lấy tổng từng tháng có dữ liệu
            $stmt = $conn->prepare("
                SELECT MONTH(ngaygiaodich) AS month, SUM(sotien) AS total
                FROM GIAODICH
                WHERE makh=? AND loai='expense' AND YEAR(ngaygiaodich)=?
                GROUP BY MONTH(ngaygiaodich)
            ");
            $stmt->bind_param("ii", $this->makh, $year);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[intval($row['month'])] = floatval($row['total']);
            }
            $stmt->close();

            // Điền 0 cho các tháng không có dữ liệu
            $allMonths = [];
            for ($m = 1; $m <= 12; $m++) {
                $allMonths[$m] = $rows[$m] ?? 0;
            }
            return $allMonths;
        }
    }

    // Controller hiển thị thống kê
    public function monthlyStatisticsController()
    {
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $month = isset($_GET['month']) ? intval($_GET['month']) : null;

        // Lấy các tháng có dữ liệu
        $availableMonths = $this->getMonthsWithExpenses($year);

        // Nếu tháng được chọn không có dữ liệu, lấy tháng đầu có dữ liệu
        if ($month && !in_array($month, $availableMonths)) {
            $month = $availableMonths[0] ?? null;
        }

        // Dữ liệu
        $data = $this->getMonthlyStatistics($year, $month);
        $transactions = $month ? $this->getTransactionsByMonth($year, $month) : [];

        include __DIR__ . '/../views/monthly_statistics.php';
    }

    // Thêm giao dịch mới
    // Thêm giao dịch mới
    public function addTransactions($makh, $machitieu, $noidung, $sotien, $loai, $ngaygiaodich, $ghichu, $anhhoadon)
    {
        try {
            $conn = self::$_connection;

            $sql = "INSERT INTO GIAODICH 
                (makh, machitieu, noidung, sotien, loai, ngaygiaodich, ghichu, anhhoadon)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "iisdssss",
                $makh,
                $machitieu,
                $noidung,
                $sotien,
                $loai,
                $ngaygiaodich,
                $ghichu,
                $anhhoadon
            );

            return $stmt->execute();
        } catch (Exception $e) {
            echo "Lỗi thêm giao dịch: " . $e->getMessage();
            return false;
        }
    }
}
