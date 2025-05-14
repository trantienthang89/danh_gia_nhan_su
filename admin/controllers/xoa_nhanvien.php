<?php

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $ma_nhan_vien = $_POST['id'];

    // Xóa các đánh giá liên quan đến nhân viên này
    $stmt1 = $conn->prepare("DELETE FROM danh_gia WHERE nguoi_duoc_danh_gia = :ma_nhan_vien OR nguoi_danh_gia = :ma_nhan_vien");
    $stmt1->execute([':ma_nhan_vien' => $ma_nhan_vien]);

    // Xóa tài khoản liên quan đến nhân viên này
    $stmt2 = $conn->prepare("DELETE FROM tai_khoan WHERE ma_nhan_vien = :ma_nhan_vien");
    $stmt2->execute([':ma_nhan_vien' => $ma_nhan_vien]);

    // Xóa nhân viên
    $stmt3 = $conn->prepare("DELETE FROM nhan_vien WHERE ma_nhan_vien = :ma_nhan_vien");
    $stmt3->execute([':ma_nhan_vien' => $ma_nhan_vien]);
}

header("Location: /admin/views/nhanvien.php?deleted=1");
exit();