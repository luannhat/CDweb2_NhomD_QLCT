-- Thêm dữ liệu mẫu cho hệ thống quản lý chi tiêu

USE QLCT;

-- Thêm khách hàng mẫu
INSERT INTO KHACHHANG (tenkh, email, matkhau, quyen) VALUES 
('Văn A', 'vana@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Nguyễn B', 'nguyenb@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Trần C', 'tranc@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Thêm danh mục chi tiêu cho khách hàng 1 (Văn A)
INSERT INTO DMCHITIEU (makh, tendanhmuc, loai) VALUES 
(1, 'Ăn uống', 'expense'),
(1, 'Hóa đơn', 'expense'),
(1, 'Đi lại', 'expense'),
(1, 'Mua sắm', 'expense'),
(1, 'Giáo dục', 'expense'),
(1, 'Y tế', 'expense'),
(1, 'Giải trí', 'expense'),
(1, 'Khác', 'expense');

-- Thêm danh mục thu nhập cho khách hàng 1
INSERT INTO DMCHITIEU (makh, tendanhmuc, loai) VALUES 
(1, 'Lương', 'income'),
(1, 'Thưởng', 'income'),
(1, 'Đầu tư', 'income'),
(1, 'Khác', 'income');

-- Thêm một số khoản chi mẫu cho khách hàng 1
INSERT INTO GIAODICH (makh, machitieu, noidung, sotien, loai, ngaygiaodich, ghichu) VALUES 
(1, 1, 'Mua đồ ăn trưa', 50000, 'expense', '2024-01-15', 'Ăn trưa tại quán cơm'),
(1, 1, 'Cà phê với bạn', 30000, 'expense', '2024-01-14', 'Hẹn bạn uống cà phê'),
(1, 2, 'Thanh toán điện nước', 450000, 'expense', '2024-01-13', 'Hóa đơn tháng 1'),
(1, 2, 'Tiền nhà', 2000000, 'expense', '2024-01-01', 'Tiền thuê nhà tháng 1'),
(1, 3, 'Đi taxi', 150000, 'expense', '2024-01-12', 'Đi taxi từ sân bay về nhà'),
(1, 3, 'Xăng xe', 200000, 'expense', '2024-01-11', 'Đổ xăng cho xe máy'),
(1, 4, 'Mua quần áo', 800000, 'expense', '2024-01-10', 'Mua quần áo mùa đông'),
(1, 4, 'Mua sắm online', 300000, 'expense', '2024-01-09', 'Mua đồ trên Shopee'),
(1, 5, 'Mua sách', 250000, 'expense', '2024-01-08', 'Mua sách lập trình'),
(1, 5, 'Học phí khóa học', 1500000, 'expense', '2024-01-07', 'Đóng học phí khóa học online'),
(1, 6, 'Khám bệnh', 300000, 'expense', '2024-01-06', 'Khám bệnh định kỳ'),
(1, 6, 'Mua thuốc', 150000, 'expense', '2024-01-05', 'Mua thuốc cảm cúm'),
(1, 7, 'Xem phim', 200000, 'expense', '2024-01-04', 'Xem phim tại rạp'),
(1, 7, 'Chơi game', 100000, 'expense', '2024-01-03', 'Mua game trên Steam'),
(1, 8, 'Chi phí khác', 50000, 'expense', '2024-01-02', 'Chi phí không xác định');

-- Thêm một số khoản thu mẫu cho khách hàng 1
INSERT INTO GIAODICH (makh, machitieu, noidung, sotien, loai, ngaygiaodich, ghichu) VALUES 
(1, 9, 'Lương tháng 1', 15000000, 'income', '2024-01-01', 'Lương cơ bản'),
(1, 10, 'Thưởng cuối năm', 5000000, 'income', '2024-01-15', 'Thưởng Tết'),
(1, 11, 'Lãi đầu tư', 500000, 'income', '2024-01-10', 'Lãi từ chứng khoán'),
(1, 12, 'Thu nhập khác', 200000, 'income', '2024-01-05', 'Bán đồ cũ');

-- Thêm ngân sách mẫu cho khách hàng 1
INSERT INTO NGANSACH (makh, machitieu, ngay, ngansach, dachi) VALUES 
(1, 1, '2024-01-01', 1000000, 80000),  -- Ngân sách ăn uống: 1M, đã chi 80K
(1, 2, '2024-01-01', 3000000, 2450000), -- Ngân sách hóa đơn: 3M, đã chi 2.45M
(1, 3, '2024-01-01', 500000, 350000),  -- Ngân sách đi lại: 500K, đã chi 350K
(1, 4, '2024-01-01', 2000000, 1100000), -- Ngân sách mua sắm: 2M, đã chi 1.1M
(1, 5, '2024-01-01', 1000000, 1750000); -- Ngân sách giáo dục: 1M, đã chi 1.75M (vượt ngân sách)

-- Thêm thông báo mẫu
INSERT INTO THONGBAO (makh, noidung, loai, trangthai) VALUES 
(1, 'Bạn đã vượt ngân sách giáo dục tháng này', 'warning', 0),
(1, 'Nhắc nhở thanh toán hóa đơn điện nước', 'reminder', 0),
(1, 'Chúc mừng! Bạn đã tiết kiệm được 500K tháng này', 'info', 1);

-- Thêm báo cáo mẫu cho tháng 1/2024
INSERT INTO BAOCAO (makh, thang, nam, tongthunhap, tongchitieu) VALUES 
(1, 1, 2024, 20700000, 6200000);

-- Thêm tiết kiệm mẫu
INSERT INTO TIETKIEM (makh, muctieu, sotiencandat, sotiendatietkiem, hanmuctieu) VALUES 
(1, 'Mua xe máy mới', 25000000, 5000000, '2024-12-31'),
(1, 'Du lịch Hàn Quốc', 15000000, 2000000, '2024-06-30'),
(1, 'Mua nhà', 2000000000, 100000000, '2030-12-31');

-- Thêm bảng nợ mẫu
INSERT INTO BANGNO (makh, sotienno, loai, nguoilienquan, ngaydaohan, trangthai) VALUES 
(1, 5000000, 'borrow', 'Ngân hàng ABC', '2024-06-30', 'pending'),
(1, 2000000, 'lend', 'Bạn A', '2024-03-31', 'pending'),
(1, 1000000, 'borrow', 'Bạn B', '2024-02-28', 'paid');
