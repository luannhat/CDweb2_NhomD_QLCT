-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Nov 25, 2025 at 06:06 AM
-- Server version: 8.0.43
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qlct_db`
--
CREATE DATABASE IF NOT EXISTS `qlct_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `qlct_db`;

-- --------------------------------------------------------

--
-- Table structure for table `BANGNO`
--

CREATE TABLE `BANGNO` (
  `mabangno` int NOT NULL,
  `makh` int NOT NULL,
  `sotienno` decimal(18,2) NOT NULL,
  `loai` enum('lend','borrow') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nguoilienquan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngaydaohan` date NOT NULL,
  `trangthai` enum('pending','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `BAOCAO`
--

CREATE TABLE `BAOCAO` (
  `mabaocao` int NOT NULL,
  `makh` int NOT NULL,
  `thang` int NOT NULL,
  `nam` int NOT NULL,
  `tongthunhap` decimal(18,2) NOT NULL DEFAULT '0.00',
  `tongchitieu` decimal(18,2) NOT NULL DEFAULT '0.00',
  `sodu` decimal(18,2) GENERATED ALWAYS AS ((`tongthunhap` - `tongchitieu`)) STORED,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CHITIETNGANSACH`
--

CREATE TABLE `CHITIETNGANSACH` (
  `machitiet` int NOT NULL,
  `mangansach` int DEFAULT NULL,
  `machitieu` int NOT NULL,
  `sotienphanbo` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `DANHMUC`
--

CREATE TABLE `DANHMUC` (
  `madanhmuc` int NOT NULL,
  `tendanhmuc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `matk` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `DMCHITIEU`
--

CREATE TABLE `DMCHITIEU` (
  `madmchitieu` int NOT NULL,
  `makh` int NOT NULL,
  `tendanhmuc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai` enum('income','expense') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `DMCHITIEU`
--

INSERT INTO `DMCHITIEU` (`madmchitieu`, `makh`, `tendanhmuc`, `loai`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ăn uống', 'expense', '2025-11-04 10:13:05', '2025-11-04 10:13:05'),
(2, 1, 'Di chuyển', 'expense', '2025-11-04 10:13:05', '2025-11-04 10:13:05'),
(3, 1, 'Mua sắm', 'expense', '2025-11-04 10:13:05', '2025-11-04 10:13:05'),
(4, 1, 'Giải trí', 'expense', '2025-11-04 10:13:05', '2025-11-04 10:13:05'),
(5, 1, 'Hóa đơn điện nước', 'expense', '2025-11-04 10:13:05', '2025-11-04 10:13:05'),
(6, 1, 'Y tế - Sức khỏe', 'expense', '2025-11-04 10:13:05', '2025-11-04 10:13:05'),
(7, 1, 'Giáo dục', 'expense', '2025-11-04 10:13:05', '2025-11-04 10:13:05'),
(8, 1, 'Nhà cửa', 'expense', '2025-11-04 10:13:05', '2025-11-04 10:13:05'),
(9, 1, 'Thú cưng', 'expense', '2025-11-04 10:13:05', '2025-11-04 10:13:05'),
(10, 1, 'Khác', 'expense', '2025-11-04 10:13:05', '2025-11-04 10:13:05');

-- --------------------------------------------------------

--
-- Table structure for table `DMTHUNHAP`
--

CREATE TABLE `DMTHUNHAP` (
  `madmthunhap` int NOT NULL,
  `makh` int NOT NULL,
  `tendanhmuc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai` enum('income','expense') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'income',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `DMTHUNHAP`
--

INSERT INTO `DMTHUNHAP` (`madmthunhap`, `makh`, `tendanhmuc`, `loai`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lương chính', 'income', '2025-11-03 08:28:18', '2025-11-03 08:28:18'),
(2, 2, 'Lương phụ', 'income', '2025-11-03 08:28:18', '2025-11-03 08:28:18'),
(3, 3, 'Tiền thưởng', 'income', '2025-11-03 08:28:18', '2025-11-03 08:28:18'),
(4, 4, 'Lương chính', 'income', '2025-11-03 08:28:18', '2025-11-03 08:28:18'),
(5, 5, 'Đầu tư chứng khoán', 'income', '2025-11-03 08:28:18', '2025-11-03 08:28:18'),
(6, 6, 'Quà tặng', 'income', '2025-11-03 08:28:18', '2025-11-03 08:28:18'),
(7, 7, 'Lương chính', 'income', '2025-11-03 08:28:18', '2025-11-03 08:28:18'),
(8, 8, 'Kinh doanh online', 'income', '2025-11-03 08:28:18', '2025-11-03 08:28:18'),
(9, 9, 'Lãi ngân hàng', 'income', '2025-11-03 08:28:18', '2025-11-03 08:28:18'),
(10, 10, 'Khác', 'income', '2025-11-03 08:28:18', '2025-11-03 08:28:18');

-- --------------------------------------------------------

--
-- Table structure for table `DSCHITIEU`
--

CREATE TABLE `DSCHITIEU` (
  `machitieu` int NOT NULL,
  `makh` int NOT NULL,
  `madmchitieu` int NOT NULL,
  `ngaychitieu` date NOT NULL,
  `noidung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai` enum('income','expense') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sotien` decimal(18,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `DSCHITIEU`
--

INSERT INTO `DSCHITIEU` (`machitieu`, `makh`, `madmchitieu`, `ngaychitieu`, `noidung`, `loai`, `sotien`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-11-01', 'Ăn trưa tại quán cơm', 'expense', 45000.00, '2025-11-04 10:14:19', '2025-11-04 10:14:19'),
(2, 1, 2, '2025-11-01', 'Đổ xăng xe máy', 'expense', 60000.00, '2025-11-04 10:14:19', '2025-11-04 10:14:19'),
(3, 1, 3, '2025-11-02', 'Mua áo sơ mi mới', 'expense', 250000.00, '2025-11-04 10:14:19', '2025-11-04 10:14:19'),
(4, 1, 4, '2025-11-02', 'Xem phim tại CGV', 'expense', 90000.00, '2025-11-04 10:14:19', '2025-11-04 10:14:19'),
(5, 1, 5, '2025-11-03', 'Thanh toán tiền điện tháng 10', 'expense', 350000.00, '2025-11-04 10:14:19', '2025-11-04 10:14:19'),
(6, 1, 6, '2025-11-03', 'Mua thuốc cảm cúm', 'expense', 85000.00, '2025-11-04 10:14:19', '2025-11-04 10:14:19'),
(7, 1, 7, '2025-11-04', 'Đóng học phí tiếng Anh', 'expense', 1200000.00, '2025-11-04 10:14:19', '2025-11-04 10:14:19'),
(8, 1, 8, '2025-11-04', 'Sửa chữa vòi nước', 'expense', 150000.00, '2025-11-04 10:14:19', '2025-11-04 10:14:19'),
(9, 1, 9, '2025-11-05', 'Mua thức ăn cho mèo', 'expense', 120000.00, '2025-11-04 10:14:19', '2025-11-04 10:14:19'),
(10, 1, 10, '2025-11-05', 'Chi tiêu linh tinh', 'expense', 50000.00, '2025-11-04 10:14:19', '2025-11-04 10:14:19'),
(12, 1, 2, '2025-11-18', 'thuê xe', 'expense', 100000.00, '2025-11-18 07:08:13', '2025-11-18 07:08:13'),
(13, 1, 4, '2025-11-18', 'du lịch', 'expense', 1000000.00, '2025-11-18 08:12:55', '2025-11-18 08:12:55'),
(16, 1, 10, '2025-11-24', 'thuê xe', 'expense', 1000000.00, '2025-11-24 01:44:09', '2025-11-24 01:44:09'),
(17, 1, 8, '2025-11-24', 'mua nhà', 'expense', 1000000000.00, '2025-11-24 01:45:14', '2025-11-24 01:45:14'),
(18, 1, 1, '2025-11-24', 'ăn uống', 'expense', 50000.00, '2025-11-24 14:41:05', '2025-11-24 14:41:05');

-- --------------------------------------------------------

--
-- Table structure for table `DSTHUNHAP`
--

CREATE TABLE `DSTHUNHAP` (
  `mathunhap` int NOT NULL,
  `makh` int NOT NULL,
  `madmthunhap` int NOT NULL,
  `ngaythunhap` date NOT NULL,
  `noidung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai` enum('income','expense') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'income',
  `sotien` decimal(18,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `DSTHUNHAP`
--

INSERT INTO `DSTHUNHAP` (`mathunhap`, `makh`, `madmthunhap`, `ngaythunhap`, `noidung`, `loai`, `sotien`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-11-01', 'Lương tháng 11', 'income', 10000000.00, '2025-11-03 10:16:18', '2025-11-03 10:16:18'),
(2, 2, 2, '2025-11-05', 'Thưởng dự án', 'income', 2000000.00, '2025-11-03 10:16:18', '2025-11-03 10:16:18'),
(3, 3, 3, '2025-11-01', 'Lương tháng 11', 'income', 12000000.00, '2025-11-03 10:16:18', '2025-11-03 10:16:18'),
(4, 4, 4, '2025-11-10', 'Tiền thưởng', 'income', 1500000.00, '2025-11-03 10:16:18', '2025-11-03 10:16:18'),
(5, 5, 5, '2025-11-01', 'Lương tháng 11', 'income', 9000000.00, '2025-11-03 10:16:18', '2025-11-03 10:16:18'),
(6, 6, 6, '2025-11-12', 'Bán đồ cũ', 'income', 500000.00, '2025-11-03 10:16:18', '2025-11-03 10:16:18'),
(7, 7, 7, '2025-11-01', 'Lương tháng 11', 'income', 11000000.00, '2025-11-03 10:16:18', '2025-11-03 10:16:18'),
(8, 8, 8, '2025-11-15', 'Hoa hồng', 'income', 1000000.00, '2025-11-03 10:16:18', '2025-11-03 10:16:18'),
(9, 9, 9, '2025-11-01', 'Lương tháng 11', 'income', 9500000.00, '2025-11-03 10:16:18', '2025-11-03 10:16:18'),
(10, 10, 10, '2025-11-20', 'Trợ cấp', 'income', 750000.00, '2025-11-03 10:16:18', '2025-11-03 10:16:18'),
(31, 1, 3, '2025-11-04', 'thuê nhà tháng 2', 'income', 43422432.00, '2025-11-04 05:02:22', '2025-11-04 05:02:22'),
(32, 1, 10, '2025-11-04', 'bán nhà', 'income', 1000000000.00, '2025-11-04 07:28:45', '2025-11-04 07:28:45'),
(33, 1, 10, '2025-11-04', 'bán xe', 'income', 20000000.00, '2025-11-04 07:34:46', '2025-11-04 07:34:46'),
(34, 1, 9, '2025-11-04', 'lãi ngân hàng', 'income', 12345.00, '2025-11-04 09:14:13', '2025-11-04 09:14:13'),
(35, 1, 3, '2025-11-04', 'thưởng', 'income', 200000.00, '2025-11-04 09:16:32', '2025-11-04 09:16:32'),
(36, 1, 3, '2025-11-23', 'tiền thưởng', 'income', 500000.00, '2025-11-23 09:11:12', '2025-11-23 09:11:12'),
(37, 1, 10, '2025-11-24', 'bán xe', 'income', 5000000.00, '2025-11-24 01:43:43', '2025-11-24 01:43:43');

-- --------------------------------------------------------

--
-- Table structure for table `GIAODICH`
--

CREATE TABLE `GIAODICH` (
  `magd` int NOT NULL,
  `makh` int NOT NULL,
  `machitieu` int NOT NULL,
  `noidung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sotien` decimal(18,2) NOT NULL,
  `loai` enum('income','expense') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngaygiaodich` date NOT NULL,
  `ghichu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anhhoadon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `KHACHHANG`
--

CREATE TABLE `KHACHHANG` (
  `makh` int NOT NULL,
  `tenkh` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `matkhau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hinhanh` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quyen` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `KHACHHANG`
--

INSERT INTO `KHACHHANG` (`makh`, `tenkh`, `email`, `matkhau`, `hinhanh`, `quyen`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Văn A', 'vana@example.com', '123456', 'avatar1.jpg', 'user', '2025-11-01 07:47:25', '2025-11-01 07:47:25'),
(2, 'Trần Thị B', 'thib@example.com', '123456', 'avatar2.jpg', 'user', '2025-11-01 07:47:25', '2025-11-01 07:47:25'),
(3, 'Lê Văn C', 'vanc@example.com', '123456', 'avatar3.jpg', 'user', '2025-11-01 07:47:25', '2025-11-01 07:47:25'),
(4, 'Phạm Thị D', 'thid@example.com', '123456', 'avatar4.jpg', 'user', '2025-11-01 07:47:25', '2025-11-01 07:47:25'),
(5, 'Hoàng Văn E', 'vane@example.com', '123456', 'avatar5.jpg', 'user', '2025-11-01 07:47:25', '2025-11-01 07:47:25'),
(6, 'Đỗ Thị F', 'thif@example.com', '123456', 'avatar6.jpg', 'user', '2025-11-01 07:47:25', '2025-11-01 07:47:25'),
(7, 'Vũ Văn G', 'vang@example.com', '123456', 'avatar7.jpg', 'admin', '2025-11-01 07:47:25', '2025-11-01 07:47:25'),
(8, 'Ngô Thị H', 'thih@example.com', '123456', 'avatar8.jpg', 'user', '2025-11-01 07:47:25', '2025-11-01 07:47:25'),
(9, 'Bùi Văn I', 'vani@example.com', '123456', 'avatar9.jpg', 'user', '2025-11-01 07:47:25', '2025-11-01 07:47:25'),
(10, 'Phan Thị K', 'thik@example.com', '123456', 'avatar10.jpg', 'user', '2025-11-01 07:47:25', '2025-11-01 07:47:25');

-- --------------------------------------------------------

--
-- Table structure for table `KHOANCHI`
--

CREATE TABLE `KHOANCHI` (
  `machi` int NOT NULL,
  `tenkhoanchi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sotien` decimal(12,2) NOT NULL,
  `madanhmuc` int DEFAULT NULL,
  `ngaybatdau` datetime NOT NULL,
  `lapphieu` enum('Hàng ngày','Hàng tháng','Hàng năm','Không lặp lại') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Không lặp lại'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `LAPNGANSACHTHEOTHANG`
--

CREATE TABLE `LAPNGANSACHTHEOTHANG` (
  `mangansach` int NOT NULL,
  `makh` int DEFAULT NULL,
  `thang` int NOT NULL,
  `nam` int NOT NULL,
  `tongngansach` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `LICHTHUCHI`
--

CREATE TABLE `LICHTHUCHI` (
  `mathuchi` int NOT NULL,
  `ngay` datetime NOT NULL,
  `loai` enum('thu','chi') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sotien` decimal(18,2) NOT NULL,
  `ghichu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `makh` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `NGANSACH`
--

CREATE TABLE `NGANSACH` (
  `mangansach` int NOT NULL,
  `makh` int NOT NULL,
  `machitieu` int NOT NULL,
  `ngay` date NOT NULL,
  `ngansach` decimal(18,2) NOT NULL,
  `dachi` decimal(18,2) NOT NULL DEFAULT '0.00',
  `chenhlech` decimal(18,2) GENERATED ALWAYS AS ((`ngansach` - `dachi`)) STORED,
  `trangthai` enum('under_budget','on_budget','over_budget') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'on_budget',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `NGANSACH`
--

INSERT INTO `NGANSACH` (`mangansach`, `makh`, `machitieu`, `ngay`, `ngansach`, `dachi`, `trangthai`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-11-22', 500000.00, 50000.00, 'under_budget', '2025-11-24 14:39:49', '2025-11-24 14:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `SUAKHOANTHUNHAP`
--

CREATE TABLE `SUAKHOANTHUNHAP` (
  `mathuanhap` int NOT NULL,
  `makh` int DEFAULT NULL,
  `tenkhoanthu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sotien` decimal(18,2) NOT NULL,
  `ngaynhan` datetime NOT NULL,
  `danhmuc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mota` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `THONGBAO`
--

CREATE TABLE `THONGBAO` (
  `matb` int NOT NULL,
  `makh` int NOT NULL,
  `noidung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai` enum('warning','reminder','info') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `trangthai` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `TIETKIEM`
--

CREATE TABLE `TIETKIEM` (
  `matk` int NOT NULL,
  `makh` int NOT NULL,
  `muctieu` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sotiencandat` decimal(18,2) NOT NULL,
  `sotiendatietkiem` decimal(18,2) NOT NULL DEFAULT '0.00',
  `hanmuctieu` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `TIMKIEM`
--

CREATE TABLE `TIMKIEM` (
  `matimkiem` int NOT NULL,
  `magd` int DEFAULT NULL,
  `makh` int DEFAULT NULL,
  `tenkhoanchi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mota` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sotien` decimal(18,2) NOT NULL,
  `danhmuc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tukhoa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngaychi` datetime NOT NULL,
  `taoluc` datetime DEFAULT CURRENT_TIMESTAMP,
  `capnhatluc` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `BANGNO`
--
ALTER TABLE `BANGNO`
  ADD PRIMARY KEY (`mabangno`),
  ADD KEY `makh` (`makh`);

--
-- Indexes for table `BAOCAO`
--
ALTER TABLE `BAOCAO`
  ADD PRIMARY KEY (`mabaocao`),
  ADD UNIQUE KEY `makh` (`makh`,`thang`,`nam`);

--
-- Indexes for table `CHITIETNGANSACH`
--
ALTER TABLE `CHITIETNGANSACH`
  ADD PRIMARY KEY (`machitiet`),
  ADD KEY `machitieu` (`machitieu`),
  ADD KEY `mangansach` (`mangansach`);

--
-- Indexes for table `DANHMUC`
--
ALTER TABLE `DANHMUC`
  ADD PRIMARY KEY (`madanhmuc`),
  ADD KEY `matk` (`matk`);

--
-- Indexes for table `DMCHITIEU`
--
ALTER TABLE `DMCHITIEU`
  ADD PRIMARY KEY (`madmchitieu`),
  ADD KEY `FK_DMCHITIEU_KHACHHANG` (`makh`);

--
-- Indexes for table `DMTHUNHAP`
--
ALTER TABLE `DMTHUNHAP`
  ADD PRIMARY KEY (`madmthunhap`),
  ADD KEY `makh` (`makh`);

--
-- Indexes for table `DSCHITIEU`
--
ALTER TABLE `DSCHITIEU`
  ADD PRIMARY KEY (`machitieu`),
  ADD KEY `makh` (`makh`),
  ADD KEY `machitieu` (`madmchitieu`);

--
-- Indexes for table `DSTHUNHAP`
--
ALTER TABLE `DSTHUNHAP`
  ADD PRIMARY KEY (`mathunhap`),
  ADD KEY `makh` (`makh`),
  ADD KEY `madmthunhap` (`madmthunhap`);

--
-- Indexes for table `GIAODICH`
--
ALTER TABLE `GIAODICH`
  ADD PRIMARY KEY (`magd`),
  ADD KEY `makh` (`makh`),
  ADD KEY `machitieu` (`machitieu`);

--
-- Indexes for table `KHACHHANG`
--
ALTER TABLE `KHACHHANG`
  ADD PRIMARY KEY (`makh`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `KHOANCHI`
--
ALTER TABLE `KHOANCHI`
  ADD PRIMARY KEY (`machi`),
  ADD KEY `madanhmuc` (`madanhmuc`);

--
-- Indexes for table `LAPNGANSACHTHEOTHANG`
--
ALTER TABLE `LAPNGANSACHTHEOTHANG`
  ADD PRIMARY KEY (`mangansach`),
  ADD KEY `makh` (`makh`);

--
-- Indexes for table `LICHTHUCHI`
--
ALTER TABLE `LICHTHUCHI`
  ADD PRIMARY KEY (`mathuchi`),
  ADD KEY `makh` (`makh`);

--
-- Indexes for table `NGANSACH`
--
ALTER TABLE `NGANSACH`
  ADD PRIMARY KEY (`mangansach`),
  ADD KEY `makh` (`makh`),
  ADD KEY `machitieu` (`machitieu`);

--
-- Indexes for table `SUAKHOANTHUNHAP`
--
ALTER TABLE `SUAKHOANTHUNHAP`
  ADD PRIMARY KEY (`mathuanhap`),
  ADD KEY `makh` (`makh`);

--
-- Indexes for table `THONGBAO`
--
ALTER TABLE `THONGBAO`
  ADD PRIMARY KEY (`matb`),
  ADD KEY `makh` (`makh`);

--
-- Indexes for table `TIETKIEM`
--
ALTER TABLE `TIETKIEM`
  ADD PRIMARY KEY (`matk`),
  ADD KEY `makh` (`makh`);

--
-- Indexes for table `TIMKIEM`
--
ALTER TABLE `TIMKIEM`
  ADD PRIMARY KEY (`matimkiem`),
  ADD KEY `magd` (`magd`),
  ADD KEY `makh` (`makh`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `BANGNO`
--
ALTER TABLE `BANGNO`
  MODIFY `mabangno` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `BAOCAO`
--
ALTER TABLE `BAOCAO`
  MODIFY `mabaocao` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `CHITIETNGANSACH`
--
ALTER TABLE `CHITIETNGANSACH`
  MODIFY `machitiet` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `DANHMUC`
--
ALTER TABLE `DANHMUC`
  MODIFY `madanhmuc` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `DMCHITIEU`
--
ALTER TABLE `DMCHITIEU`
  MODIFY `madmchitieu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `DMTHUNHAP`
--
ALTER TABLE `DMTHUNHAP`
  MODIFY `madmthunhap` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `DSCHITIEU`
--
ALTER TABLE `DSCHITIEU`
  MODIFY `machitieu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `DSTHUNHAP`
--
ALTER TABLE `DSTHUNHAP`
  MODIFY `mathunhap` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `GIAODICH`
--
ALTER TABLE `GIAODICH`
  MODIFY `magd` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `KHACHHANG`
--
ALTER TABLE `KHACHHANG`
  MODIFY `makh` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `KHOANCHI`
--
ALTER TABLE `KHOANCHI`
  MODIFY `machi` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `LAPNGANSACHTHEOTHANG`
--
ALTER TABLE `LAPNGANSACHTHEOTHANG`
  MODIFY `mangansach` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `LICHTHUCHI`
--
ALTER TABLE `LICHTHUCHI`
  MODIFY `mathuchi` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NGANSACH`
--
ALTER TABLE `NGANSACH`
  MODIFY `mangansach` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `SUAKHOANTHUNHAP`
--
ALTER TABLE `SUAKHOANTHUNHAP`
  MODIFY `mathuanhap` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `THONGBAO`
--
ALTER TABLE `THONGBAO`
  MODIFY `matb` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `TIETKIEM`
--
ALTER TABLE `TIETKIEM`
  MODIFY `matk` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `TIMKIEM`
--
ALTER TABLE `TIMKIEM`
  MODIFY `matimkiem` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `BANGNO`
--
ALTER TABLE `BANGNO`
  ADD CONSTRAINT `bangno_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`);

--
-- Constraints for table `BAOCAO`
--
ALTER TABLE `BAOCAO`
  ADD CONSTRAINT `baocao_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`);

--
-- Constraints for table `CHITIETNGANSACH`
--
ALTER TABLE `CHITIETNGANSACH`
  ADD CONSTRAINT `chitietngansach_ibfk_1` FOREIGN KEY (`machitieu`) REFERENCES `DMCHITIEU` (`madmchitieu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chitietngansach_ibfk_2` FOREIGN KEY (`mangansach`) REFERENCES `LAPNGANSACHTHEOTHANG` (`mangansach`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `DANHMUC`
--
ALTER TABLE `DANHMUC`
  ADD CONSTRAINT `danhmuc_ibfk_1` FOREIGN KEY (`matk`) REFERENCES `TIMKIEM` (`matimkiem`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `DMCHITIEU`
--
ALTER TABLE `DMCHITIEU`
  ADD CONSTRAINT `FK_DMCHITIEU_KHACHHANG` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`);

--
-- Constraints for table `DMTHUNHAP`
--
ALTER TABLE `DMTHUNHAP`
  ADD CONSTRAINT `dmthunhap_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `DSCHITIEU`
--
ALTER TABLE `DSCHITIEU`
  ADD CONSTRAINT `dschitieu_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`),
  ADD CONSTRAINT `dschitieu_ibfk_2` FOREIGN KEY (`madmchitieu`) REFERENCES `DMCHITIEU` (`madmchitieu`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
