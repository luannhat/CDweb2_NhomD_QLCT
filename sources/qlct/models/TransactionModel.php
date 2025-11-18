<?php
require_once __DIR__ . '/../models/BaseModel.php';

class TransactionModel extends BaseModel
{

    public function __construct()
    {
        parent::__construct();
    }
    // Lấy tất cả giao dịch
    public function getAllTransaction($makh)
    {
        $conn = self::$_connection;

        if ($makh !== null) {
            // Nếu có mã khách hàng, chỉ lấy giao dịch của khách đó
            $makh = intval($makh);
            $sql = "SELECT * FROM GIAODICH WHERE makh = $makh";
        } else {
            // Nếu không truyền mã khách hàng, lấy tất cả
            $sql = "SELECT * FROM GIAODICH";
        }

        $result = $conn->query($sql);

        $rows = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    // Cập nhật ghi chú cho giao dịch
    public function updateGhichu($magd, $ghichu)
    {
        $conn = self::$_connection;

        // Chuẩn bị câu lệnh tránh lỗi SQL Injection
        $stmt = $conn->prepare("UPDATE GIAODICH SET ghichu = ? WHERE magd = ?");
        if ($stmt) {
            $stmt->bind_param("si", $ghichu, $magd);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            return $affected > 0; // trả về true nếu có dòng được cập nhật
        }
        return false;
    }

    //lấy tên khách hàng để hiện cho bảng giao dịch của ai
    public function getCustomerName($makh)
    {
        $conn = self::$_connection;
        $stmt = $conn->prepare("SELECT tenkh FROM KHACHHANG WHERE makh = ?");
        $stmt->bind_param("i", $makh);
        $stmt->execute();
        $stmt->bind_result($tenkh);
        if ($stmt->fetch()) {
            $stmt->close();
            return $tenkh;
        }
        $stmt->close();
        return null; // nếu không tìm thấy
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
    public function getTransactionsByMonth($makh, $year, $month) {
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
    while($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
    return $rows;
}

}
