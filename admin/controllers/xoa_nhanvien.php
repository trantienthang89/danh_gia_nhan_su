<?php
include '../../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Bắt đầu transaction để đảm bảo tính nhất quán
        $conn->beginTransaction();

        // Xóa tài khoản của nhân viên trước
        $sql0 = "DELETE FROM tai_khoan WHERE ma_nhan_vien = :id";
        $stmt0 = $conn->prepare($sql0);
        $stmt0->bindParam(':id', $id);
        $stmt0->execute();

        // Xóa dữ liệu trong `lich_su_danh_gia` liên quan đến nhân viên
        $sql1 = "DELETE FROM lich_su_danh_gia WHERE nguoi_duoc_danh_gia = :id";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bindParam(':id', $id);
        $stmt1->execute();

        // Xóa dữ liệu trong `danh_gia` liên quan đến nhân viên
        $sql2 = "DELETE FROM danh_gia WHERE nguoi_duoc_danh_gia = :id";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bindParam(':id', $id);
        $stmt2->execute();

        // Sau cùng mới xóa nhân viên trong bảng `nhan_vien`
        $sql3 = "DELETE FROM nhan_vien WHERE ma_nhan_vien = :id";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bindParam(':id', $id);
        $stmt3->execute();

        // Hoàn thành transaction
        $conn->commit();

        header("Location: /admin/views/nhanvien.php?message=Xóa thành công");

        exit();
    } catch (PDOException $e) {
        // Nếu có lỗi, rollback để tránh lỗi dữ liệu
        $conn->rollBack();
        echo "Lỗi: " . $e->getMessage();
    }
}
?>
