# Hướng Dẫn Xử Lý Lỗi

## Lỗi: Access denied for user 'user'@'%' to database

### Mô tả lỗi
```
Fatal error: Uncaught mysqli_sql_exception: Access denied for user 'user'@'%' to database 'qlct'
```

### Nguyên nhân
User `user` trong MySQL không có quyền truy cập database `QLCT`.

### Giải pháp

#### 1. Cấp quyền thủ công
```bash
docker exec mysql-db mysql -uroot -proot -e "GRANT ALL PRIVILEGES ON QLCT.* TO 'user'@'%'; FLUSH PRIVILEGES;"
```

#### 2. Kiểm tra kết nối
```bash
# Chạy trong container
docker exec php-web php /var/www/html/init_database.php

# Hoặc chạy local (nếu có PHP)
php sources/qlct/init_database.php
```

#### 3. Kiểm tra quyền hiện tại
```bash
docker exec mysql-db mysql -uroot -proot -e "SHOW GRANTS FOR 'user'@'%';"
```

### Kiểm tra Docker container

```bash
# Xem các container đang chạy
docker ps

# Khởi động lại containers
docker-compose down
docker-compose up -d

# Xem logs
docker logs mysql-db
docker logs php-web
```

## Lỗi: Database không có dữ liệu

### Giải pháp: Import dữ liệu từ file SQL

```bash
# Import schema
docker exec -i mysql-db mysql -uroot -proot QLCT < sources/qlct/database/QLCT.sql

# Import dữ liệu mẫu
docker exec -i mysql-db mysql -uroot -proot QLCT < sources/qlct/database/seed_data.sql
```

## Lỗi: Port đã được sử dụng

### Mô tả
```
Error: Port 3307 is already allocated
```

### Giải pháp
Thay đổi port trong `Docker-compose.yml`:
```yaml
ports:
  - "3308:3306"  # Đổi 3307 -> 3308
```

## Các lệnh hữu ích

### Truy cập MySQL trong container
```bash
# Dùng root
docker exec mysql-db mysql -uroot -proot QLCT

# Dùng user thường
docker exec mysql-db mysql -uuser -ppass QLCT
```

### Xem logs PHP
```bash
docker logs php-web -f
```

### Restart services
```bash
docker-compose restart web
docker-compose restart db
```

### Xóa và tạo lại containers
```bash
docker-compose down
docker-compose up -d --build
```

## Checklist khi gặp lỗi

- [ ] Docker containers đang chạy? (`docker ps`)
- [ ] Database QLCT tồn tại? (`docker exec mysql-db mysql -uroot -proot -e "SHOW DATABASES;"`)
- [ ] User có quyền truy cập? (`docker exec mysql-db mysql -uroot -proot -e "SHOW GRANTS FOR 'user'@'%';"`)
- [ ] Các bảng đã được tạo? (`docker exec mysql-db mysql -uuser -ppass QLCT -e "SHOW TABLES;"`)
- [ ] File config đúng? (Kiểm tra `configs/database.php`)
- [ ] Biến môi trường đúng? (Kiểm tra `Docker-compose.yml`)

## Thông tin kết nối

### Từ application (trong Docker)
- Host: `db`
- User: `user`
- Password: `pass`
- Database: `QLCT`
- Port: `3306`

### Từ máy host (localhost)
- Host: `localhost`
- User: `root`
- Password: `root`
- Database: `QLCT`
- Port: `3307`

### phpMyAdmin
- URL: http://localhost:8081
- User: `root`
- Password: `root`

### Web Application
- URL: http://localhost:8080/views/khoanchi.php

