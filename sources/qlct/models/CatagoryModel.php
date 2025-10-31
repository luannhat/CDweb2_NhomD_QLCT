<?php
require_once __DIR__ . '/../models/BaseModel.php';

class DanhmucModel extends BaseModel {
    
    //  Thêm danh mục mới
    public function insertCatagories($makh, $tendanhmuc, $loai) {
        $conn = self::$_connection;
        $makh = intval($makh);
        $tendanhmuc = $conn->real_escape_string($tendanhmuc);
        $loai = $conn->real_escape_string($loai);

        $sql = "INSERT INTO DMCHITIEU (makh, tendanhmuc, loai)
                VALUES ($makh, '$tendanhmuc', '$loai')";
        
        if ($conn->query($sql)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => $conn->error];
        }
    }

    //  Lấy toàn bộ danh mục theo mã khách hàng
    public function getAllCatagory($makh) {
        $conn = self::$_connection;
        $makh = intval($makh);
        $sql = "SELECT * FROM DMCHITIEU ";
        $result = $conn->query($sql);

        $rows = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    //  Lấy thông tin 1 danh mục theo mã
    public function getCatagoriesById($machitieu) {
        $conn = self::$_connection;
        $machitieu = intval($machitieu);
        $sql = "SELECT * FROM DMCHITIEU WHERE machitieu = $machitieu LIMIT 1";
        $result = $conn->query($sql);

        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
    }

    //  Cập nhật danh mục
    public function updateCatagories($machitieu, $tendanhmuc, $loai) {
        $conn = self::$_connection;
        $machitieu = intval($machitieu);
        $tendanhmuc = $conn->real_escape_string($tendanhmuc);
        $loai = $conn->real_escape_string($loai);

        $sql = "UPDATE DMCHITIEU 
                SET tendanhmuc = '$tendanhmuc', loai = '$loai', updated_at = NOW() 
                WHERE machitieu = $machitieu";

        if ($conn->query($sql)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => $conn->error];
        }
    }
    //xóa danh mục đc chọn
    public function deleteCatagories($machitieu) {
        $conn = self::$_connection;
        $machitieu = intval($machitieu);

        $sql = "DELETE FROM DMCHITIEU WHERE machitieu = $machitieu";

        if ($conn->query($sql)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => $conn->error];
        }
    }
}
