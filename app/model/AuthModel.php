<?php
require_once '../../config/db.php';

class AuthModel {
    public function checkLogin($ma_nhan_vien, $ten_dang_nhap, $mat_khau) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM tai_khoan WHERE ma_nhan_vien = :ma AND ten_dang_nhap = :ten ");
        $stmt->execute([
            ':ma' => $ma_nhan_vien,
            ':ten' => $ten_dang_nhap,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
