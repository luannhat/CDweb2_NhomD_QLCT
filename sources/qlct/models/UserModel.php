<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel
{
    private $conn;
    protected $table = 'KHACHHANG'; // Thay 'users' bằng tên bảng thật của bạn

    public function login($email, $password)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = '" . self::$_connection->real_escape_string($email) . "' LIMIT 1";
        $result = self::$_connection->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Nếu password đã hash
            if (isset($user['matkhau']) && password_verify($password, $user['matkhau'])) {
                return $user;
            }

            // Nếu password plain text (dev/test)
            if (isset($user['matkhau']) && $password === $user['matkhau']) {
                return $user;
            }
        }

        return false;
    }


    public function findUserById($id)
    {
        $sql = 'SELECT * FROM users WHERE id = ' . $id;
        $user = $this->select($sql);

        return $user;
    }

    public function findUser($keyword)
    {
        $sql = 'SELECT * FROM users WHERE user_name LIKE "%' . $keyword . '%" OR user_email LIKE "%' . $keyword . '%"';

        $user = $this->select($sql);

        return $user;
    }

    /**
     * Authentication user
     * @param $userName
     * @param $password
     * @return array
     */
    public function auth($userName, $password)
    {
        $md5Password = md5($password);
        $sql = 'SELECT * FROM users WHERE name = "' . $userName . '" AND password = "' . $md5Password . '"';

        $user = $this->select($sql);
        return $user;
    }

    /**
     * Delete user by id
     * @param $id
     * @return mixed
     */
    public function deleteUserById($id)
    {
        $sql = 'DELETE FROM users WHERE id = ' . $id;
        return $this->delete($sql);
    }

    /**
     * Update user
     * @param $input
     * @return mixed
     */
    public function updateUser($input)
    {
        $sql = 'UPDATE users SET 
                 name = "' . mysqli_real_escape_string(self::$_connection, $input['name']) . '", 
                 password="' . md5($input['password']) . '"
                WHERE id = ' . $input['id'];

        $user = $this->update($sql);

        return $user;
    }

    /**
     * Insert user
     * @param $input
     * @return mixed
     */
    public function insertUser($input)
    {
        $sql = "INSERT INTO `app_web1`.`users` (`name`, `password`) VALUES (" .
            "'" . $input['name'] . "', '" . md5($input['password']) . "')";

        $user = $this->insert($sql);

        return $user;
    }

    /**
     * Search users
     * @param array $params
     * @return array
     */
    public function getUsers($params = [])
    {
        //Keyword
        if (!empty($params['keyword'])) {
            $sql = 'SELECT * FROM users WHERE name LIKE "%' . $params['keyword'] . '%"';

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
    //set avartar
    public function updateAvatar($id, $path)
    {
        $sql = "UPDATE KHACHHANG SET hinhanh = ? WHERE makh = ?";
        $stmt = self::$_connection->prepare($sql);
        $stmt->bind_param("si", $path, $id);
        return $stmt->execute();
    }
}
