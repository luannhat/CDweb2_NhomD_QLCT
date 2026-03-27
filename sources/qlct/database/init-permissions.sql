-- Script tự động cấp quyền cho user khi khởi tạo MySQL container
-- File này nên được mount vào /docker-entrypoint-initdb.d/ trong Docker

-- Cấp quyền cho user 'user' trên database QLCT
GRANT ALL PRIVILEGES ON QLCT.* TO 'user'@'%';
GRANT ALL PRIVILEGES ON qlct.* TO 'user'@'%';

-- Cấp quyền cho user 'user' trên database qlct_db (nếu có)
GRANT ALL PRIVILEGES ON qlct_db.* TO 'user'@'%';

-- Áp dụng thay đổi
FLUSH PRIVILEGES;

-- Hiển thị quyền đã cấp
SHOW GRANTS FOR 'user'@'%';

