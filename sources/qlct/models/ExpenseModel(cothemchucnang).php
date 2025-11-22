<?php
require_once 'BaseModel.php';

class ExpenseModel extends BaseModel {

    // ------------------ KHOẢN CHI & DANH MỤC ------------------

    public function addKhoanChi($tenkhoanchi, $sotien, $madanhmuc, $ngaybatdau, $lapphieu = 'Không lặp lại') {
        $sql = "INSERT INTO KHOANCHI (tenkhoanchi, sotien, madanhmuc, ngaybatdau, lapphieu) VALUES (?, ?, ?, ?, ?)";
        return $this->insertPrepared($sql, "sdiss", [$tenkhoanchi, $sotien, $madanhmuc, $ngaybatdau, $lapphieu]);
    }

    public function getAllKhoanChi() {
        $sql = "SELECT kc.machi, kc.tenkhoanchi, kc.sotien, kc.ngaybatdau, kc.lapphieu, dm.tendanhmuc
                FROM KHOANCHI kc
                LEFT JOIN DANHMUC dm ON kc.madanhmuc = dm.madanhmuc
                ORDER BY kc.ngaybatdau DESC";
        return $this->select($sql);
    }

    public function deleteKhoanChi($machi) {
        $sql = "DELETE FROM KHOANCHI WHERE machi=?";
        return $this->updatePrepared($sql, "i", [$machi]);
    }

    public function updateKhoanChi($machi, $tenkhoanchi, $sotien, $madanhmuc, $ngaybatdau, $lapphieu) {
        $sql = "UPDATE KHOANCHI SET tenkhoanchi=?, sotien=?, madanhmuc=?, ngaybatdau=?, lapphieu=? WHERE machi=?";
        return $this->updatePrepared($sql, "sdissi", [$tenkhoanchi, $sotien, $madanhmuc, $ngaybatdau, $lapphieu, $machi]);
    }

    public function getDanhMucList() {
        $sql = "SELECT madanhmuc, tendanhmuc FROM DANHMUC ORDER BY tendanhmuc ASC";
        return $this->select($sql);
    }

    public function addDanhMuc($tenDanhMuc) {
        $check = $this->queryPrepared("SELECT madanhmuc FROM DANHMUC WHERE tendanhmuc=? LIMIT 1", "s", [$tenDanhMuc]);
        if ($check->num_rows == 0) {
            $this->insertPrepared("INSERT INTO DANHMUC(tendanhmuc) VALUES(?)", "s", [$tenDanhMuc]);
        }
        $res = $this->queryPrepared("SELECT madanhmuc FROM DANHMUC WHERE tendanhmuc=? LIMIT 1", "s", [$tenDanhMuc]);
        $row = $res->fetch_assoc();
        return $row['madanhmuc'] ?? null;
    }

    // ------------------ CHI TIÊU ------------------

