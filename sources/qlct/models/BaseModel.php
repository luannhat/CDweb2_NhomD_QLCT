<?php
require_once __DIR__ . '/../configs/database.php';

abstract class BaseModel
{
    protected static $_connection = null;

    public function __construct() {
        if (self::$_connection === null) {
            $host = DB_HOST;
            $user = DB_USER;
            $pass = DB_PASSWORD;
            $db   = DB_NAME;
            $port = DB_PORT;

            self::$_connection = new mysqli($host, $user, $pass, $db, $port);

            if (self::$_connection->connect_errno) {
                die("❌ Failed to connect to MySQL: " . self::$_connection->connect_error);
            }
            
            // Set charset để hỗ trợ tiếng Việt
            self::$_connection->set_charset("utf8mb4");
        }
    }

    protected function query($sql)
    {
        if (!self::$_connection) {
            throw new Exception("Database connection not established.");
        }

        $result = self::$_connection->query($sql);

        if ($result === false) {
            throw new Exception("❌ Query failed: " . self::$_connection->error);
        }

        return $result;
    }

    protected function select($sql)
    {
        $result = $this->query($sql);
        $rows = [];

        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    protected function insert($sql)
    {
        return $this->query($sql);
    }

    protected function update($sql)
    {
        return $this->query($sql);
    }

    protected function delete($sql)
    {
        return $this->query($sql);
    }
}
