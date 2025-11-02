

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `BANGNO` (
  `mabangno` int NOT NULL,
  `makh` int NOT NULL,
  `sotienno` decimal(18,2) NOT NULL,
  `loai` enum('lend','borrow') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nguoilienquan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngaydaohan` date NOT NULL,
  `trangthai` enum('pending','paid') COLLATE utf8mb4_unicode_ci NOT NULL,
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
) ;

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
  `tendanhmuc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matk` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `DMCHITIEU`
--

CREATE TABLE `DMCHITIEU` (
  `machitieu` int NOT NULL,
  `makh` int NOT NULL,
  `tendanhmuc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai` enum('income','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `DSCHITIEU`
--

CREATE TABLE `DSCHITIEU` (
  `maloaichitieu` int NOT NULL,
  `makh` int NOT NULL,
  `machitieu` int NOT NULL,
  `ngaychitieu` date NOT NULL,
  `noidung` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai` enum('income','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sotien` decimal(18,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `DSTHUNHAP`
--

CREATE TABLE `DSTHUNHAP` (
  `mathunhap` int NOT NULL,
  `makh` int NOT NULL,
  `machitieu` int NOT NULL,
  `ngaythunhap` date NOT NULL,
  `noidung` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai` enum('income','expense') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'income',
  `sotien` decimal(18,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `GIAODICH`
--

CREATE TABLE `GIAODICH` (
  `magd` int NOT NULL,
  `makh` int NOT NULL,
  `machitieu` int NOT NULL,
  `noidung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sotien` decimal(18,2) NOT NULL,
  `loai` enum('income','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngaygiaodich` date NOT NULL,
  `ghichu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anhhoadon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `KHACHHANG`
--

CREATE TABLE `KHACHHANG` (
  `makh` int NOT NULL,
  `tenkh` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matkhau` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hinhanh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quyen` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `KHOANCHI`
--

CREATE TABLE `KHOANCHI` (
  `machi` int NOT NULL,
  `tenkhoanchi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sotien` decimal(12,2) NOT NULL,
  `madanhmuc` int DEFAULT NULL,
  `ngaybatdau` datetime NOT NULL,
  `lapphieu` enum('Hàng ngày','Hàng tháng','Hàng năm','Không lặp lại') COLLATE utf8mb4_unicode_ci DEFAULT 'Không lặp lại'
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
  `loai` enum('thu','chi') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sotien` decimal(18,2) NOT NULL,
  `ghichu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `trangthai` enum('under_budget','on_budget','over_budget') COLLATE utf8mb4_unicode_ci DEFAULT 'on_budget',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SUAKHOANTHUNHAP`
--

CREATE TABLE `SUAKHOANTHUNHAP` (
  `mathuanhap` int NOT NULL,
  `makh` int DEFAULT NULL,
  `tenkhoanthu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sotien` decimal(18,2) NOT NULL,
  `ngaynhan` datetime NOT NULL,
  `danhmuc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mota` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `THONGBAO`
--

CREATE TABLE `THONGBAO` (
  `matb` int NOT NULL,
  `makh` int NOT NULL,
  `noidung` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai` enum('warning','reminder','info') COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `muctieu` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `tenkhoanchi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mota` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sotien` decimal(18,2) NOT NULL,
  `danhmuc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tukhoa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
  ADD PRIMARY KEY (`machitieu`),
  ADD KEY `FK_DMCHITIEU_KHACHHANG` (`makh`);

--
-- Indexes for table `DSCHITIEU`
--
ALTER TABLE `DSCHITIEU`
  ADD PRIMARY KEY (`maloaichitieu`),
  ADD KEY `makh` (`makh`),
  ADD KEY `machitieu` (`machitieu`);

--
-- Indexes for table `DSTHUNHAP`
--
ALTER TABLE `DSTHUNHAP`
  ADD PRIMARY KEY (`mathunhap`),
  ADD KEY `makh` (`makh`),
  ADD KEY `machitieu` (`machitieu`);

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
  MODIFY `machitieu` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `DSCHITIEU`
--
ALTER TABLE `DSCHITIEU`
  MODIFY `maloaichitieu` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `DSTHUNHAP`
--
ALTER TABLE `DSTHUNHAP`
  MODIFY `mathunhap` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `GIAODICH`
--
ALTER TABLE `GIAODICH`
  MODIFY `magd` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `KHACHHANG`
--
ALTER TABLE `KHACHHANG`
  MODIFY `makh` int NOT NULL AUTO_INCREMENT;

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
  MODIFY `mangansach` int NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `chitietngansach_ibfk_1` FOREIGN KEY (`machitieu`) REFERENCES `DMCHITIEU` (`machitieu`) ON DELETE CASCADE ON UPDATE CASCADE,
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
-- Constraints for table `DSCHITIEU`
--
ALTER TABLE `DSCHITIEU`
  ADD CONSTRAINT `dschitieu_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`),
  ADD CONSTRAINT `dschitieu_ibfk_2` FOREIGN KEY (`machitieu`) REFERENCES `DMCHITIEU` (`machitieu`);

--
-- Constraints for table `DSTHUNHAP`
--
ALTER TABLE `DSTHUNHAP`
  ADD CONSTRAINT `dsthunhap_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`),
  ADD CONSTRAINT `dsthunhap_ibfk_2` FOREIGN KEY (`machitieu`) REFERENCES `DMCHITIEU` (`machitieu`);

--
-- Constraints for table `GIAODICH`
--
ALTER TABLE `GIAODICH`
  ADD CONSTRAINT `giaodich_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`),
  ADD CONSTRAINT `giaodich_ibfk_2` FOREIGN KEY (`machitieu`) REFERENCES `DMCHITIEU` (`machitieu`);

--
-- Constraints for table `KHOANCHI`
--
ALTER TABLE `KHOANCHI`
  ADD CONSTRAINT `khoanchi_ibfk_1` FOREIGN KEY (`madanhmuc`) REFERENCES `DANHMUC` (`madanhmuc`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `LAPNGANSACHTHEOTHANG`
--
ALTER TABLE `LAPNGANSACHTHEOTHANG`
  ADD CONSTRAINT `lapngansachtheothang_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `LICHTHUCHI`
--
ALTER TABLE `LICHTHUCHI`
  ADD CONSTRAINT `lichthuchi_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `NGANSACH`
--
ALTER TABLE `NGANSACH`
  ADD CONSTRAINT `ngansach_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`),
  ADD CONSTRAINT `ngansach_ibfk_2` FOREIGN KEY (`machitieu`) REFERENCES `DMCHITIEU` (`machitieu`);

--
-- Constraints for table `SUAKHOANTHUNHAP`
--
ALTER TABLE `SUAKHOANTHUNHAP`
  ADD CONSTRAINT `suakhoanthunhap_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `THONGBAO`
--
ALTER TABLE `THONGBAO`
  ADD CONSTRAINT `thongbao_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`);

--
-- Constraints for table `TIETKIEM`
--
ALTER TABLE `TIETKIEM`
  ADD CONSTRAINT `tietkiem_ibfk_1` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`);

--
-- Constraints for table `TIMKIEM`
--
ALTER TABLE `TIMKIEM`
  ADD CONSTRAINT `timkiem_ibfk_1` FOREIGN KEY (`magd`) REFERENCES `GIAODICH` (`magd`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `timkiem_ibfk_2` FOREIGN KEY (`makh`) REFERENCES `KHACHHANG` (`makh`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


/* Nối mã liên tiếp*/
/*Bảng nợ*/
SET @max_id = (SELECT IFNULL(MAX(mabangno), 0) + 1 FROM BANGNO);
SET @sql = CONCAT('ALTER TABLE BANGNO AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

--Báo cáo
SET @max_id = (SELECT IFNULL(MAX(mabaocao), 0) + 1 FROM BAOCAO);
SET @sql = CONCAT('ALTER TABLE BAOCAO AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- chi tiết ngân sách
SET @max_id = (SELECT IFNULL(MAX(machitiet), 0) + 1 FROM CHITIETNGANSACH);
SET @sql = CONCAT('ALTER TABLE CHITIETNGANSACH AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

--danh mục
SET @max_id = (SELECT IFNULL(MAX(madanhmuc), 0) + 1 FROM DANHMUC);
SET @sql = CONCAT('ALTER TABLE DANHMUC AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

--danh mục chi tiêu
SET @max_id = (SELECT IFNULL(MAX(machitieu), 0) + 1 FROM DMCHITIEU);
SET @sql = CONCAT('ALTER TABLE DMCHITIEU AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
--danh sách chi tiêu
SET @max_id = (SELECT IFNULL(MAX(maloaichitieu), 0) + 1 FROM DSCHITIEU);
SET @sql = CONCAT('ALTER TABLE DSCHITIEU AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
--ds thu nhập
SET @max_id = (SELECT IFNULL(MAX(mathunhap), 0) + 1 FROM DSTHUNHAP);
SET @sql = CONCAT('ALTER TABLE DSTHUNHAP AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
--giao dịch
SET @max_id = (SELECT IFNULL(MAX(magd), 0) + 1 FROM GIAODICH);
SET @sql = CONCAT('ALTER TABLE GIAODICH AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
--khách hàng
SET @max_id = (SELECT IFNULL(MAX(makh), 0) + 1 FROM KHACHHANG);
SET @sql = CONCAT('ALTER TABLE KHACHHANG AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
--khoản chi
SET @max_id = (SELECT IFNULL(MAX(machi), 0) + 1 FROM KHOANCHI);
SET @sql = CONCAT('ALTER TABLE KHOANCHI AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
--lập ngân sách theo tháng
SET @max_id = (SELECT IFNULL(MAX(mangansach), 0) + 1 FROM LAPNGANSACHTHEOTHANG);
SET @sql = CONCAT('ALTER TABLE LAPNGANSACHTHEOTHANG AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
--lịch thu chi
SET @max_id = (SELECT IFNULL(MAX(mathuchi), 0) + 1 FROM LICHTHUCHI);
SET @sql = CONCAT('ALTER TABLE LICHTHUCHI AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
--ngân sách
SET @max_id = (SELECT IFNULL(MAX(mangansach), 0) + 1 FROM NGANSACH);
SET @sql = CONCAT('ALTER TABLE NGANSACH AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
--sửa khoản thu nhập
SET @max_id = (SELECT IFNULL(MAX(mathuanhap), 0) + 1 FROM SUAKHOANTHUNHAP);
SET @sql = CONCAT('ALTER TABLE SUAKHOANTHUNHAP AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
--thông báo
SET @max_id = (SELECT IFNULL(MAX(matb), 0) + 1 FROM THONGBAO);
SET @sql = CONCAT('ALTER TABLE THONGBAO AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
--tiết kiệm
SET @max_id = (SELECT IFNULL(MAX(matk), 0) + 1 FROM TIETKIEM);
SET @sql = CONCAT('ALTER TABLE TIETKIEM AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
--tìm kiếm
SET @max_id = (SELECT IFNULL(MAX(matimkiem), 0) + 1 FROM TIMKIEM);
SET @sql = CONCAT('ALTER TABLE TIMKIEM AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
