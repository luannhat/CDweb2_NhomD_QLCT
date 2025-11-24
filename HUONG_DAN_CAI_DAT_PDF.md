# Hướng dẫn cài đặt thư viện PDF

Để sử dụng chức năng xuất PDF trong hệ thống, bạn cần cài đặt thư viện mPDF.

## Cách 1: Sử dụng Composer (Khuyến nghị)

1. Mở terminal/command prompt
2. Di chuyển đến thư mục gốc của project:
   ```bash
   cd D:\chuyendephattrienweb2\CDweb2_NhomD_QLCT
   ```
3. Chạy lệnh cài đặt:
   ```bash
   composer require mpdf/mpdf
   ```

## Cách 2: Tải thư viện về thủ công

1. Tải mPDF từ: https://github.com/mpdf/mpdf/releases
2. Giải nén và đặt vào thư mục: `sources/qlct/vendor/mpdf/mpdf/`
3. Đảm bảo file `sources/qlct/vendor/autoload.php` tồn tại

## Kiểm tra cài đặt

Sau khi cài đặt, kiểm tra xem file sau có tồn tại không:
- `sources/qlct/vendor/autoload.php`

Nếu file tồn tại, chức năng xuất PDF sẽ hoạt động tự động.

## Lưu ý

- Nếu sử dụng Docker, cần cài đặt trong container
- Đảm bảo PHP version >= 7.1
- Cần có quyền ghi vào thư mục vendor/

