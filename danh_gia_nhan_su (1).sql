-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306:3306
-- Thời gian đã tạo: Th5 08, 2025 lúc 08:12 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `danh_gia_nhan_su`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuc_vu`
--

CREATE TABLE `chuc_vu` (
  `ma_chuc_vu` int(11) NOT NULL,
  `ten_chuc_vu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chuc_vu`
--

INSERT INTO `chuc_vu` (`ma_chuc_vu`, `ten_chuc_vu`) VALUES
(1, 'Giám đốc'),
(2, 'Phó giám đốc'),
(3, 'Trưởng phòng'),
(4, 'Phó phòng'),
(5, 'Nhân viên');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_gia`
--

CREATE TABLE `danh_gia` (
  `ma_danh_gia` int(11) NOT NULL,
  `ma_dot` int(11) DEFAULT NULL,
  `nguoi_danh_gia` int(11) NOT NULL,
  `nguoi_duoc_danh_gia` int(11) NOT NULL,
  `diem_tuan_thu` int(11) NOT NULL,
  `diem_hop_tac` int(11) NOT NULL,
  `diem_tan_tuy` int(11) NOT NULL,
  `nhan_xet` text DEFAULT NULL,
  `diem_trung_binh` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_gia`
--

INSERT INTO `danh_gia` (`ma_danh_gia`, `ma_dot`, `nguoi_danh_gia`, `nguoi_duoc_danh_gia`, `diem_tuan_thu`, `diem_hop_tac`, `diem_tan_tuy`, `nhan_xet`, `diem_trung_binh`) VALUES
(312, NULL, 110, 101, 1, 1, 2, 's', 1.33),
(314, NULL, 110, 102, 2, 1, 3, 'vf', 2),
(315, NULL, 110, 104, 3, 2, 1, '1', 2),
(317, NULL, 110, 103, 4, 2, 1, 'đợt 3', 2.33),
(318, 27, 110, 105, 1, 5, 2, '', 2.33),
(319, 28, 110, 106, 4, 3, 3, 'hi', 3.33),
(320, 30, 101, 103, 1, 1, 4, '', 1.83),
(321, 31, 101, 102, 1, 1, 1, '1', 1),
(322, 32, 101, 105, 1, 2, 2, 'ư', 1.5),
(323, 33, 101, 102, 1, 1, 1, 'hi', 1),
(324, 33, 101, 103, 3, 2, 1, 'hu', 2),
(325, 33, 105, 103, 2, 10, 10, 'gi', 7.33),
(326, 33, 105, 110, 10, 10, 10, 'ok', 10),
(327, 33, 103, 110, 5, 5, 5, 'hi', 5),
(328, 33, 103, 106, 1, 1, 1, 'hi', 1),
(329, 33, 101, 106, 10, 10, 10, 'hu', 10),
(330, 34, 101, 102, 1, 1, 1, 'hihihi', 1),
(331, 34, 105, 101, 2, 3, 4, '5', 3),
(332, 34, 105, 103, 4, 2, 5, 'ok', 3.67),
(333, 34, 101, 103, 9, 1, 1, 'okok', 3.67);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dot_danh_gia`
--

CREATE TABLE `dot_danh_gia` (
  `ma_dot` int(11) NOT NULL,
  `ten_dot` varchar(100) NOT NULL,
  `thoi_gian_bat_dau` datetime DEFAULT NULL,
  `thoi_gian_ket_thuc` datetime DEFAULT NULL,
  `trang_thai` enum('Dang Dien Ra','Da Ket Thuc','Chua Bat Dau') DEFAULT 'Dang Dien Ra'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `dot_danh_gia`
--

INSERT INTO `dot_danh_gia` (`ma_dot`, `ten_dot`, `thoi_gian_bat_dau`, `thoi_gian_ket_thuc`, `trang_thai`) VALUES
(1, 'Đánh giá quý 1/2025', '2025-01-01 08:00:00', '2025-01-31 23:59:59', 'Da Ket Thuc'),
(20, 'mới', '2025-04-24 23:19:00', '2025-04-24 23:21:00', 'Da Ket Thuc'),
(21, 'hi', '2025-04-24 23:25:00', '2025-04-24 23:27:00', 'Da Ket Thuc'),
(22, 'chốt', '2025-04-24 23:27:00', '2025-04-24 23:29:00', 'Da Ket Thuc'),
(23, 'cuôií', '2025-04-24 23:30:00', '2025-04-24 23:31:00', 'Da Ket Thuc'),
(24, 'đợt 2', '2025-04-24 23:40:00', '2025-04-24 23:43:00', 'Da Ket Thuc'),
(25, 'đợt 2', '2025-04-24 23:40:00', '2025-04-24 23:43:00', 'Da Ket Thuc'),
(26, 'đợt 3', '2025-04-24 23:45:00', '2025-04-24 23:48:00', 'Da Ket Thuc'),
(27, 'đợt 4 thực hành', '2025-04-24 23:48:00', '2025-04-24 23:50:00', 'Da Ket Thuc'),
(28, 'đợt 5', '2025-04-24 23:51:00', '2025-04-24 23:53:00', 'Da Ket Thuc'),
(29, 'đợt 6', '2025-04-24 23:56:00', '2025-04-24 23:59:00', 'Da Ket Thuc'),
(30, 'đợt 7', '2025-04-25 00:06:00', '2025-04-25 00:08:00', 'Da Ket Thuc'),
(31, 'đọt 8', '2025-04-25 00:08:00', '2025-04-25 00:10:00', 'Da Ket Thuc'),
(32, '9', '2025-04-25 00:14:00', '2025-04-25 00:16:00', 'Da Ket Thuc'),
(33, 'đợt 10', '2025-04-27 20:25:00', '2025-04-30 20:25:00', 'Da Ket Thuc'),
(34, 'đợt chốt', '2025-05-07 23:42:00', '2025-05-10 23:42:00', 'Dang Dien Ra');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su_danh_gia`
--

CREATE TABLE `lich_su_danh_gia` (
  `ma_lich_su` int(11) NOT NULL,
  `ma_dot` int(11) DEFAULT NULL,
  `nguoi_danh_gia` int(11) DEFAULT NULL,
  `nguoi_duoc_danh_gia` int(11) DEFAULT NULL,
  `thoi_gian` datetime DEFAULT current_timestamp(),
  `diem_tuan_thu` decimal(5,2) DEFAULT NULL,
  `diem_hop_tac` decimal(5,2) DEFAULT NULL,
  `diem_tan_tuy` decimal(5,2) DEFAULT NULL,
  `nhan_xet` text DEFAULT NULL,
  `diem_trung_binh` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lich_su_danh_gia`
--

INSERT INTO `lich_su_danh_gia` (`ma_lich_su`, `ma_dot`, `nguoi_danh_gia`, `nguoi_duoc_danh_gia`, `thoi_gian`, `diem_tuan_thu`, `diem_hop_tac`, `diem_tan_tuy`, `nhan_xet`, `diem_trung_binh`) VALUES
(1, 1, 101, 103, '2025-01-15 10:30:00', 9.50, 8.00, 9.00, 'Làm việc chăm chỉ, cần cải thiện giao tiếp.', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhan_vien`
--

CREATE TABLE `nhan_vien` (
  `ma_nhan_vien` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `chuc_vu_id` int(11) DEFAULT NULL,
  `phong_ban_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nhan_vien`
--

INSERT INTO `nhan_vien` (`ma_nhan_vien`, `ho_ten`, `chuc_vu_id`, `phong_ban_id`) VALUES
(101, 'Nguyễn Văn A', 2, 1),
(102, 'Trần Thị B', 3, 4),
(103, 'Lê Văn C', 2, 1),
(104, 'Phạm Thị D', 2, 1),
(105, 'Trần Tiến Thăng', 3, 3),
(106, 'ttt', 2, 1),
(110, 'rớt', 1, 4),
(111, 'hi', 5, 1),
(112, 'ho', 1, 1),
(113, 'hoho', 1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phong_ban`
--

CREATE TABLE `phong_ban` (
  `ma_phong_ban` int(11) NOT NULL,
  `ten_phong_ban` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phong_ban`
--

INSERT INTO `phong_ban` (`ma_phong_ban`, `ten_phong_ban`) VALUES
(1, 'Phòng Kinh doanh'),
(2, 'Phòng Kế toán'),
(3, 'Phòng Nhân sự'),
(4, 'Phòng Công nghệ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tai_khoan`
--

CREATE TABLE `tai_khoan` (
  `ma_tai_khoan` int(11) NOT NULL,
  `ma_nhan_vien` int(11) DEFAULT NULL,
  `ten_dang_nhap` varchar(50) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `quyen` varchar(20) DEFAULT NULL CHECK (`quyen` in ('Admin','User'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tai_khoan`
--

INSERT INTO `tai_khoan` (`ma_tai_khoan`, `ma_nhan_vien`, `ten_dang_nhap`, `mat_khau`, `quyen`) VALUES
(1, 101, 'giam_doc', '$2y$10$ORmBiUBS0/NLOMvRpsHtjeHURZSDVOqcaYB9cyPiddnj9ZFjn96Me', 'Admin'),
(2, 102, 'truong_phong', '$2y$10$3JpTVA5m3QgXyflK.nmyvu5cugSCqNgM/T1CM5Wfnv.VeX4jVodCW', 'User'),
(3, 103, 'nhanvien1', '$2y$10$pyGa5qtEst7hgv4YDta4/.lw9fvhwoxSRNWbHGOj8c4hnnQkV9opu', 'User'),
(4, 104, 'pho_giam_doc', '$2y$10$DylMen7Dz1hCzkgnVyIc0OAdKrZLD00fUyRdOEXBfE1VSeyQ/sDP2', 'User'),
(5, 105, 'trantienthang', '$2y$10$fUluvXORcXcfzbW43y83GuiUYvcsKQ9TYv7FITpzIFmvE4ngNjEwm', 'User'),
(110, 110, 'rot', '$2y$10$dKujM8y9Q7yGr3omVvZZ/.G/rFTIAfoXaLatWeWoS9NMY0GBN1a.G', 'Admin'),
(112, 111, 'hi', '$2y$10$GD0lOcZW0Gh.KvsYl1VHqu1h66EZbSYxMW9GlpFFnzrrlYFJ.46bK', 'User');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `trong_so_danh_gia`
--

CREATE TABLE `trong_so_danh_gia` (
  `ma_chuc_vu` int(11) NOT NULL,
  `trong_so` float DEFAULT NULL CHECK (`trong_so` between 0 and 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `trong_so_danh_gia`
--

INSERT INTO `trong_so_danh_gia` (`ma_chuc_vu`, `trong_so`) VALUES
(1, 20),
(2, 15),
(3, 10),
(4, 5),
(5, 50);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chuc_vu`
--
ALTER TABLE `chuc_vu`
  ADD PRIMARY KEY (`ma_chuc_vu`);

--
-- Chỉ mục cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD PRIMARY KEY (`ma_danh_gia`),
  ADD KEY `ma_dot` (`ma_dot`),
  ADD KEY `nguoi_danh_gia` (`nguoi_danh_gia`),
  ADD KEY `nguoi_duoc_danh_gia` (`nguoi_duoc_danh_gia`);

--
-- Chỉ mục cho bảng `dot_danh_gia`
--
ALTER TABLE `dot_danh_gia`
  ADD PRIMARY KEY (`ma_dot`);

--
-- Chỉ mục cho bảng `lich_su_danh_gia`
--
ALTER TABLE `lich_su_danh_gia`
  ADD PRIMARY KEY (`ma_lich_su`),
  ADD KEY `ma_dot` (`ma_dot`),
  ADD KEY `nguoi_danh_gia` (`nguoi_danh_gia`),
  ADD KEY `nguoi_duoc_danh_gia` (`nguoi_duoc_danh_gia`);

--
-- Chỉ mục cho bảng `nhan_vien`
--
ALTER TABLE `nhan_vien`
  ADD PRIMARY KEY (`ma_nhan_vien`),
  ADD KEY `fk_nhanvien_phongban` (`phong_ban_id`),
  ADD KEY `fk_nhanvien_trongso` (`chuc_vu_id`);

--
-- Chỉ mục cho bảng `phong_ban`
--
ALTER TABLE `phong_ban`
  ADD PRIMARY KEY (`ma_phong_ban`);

--
-- Chỉ mục cho bảng `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD PRIMARY KEY (`ma_tai_khoan`),
  ADD UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`),
  ADD UNIQUE KEY `ma_nhan_vien` (`ma_nhan_vien`);

--
-- Chỉ mục cho bảng `trong_so_danh_gia`
--
ALTER TABLE `trong_so_danh_gia`
  ADD PRIMARY KEY (`ma_chuc_vu`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chuc_vu`
--
ALTER TABLE `chuc_vu`
  MODIFY `ma_chuc_vu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `ma_danh_gia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=334;

--
-- AUTO_INCREMENT cho bảng `dot_danh_gia`
--
ALTER TABLE `dot_danh_gia`
  MODIFY `ma_dot` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT cho bảng `lich_su_danh_gia`
--
ALTER TABLE `lich_su_danh_gia`
  MODIFY `ma_lich_su` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `nhan_vien`
--
ALTER TABLE `nhan_vien`
  MODIFY `ma_nhan_vien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT cho bảng `phong_ban`
--
ALTER TABLE `phong_ban`
  MODIFY `ma_phong_ban` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `tai_khoan`
--
ALTER TABLE `tai_khoan`
  MODIFY `ma_tai_khoan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD CONSTRAINT `danh_gia_ibfk_1` FOREIGN KEY (`ma_dot`) REFERENCES `dot_danh_gia` (`ma_dot`),
  ADD CONSTRAINT `danh_gia_ibfk_2` FOREIGN KEY (`nguoi_danh_gia`) REFERENCES `nhan_vien` (`ma_nhan_vien`),
  ADD CONSTRAINT `danh_gia_ibfk_3` FOREIGN KEY (`nguoi_duoc_danh_gia`) REFERENCES `nhan_vien` (`ma_nhan_vien`);

--
-- Các ràng buộc cho bảng `lich_su_danh_gia`
--
ALTER TABLE `lich_su_danh_gia`
  ADD CONSTRAINT `lich_su_danh_gia_ibfk_1` FOREIGN KEY (`ma_dot`) REFERENCES `dot_danh_gia` (`ma_dot`),
  ADD CONSTRAINT `lich_su_danh_gia_ibfk_2` FOREIGN KEY (`nguoi_danh_gia`) REFERENCES `nhan_vien` (`ma_nhan_vien`),
  ADD CONSTRAINT `lich_su_danh_gia_ibfk_3` FOREIGN KEY (`nguoi_duoc_danh_gia`) REFERENCES `nhan_vien` (`ma_nhan_vien`);

--
-- Các ràng buộc cho bảng `nhan_vien`
--
ALTER TABLE `nhan_vien`
  ADD CONSTRAINT `fk_nhanvien_chucvu` FOREIGN KEY (`chuc_vu_id`) REFERENCES `chuc_vu` (`ma_chuc_vu`),
  ADD CONSTRAINT `fk_nhanvien_phongban` FOREIGN KEY (`phong_ban_id`) REFERENCES `phong_ban` (`ma_phong_ban`),
  ADD CONSTRAINT `fk_nhanvien_trongso` FOREIGN KEY (`chuc_vu_id`) REFERENCES `trong_so_danh_gia` (`ma_chuc_vu`),
  ADD CONSTRAINT `nhan_vien_ibfk_1` FOREIGN KEY (`chuc_vu_id`) REFERENCES `chuc_vu` (`ma_chuc_vu`),
  ADD CONSTRAINT `nhan_vien_ibfk_2` FOREIGN KEY (`phong_ban_id`) REFERENCES `phong_ban` (`ma_phong_ban`);

--
-- Các ràng buộc cho bảng `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD CONSTRAINT `fk_taikhoan_nhanvien` FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`),
  ADD CONSTRAINT `tai_khoan_ibfk_1` FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`);

--
-- Các ràng buộc cho bảng `trong_so_danh_gia`
--
ALTER TABLE `trong_so_danh_gia`
  ADD CONSTRAINT `trong_so_danh_gia_ibfk_1` FOREIGN KEY (`ma_chuc_vu`) REFERENCES `chuc_vu` (`ma_chuc_vu`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