    public function getChiTieuByMonth($makh, $thang, $nam) {
        $sql = "SELECT noidung as danhmuc, SUM(sotien) as tongtien 
                FROM DSCHITIEU 
                WHERE makh=? AND MONTH(ngaychitieu)=? AND YEAR(ngaychitieu)=?
                GROUP BY noidung";
        $result = $this->queryPrepared($sql, "iii", [$makh, $thang, $nam]);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Lấy danh sách chi tiêu theo ngày (chi tiết)
     * Trả về mảng các bản ghi: noidung, sotien, machitieu, ngaychitieu, tendanhmuc
     */
    public function getExpensesByDate($makh, $date) {
        $sql = "SELECT d.maloaichitieu, d.noidung, d.sotien, d.machitieu, d.ngaychitieu, IFNULL(dm.tendanhmuc,'Khác') AS tendanhmuc
                FROM DSCHITIEU d
                LEFT JOIN DMCHITIEU dm ON d.machitieu = dm.machitieu
                WHERE d.makh = ? AND d.ngaychitieu = ?
                ORDER BY d.ngaychitieu DESC, d.maloaichitieu DESC";
        $res = $this->queryPrepared($sql, "is", [$makh, $date]);
        $out = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $out[] = $row;
            }
        }
        return $out;
    }

    /**
     * Lấy tổng chi theo ngày
     */
    public function getTotalExpensesByDate($makh, $date) {
        $sql = "SELECT SUM(sotien) as total FROM DSCHITIEU WHERE makh = ? AND ngaychitieu = ?";
        $res = $this->queryPrepared($sql, "is", [$makh, $date]);
        if (!$res) return 0;
        $row = $res->fetch_assoc();
        return (float)($row['total'] ?? 0);
    }

    /**
     * Lấy tổng chi theo danh mục cho ngày dùng để vẽ biểu đồ
     */
    public function getExpensesSummaryByDate($makh, $date) {
        $sql = "SELECT IFNULL(dm.tendanhmuc,'Khác') AS danhmuc, SUM(d.sotien) AS tongtien
                FROM DSCHITIEU d
                LEFT JOIN DMCHITIEU dm ON d.machitieu = dm.machitieu
                WHERE d.makh = ? AND d.ngaychitieu = ?
                GROUP BY danhmuc
                ORDER BY tongtien DESC";
        $res = $this->queryPrepared($sql, "is", [$makh, $date]);
        $out = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $out[] = $row;
            }
        }
        return $out;
    }

    public function getTotalChiTieu($makh, $thang, $nam) {
        $sql = "SELECT SUM(sotien) as total FROM DSCHITIEU WHERE makh=? AND MONTH(ngaychitieu)=? AND YEAR(ngaychitieu)=?";
        $res = $this->queryPrepared($sql, "iii", [$makh, $thang, $nam]);
        if (!$res) return 0;
        $row = $res->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    // ------------------ LỊCH THU CHI (LICHTHUCHI) ------------------

    /**
     * Lấy danh sách bản ghi lịch thu/chi cho một tháng
     * Trả về mảng các bản ghi: mathuchi, ngay, loai, sotien, ghichu
     */
    public function getLedgerByMonth($makh, $thang, $nam) {
        $sql = "SELECT mathuchi, ngay, loai, sotien, ghichu FROM LICHTHUCHI WHERE makh = ? AND MONTH(ngay) = ? AND YEAR(ngay) = ? ORDER BY ngay ASC";
        $res = $this->queryPrepared($sql, "iii", [$makh, $thang, $nam]);
        $out = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $out[] = $row;
            }
        }
        return $out;
    }

    /**
     * Lấy tổng thu và tổng chi cho một tháng (theo LICHTHUCHI)
     * Trả về associative array: ['tong_thu' => ..., 'tong_chi' => ...]
     */
    public function getLedgerTotalsByMonth($makh, $thang, $nam) {
        $sql = "SELECT 
                    IFNULL(SUM(CASE WHEN loai = 'thu' THEN sotien ELSE 0 END),0) AS tong_thu,
                    IFNULL(SUM(CASE WHEN loai = 'chi' THEN sotien ELSE 0 END),0) AS tong_chi
                FROM LICHTHUCHI
                WHERE makh = ? AND MONTH(ngay) = ? AND YEAR(ngay) = ?";
        $res = $this->queryPrepared($sql, "iii", [$makh, $thang, $nam]);
        if (!$res) return ['tong_thu' => 0.0, 'tong_chi' => 0.0];
        $row = $res->fetch_assoc();
        return [
            'tong_thu' => (float)($row['tong_thu'] ?? 0),
            'tong_chi' => (float)($row['tong_chi'] ?? 0),
        ];
    }

    /**
     * Thêm bản ghi lịch thu/chi (LICHTHUCHI)
     * Dùng cho các khoản tiền định kỳ
     */
    public function addRecurringLedger($makh, $ngay, $loai, $sotien, $ghichu = '') {
        $sql = "INSERT INTO LICHTHUCHI (makh, ngay, loai, sotien, ghichu)
                VALUES (?, ?, ?, ?, ?)";
        return $this->insertPrepared($sql, "issds", [$makh, $ngay, $loai, $sotien, $ghichu]);
    }

    /**
     * Lấy danh sách bản ghi lịch thu/chi của người dùng
     * Có thể lọc theo loại (thu/chi)
     */
    public function getRecurringLedgerList($makh, $loai = null, $limit = 20, $offset = 0) {
        if ($loai && in_array($loai, ['khonglaplai', 'laplaitheongay', 'laplaitheotuan', 'laplaitheothang'])) {
            $sql = "SELECT mathuchi, ngay, loai, sotien, ghichu FROM LICHTHUCHI 
                    WHERE makh = ? AND loai = ?
                    ORDER BY ngay DESC
                    LIMIT ? OFFSET ?";
            $res = $this->queryPrepared($sql, "isii", [$makh, $loai, $limit, $offset]);
        } else {
            $sql = "SELECT mathuchi, ngay, loai, sotien, ghichu FROM LICHTHUCHI 
                    WHERE makh = ?
                    ORDER BY ngay DESC
                    LIMIT ? OFFSET ?";
            $res = $this->queryPrepared($sql, "iii", [$makh, $limit, $offset]);
        }
        
        $out = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $out[] = $row;
            }
        }
        return $out;
    }

    /**
     * Lấy chi tiết một bản ghi lịch thu/chi
     */
    public function getRecurringLedgerById($mathuchi, $makh = null) {
        if ($makh) {
            $sql = "SELECT * FROM LICHTHUCHI WHERE mathuchi = ? AND makh = ? LIMIT 1";
            $res = $this->queryPrepared($sql, "ii", [$mathuchi, $makh]);
        } else {
            $sql = "SELECT * FROM LICHTHUCHI WHERE mathuchi = ? LIMIT 1";
            $res = $this->queryPrepared($sql, "i", [$mathuchi]);
        }
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Cập nhật bản ghi lịch thu/chi
     */
    public function updateRecurringLedger($mathuchi, $makh, $ngay, $loai, $sotien, $ghichu = '') {
        $sql = "UPDATE LICHTHUCHI SET ngay = ?, loai = ?, sotien = ?, ghichu = ? 
                WHERE mathuchi = ? AND makh = ?";
        // types: ngay (s), loai (s), sotien (d), ghichu (s), mathuchi (i), makh (i)
        return $this->updatePrepared($sql, "ssdsii", [$ngay, $loai, $sotien, $ghichu, $mathuchi, $makh]);
    }

    /**
     * Xóa bản ghi lịch thu/chi (với kiểm tra quyền sở hữu)
     */
    public function deleteRecurringLedger($mathuchi, $makh) {
        $sql = "DELETE FROM LICHTHUCHI WHERE mathuchi = ? AND makh = ?";
        return $this->updatePrepared($sql, "ii", [$mathuchi, $makh]);
    }

    /**
     * Đếm bản ghi lịch thu/chi của người dùng
     */
    public function countRecurringLedger($makh, $loai = null) {
        if ($loai && in_array($loai, ['thu', 'chi'])) {
            $sql = "SELECT COUNT(*) as total FROM LICHTHUCHI WHERE makh = ? AND loai = ?";
            $res = $this->queryPrepared($sql, "is", [$makh, $loai]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM LICHTHUCHI WHERE makh = ?";
            $res = $this->queryPrepared($sql, "i", [$makh]);
        }
        if (!$res) return 0;
        $row = $res->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    // ------------------ NGÂN SÁCH (LAPNGANSACHTHEOTHANG) ------------------

    public function getNganSach($makh, $thang, $nam) {
        $sql = "SELECT mangansach, makh, thang, nam, tongngansach FROM LAPNGANSACHTHEOTHANG 
                WHERE makh=? AND thang=? AND nam=? LIMIT 1";
        $res = $this->queryPrepared($sql, "iii", [$makh, $thang, $nam]);
        if (!$res) return null;
        $row = $res->fetch_assoc();
        return $row ?: null;
    }

    public function saveNganSach($makh, $thang, $nam, $tien_nha, $an_uong, $di_lai, $mua_sam) {
        // Calculate total budget (tien_nha is usually 0 for simple budgets)
        $tongngansach = $tien_nha + $an_uong + $di_lai + $mua_sam;
        
        // Check if record exists
        $existing = $this->getNganSach($makh, $thang, $nam);

        if ($existing) {
            // Update existing record
            $sql = "UPDATE LAPNGANSACHTHEOTHANG SET tongngansach=? 
                    WHERE makh=? AND thang=? AND nam=?";
            return $this->updatePrepared($sql, "diii", [$tongngansach, $makh, $thang, $nam]);
        } else {
            // Insert new record
            $sql = "INSERT INTO LAPNGANSACHTHEOTHANG (makh, thang, nam, tongngansach)
                    VALUES (?, ?, ?, ?)";
            return $this->insertPrepared($sql, "iiid", [$makh, $thang, $nam, $tongngansach]);
        }
    }

    public function getLapNganSachList($makh, $limit = 12, $offset = 0) {
        $sql = "SELECT mangansach, makh, thang, nam, tongngansach 
                FROM LAPNGANSACHTHEOTHANG 
                WHERE makh=? 
                ORDER BY nam DESC, thang DESC 
                LIMIT ? OFFSET ?";
        $res = $this->queryPrepared($sql, "iii", [$makh, $limit, $offset]);
        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function deleteLapNganSach($mangansach, $makh = null) {
        // If makh is provided, ensure ownership
        if ($makh) {
            $sql = "DELETE FROM LAPNGANSACHTHEOTHANG WHERE mangansach=? AND makh=?";
            return $this->updatePrepared($sql, "ii", [$mangansach, $makh]);
        }
        
        $sql = "DELETE FROM LAPNGANSACHTHEOTHANG WHERE mangansach=?";
        return $this->updatePrepared($sql, "i", [$mangansach]);
    }

    public function getTotalBudgetByYear($makh, $nam) {
        $sql = "SELECT SUM(tongngansach) as total FROM LAPNGANSACHTHEOTHANG 
                WHERE makh=? AND nam=?";
        $res = $this->queryPrepared($sql, "ii", [$makh, $nam]);
        if (!$res) return 0;
        $row = $res->fetch_assoc();
        return (float)($row['total'] ?? 0);
    }

    public function countNganSachByUser($makh) {
        $sql = "SELECT COUNT(*) as total FROM LAPNGANSACHTHEOTHANG WHERE makh=?";
        $res = $this->queryPrepared($sql, "i", [$makh]);
        if (!$res) return 0;
        $row = $res->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    public function getAllNganSach($makh = null) {
        if ($makh) {
            $sql = "SELECT * FROM LAPNGANSACHTHEOTHANG WHERE makh=? ORDER BY nam DESC, thang DESC";
            $res = $this->queryPrepared($sql, "i", [$makh]);
        } else {
            $sql = "SELECT * FROM LAPNGANSACHTHEOTHANG ORDER BY makh, nam DESC, thang DESC";
            $res = $this->queryPrepared($sql, "", []);
        }
        
        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // --------- NGÂN SÁCH CHI TIẾT (NGANSACH TABLE) ---------
    // Store detailed budget breakdown per category
    
    public function getNganSachChiTiet($makh, $thang, $nam) {
        $sql = "SELECT * FROM NGANSACH WHERE makh=? AND MONTH(ngay)=? AND YEAR(ngay)=? LIMIT 1";
        $res = $this->queryPrepared($sql, "iii", [$makh, $thang, $nam]);
        return $res ? $res->fetch_assoc() : null;
    }

    public function saveNganSachChiTiet($makh, $ngay, $tiennha = 0, $anuong = 0, $dilai = 0, $muasam = 0) {
        $thang = date('n', strtotime($ngay));
        $nam = date('Y', strtotime($ngay));
        
        // Check if exists
        $existing = $this->getNganSachChiTiet($makh, $thang, $nam);
        
        if ($existing) {
            $sql = "UPDATE NGANSACH SET tiennha=?, anuong=?, dilai=?, muasam=? 
                    WHERE makh=? AND MONTH(ngay)=? AND YEAR(ngay)=?";
            return $this->updatePrepared($sql, "dddiii", [$tiennha, $anuong, $dilai, $muasam, $makh, $thang, $nam]);
        } else {
            $sql = "INSERT INTO NGANSACH (makh, machitieu, ngay, tiennha, anuong, dilai, muasam) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            return $this->insertPrepared($sql, "iisddd", [$makh, 1, $ngay, $tiennha, $anuong, $dilai, $muasam]);
        }
    }

    public function getBudgetBreakdown($makh, $thang, $nam) {
        $sql = "SELECT tiennha, anuong, dilai, muasam, ngansach as tong_ngan_sach 
                FROM NGANSACH 
                WHERE makh=? AND MONTH(ngay)=? AND YEAR(ngay)=? 
                LIMIT 1";
        $res = $this->queryPrepared($sql, "iii", [$makh, $thang, $nam]);
        return $res ? $res->fetch_assoc() : null;
    }

    public function getMonthlyBudgetSummary($makh, $thang, $nam) {
        $budget = $this->getBudgetBreakdown($makh, $thang, $nam);
        $expenses = $this->getChiTieuByMonth($makh, $thang, $nam);
        
        $result = [
            'budget' => $budget ?? [],
            'expenses' => $expenses ?? [],
            'total_budget' => (float)($budget['tong_ngan_sach'] ?? 0),
            'total_expense' => 0
        ];
        
        foreach ($expenses as $exp) {
            $result['total_expense'] += (float)$exp['tongtien'];
        }
        
        $result['remaining'] = $result['total_budget'] - $result['total_expense'];
        $result['percentage'] = $result['total_budget'] > 0 ? ($result['total_expense'] / $result['total_budget']) * 100 : 0;
        
        return $result;
    }

    // --------- HELPER ------------------
    public function insertPrepared($sql, $types, $params) {
        $stmt = self::$_connection->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param($types, ...$params);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public function updatePrepared($sql, $types, $params) {
        return $this->insertPrepared($sql, $types, $params);
    }

    // --------- SEARCH (TÌM KIẾM) ---------

    /**
     * Tìm kiếm chi tiêu theo từ khóa (noidung hoặc danh mục)
     * Tìm kiếm trong DSCHITIEU, JOIN với DMCHITIEU
     */
    public function searchExpenses($makh, $keyword, $limit = 20, $offset = 0) {
        $sql = "SELECT d.maloaichitieu, d.ngaychitieu, d.noidung, d.sotien, 
                       IFNULL(dm.tendanhmuc, 'Khác') AS tendanhmuc
                FROM DSCHITIEU d
                LEFT JOIN DMCHITIEU dm ON d.machitieu = dm.machitieu
                WHERE d.makh = ? AND (d.noidung LIKE ? OR dm.tendanhmuc LIKE ?)
                ORDER BY d.ngaychitieu DESC
                LIMIT ? OFFSET ?";
        $keyword_like = "%" . trim($keyword) . "%";
        $res = $this->queryPrepared($sql, "isssii", [$makh, $keyword_like, $keyword_like, $limit, $offset]);
        $out = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $out[] = $row;
            }
        }
        return $out;
    }

    /**
     * Đếm kết quả tìm kiếm chi tiêu
     */
    public function countSearchExpenses($makh, $keyword) {
        $sql = "SELECT COUNT(*) as total FROM DSCHITIEU d
                LEFT JOIN DMCHITIEU dm ON d.machitieu = dm.machitieu
                WHERE d.makh = ? AND (d.noidung LIKE ? OR dm.tendanhmuc LIKE ?)";
        $keyword_like = "%" . trim($keyword) . "%";
        $res = $this->queryPrepared($sql, "iss", [$makh, $keyword_like, $keyword_like]);
        if (!$res) return 0;
        $row = $res->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Tìm kiếm tất cả (KHOANCHI, DSCHITIEU, DMCHITIEU)
     * Trả về kết quả hợp nhất từ nhiều bảng
     */
    public function globalSearch($makh, $keyword, $limit = 50) {
        $results = [
            'khoanni' => [],      // từ KHOANCHI (lịch khoản chi)
            'chitieu' => [],      // từ DSCHITIEU (danh sách chi tiêu)
            'danhmuc' => []       // từ DMCHITIEU (danh mục)
        ];
        
        $keyword_param = "%" . trim($keyword) . "%";
        
        // Tìm trong KHOANCHI
        $sql_khoanni = "SELECT machi, tenkhoanchi, sotien, ngaybatdau, 'khoanni' as loai 
                        FROM KHOANCHI WHERE tenkhoanchi LIKE ? LIMIT ?";
        $res1 = $this->queryPrepared($sql_khoanni, "si", [$keyword_param, $limit]);
        if ($res1) {
            while ($row = $res1->fetch_assoc()) {
                $results['khoanni'][] = $row;
            }
        }
        
        // Tìm trong DSCHITIEU
        $sql_chitieu = "SELECT d.maloaichitieu, d.ngaychitieu, d.noidung, d.sotien, 
                        IFNULL(dm.tendanhmuc, 'Khác') AS tendanhmuc, 'chitieu' as loai
                        FROM DSCHITIEU d
                        LEFT JOIN DMCHITIEU dm ON d.machitieu = dm.machitieu
                        WHERE d.makh = ? AND (d.noidung LIKE ? OR dm.tendanhmuc LIKE ?)
                        LIMIT ?";
        $res2 = $this->queryPrepared($sql_chitieu, "isssi", [$makh, $keyword_param, $keyword_param, $limit]);
        if ($res2) {
            while ($row = $res2->fetch_assoc()) {
                $results['chitieu'][] = $row;
            }
        }
        
        // Tìm trong DMCHITIEU
        $sql_danhmuc = "SELECT machitieu, tendanhmuc, 'danhmuc' as loai 
                        FROM DMCHITIEU WHERE makh = ? AND tendanhmuc LIKE ? LIMIT ?";
        $res3 = $this->queryPrepared($sql_danhmuc, "isi", [$makh, $keyword_param, $limit]);
        if ($res3) {
            while ($row = $res3->fetch_assoc()) {
                $results['danhmuc'][] = $row;
            }
        }
        
        return $results;
    }

    // ------------------ INCOME (SUAKHOANTHUNHAP) ------------------

    /**
     * Lấy thông tin khoản thu nhập để sửa
     */
    public function getIncomeById($mathuanhap, $makh) {
        $sql = "SELECT * FROM SUAKHOANTHUNHAP WHERE mathuanhap = ? AND makh = ? LIMIT 1";
        $res = $this->queryPrepared($sql, "ii", [$mathuanhap, $makh]);
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Cập nhật khoản thu nhập (SUAKHOANTHUNHAP)
     */
    public function updateIncome($mathuanhap, $makh, $tenkhoanthu, $sotien, $ngaynhan, $danhmuc, $mota) {
        $sql = "UPDATE SUAKHOANTHUNHAP SET tenkhoanthu = ?, sotien = ?, ngaynhan = ?, danhmuc = ?, mota = ? WHERE mathuanhap = ? AND makh = ?";
        return $this->updatePrepared($sql, "sdsssii", [$tenkhoanthu, $sotien, $ngaynhan, $danhmuc, $mota, $mathuanhap, $makh]);
    }
}
?>
