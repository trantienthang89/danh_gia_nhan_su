<?php

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma_nhan_vien = $_POST['ma_nhan_vien'] ?? '';
    $ho_ten = $_POST['ho_ten'] ?? '';
    $chuc_vu_id = $_POST['chuc_vu_id'] ?? '';
    $phong_ban_id = $_POST['phong_ban_id'] ?? '';

    if ($ma_nhan_vien && $ho_ten && $chuc_vu_id && $phong_ban_id) {
        $stmt = $conn->prepare("UPDATE nhan_vien SET ho_ten = ?, chuc_vu_id = ?, phong_ban_id = ? WHERE ma_nhan_vien = ?");
        $result = $stmt->execute([$ho_ten, $chuc_vu_id, $phong_ban_id, $ma_nhan_vien]);
        if ($result) {
            header("Location: /admin/views/nhanvien.php?updated=1");
            exit;
        } else {
            echo "Lỗi khi cập nhật!";
        }
    } else {
        echo "Vui lòng nhập đầy đủ thông tin!";
    }
}