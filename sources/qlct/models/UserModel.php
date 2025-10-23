<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel {

    public function findUserById($id) {
        $sql = 'SELECT * FROM users WHERE id = '.$id;
        $user = $this->select($sql);

        return $user;
    }

    public function findUser($keyword) {
        $sql = 'SELECT * FROM users WHERE user_name LIKE "%'.$keyword.'%" OR user_email LIKE "%'.$keyword.'%"';

        $user = $this->select($sql);

        return $user;
    }

    /**
     * Authentication user
     * @param $userName
     * @param $password
     * @return array
     */
    public function auth($userName, $password) {
        $md5Password = md5($password);
        $sql = 'SELECT * FROM users WHERE name = "' . $userName . '" AND password = "'.$md5Password.'"';

        $user = $this->select($sql);
        return $user;
    }

    /**
     * Delete user by id
     * @param $id
     * @return mixed
     */
    public function deleteUserById($id) {
        $sql = 'DELETE FROM users WHERE id = '.$id;
        return $this->delete($sql);

    }

    /**
     * Update user
     * @param $input
     * @return mixed
     */
    public function updateUser($input) {
        $sql = 'UPDATE users SET 
                 name = "' . mysqli_real_escape_string(self::$_connection, $input['name']) .'", 
                 password="'. md5($input['password']) .'"
                WHERE id = ' . $input['id'];

        $user = $this->update($sql);

        return $user;
    }

    /**
     * Insert user
     * @param $input
     * @return mixed
     */
    public function insertUser($input) {
        $sql = "INSERT INTO `app_web1`.`users` (`name`, `password`) VALUES (" .
                "'" . $input['name'] . "', '".md5($input['password'])."')";

        $user = $this->insert($sql);

        return $user;
    }

    /**
     * Search users
     * @param array $params
     * @return array
     */
    public function getUsers($params = []) {
        //Keyword
        if (!empty($params['keyword'])) {
            $sql = 'SELECT * FROM users WHERE name LIKE "%' . $params['keyword'] .'%"';

            //Keep this line to use Sql Injection
            //Don't change
            //Example keyword: abcef%";TRUNCATE banks;##
            $users = self::$_connection->multi_query($sql);

            //Get data
            $users = $this->query($sql);
        } else {
            $sql = 'SELECT * FROM users';
            $users = $this->select($sql);
        }

        return $users;
    }

    /**
     * Tìm user theo email
     * @param $email
     * @return array|null
     */
    public function findUserByEmail($email) {
        $sql = 'SELECT * FROM users WHERE user_email = "' . mysqli_real_escape_string(self::$_connection, $email) . '"';
        $user = $this->select($sql);
        return !empty($user) ? $user[0] : null;
    }

    /**
     * Tạo reset token cho user
     * @param $userId
     * @param $token
     * @param $expiry
     * @return bool
     */
    public function setResetToken($userId, $token, $expiry) {
        $sql = 'UPDATE users SET 
                 reset_token = "' . mysqli_real_escape_string(self::$_connection, $token) . '",
                 reset_token_expiry = "' . $expiry . '"
                WHERE id = ' . (int)$userId;
        
        return $this->update($sql);
    }

    /**
     * Lấy user theo reset token
     * @param $token
     * @return array|null
     */
    public function getUserByResetToken($token) {
        $sql = 'SELECT * FROM users WHERE reset_token = "' . mysqli_real_escape_string(self::$_connection, $token) . '"';
        $user = $this->select($sql);
        return !empty($user) ? $user[0] : null;
    }

    /**
     * Cập nhật mật khẩu và xóa reset token
     * @param $userId
     * @param $hashedPassword
     * @return bool
     */
    public function updatePasswordAndClearToken($userId, $hashedPassword) {
        $sql = 'UPDATE users SET 
                 password = "' . mysqli_real_escape_string(self::$_connection, $hashedPassword) . '",
                 reset_token = NULL,
                 reset_token_expiry = NULL
                WHERE id = ' . (int)$userId;
        
        return $this->update($sql);
    }

    /**
     * Gửi email reset password (placeholder - cần implement thực tế)
     * @param $email
     * @param $token
     * @return bool
     */
    public function sendResetPasswordEmail($email, $token) {
        // Trong thực tế, bạn cần implement gửi email thật
        // Đây chỉ là placeholder
        $resetLink = "http://localhost/sources/qlct/views/Reset_password.php?token=" . $token;
        
        // Log để test (trong production nên gửi email thật)
        error_log("Reset password link for $email: $resetLink");
        
        return true;
    }
}