<?php
include '../db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nguoi_danh_gia = $_POST['nguoi_danh_gia'];
    $nguoi_duoc_danh_gia = $_POST['nguoi_duoc_danh_gia'];
    $diem_tuan_thu = $_POST['diem_tuan_thu'];
    $diem_hop_tac = $_POST['diem_hop_tac'];
    $diem_tan_tuy = $_POST['diem_tan_tuy'];
    $nhan_xet = $_POST['nhan_xet'];
    $ma_dot = 1; // Giả sử mã đợt đánh giá hiện tại là 1

    // Tính điểm trung bình
    $diem_trung_binh = ($diem_tuan_thu + $diem_hop_tac + $diem_tan_tuy) / 3;

    // Lưu vào bảng danh_gia
    $sql = "INSERT INTO danh_gia (ma_dot, nguoi_danh_gia, nguoi_duoc_danh_gia, diem_tuan_thu, diem_hop_tac, diem_tan_tuy, nhan_xet, thoi_gian, diem_trung_binh)
            VALUES ('$ma_dot', '$nguoi_danh_gia', '$nguoi_duoc_danh_gia', '$diem_tuan_thu', '$diem_hop_tac', '$diem_tan_tuy', '$nhan_xet', NOW(), '$diem_trung_binh')";

    if ($conn->query($sql) === TRUE) {
        // Lưu vào bảng lịch sử đánh giá
        $sql_ls = "INSERT INTO lich_su_danh_gia (ma_dot, nguoi_danh_gia, nguoi_duoc_danh_gia, thoi_gian, diem_tuan_thu, diem_hop_tac, diem_tan_tuy, nhan_xet, diem_trung_binh)
                   VALUES ('$ma_dot', '$nguoi_danh_gia', '$nguoi_duoc_danh_gia', NOW(), '$diem_tuan_thu', '$diem_hop_tac', '$diem_tan_tuy', '$nhan_xet', '$diem_trung_binh')";
        $conn->query($sql_ls);

        echo "Đánh giá đã được lưu thành công!";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

$conn->close();
?>
