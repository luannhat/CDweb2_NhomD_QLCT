<?php

require_once __DIR__ . '/BaseModel.php';

class NgansachModel extends BaseModel
{
    public function getBudgets(int $makh, array $filters = []): array
    {
        $makh = intval($makh);
        $conditions = ["ns.makh = {$makh}"];

        if (!empty($filters['week'])) {
            $conditions[] = "CEIL(DAY(ns.ngay) / 7) = " . intval($filters['week']);
        }
        if (!empty($filters['month'])) {
            $conditions[] = "MONTH(ns.ngay) = " . intval($filters['month']);
        }
        if (!empty($filters['year'])) {
            $conditions[] = "YEAR(ns.ngay) = " . intval($filters['year']);
        }

        $whereSql = implode(' AND ', $conditions);

        $sql = "SELECT 
                    ns.mangansach,
                    ns.machitieu,
                    ns.ngay,
                    ns.ngansach,
                    ns.dachi,
                    ns.chenhlech,
                    ns.trangthai,
                    dm.tendanhmuc
                FROM NGANSACH ns
                LEFT JOIN DMCHITIEU dm ON dm.madmchitieu = ns.machitieu
                WHERE {$whereSql}
                ORDER BY ns.ngay DESC, ns.mangansach DESC";

        $rows = $this->select($sql);
        if (empty($rows)) {
            return [];
        }

        $result = [];
        foreach ($rows as $row) {
            $date = new DateTime($row['ngay']);
            $dayOfMonth = (int)$date->format('j');
            $month = (int)$date->format('n');
            $year = (int)$date->format('Y');
            
            // Tính tuần trong tháng dựa trên ngày đã lưu (theo logic thực tế):
            // Tuần 1: ngày 1-7
            // Tuần 2: ngày 8-14
            // Tuần 3: ngày 15-21
            // Tuần 4: ngày 22 đến cuối tháng (22-28, 22-29, 22-30, hoặc 22-31)
            // Logic xác định tuần:
            if ($dayOfMonth >= 22) {
                // Ngày >= 22: thuộc tuần 4
                $weekInMonth = 4;
            } elseif ($dayOfMonth >= 15) {
                // Ngày 15-21: thuộc tuần 3
                $weekInMonth = 3;
            } elseif ($dayOfMonth >= 8) {
                // Ngày 8-14: thuộc tuần 2
                $weekInMonth = 2;
            } else {
                // Ngày 1-7: thuộc tuần 1
                $weekInMonth = 1;
            }
            
            // Đảm bảo tuần hợp lệ (1-4)
            $weekInMonth = max(1, min(4, $weekInMonth));

            // Tính "đã chi" dựa trên khoảng thời gian của tuần đó
            // Tuần 1: ngày 1-7
            // Tuần 2: ngày 8-14
            // Tuần 3: ngày 15-21
            // Tuần 4: ngày 22 đến cuối tháng (22-28, 22-29, 22-30, hoặc 22-31)
            $actualSpent = $this->calculateSpentForWeek(
                $makh,
                intval($row['machitieu']),
                $weekInMonth,
                $month,
                $year
            );

            $status = $this->resolveStatus(floatval($row['ngansach']), $actualSpent);

            if (abs($actualSpent - floatval($row['dachi'])) > 0.01 || $row['trangthai'] !== $status) {
                $this->updateBudgetTracking(intval($row['mangansach']), $actualSpent, $status);
            }

            $row['dachi'] = $actualSpent;
            $row['chenhlech_value'] = floatval($row['ngansach']) - $actualSpent;
            $row['trangthai'] = $status;
            $row['week_in_month'] = $weekInMonth;
            $row['month'] = $month;
            $row['year'] = $year;
            $result[] = $row;
        }

        return $result;
    }

    public function createWeeklyBudget(int $makh, int $categoryId, int $week, int $month, int $year, float $amount): array
    {
        $conn = self::$_connection;

        // Chỉ có 4 tuần trong tháng
        $week = max(1, min(4, $week));
        $month = max(1, min(12, $month));
        $year = max(2000, min(2100, $year));
        $amount = round($amount, 2);
        $categoryId = intval($categoryId);

        $weekStart = $this->getWeekStartDate($week, $month, $year);

        $stmt = $conn->prepare("
            INSERT INTO NGANSACH (makh, machitieu, ngay, ngansach, dachi, trangthai)
            VALUES (?, ?, ?, ?, 0, 'on_budget')
        ");

        if (!$stmt) {
            return ['success' => false, 'message' => $conn->error];
        }

        $stmt->bind_param("iisd", $makh, $categoryId, $weekStart, $amount);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => $error];
        }

        $stmt->close();

