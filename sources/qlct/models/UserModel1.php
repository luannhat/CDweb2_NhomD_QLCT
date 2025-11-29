<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/BaseModel.php';
class UserModel extends BaseModel {
   private $table = "KHACHHANG";

    public function layKhachHangTheoId($makh) {
        $sql = "SELECT * FROM {$this->table} WHERE makh = ? LIMIT 1";
        $res = $this->queryPrepared($sql, "i", [$makh]);
        if ($res) return $res->fetch_assoc();
        return false;
    }

    public function capNhatHoSo(array $data) {
        $sql = "UPDATE {$this->table} 
                SET tenkh=?, email=?, matkhau=?, hinhanh=?, sodienthoai=?, ngaysinh=?, gioitinh=?, diachi=?, updated_at=NOW() 
                WHERE makh=?";
        return $this->queryPrepared(
            $sql,
            "ssssssssi",
            [
                $data['tenkh'],
                $data['email'],
                $data['matkhau'],
                $data['hinhanh'],
                $data['sodienthoai'],
                $data['ngaysinh'],
                $data['gioitinh'],
                $data['diachi'],
                $data['makh']
            ]
        );
    }


    // Wrapper for clarity
    public function getUserById(int $id)
    {
        return $this->layKhachHangTheoId($id);
    }

    public function getUserByEmail(string $email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        $res = $this->queryPrepared($sql, "s", [$email]);
        if ($res) return $res->fetch_assoc();
        return false;
    }

    // Login using secure password hashing
    public function login(string $email, string $password)
    {
        $user = $this->getUserByEmail($email);
        if ($user && isset($user['matkhau']) && password_verify($password, $user['matkhau'])) {
            return $user;
        }
        return false;
    }

    // NOTE: Deprecated / removed insecure raw query methods that referenced a different `users` table.
}