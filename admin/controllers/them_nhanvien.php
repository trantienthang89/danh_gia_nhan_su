<?php
include '../../config/db.php';

// Lấy danh sách chức vụ và phòng ban từ cơ sở dữ liệu
$sql_chuc_vu = "SELECT * FROM chuc_vu"; // Lấy tất cả chức vụ
$stmt_chuc_vu = $conn->prepare($sql_chuc_vu);
$stmt_chuc_vu->execute();
$chuc_vus = $stmt_chuc_vu->fetchAll();

$sql_phong_ban = "SELECT * FROM phong_ban"; // Lấy tất cả phòng ban
$stmt_phong_ban = $conn->prepare($sql_phong_ban);
$stmt_phong_ban->execute();
$phong_bans = $stmt_phong_ban->fetchAll();

// Xử lý form khi người dùng gửi dữ liệu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ma_nv = $_POST['ma_nhan_vien'];
    $ho_ten = $_POST['ho_ten'];
    $chuc_vu = $_POST['chuc_vu'];
    $phong_ban = $_POST['phong_ban'];

    $sql = "INSERT INTO nhan_vien (ma_nhan_vien, ho_ten, chuc_vu_id, phong_ban_id) 
            VALUES (:ma_nv, :ho_ten, :chuc_vu, :phong_ban)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ma_nv', $ma_nv);
    $stmt->bindParam(':ho_ten', $ho_ten);
    $stmt->bindParam(':chuc_vu', $chuc_vu);
    $stmt->bindParam(':phong_ban', $phong_ban);
    
    if ($stmt->execute()) {
        header("Location: http://localhost/dacs3/admin/views/nhanvien.php");        exit;  // Dừng script sau khi chuyển hướng
    } else {
        echo "Lỗi khi thêm nhân viên!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm nhân viên</title>
</head>
<body>
    <h2>Thêm nhân viên mới</h2>
    <form method="POST">
        Mã NV: <input type="text" name="ma_nhan_vien" required><br>
        Họ tên: <input type="text" name="ho_ten" required><br>
        
        <!-- Hiển thị chức vụ -->
        Chức vụ:
        <select name="chuc_vu" required>
            <option value="">Chọn chức vụ</option>
            <?php foreach ($chuc_vus as $chuc_vu): ?>
                <option value="<?= $chuc_vu['ma_chuc_vu'] ?>"><?= $chuc_vu['ten_chuc_vu'] ?></option>
            <?php endforeach; ?>
        </select><br>
        
        <!-- Hiển thị phòng ban -->
        Phòng ban:
        <select name="phong_ban" required>
            <option value="">Chọn phòng ban</option>
            <?php foreach ($phong_bans as $phong_ban): ?>
                <option value="<?= $phong_ban['ma_phong_ban'] ?>"><?= $phong_ban['ten_phong_ban'] ?></option>
            <?php endforeach; ?>
        </select><br>
        
        <button type="submit">Thêm</button>
    </form>
</body>
</html>
