# Bản Tóm Tắt Các Thay Đổi - Tính Năng Thống Kê Chi Tiêu Tuần

## Tổng Quan
Đã thêm tính năng "Thống kê chi tiêu theo tuần" vào ứng dụng quản lý chi tiêu. Tính năng này cho phép người dùng xem chi tiết chi tiêu được phân chia theo 4 tuần trong tháng, với biểu đồ so sánh và danh sách chi tiết.

---

## 📁 Các Tệp Tạo Mới

### 1. Views (Giao Diện)

#### `sources/qlct/views/thongke_chi_tieu_tuan_main.php`
- **Mục đích**: Trang chính để hiển thị thống kê tuần
- **Tính năng**:
  - HTML layout đầy đủ với header, sidebar, footer
  - Tích hợp CSS từ file khoanchi.css
  - Session check để bảo mật
  - Gọi controller action tương ứng

#### `sources/qlct/views/thongke_chi_tieu_tuan.php`
- **Mục đích**: Component hiển thị bảng thống kê tuần
- **Tính năng**:
  - Form chọn năm/tháng
  - Bảng thống kê 4 tuần với cột: Tuần, Thu nhập, Chi tiêu, Chênh lệch
  - Biểu đồ cột so sánh thu nhập và chi tiêu
  - Responsive design
  - Styling CSS inline

#### `sources/qlct/views/thongke_chi_tieu_tuan_detail.php`
- **Mục đfischer**: Trang chi tiết chi tiêu của một tuần cụ thể
- **Tính năng**:
  - Thẻ thông tin tóm tắt (Tháng, Tuần, Thu nhập, Chi tiêu, Chênh lệch)
  - Danh sách chi tiêu chi tiết theo danh mục
  - Biểu đồ tròn phân bố chi tiêu theo danh mục
  - Nút quay lại
  - Responsive design

---

## 🔧 Các Tệp Được Sửa Đổi

### 1. Controller

#### `sources/qlct/controllers/StatisticalController.php`
**Các method thêm mới:**

- `weeklyStatistics()`
  - Lấy dữ liệu 4 tuần từ model
  - Xử lý tham số GET (year, month)
  - Tính toán tổng theo tuần
  - Include view thongke_chi_tieu_tuan.php

- `weeklyDetail()`
  - Lấy chi tiết chi tiêu của một tuần
  - Xử lý tham số GET (year, month, week)
  - Nhóm giao dịch theo danh mục
  - Include view thongke_chi_tieu_tuan_detail.php

### 2. Model

#### `sources/qlct/models/StatisticalModel.php`
**Method thêm mới:**

- `getWeeklyExpenseDetails($makh, $year, $month, $week)`
  - Lấy tất cả giao dịch chi tiêu trong một tuần cụ thể
  - Chia tuần dựa vào ngày (1-7, 8-14, 15-21, 22-31)
  - Lọc theo makh (mã khách hàng)
  - Sắp xếp theo ngày giao dịch giảm dần
  - Return: Array của các giao dịch

### 3. Views Hiện Tại

#### `sources/qlct/views/layouts/sidebar.php`
**Thay đổi:**
- Cập nhật logic active state để bao gồm tính năng mới
- Thêm file `thongke_chi_tieu_tuan.php` và action `weeklyStatistics`, `weeklyDetail` vào check
- Vẫn sử dụng link "Báo cáo" thống nhất

#### `sources/qlct/views/baocao.php`
**Thay đổi:**
- Cập nhật handler sự kiện nút "Trong tuần"
- Thay vì `console.log`, giờ chuyển hướng đến `thongke_chi_tieu_tuan_main.php?action=weeklyStatistics`

---

## 📊 Luồng Dữ Liệu

```
User
  ↓
[Nhấp nút "Báo cáo" → "Trong tuần"]
  ↓
baocao.php (event handler)
  ↓
thongke_chi_tieu_tuan_main.php?action=weeklyStatistics
  ↓
StatisticalController::weeklyStatistics()
  ↓
StatisticalModel::getWeeklyIncomeExpenseByMonth($makh, $year, $month)
  ↓
[Trả về dữ liệu 4 tuần]
  ↓
thongke_chi_tieu_tuan.php (render bảng + biểu đồ)
```

---

## 🗄️ Cấu Trúc Dữ Liệu

### Input Parameters
- `year`: Năm (mặc định: năm hiện tại)
- `month`: Tháng (mặc định: tháng hiện tại)
- `week`: Số tuần (1-4) cho chi tiết

### Database Tables
- **DSTHUNHAP**: 
  - Cột: makh, sotien, ngaythunhap
- **DSCHITIEU**:
  - Cột: makh, machitieu, sotien, ngaychitieu, ghichu, loai

### Data Returned
```php
// getWeeklyIncomeExpenseByMonth
[
  [
    'label' => 'Tuần 1',
    'thu_nhap' => 5000000,
    'chi_tieu' => 3000000
  ],
  ...
]

// getWeeklyExpenseDetails
[
  [
    'machitieu' => 'DM001',
    'sotien' => 500000,
    'ngaychitieu' => '2025-01-15',
    'ghichu' => 'Ăn trưa',
    'category_id' => 'DM001'
  ],
  ...
]
```

---

## 🎨 UI/UX Features

1. **Responsive Design**
   - Mobile-friendly layout
   - Grid system cho summary cards
   - Scrollable tables

2. **Color Coding**
   - Xanh: Thu nhập, tiết kiệm
   - Đỏ: Chi tiêu, thâu chi
   - Xám: Hàng tổng cộng

3. **Biểu Đồ**
   - Chart.js cho biểu đồ cột
   - Chart.js cho biểu đồ tròn
   - Responsive options

4. **Interactivity**
   - Dropdown auto-submit
   - Hover effects trên hàng bảng
   - Click vào chi tiết tuần

---

## 🔐 Bảo Mật

- Session check ở controller
- Session check ở view main
- User chỉ nhìn dữ liệu của chính mình (`$this->makh`)
- Escape HTML output với `htmlspecialchars()`

---

## 📈 Hiệu Suất

- Query tối ưu với prepared statements (getWeeklyIncomeExpenseByMonth)
- Tính toán nhóm trên database (GROUP BY)
- Lazy loading không áp dụng (bảng không quá lớn)

---

## 🧪 Testing Checklist

- [ ] Kiểm tra khi không có dữ liệu trong tuần
- [ ] Kiểm tra hiển thị khi có nhiều giao dịch
- [ ] Kiểm tra responsive trên mobile
- [ ] Kiểm tra biểu đồ load đúng
- [ ] Kiểm tra link chi tiết tuần hoạt động
- [ ] Kiểm tra quay lại từ chi tiết
- [ ] Kiểm tra dropdown auto-submit
- [ ] Kiểm tra format tiền tệ (VND)

---

## 📝 Hướng Dẫn Sử Dụng

Xem file: `HUONG_DAN_THONGKE_CHI_TIEU_TUAN.md`

---

## 🚀 Tính Năng Có Thể Mở Rộng

1. **Export PDF/Excel** cho báo cáo tuần
2. **So sánh nhiều tuần** trên cùng một biểu đồ
3. **Cảnh báo** khi chi tiêu vượt ngân sách
4. **Dự báo** chi tiêu hàng tuần
5. **Thẻ gợi ý** cách tiết kiệm
6. **Widget mini** cho dashboard

---

## 📞 Hỗ Trợ

Các tệp liên quan:
- `README.md` - Tài liệu tổng quát dự án
- `DEBUG_GUIDE.md` - Hướng dẫn debug
- `TROUBLESHOOTING.md` - Khắc phục sự cố
