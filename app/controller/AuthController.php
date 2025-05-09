<?php
require_once '../model/AuthModel.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ma_nhan_vien = $_POST['ma_nhan_vien'];
    $ten_dang_nhap = $_POST['ten_dang_nhap'];
    $mat_khau = $_POST['mat_khau'];

    $auth = new AuthModel();
    $user = $auth->checkLogin($ma_nhan_vien, $ten_dang_nhap, $mat_khau);

    if ($user && password_verify($mat_khau, $user['mat_khau'])) {
        $_SESSION['user'] = $user;

        if ($user['quyen'] === 'Admin') {
            header("Location: /admin/views/dashboard.php");    
        } else {
            header("Location: /app/view/trang_danh_gia.php");        }
        exit();
    } else {
        header("Location: ../view/login.php?error=Thông tin sai!");
        exit();
    }
}
