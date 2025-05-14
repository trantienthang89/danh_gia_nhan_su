<?php

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma_nhan_vien = $_POST['ma_nhan_vien'] ?? '';
    $ho_ten = $_POST['ho_ten'] ?? '';
    $chuc_vu_id = $_POST['chuc_vu_id'] ?? '';
    $phong_ban_id = $_POST['phong_ban_id'] ?? '';

    if ($ma_nhan_vien && $ho_ten && $chuc_vu_id && $phong_ban_id) {
        // Kiểm tra trùng mã nhân viên
        $stmt_check = $conn->prepare("SELECT COUNT(*) FROM nhan_vien WHERE ma_nhan_vien = ?");
        $stmt_check->execute([$ma_nhan_vien]);
        $exists = $stmt_check->fetchColumn();

        if ($exists > 0) {
            // Trùng mã nhân viên
            header("Location: /admin/views/nhanvien.php?error=trung_ma");
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO nhan_vien (ma_nhan_vien, ho_ten, chuc_vu_id, phong_ban_id) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$ma_nhan_vien, $ho_ten, $chuc_vu_id, $phong_ban_id]);
        if ($result) {
            header("Location: /admin/views/nhanvien.php?success=1");
            exit;
        } else {
            header("Location: /admin/views/nhanvien.php?error=loi_he_thong");
            exit;
        }
    } else {
        header("Location: /admin/views/nhanvien.php?error=thieu_thong_tin");
        exit;
    }
}