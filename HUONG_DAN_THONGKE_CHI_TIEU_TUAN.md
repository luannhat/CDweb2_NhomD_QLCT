# Hướng dẫn Sử dụng Tính năng Thống Kê Chi Tiêu Theo Tuần

## Giới thiệu
Tính năng "Thống kê chi tiêu theo tuần" cho phép bạn theo dõi và phân tích chi tiêu hàng tuần một cách chi tiết. Bạn có thể xem biểu đồ so sánh thu nhập và chi tiêu, phân bố chi tiêu theo danh mục, và xem chi tiết từng giao dịch.

## Cách Truy Cập

### Cách 1: Từ Menu Báo Cáo
1. Nhấp vào menu **"Báo cáo"** trong thanh bên trái
2. Trên trang báo cáo, trong phần **"Thống kê chi tiêu"**, nhấp nút **"Trong tuần"**
3. Trang sẽ chuyển đến **"Thống kê chi tiêu theo tuần"**

### Cách 2: Trực tiếp từ URL
Bạn cũng có thể truy cập trực tiếp bằng URL:
```
/views/thongke_chi_tieu_tuan_main.php
```

## Các Tính Năng

### 1. Chọn Năm và Tháng
- Sử dụng dropdown **"Chọn năm"** để chọn năm bạn muốn xem
- Sử dụng dropdown **"Chọn tháng"** để chọn tháng cụ thể
- Nhấp **"Xem thống kê"** hoặc dropdown sẽ tự động cập nhật khi bạn chọn

### 2. Bảng Thống Kê Tuần
Bảng hiển thị thông tin cho 4 tuần trong tháng:

| Cột | Mô tả |
|-----|-------|
| **Tuần** | Tuần 1, Tuần 2, Tuần 3 hoặc Tuần 4 |
| **Thu nhập** | Tổng tiền thu nhập trong tuần (màu xanh) |
| **Chi tiêu** | Tổng tiền chi tiêu trong tuần (màu đỏ) |
| **Chênh lệch** | Hiệu giữa thu nhập và chi tiêu |
| **Chi tiết** | Nút để xem chi tiết giao dịch |

**Giải thích:**
- **Tiết kiệm**: Thu nhập > Chi tiêu (chênh lệch dương, hiển thị xanh)
- **Thâu chi**: Thu nhập < Chi tiêu (chênh lệch âm, hiển thị đỏ)

### 3. Hàng Tổng Cộng
Hàng cuối cùng của bảng hiển thị **tổng cộng** của tháng:
- Tổng thu nhập toàn tháng
- Tổng chi tiêu toàn tháng
- Chênh lệch toàn tháng

### 4. Biểu Đồ Chi Tiêu Tuần
Biểu đồ cột so sánh:
- **Màu xanh**: Thu nhập theo từng tuần
- **Màu đỏ**: Chi tiêu theo từng tuần

Biểu đồ giúp bạn dễ dàng nhìn thấy xu hướng chi tiêu và thu nhập theo từng tuần.

## Xem Chi Tiết Tuần

### Cách Truy Cập
Nhấp vào nút **"📋"** (Chi tiết) ở cuối hàng tuần bạn muốn xem.

### Thông Tin Hiển Thị

#### Thẻ Thông Tin Tóm Tắt
Hiển thị 5 thẻ thông tin:
1. **Tháng**: Tháng và năm được chọn
2. **Tuần**: Tên tuần (Tuần 1-4)
3. **Thu nhập**: Tổng thu nhập tuần
4. **Chi tiêu**: Tổng chi tiêu tuần
5. **Chênh lệch**: Hiệu số và tình trạng tiết kiệm/thâu chi

#### Danh Sách Chi Tiêu Chi Tiết
Bảng liệt kê tất cả các giao dịch chi tiêu trong tuần:

| Cột | Mô tả |
|-----|-------|
| **Ngày giao dịch** | Ngày thực hiện giao dịch |
| **Danh mục** | Danh mục chi tiêu (ví dụ: Ăn uống, Mua sắm, v.v.) |
| **Số tiền** | Số tiền chi tiêu (hiển thị màu đỏ) |
| **Ghi chú** | Ghi chú thêm về giao dịch |

