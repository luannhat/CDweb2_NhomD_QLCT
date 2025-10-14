create database QLCT
go
use QLCT
go
set dateformat dmy
go

--bảng khách hàng
CREATE TABLE [KHACHHANG] (
    makh INT IDENTITY(1,1) PRIMARY KEY,  -- Khóa chính tự tăng
    tenkh NVARCHAR(100) NOT NULL,            -- Tên người dùng
    email NVARCHAR(150) NOT NULL UNIQUE,    -- Email duy nhất
    matkhau NVARCHAR(255) NOT NULL,        -- Mật khẩu (hash)
    hinhanh NVARCHAR(255) NULL,              -- Ảnh đại diện (có thể null)
    quyen NVARCHAR(20) NOT NULL DEFAULT 'user', -- Phân quyền (user/admin)
    created_at DATETIME DEFAULT GETDATE(),  -- Thời điểm tạo
    updated_at DATETIME DEFAULT GETDATE()   -- Thời điểm cập nhật
);
--bảng danh mục chi tiêu
CREATE TABLE DMCHITIEU (
    machitieu INT IDENTITY(1,1) PRIMARY KEY,     -- Khóa chính tự tăng
    makh INT NOT NULL,                          -- Khóa ngoại liên kết User
    tendanhmuc NVARCHAR(100) NOT NULL,                   -- Tên danh mục (ăn uống, đi lại, lương…)
    loai NVARCHAR(20) NOT NULL CHECK (loai IN ('income', 'expense')), 
                                                   -- Loại (income hoặc expense)
    created_at DATETIME DEFAULT GETDATE(),         -- Thời điểm tạo
    updated_at DATETIME DEFAULT GETDATE(),         -- Thời điểm cập nhật

    CONSTRAINT FK_DMCHITIEU_KHACHHANG FOREIGN KEY (makh) REFERENCES [KHACHHANG](makh) 
        ON DELETE CASCADE ON UPDATE CASCADE
);
--bảng giao dịch
CREATE TABLE [GIAODICH] (
    magd INT IDENTITY(1,1) PRIMARY KEY,     -- Khóa chính tự tăng
    makh INT NOT NULL,                             -- FK → Users
    machitieu INT NOT NULL,                         -- FK → Categories
    sotien DECIMAL(18,2) NOT NULL,                    -- Số tiền (định dạng tiền tệ)
    loai NVARCHAR(20) NOT NULL CHECK (loai IN ('income', 'expense')), 
                                                     -- Thu nhập hoặc Chi tiêu
    [ngaygiaodich] DATE NOT NULL,                             -- Ngày giao dịch
    ghichu NVARCHAR(255) NULL,                          -- Ghi chú (có thể null)
    anhhoadon NVARCHAR(255) NULL,                    -- Ảnh hóa đơn (có thể null)
    created_at DATETIME DEFAULT GETDATE(),            -- Thời điểm tạo
    updated_at DATETIME DEFAULT GETDATE()            -- Thời điểm cập nhật

);
ALTER TABLE GIAODICH
ADD CONSTRAINT FK_GIAODICH_KHACHHANG FOREIGN KEY (makh)
    REFERENCES KHACHHANG(makh)
    ON DELETE NO ACTION ON UPDATE CASCADE;

ALTER TABLE GIAODICH
ADD CONSTRAINT FK_GIAODICH_DMCHITIEU
FOREIGN KEY (MaChiTieu)
REFERENCES DMCHITIEU(MaChiTieu)
ON DELETE NO ACTION
ON UPDATE NO ACTION;




