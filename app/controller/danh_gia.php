<?php
include '../../config/db.php';
session_start();

if (!isset($_SESSION['user']['ma_nhan_vien'])) {
    header('Location: login.php');
    exit();
}

$nguoi_danh_gia = $_POST['nguoi_danh_gia'];
$nguoi_duoc_danh_gia = $_POST['nguoi_duoc_danh_gia'];
$diem_tuan_thu = (float)$_POST['diem_tuan_thu'];
$diem_hop_tac = (float)$_POST['diem_hop_tac'];
$diem_tan_tuy = (float)$_POST['diem_tan_tuy'];
$nhan_xet = $_POST['nhan_xet'];
$thoi_gian = date('Y-m-d H:i:s');
$ma_dot = 1;
$diem_tb = round(($diem_tuan_thu + $diem_hop_tac + $diem_tan_tuy) / 3, 2);

// Kiểm tra xem đã tồn tại đánh giá chưa
$sql_check = "SELECT ma_danh_gia FROM danh_gia 
              WHERE ma_dot = :ma_dot AND nguoi_danh_gia = :nguoi_danh_gia AND nguoi_duoc_danh_gia = :nguoi_duoc_danh_gia";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->execute([
    ':ma_dot' => $ma_dot,
    ':nguoi_danh_gia' => $nguoi_danh_gia,
    ':nguoi_duoc_danh_gia' => $nguoi_duoc_danh_gia
]);

if ($stmt_check->rowCount() > 0) {
    // Đã tồn tại -> Cập nhật
    $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
    $ma_danh_gia = $row['ma_danh_gia'];

    $sql_update = "UPDATE danh_gia SET 
                    diem_tuan_thu = :diem_tuan_thu,
                    diem_hop_tac = :diem_hop_tac,
                    diem_tan_tuy = :diem_tan_tuy,
                    nhan_xet = :nhan_xet,
                    thoi_gian = :thoi_gian,
                    diem_trung_binh = :diem_tb
                WHERE ma_danh_gia = :ma_danh_gia";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->execute([
        ':diem_tuan_thu' => $diem_tuan_thu,
        ':diem_hop_tac' => $diem_hop_tac,
        ':diem_tan_tuy' => $diem_tan_tuy,
        ':nhan_xet' => $nhan_xet,
        ':thoi_gian' => $thoi_gian,
        ':diem_tb' => $diem_tb,
        ':ma_danh_gia' => $ma_danh_gia
    ]);
} else {
    // Chưa có -> Thêm mới
    $sql_insert = "INSERT INTO danh_gia 
        (ma_dot, nguoi_danh_gia, nguoi_duoc_danh_gia, diem_tuan_thu, diem_hop_tac, diem_tan_tuy, nhan_xet, thoi_gian, diem_trung_binh)
        VALUES 
        (:ma_dot, :nguoi_danh_gia, :nguoi_duoc_danh_gia, :diem_tuan_thu, :diem_hop_tac, :diem_tan_tuy, :nhan_xet, :thoi_gian, :diem_tb)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->execute([
        ':ma_dot' => $ma_dot,
        ':nguoi_danh_gia' => $nguoi_danh_gia,
        ':nguoi_duoc_danh_gia' => $nguoi_duoc_danh_gia,
        ':diem_tuan_thu' => $diem_tuan_thu,
        ':diem_hop_tac' => $diem_hop_tac,
        ':diem_tan_tuy' => $diem_tan_tuy,
        ':nhan_xet' => $nhan_xet,
        ':thoi_gian' => $thoi_gian,
        ':diem_tb' => $diem_tb
    ]);
}

echo "<script>alert('Đánh giá đã được lưu thành công!'); window.location.href='trang_danh_gia.php';</script>";
exit();
?>
