<?php
include '../../config/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();

// Kiểm tra quyền admin nếu cần
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     header('Location: ../login.php');
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_dot = $_POST['ten_dot'];
    $thoi_gian_bat_dau = $_POST['ngay_bat_dau'];
    $thoi_gian_ket_thuc = $_POST['ngay_ket_thuc'];

    $now = date('Y-m-d H:i:s');

    // Xác định trạng thái
    if ($now < $thoi_gian_bat_dau) {
        $trang_thai = 'Chua Bat Dau';
    } elseif ($now >= $thoi_gian_bat_dau && $now <= $thoi_gian_ket_thuc) {
        $trang_thai = 'Dang Dien Ra';
    } else {
        $trang_thai = 'Da Ket Thuc';
    }

   // Thêm đợt đánh giá mới
$stmt = $conn->prepare("INSERT INTO dot_danh_gia (ten_dot, thoi_gian_bat_dau, thoi_gian_ket_thuc, trang_thai) VALUES (?, ?, ?, ?)");
$stmt->execute([$ten_dot, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $trang_thai]);

// Lấy mã đợt vừa thêm
$ma_dot_moi = $conn->lastInsertId();

    

    header('Location: /admin/views/quan_li_dot.php');
    exit();
}
$now = date('Y-m-d H:i:s');

date_default_timezone_set('Asia/Ho_Chi_Minh'); // Đảm bảo đúng timezone
$now = date('Y-m-d H:i:s');

// Cập nhật trạng thái tất cả đợt đánh giá
$sql = "UPDATE dot_danh_gia SET 
        trang_thai = CASE 
            WHEN thoi_gian_bat_dau > '$now' THEN 'Chua Bat Dau'
            WHEN thoi_gian_bat_dau <= '$now' AND thoi_gian_ket_thuc >= '$now' THEN 'Dang Dien Ra'
            WHEN thoi_gian_ket_thuc < '$now' THEN 'Da Ket Thuc'
        END";
$conn->exec($sql);



// Lấy danh sách đợt đánh giá
$stmt = $conn->query("SELECT * FROM dot_danh_gia ORDER BY ma_dot DESC");
$ds_dot = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đợt đánh giá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .main { display: flex; }
        .content { flex-grow: 1; padding: 20px; }
    </style>
</head>
<body>



<div class="main">
    <?php include './siderbar.php'; ?>

    <div class="content">
        <h2>Quản lý đợt đánh giá</h2>

        <!-- Form thêm đợt đánh giá -->
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label class="form-label">Tên đợt</label>
                <input type="text" name="ten_dot" class="form-control" required>
            </div>
            <div class="mb-3">
    <label class="form-label">Thời gian bắt đầu</label>
    <input type="datetime-local" name="ngay_bat_dau" class="form-control" required>
</div>
<div class="mb-3">
    <label class="form-label">Thời gian kết thúc</label>
    <input type="datetime-local" name="ngay_ket_thuc" class="form-control" required>
</div>

            <button type="submit" class="btn btn-primary">Thêm đợt</button>
        </form>

        <!-- Danh sách các đợt -->
        <h4>Danh sách đợt đánh giá</h4>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Mã đợt</th>
                    <th>Tên đợt</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ds_dot as $dot): ?>
                    <tr>
                        <td><?= $dot['ma_dot'] ?></td>
                        <td><?= htmlspecialchars($dot['ten_dot']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($dot['thoi_gian_bat_dau'])) ?></td>
<td><?= date('d/m/Y H:i', strtotime($dot['thoi_gian_ket_thuc'])) ?></td>

                        <td>
    <span class="badge 
        <?php
            if ($dot['trang_thai'] === 'Dang Dien Ra') echo 'bg-success';
            elseif ($dot['trang_thai'] === 'Da Ket Thuc') echo 'bg-danger';
            else echo 'bg-warning';
        ?>">
        <?php
            if ($dot['trang_thai'] === 'Dang Dien Ra') echo 'Đang diễn ra';
            elseif ($dot['trang_thai'] === 'Da Ket Thuc') echo 'Đã kết thúc';
            else echo 'Chưa bắt đầu';
        ?>
    </span>
</td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


</body>
</html>