--bảng ngân sách chi tiêu
CREATE TABLE NGANSACH (
    mangansach INT IDENTITY(1,1) PRIMARY KEY,          -- Khóa chính tự tăng
    makh INT NOT NULL,                             -- FK → Users
    machitieu INT NOT NULL,                         -- FK → Categories
    amount DECIMAL(18,2) NOT NULL,                    -- Số tiền ngân sách
    chuky NVARCHAR(20) NOT NULL 
        CHECK (chuky IN ('week', 'month', 'year')),  -- Chu kỳ ngân sách
    ngaybatdau DATE NOT NULL,                         -- Ngày bắt đầu
    ngayketthuc DATE NOT NULL,                           -- Ngày kết thúc
    created_at DATETIME DEFAULT GETDATE(),            -- Thời điểm tạo
    updated_at DATETIME DEFAULT GETDATE(),            -- Thời điểm cập nhật

    CONSTRAINT FK_NGANSACH_KHACHHANG FOREIGN KEY (makh) 
        REFERENCES [KHACHHANG](makh)
        ON DELETE CASCADE ON UPDATE CASCADE,



);
    ALTER TABLE NGANSACH
ADD CONSTRAINT FK_NGANSACH_DMCHITIEU
FOREIGN KEY (MaChiTieu)
REFERENCES DMCHITIEU(MaChiTieu)
ON DELETE NO ACTION
ON UPDATE NO ACTION;
-- bảng nợ
CREATE TABLE BANGNO (
    mabangno INT IDENTITY(1,1) PRIMARY KEY,           -- Khóa chính tự tăng
    makh INT NOT NULL,                            -- FK → Users
    sotienno DECIMAL(18,2) NOT NULL,                   -- Số tiền nợ
    loai NVARCHAR(10) NOT NULL 
        CHECK (loai IN ('lend', 'borrow')),          -- Cho vay hoặc Vay
    nguoilienquan NVARCHAR(100) NOT NULL,                   -- Tên người liên quan
    ngaydaohan DATE NOT NULL,                          -- Ngày đáo hạn
    trangthai NVARCHAR(10) NOT NULL 
        CHECK (trangthai IN ('pending', 'paid')),       -- Trạng thái nợ
    created_at DATETIME DEFAULT GETDATE(),           -- Thời điểm tạo
    updated_at DATETIME DEFAULT GETDATE(),           -- Thời điểm cập nhật

    CONSTRAINT FK_BANGNO_KHACHHANG FOREIGN KEY (makh) 
        REFERENCES [KHACHHANG](makh)
        ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE TIETKIEM (
    matk INT IDENTITY(1,1) PRIMARY KEY,          -- Khóa chính tự tăng
    makh INT NOT NULL,                             -- FK → Users
    muctieu NVARCHAR(150) NOT NULL,                 -- Mục tiêu (VD: mua xe, du lịch)
    sotiencandat DECIMAL(18,2) NOT NULL,             -- Số tiền cần đạt
    sotiendatietkiem DECIMAL(18,2) NOT NULL DEFAULT 0,  -- Số tiền đã tiết kiệm
    hanmuctieu DATE NOT NULL,                           -- Hạn mục tiêu
    created_at DATETIME DEFAULT GETDATE(),            -- Thời điểm tạo
    updated_at DATETIME DEFAULT GETDATE(),            -- Thời điểm cập nhật

    CONSTRAINT FK_TIETKIEM_KHACHHANG FOREIGN KEY (makh) 
        REFERENCES [KHACHHANG](makh)
        ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE THONGBAO (
    matb INT IDENTITY(1,1) PRIMARY KEY,   -- Khóa chính tự tăng
    makh INT NOT NULL,                            -- FK → Users
    noidung NVARCHAR(255) NOT NULL,                  -- Nội dung thông báo
    loai NVARCHAR(20) NOT NULL 
        CHECK (loai IN ('warning', 'reminder', 'info')), -- Loại thông báo
    trangthai BIT NOT NULL DEFAULT 0,                  -- Đã đọc hay chưa (0 = chưa, 1 = đã)
    created_at DATETIME DEFAULT GETDATE(),           -- Thời điểm tạo

    CONSTRAINT FK_THONGBAO_KHACHHANG FOREIGN KEY (makh) 
        REFERENCES [KHACHHANG](makh)
        ON DELETE CASCADE ON UPDATE CASCADE
);
