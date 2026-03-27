# 🐛 Hướng Dẫn Debug PHP

## 1. Debug Đơn Giản - In ra màn hình/logs

### Cách 1: Dùng `var_dump()` và `print_r()`
```php
// Trong controller hoặc model
var_dump($data);  // In chi tiết kiểu dữ liệu
print_r($data);   // In dễ đọc hơn
die();            // Dừng thực thi để xem kết quả
```

### Cách 2: Dùng `error_log()` (Khuyên dùng)
```php
// Ghi vào file log, không ảnh hưởng hiển thị
error_log("Debug: " . print_r($data, true));
error_log("Giá trị sotien: " . $sotien);
```

### Xem logs:
```bash
# Xem logs realtime
docker logs php-web -f

# Hoặc xem trong container
docker exec php-web tail -f /var/log/apache2/error.log
```

---

## 2. Debug với Xdebug + VSCode/Cursor (Chuyên nghiệp)

### Bước 1: Cài đặt Xdebug vào Dockerfile

Cập nhật file `Dockerfile`:
```dockerfile
# Base image PHP + Apache
FROM php:8.2-apache

# Cài extension PHP cần cho MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Cài đặt Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Cấu hình Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Copy toàn bộ mã nguồn vào container
COPY . /var/www/html/

# Mở cổng 80 cho web
EXPOSE 80
```

### Bước 2: Rebuild container
```bash
docker-compose down
docker-compose up -d --build
```

### Bước 3: Cài extension trong VSCode/Cursor
Tìm và cài extension: **PHP Debug** (by Xdebug)

### Bước 4: Tạo file cấu hình debug

Tạo file `.vscode/launch.json`:
```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/var/www/html": "${workspaceFolder}/sources/qlct"
            }
        }
    ]
}
```

### Bước 5: Bắt đầu debug
1. Đặt breakpoint (click vào số dòng trong code)
2. Nhấn F5 hoặc Run → Start Debugging
3. Truy cập trang web
4. Code sẽ dừng tại breakpoint!

---

## 3. Debug Database với phpMyAdmin

Truy cập: http://localhost:8081
- User: `root`
- Password: `root`

Có thể:
- Xem dữ liệu trong bảng
- Chạy SQL query trực tiếp
- Kiểm tra cấu trúc bảng

---

## 4. Debug Helper Function

Tạo file `sources/qlct/helpers/debug.php`:
```php
<?php
/**
 * Debug helper functions
 */

// In đẹp và dừng
function dd($data) {
    echo '<pre style="background:#1e1e1e;color:#fff;padding:20px;border-radius:8px;">';
    print_r($data);
    echo '</pre>';
    die();
}

// In đẹp nhưng không dừng
function dump($data) {
    echo '<pre style="background:#1e1e1e;color:#fff;padding:20px;border-radius:8px;">';
    print_r($data);
    echo '</pre>';
}

// Log vào file
function debug_log($message, $data = null) {
    $log = "[" . date('Y-m-d H:i:s') . "] " . $message;
    if ($data !== null) {
        $log .= ": " . print_r($data, true);
    }
    error_log($log);
}
```

Sử dụng:
```php
require_once __DIR__ . '/helpers/debug.php';

dd($expenses);  // In và dừng
dump($data);    // In nhưng tiếp tục
debug_log("Controller data", $data);  // Ghi vào log
```

---

## 5. Debug SQL Queries

Trong BaseModel, thêm logging:
```php
protected function query($sql)
{
    error_log("SQL Query: " . $sql);  // Log câu query
    
    if (!self::$_connection) {
        throw new Exception("Database connection not established.");
    }

    $result = self::$_connection->query($sql);

    if ($result === false) {
        error_log("SQL Error: " . self::$_connection->error);  // Log lỗi
        throw new Exception("❌ Query failed: " . self::$_connection->error);
    }

    return $result;
}
```

---

## 6. Các Lệnh Debug Hữu Ích

### Kiểm tra PHP info
```bash
docker exec php-web php -i | grep xdebug
```

### Xem logs realtime
```bash
# Apache error log
docker logs php-web -f

# MySQL log
docker logs mysql-db -f
```

### Kiểm tra kết nối database
```bash
docker exec php-web php /var/www/html/init_database.php
```

### Vào container để debug
```bash
docker exec -it php-web bash
```

---

## 7. Debug Checklist

Khi gặp lỗi, kiểm tra theo thứ tự:

1. [ ] Xem logs: `docker logs php-web -f`
2. [ ] Kiểm tra database: `phpMyAdmin` hoặc `init_database.php`
3. [ ] Thêm `error_log()` vào code để trace
4. [ ] Dùng `var_dump()` để xem giá trị biến
5. [ ] Kiểm tra SQL query có đúng không
6. [ ] Xem network tab trong Chrome DevTools (F12)
7. [ ] Kiểm tra Console tab có lỗi JS không

---

## 8. Ví Dụ Debug Thực Tế

### Ví dụ: Debug form thêm khoản chi

```php
// Trong KhoanchiController::add()
public function add()
{
    error_log("=== DEBUG ADD EXPENSE ===");
    error_log("POST data: " . print_r($_POST, true));
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
        return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
    }

    $machitieu = intval($_POST['machitieu'] ?? 0);
    $noidung = trim($_POST['noidung'] ?? '');
    $sotien = floatval($_POST['sotien'] ?? 0);
    
    error_log("Parsed data - machitieu: $machitieu, noidung: $noidung, sotien: $sotien");
    
    // ... rest of code
}
```

Sau đó xem logs:
```bash
docker logs php-web -f
```

---

## 9. Tips Debug Nhanh

### Kiểm tra giá trị nhanh
```php
echo "<script>console.log('Debug:', " . json_encode($data) . ");</script>";
```

### Trace function calls
```php
error_log("Called from: " . debug_backtrace()[1]['function']);
```

### Đo thời gian thực thi
```php
$start = microtime(true);
// ... code
$time = microtime(true) - $start;
error_log("Execution time: " . round($time, 4) . "s");
```

---

## 10. Chrome DevTools (F12)

- **Console**: Xem lỗi JavaScript
- **Network**: Xem AJAX requests/responses
- **Application**: Kiểm tra cookies, session storage
- **Sources**: Debug JavaScript với breakpoints

---

**Khuyên dùng:**
- Học viên mới: Dùng `error_log()` + `docker logs`
- Nâng cao: Cài Xdebug + VSCode debugging

