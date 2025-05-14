<?php
require_once '../../config/db.php';

class AuthModel {
public function checkLogin($ma_nhan_vien, $ten_dang_nhap, $mat_khau) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT tk.*, nv.ho_ten 
        FROM tai_khoan tk
        JOIN nhan_vien nv ON tk.ma_nhan_vien = nv.ma_nhan_vien
        WHERE tk.ma_nhan_vien = :ma AND tk.ten_dang_nhap = :ten
    ");
    $stmt->execute([
        ':ma' => $ma_nhan_vien,
        ':ten' => $ten_dang_nhap,
    ]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}