**Lưu ý**: 
- Dữ liệu được sắp xếp từ ngày mới nhất đến cũ nhất
- Các giao dịch được nhóm theo danh mục
- Mỗi danh mục có hàng **"Tổng"** để tính tổng chi tiêu theo danh mục

#### Biểu Đồ Phân Bố Chi Tiêu
Biểu đồ tròn (doughnut) hiển thị:
- Phân bố chi tiêu theo từng danh mục
- Số tiền và tỷ lệ phần trăm của mỗi danh mục
- Giúp bạn nhìn thấy danh mục nào tiêu hao tiền nhiều nhất

### Quay Lại
Nhấp nút **"← Quay lại"** để trở về trang thống kê tuần chính.

## Các Mốc Tuần

Các tuần được chia như sau:
- **Tuần 1**: Ngày 1-7 của tháng
- **Tuần 2**: Ngày 8-14 của tháng
- **Tuần 3**: Ngày 15-21 của tháng
- **Tuần 4**: Ngày 22-cuối tháng

## Mẹo Sử Dụng

1. **So sánh theo tháng**: Chọn các tháng khác nhau để so sánh hành vi chi tiêu
2. **Xác định tháng cao điểm**: Xem tổng cộng để biết tháng nào bạn chi tiêu nhiều nhất
3. **Phân tích danh mục**: Vào chi tiết tuần để xem danh mục nào tiêu hao tiền nhiều
4. **Đặt mục tiêu**: Dùng chênh lệch tiết kiệm/thâu chi để đặt mục tiêu hàng tuần

## Lưu Ý Quan Trọng

- Dữ liệu được tính từ bảng `DSTHUNHAP` (thu nhập) và `DSCHITIEU` (chi tiêu) trong cơ sở dữ liệu
- Chỉ hiển thị dữ liệu của người dùng đang đăng nhập
- Cần phải đăng nhập để sử dụng tính năng này
- Định dạng tiền tệ: VND (Đồng Việt Nam)
- Định dạng ngày: DD/MM/YYYY

## Các Tệp Liên Quan

### Controllers
- `sources/qlct/controllers/StatisticalController.php` - Xử lý logic thống kê

**Methods chính:**
- `weeklyStatistics()` - Hiển thị trang thống kê tuần
- `weeklyDetail()` - Hiển thị chi tiết tuần

### Models
- `sources/qlct/models/StatisticalModel.php` - Truy vấn dữ liệu

**Methods chính:**
- `getWeeklyIncomeExpenseByMonth($makh, $year, $month)` - Lấy dữ liệu 4 tuần
- `getWeeklyExpenseDetails($makh, $year, $month, $week)` - Lấy chi tiết tuần
- `getMonthsWithTransactions($makh, $year)` - Lấy danh sách tháng có giao dịch

### Views
- `sources/qlct/views/thongke_chi_tieu_tuan_main.php` - Layout chính
- `sources/qlct/views/thongke_chi_tieu_tuan.php` - Bảng thống kê tuần
- `sources/qlct/views/thongke_chi_tieu_tuan_detail.php` - Chi tiết tuần

### Cập nhật khác
- `sources/qlct/views/layouts/sidebar.php` - Cập nhật navigation
- `sources/qlct/views/baocao.php` - Cập nhật nút "Trong tuần"

## Cơ Sở Dữ Liệu

Tính năng này lấy dữ liệu từ các bảng:

1. **DSTHUNHAP** (Danh sách thu nhập)
   - `makh`: Mã khách hàng
   - `sotien`: Số tiền thu nhập
   - `ngaythunhap`: Ngày thu nhập

2. **DSCHITIEU** (Danh sách chi tiêu)
   - `makh`: Mã khách hàng
   - `sotien`: Số tiền chi tiêu
   - `ngaychitieu`: Ngày chi tiêu
   - `machitieu`: Mã danh mục chi tiêu
   - `ghichu`: Ghi chú

## Hỗ Trợ

Nếu gặp vấn đề:
1. Kiểm tra xem bạn đã đăng nhập chưa
2. Kiểm tra xem dữ liệu chi tiêu đã được nhập chưa
3. Kiểm tra xem năm và tháng được chọn có hợp lệ không
4. Xem lại các bản ghi chi tiêu trong trang "Khoản chi"
