<?php
require_once __DIR__ . '/../models/BaseModel.php';

class StatisticalModel extends BaseModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
        if ($this->conn->connect_errno) {
            die("Lỗi kết nối MySQL: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8mb4");
    }

    // Lấy tất cả danh mục + tổng chi tiêu
    public function getAllExpenseByCategory($makh)
    {
        $stmt = $this->_connection->prepare("
            SELECT machitieu, SUM(sotien) AS total
            FROM GIAODICH
            WHERE makh = ?
            GROUP BY machitieu
        ");
        $stmt->bind_param("i", $makh);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    // Lấy thống kê chi tiêu theo năm và danh mục
    public function getExpenseByCategoryAndYear($makh, $year)
    {
        $stmt = $this->conn
            ->prepare("
            SELECT machitieu, SUM(sotien) AS total
            FROM GIAODICH
            WHERE makh = ? AND YEAR(ngaygiaodich) = ?
            GROUP BY machitieu
        ");
        $stmt->bind_param("ii", $makh, $year);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    // Lấy tổng chi tiêu cả năm
    public function getTotalExpenseByYear($makh, $year)
    {
        $stmt = $this->conn
            ->prepare("
            SELECT SUM(sotien) AS total
            FROM GIAODICH
            WHERE makh = ? AND YEAR(ngaygiaodich) = ?
        ");
        $stmt->bind_param("ii", $makh, $year);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function getMonthsWithTransactions($makh, $year)
    {
        $stmt = $this->conn
            ->prepare("
            SELECT DISTINCT MONTH(ngaygiaodich) AS month
            FROM GIAODICH
            WHERE makh = ? AND YEAR(ngaygiaodich) = ?
            ORDER BY month ASC
        ");
        $stmt->bind_param("ii", $makh, $year);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return array_column($result, 'month');
    }

    // Lấy dữ liệu thu nhập và chi tiêu theo khoảng thời gian, nhóm theo tháng
    public function getIncomeExpenseByDateRange($makh, $fromDate, $toDate)
    {
        $makh = intval($makh);
        $fromDate = $this->conn->real_escape_string($fromDate);
        $toDate = $this->conn->real_escape_string($toDate);

        $sql = "SELECT 
                    DATE_FORMAT(ngaythunhap, '%Y-%m') AS thang,
                    COALESCE(SUM(sotien), 0) AS thu_nhap
                FROM DSTHUNHAP
                WHERE makh = {$makh}
                AND ngaythunhap >= '{$fromDate}'
                AND ngaythunhap <= '{$toDate}'
                GROUP BY DATE_FORMAT(ngaythunhap, '%Y-%m')
                ORDER BY thang ASC";

        $incomeResult = $this->conn->query($sql);
        $incomeData = [];
        if ($incomeResult) {
            while ($row = $incomeResult->fetch_assoc()) {
                $incomeData[$row['thang']] = floatval($row['thu_nhap']);
            }
        }

        $sql = "SELECT 
                    DATE_FORMAT(ngaychitieu, '%Y-%m') AS thang,
                    COALESCE(SUM(sotien), 0) AS chi_tieu
                FROM DSCHITIEU
                WHERE makh = {$makh}
                AND loai = 'expense'
                AND ngaychitieu >= '{$fromDate}'
                AND ngaychitieu <= '{$toDate}'
                GROUP BY DATE_FORMAT(ngaychitieu, '%Y-%m')
                ORDER BY thang ASC";

        $expenseResult = $this->conn->query($sql);
        $expenseData = [];
        if ($expenseResult) {
            while ($row = $expenseResult->fetch_assoc()) {
                $expenseData[$row['thang']] = floatval($row['chi_tieu']);
            }
        }

        // Tạo mảng kết hợp tất cả các tháng
        $allMonths = array_unique(array_merge(array_keys($incomeData), array_keys($expenseData)));
        sort($allMonths);

        $result = [];
        foreach ($allMonths as $month) {
            $result[] = [
                'thang' => $month,
                'thu_nhap' => $incomeData[$month] ?? 0,
                'chi_tieu' => $expenseData[$month] ?? 0
            ];
        }

        return $result;
    }

    // Lấy tổng thu nhập và chi tiêu trong khoảng thời gian
    public function getTotalIncomeExpenseByDateRange($makh, $fromDate, $toDate)
    {
        $makh = intval($makh);
        $fromDate = $this->conn->real_escape_string($fromDate);
        $toDate = $this->conn->real_escape_string($toDate);

        $sql = "SELECT COALESCE(SUM(sotien), 0) AS tong_thu_nhap
                FROM DSTHUNHAP
                WHERE makh = {$makh}
                AND ngaythunhap >= '{$fromDate}'
                AND ngaythunhap <= '{$toDate}'";

        $result = $this->conn->query($sql);
        $row = $result ? $result->fetch_assoc() : null;
        $totalIncome = $row ? floatval($row['tong_thu_nhap']) : 0;

        $sql = "SELECT COALESCE(SUM(sotien), 0) AS tong_chi_tieu
                FROM DSCHITIEU
                WHERE makh = {$makh}
                AND loai = 'expense'
                AND ngaychitieu >= '{$fromDate}'
                AND ngaychitieu <= '{$toDate}'";

        $result = $this->conn->query($sql);
        $row = $result ? $result->fetch_assoc() : null;
        $totalExpense = $row ? floatval($row['tong_chi_tieu']) : 0;

        return [
            'tong_thu_nhap' => $totalIncome,
            'tong_chi_tieu' => $totalExpense,
            'chenh_lech' => $totalIncome - $totalExpense
        ];
    }

    /**
     * Lấy dữ liệu thu nhập/chi tiêu theo từng tuần (4 tuần) trong 1 tháng cụ thể.
     * Tuần được chia theo các mốc 1-7, 8-14, 15-21, 22-cuối tháng.
     */
    public function getWeeklyIncomeExpenseByMonth($makh, $year, $month)
    {
        $makh = intval($makh);
        $year = intval($year);
        $month = intval($month);

        if ($month < 1 || $month > 12) {
            $month = intval(date('n'));
        }

        $incomeSql = "
            SELECT 
                CASE
                    WHEN DAY(ngaythunhap) BETWEEN 1 AND 7 THEN 1
                    WHEN DAY(ngaythunhap) BETWEEN 8 AND 14 THEN 2
                    WHEN DAY(ngaythunhap) BETWEEN 15 AND 21 THEN 3
                    ELSE 4
                END AS week_index,
                COALESCE(SUM(sotien), 0) AS total
            FROM DSTHUNHAP
            WHERE makh = {$makh}
                AND YEAR(ngaythunhap) = {$year}
                AND MONTH(ngaythunhap) = {$month}
            GROUP BY week_index
        ";

        $expenseSql = "
            SELECT 
                CASE
                    WHEN DAY(ngaychitieu) BETWEEN 1 AND 7 THEN 1
                    WHEN DAY(ngaychitieu) BETWEEN 8 AND 14 THEN 2
                    WHEN DAY(ngaychitieu) BETWEEN 15 AND 21 THEN 3
                    ELSE 4
                END AS week_index,
                COALESCE(SUM(sotien), 0) AS total
            FROM DSCHITIEU
            WHERE makh = {$makh}
                AND loai = 'expense'
                AND YEAR(ngaychitieu) = {$year}
                AND MONTH(ngaychitieu) = {$month}
            GROUP BY week_index
        ";

        $incomeResult = $this->conn->query($incomeSql);
        $expenseResult = $this->conn->query($expenseSql);

        $weeks = [
            1 => ['label' => 'Tuần 1', 'thu_nhap' => 0, 'chi_tieu' => 0],
            2 => ['label' => 'Tuần 2', 'thu_nhap' => 0, 'chi_tieu' => 0],
            3 => ['label' => 'Tuần 3', 'thu_nhap' => 0, 'chi_tieu' => 0],
            4 => ['label' => 'Tuần 4', 'thu_nhap' => 0, 'chi_tieu' => 0],
        ];

        if ($incomeResult) {
            while ($row = $incomeResult->fetch_assoc()) {
                $index = intval($row['week_index']);
                if (isset($weeks[$index])) {
                    $weeks[$index]['thu_nhap'] = floatval($row['total']);
                }
            }
        }

        if ($expenseResult) {
            while ($row = $expenseResult->fetch_assoc()) {
                $index = intval($row['week_index']);
                if (isset($weeks[$index])) {
                    $weeks[$index]['chi_tieu'] = floatval($row['total']);
                }
            }
        }

        return array_values($weeks);
    }

    public function getTransactionsByMonth($makh, $year, $month)
    {
        $stmt = $this->conn->prepare("
            SELECT * 
            FROM GIAODICH
            WHERE makh = ? AND YEAR(ngaygiaodich) = ? AND MONTH(ngaygiaodich) = ?
            ORDER BY ngaygiaodich ASC
        ");
        $stmt->bind_param("iii", $makh, $year, $month);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function getMonthlyTotals($makh, $year)
    {
        $stmt = $this->conn->prepare("
            SELECT MONTH(ngaygiaodich) AS month, SUM(sotien) AS total
            FROM GIAODICH
            WHERE makh = ? AND YEAR(ngaygiaodich) = ?
            GROUP BY MONTH(ngaygiaodich)
        ");
        $stmt->bind_param("ii", $makh, $year);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $totals = [];
        foreach ($result as $row) {
            $totals[$row['month']] = $row['total'];
        }
        return $totals;
    }

    /**
     * Lấy chi tiết các giao dịch chi tiêu trong một tuần cụ thể
     */
    public function getWeeklyExpenseDetails($makh, $year, $month, $week)
    {
        $makh = intval($makh);
        $year = intval($year);
        $month = intval($month);
        $week = intval($week);

        // Xác định ngày bắt đầu và kết thúc của tuần
        $dayStart = ($week - 1) * 7 + 1;
        $dayEnd = $dayStart + 6;

        if ($week == 4) {
            $dayEnd = 31; // Tuần 4 có thể đến hết tháng
        }

        $sql = "
            SELECT 
                machitieu,
                sotien,
                ngaychitieu,
                ghichu,
                machitieu as category_id
            FROM DSCHITIEU
            WHERE makh = {$makh}
                AND loai = 'expense'
                AND YEAR(ngaychitieu) = {$year}
                AND MONTH(ngaychitieu) = {$month}
                AND DAY(ngaychitieu) >= {$dayStart}
                AND DAY(ngaychitieu) <= {$dayEnd}
            ORDER BY ngaychitieu DESC
        ";

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