        return ['success' => true, 'message' => 'OK'];
    }

    public function getExpenseCategories(int $makh): array
    {
        $makh = intval($makh);
        $sql = "SELECT madmchitieu, tendanhmuc 
                FROM DMCHITIEU 
                WHERE makh = {$makh} AND loai = 'expense'
                ORDER BY tendanhmuc";
        return $this->select($sql);
    }

    private function calculateSpentForWeek(int $makh, int $categoryId, int $week, int $month, int $year): float
    {
        if ($categoryId <= 0) {
            return 0;
        }

        // Lấy khoảng thời gian của tuần: [startDate, endDate]
        // Tuần 1: ngày 1-7
        // Tuần 2: ngày 8-14
        // Tuần 3: ngày 15-21
        // Tuần 4: ngày 22 đến cuối tháng
        // Ví dụ: tuần 4 tháng 11/2025 = từ 22/11/2025 đến 30/11/2025
        [$startDate, $endDate] = $this->getWeekRange($week, $month, $year);

        $makh = intval($makh);
        $categoryId = intval($categoryId);
        
        // Escape dates để tránh SQL injection
        $startDate = self::$_connection->real_escape_string($startDate);
        $endDate = self::$_connection->real_escape_string($endDate);

        // Tính tổng chi tiêu trong khoảng thời gian đó
        // Chỉ lấy các khoản chi (loai = 'expense') có:
        // - Cùng mã khách hàng (makh)
        // - Cùng danh mục chi tiêu (madmchitieu)
        // - Ngày chi tiêu (ngaychitieu) nằm trong khoảng [startDate, endDate]
        $sql = "SELECT COALESCE(SUM(sotien), 0) AS total
                FROM DSCHITIEU
                WHERE makh = {$makh}
                  AND madmchitieu = {$categoryId}
                  AND loai = 'expense'
                  AND DATE(ngaychitieu) >= DATE('{$startDate}')
                  AND DATE(ngaychitieu) <= DATE('{$endDate}')";

        $rows = $this->select($sql);
        $total = isset($rows[0]['total']) ? floatval($rows[0]['total']) : 0.0;
        
        // Debug log (có thể bỏ comment để debug khi cần)
        // error_log("CalculateSpent: makh={$makh}, categoryId={$categoryId}, week={$week}, month={$month}, year={$year}, start={$startDate}, end={$endDate}, total={$total}");
        
        return $total;
    }

    private function updateBudgetTracking(int $budgetId, float $spent, string $status): void
    {
        $budgetId = intval($budgetId);
        $spent = round($spent, 2);
        $status = self::$_connection->real_escape_string($status);

        $sql = "UPDATE NGANSACH
                SET dachi = {$spent},
                    trangthai = '{$status}'
                WHERE mangansach = {$budgetId}";

        $this->update($sql);
    }

    private function resolveStatus(float $budget, float $spent): string
    {
        if ($spent == 0) {
            return 'on_budget';
        }

        if ($spent < $budget) {
            return 'under_budget';
        }

        if (abs($spent - $budget) < 0.01) {
            return 'on_budget';
        }

        return 'over_budget';
    }

    private function getWeekStartDate(int $week, int $month, int $year): string
    {
        // Chỉ có 4 tuần trong tháng
        $week = max(1, min(4, $week));
        
        // Logic tính ngày bắt đầu tuần:
        // Tuần 1: ngày 1 (1 + (1-1)*7 = 1)
        // Tuần 2: ngày 8 (1 + (2-1)*7 = 8)
        // Tuần 3: ngày 15 (1 + (3-1)*7 = 15)
        // Tuần 4: ngày 22 (1 + (4-1)*7 = 22) - kéo dài đến cuối tháng
        $dayOfMonth = 1 + ($week - 1) * 7;
        
        // Đảm bảo không vượt quá số ngày trong tháng
        $date = new DateTime(sprintf('%04d-%02d-01', $year, $month));
        $lastDayOfMonth = (int)$date->format('t');
        $dayOfMonth = min($dayOfMonth, $lastDayOfMonth);
        
        return sprintf('%04d-%02d-%02d', $year, $month, $dayOfMonth);
    }

    private function getWeekRange(int $week, int $month, int $year): array
    {
        // Lấy ngày bắt đầu tuần
        // Ví dụ: tuần 4 tháng 11/2025 = ngày 22/11/2025
        $start = new DateTime($this->getWeekStartDate($week, $month, $year));
        
        // Lấy ngày cuối cùng của tháng
        $lastDayOfMonth = new DateTime(sprintf('%04d-%02d-01', $year, $month));
        $lastDayOfMonth->modify('last day of this month');
        
        // Logic tính khoảng tuần (theo thực tế, không theo thời gian):
        // Tuần 1: ngày 1-7 (từ ngày 1, kết thúc sau 6 ngày = ngày 7)
        // Tuần 2: ngày 8-14 (từ ngày 8, kết thúc sau 6 ngày = ngày 14)
        // Tuần 3: ngày 15-21 (từ ngày 15, kết thúc sau 6 ngày = ngày 21)
        // Tuần 4: ngày 22 đến cuối tháng (22-28, 22-29, 22-30, hoặc 22-31 tùy tháng)
        if ($week == 4) {
            // Tuần 4 luôn kéo dài đến cuối tháng
            $end = $lastDayOfMonth;
        } else {
            // Tuần 1, 2, 3: mỗi tuần đúng 7 ngày (bắt đầu + 6 ngày)
            // Tuần 1: ngày 1 + 6 = ngày 7
            // Tuần 2: ngày 8 + 6 = ngày 14
            // Tuần 3: ngày 15 + 6 = ngày 21
            $end = clone $start;
            $end->modify('+6 days');
            
            // Đảm bảo không vượt quá cuối tháng (trường hợp hiếm, nhưng an toàn)
            if ($end > $lastDayOfMonth) {
                $end = $lastDayOfMonth;
            }
        }

        // Trả về [startDate, endDate]
        // Ví dụ tuần 4 tháng 11/2025: ['2025-11-22', '2025-11-30']
        return [$start->format('Y-m-d'), $end->format('Y-m-d')];
    }
}

