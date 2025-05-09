<?php
include '../../config/db.php';

// Lấy ID nhân viên
$id = $_GET['id'];

// Lấy thông tin nhân viên cần sửa
$sql = "SELECT * FROM nhan_vien WHERE ma_nhan_vien=:id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Lấy danh sách chức vụ
$sql_chucvu = "SELECT ma_chuc_vu, ten_chuc_vu FROM chuc_vu";  // Sửa cột id thành ma_chuc_vu
$stmt_chucvu = $conn->prepare($sql_chucvu);
$stmt_chucvu->execute();
$chuc_vus = $stmt_chucvu->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách phòng ban
$sql_phongban = "SELECT ma_phong_ban, ten_phong_ban FROM phong_ban";  // Sửa cột id thành ma_phong_ban
$stmt_phongban = $conn->prepare($sql_phongban);
$stmt_phongban->execute();
$phong_bans = $stmt_phongban->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ho_ten = $_POST['ho_ten'];
    $chuc_vu = $_POST['chuc_vu'];
    $phong_ban = $_POST['phong_ban'];

    $sql = "UPDATE nhan_vien SET ho_ten=:ho_ten, chuc_vu_id=:chuc_vu, phong_ban_id=:phong_ban WHERE ma_nhan_vien=:id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ho_ten', $ho_ten);
    $stmt->bindParam(':chuc_vu', $chuc_vu);
    $stmt->bindParam(':phong_ban', $phong_ban);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        header("Location: /admin/views/nhanvien.php");
    } else {
        echo "Lỗi khi cập nhật!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa nhân viên</title>
</head>
<body>
    <h2>Sửa thông tin nhân viên</h2>
    <form method="POST">
        <label>Họ tên:</label>
        <input type="text" name="ho_ten" value="<?= htmlspecialchars($row['ho_ten']) ?>" required><br>

        <label>Chức vụ:</label>
        <select name="chuc_vu" required>
            <?php foreach ($chuc_vus as $cv): ?>
                <option value="<?= $cv['ma_chuc_vu'] ?>" <?= ($cv['ma_chuc_vu'] == $row['chuc_vu_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cv['ten_chuc_vu']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Phòng ban:</label>
        <select name="phong_ban" required>
            <?php foreach ($phong_bans as $pb): ?>
                <option value="<?= $pb['ma_phong_ban'] ?>" <?= ($pb['ma_phong_ban'] == $row['phong_ban_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($pb['ten_phong_ban']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit">Lưu</button>
    </form>
</body>
</html>
