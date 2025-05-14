<?php

include '../../config/db.php';

// Lấy mã đợt từ URL
$ma_dot = $_GET['ma_dot'] ?? null;

if ($ma_dot) {
    $sql = "
        SELECT 
            nddg.ho_ten AS ten_nguoi_duoc_danh_gia, 
            SUM(dg.diem_tuan_thu * ts.trong_so) / SUM(ts.trong_so) AS diem_tuan_thu_tb, 
            SUM(dg.diem_hop_tac * ts.trong_so) / SUM(ts.trong_so) AS diem_hop_tac_tb, 
            SUM(dg.diem_tan_tuy * ts.trong_so) / SUM(ts.trong_so) AS diem_tan_tuy_tb, 
            SUM(dg.diem_trung_binh * ts.trong_so) / SUM(ts.trong_so) AS diem_trung_binh_tb
        FROM danh_gia dg
        JOIN nhan_vien ndg ON dg.nguoi_danh_gia = ndg.ma_nhan_vien
        JOIN nhan_vien nddg ON dg.nguoi_duoc_danh_gia = nddg.ma_nhan_vien
        JOIN trong_so_danh_gia ts ON ndg.chuc_vu_id = ts.ma_chuc_vu
        WHERE dg.ma_dot = :ma_dot
        GROUP BY dg.nguoi_duoc_danh_gia
        ORDER BY diem_trung_binh_tb DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ma_dot', $ma_dot);
    $stmt->execute();
    $bang_xep_hang = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $bang_xep_hang = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng xếp hạng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
        }
        .main {
            display: flex;
            min-height: 100vh;
        }
        .content {
            flex: 1;
            padding: 32px 24px;
            background: #fff;
            border-radius: 12px;
            margin: 32px auto;
            max-width: 900px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .top-medal {
            width: 38px;
            height: 38px;
            margin-bottom: 6px;
        }
        .top-row {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 32px;
            margin-bottom: 32px;
        }
        .top-box {
            background: #f8fafc;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            padding: 18px 16px;
            min-width: 120px;
            text-align: center;
        }
        .top-box.top1 {
            border: 2px solid #facc15;
            background: #fffbe6;
            scale: 1.08;
        }
        .table thead th {
            background: #f1f5f9;
        }
    </style>
</head>
<body>
<div class="main">
    <?php include './sidebar.php'; ?>

    <div class="content">
        <h2 class="mb-4"><i class="bi bi-trophy-fill text-warning"></i> Bảng xếp hạng</h2>

        <form method="get" class="row g-2 align-items-end mb-4">
            <div class="col-auto">
                <label for="ma_dot" class="form-label mb-0">Chọn đợt:</label>
            </div>
            <div class="col-auto">
                <select name="ma_dot" id="ma_dot" class="form-select">
                    <?php
                    $dots = $conn->query("SELECT ma_dot, ten_dot FROM dot_danh_gia ORDER BY ma_dot DESC")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($dots as $dot):
                    ?>
                        <option value="<?= $dot['ma_dot'] ?>" <?= ($ma_dot == $dot['ma_dot']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dot['ten_dot']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Xem</button>
            </div>
        </form>

        <!-- Hiển thị Top 1, 2, 3 -->
        <div class="top-row">
            <?php if (isset($bang_xep_hang[1])): ?>
                <div class="top-box">
    <span style="font-size:2.2rem;display:block;">🥈</span>
                    <div class="fw-semibold"><?= htmlspecialchars($bang_xep_hang[1]['ten_nguoi_duoc_danh_gia']) ?></div>
                    <div class="text-secondary small">Hạng 2</div>
                </div>
            <?php endif; ?>

            <?php if (isset($bang_xep_hang[0])): ?>
                <div class="top-box top1">
    <span style="font-size:2.2rem;display:block;">🥇</span>
                    <div class="fw-bold"><?= htmlspecialchars($bang_xep_hang[0]['ten_nguoi_duoc_danh_gia']) ?></div>
                    <div class="text-warning small">Hạng 1</div>
                </div>
            <?php endif; ?>

            <?php if (isset($bang_xep_hang[2])): ?>
                <div class="top-box">
    <span style="font-size:2.2rem;display:block;">🥉</span>
                    <div class="fw-semibold"><?= htmlspecialchars($bang_xep_hang[2]['ten_nguoi_duoc_danh_gia']) ?></div>
                    <div class="text-secondary small">Hạng 3</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Hiển thị bảng xếp hạng -->
        <?php if (!empty($bang_xep_hang)): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Hạng</th>
                        <th>Người được đánh giá</th>
                        <th>Điểm Tuân Thủ (TB)</th>
                        <th>Điểm Hợp Tác (TB)</th>
                        <th>Điểm Tận Tụy (TB)</th>
                        <th>Điểm Trung Bình</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bang_xep_hang as $i => $row): ?>
                        <tr<?= $i < 3 ? ' class="table-warning"' : '' ?>>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($row['ten_nguoi_duoc_danh_gia']) ?></td>
                            <td><?= number_format($row['diem_tuan_thu_tb'], 2) ?></td>
                            <td><?= number_format($row['diem_hop_tac_tb'], 2) ?></td>
                            <td><?= number_format($row['diem_tan_tuy_tb'], 2) ?></td>
                            <td class="fw-bold"><?= number_format($row['diem_trung_binh_tb'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="alert alert-info mt-4">Không có dữ liệu đánh giá cho đợt này.</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>