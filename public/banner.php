<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /app/view/login.php");
    exit();
}
?>

<style>
    .banner {
        background: #343a40;
        color: white;
        padding: 10px;
        text-align: center;
        font-weight: bold;
        position: relative;
    }
    .logout-btn {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        background: red;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
    }
    .logout-btn:hover {
        background: darkred;
    }
</style>

<div class="banner"> 
    🔔 Trang này dành cho Admin 🔔
    <a href="/app/view/login.php" class="logout-btn">Đăng xuất</a></div>
