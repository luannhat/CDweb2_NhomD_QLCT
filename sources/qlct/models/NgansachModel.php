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
            $weekInMonth = (int)ceil(intval($date->format('j')) / 7);
            $month = (int)$date->format('n');
            $year = (int)$date->format('Y');

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

        $week = max(1, min(5, $week));
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

        [$startDate, $endDate] = $this->getWeekRange($week, $month, $year);

        $makh = intval($makh);
        $categoryId = intval($categoryId);

        $sql = "SELECT COALESCE(SUM(sotien), 0) AS total
                FROM DSCHITIEU
                WHERE makh = {$makh}
                  AND madmchitieu = {$categoryId}
                  AND loai = 'expense'
                  AND ngaychitieu BETWEEN '{$startDate}' AND '{$endDate}'";

        $rows = $this->select($sql);
        return isset($rows[0]['total']) ? floatval($rows[0]['total']) : 0.0;
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
        $week = max(1, $week);
        $date = new DateTime(sprintf('%04d-%02d-01', $year, $month));
        if ($week > 1) {
            $date->modify('+' . ($week - 1) * 7 . ' days');
        }
        return $date->format('Y-m-d');
    }

    private function getWeekRange(int $week, int $month, int $year): array
    {
        $start = new DateTime($this->getWeekStartDate($week, $month, $year));
        $end = clone $start;
        $end->modify('+6 days');

        $lastDayOfMonth = new DateTime(sprintf('%04d-%02d-01', $year, $month));
        $lastDayOfMonth->modify('last day of this month');

        if ($end > $lastDayOfMonth) {
            $end = $lastDayOfMonth;
        }

        return [$start->format('Y-m-d'), $end->format('Y-m-d')];
    }
}

