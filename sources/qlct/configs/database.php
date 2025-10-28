<?php
// Cấu hình database - ưu tiên biến môi trường từ Docker
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'user');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'pass');
define('DB_PORT', getenv('DB_PORT') ?: 3306);
define('DB_NAME', getenv('DB_NAME') ?: 'QLCT');
