<?php
include '../../config/db.php';

// Lấy danh sách các đợt đánh giá
$sql_dots = "SELECT ma_dot, ten_dot FROM dot_danh_gia ORDER BY ma_dot DESC";
$stmt_dots = $conn->query($sql_dots);
$ds_dots = $stmt_dots->fetchAll(PDO::FETCH_ASSOC);

// Lấy các tham số lọc từ GET
$ma_dot = $_GET['ma_dot'] ?? null;
$nguoi_danh_gia = $_GET['nguoi_danh_gia'] ?? null;
$nguoi_duoc_danh_gia = $_GET['nguoi_duoc_danh_gia'] ?? null;
$diem_tu = $_GET['diem_tu'] ?? null;
$diem_den = $_GET['diem_den'] ?? null;

// Lấy danh sách đánh giá theo các tiêu chí lọc
$conditions = [];
$params = [];

// Lọc theo mã đợt
if (!empty($ma_dot)) {
    $conditions[] = "dg.ma_dot = :ma_dot";
    $params[':ma_dot'] = $ma_dot;
}

// Lọc theo tên người đánh giá
if (!empty($nguoi_danh_gia)) {
    $conditions[] = "ndg.ho_ten LIKE :nguoi_danh_gia";
    $params[':nguoi_danh_gia'] = '%' . $nguoi_danh_gia . '%';
}

// Lọc theo tên người được đánh giá
if (!empty($nguoi_duoc_danh_gia)) {
    $conditions[] = "nddg.ho_ten LIKE :nguoi_duoc_danh_gia";
    $params[':nguoi_duoc_danh_gia'] = '%' . $nguoi_duoc_danh_gia . '%';
}

// Lọc theo khoảng điểm
if (!empty($diem_tu)) {
    $conditions[] = "dg.diem_trung_binh >= :diem_tu";
    $params[':diem_tu'] = $diem_tu;
}
if (!empty($diem_den)) {
    $conditions[] = "dg.diem_trung_binh <= :diem_den";
    $params[':diem_den'] = $diem_den;
}

// Kết hợp các điều kiện
$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = "
    SELECT dg.*, 
           ndg.ho_ten AS ten_nguoi_danh_gia, 
           nddg.ho_ten AS ten_nguoi_duoc_danh_gia
    FROM danh_gia dg
    JOIN nhan_vien ndg ON dg.nguoi_danh_gia = ndg.ma_nhan_vien
    JOIN nhan_vien nddg ON dg.nguoi_duoc_danh_gia = nddg.ma_nhan_vien
    $whereClause
";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$ds_danh_gia = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .content {
            flex-grow: 1;
            padding: 20px;
        }
        .main {
            display: flex;
            flex-grow: 1;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        h2 {
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>


<div class="main">
    <?php include './siderbar.php'; ?>

    <div class="content">
        <h2>Danh sách đánh giá nhân viên</h2>
        <button id="toggleFilter" class="btn btn-secondary mb-3">Hiển thị bộ lọc</button>

<div id="filterForm" style="display: none; margin-bottom: 20px;">
    <form method="GET" action="">
        <div class="row">
            <div class="col-md-3">
                <label for="ma_dot">Chọn đợt đánh giá:</label>
                <select name="ma_dot" id="ma_dot" class="form-select">
                    <option value="">-- Tất cả các đợt --</option>
                    <?php foreach ($ds_dots as $dot): ?>
                        <option value="<?= $dot['ma_dot'] ?>" <?= ($dot['ma_dot'] == $ma_dot) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dot['ten_dot']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="nguoi_danh_gia">Người đánh giá:</label>
                <input type="text" name="nguoi_danh_gia" id="nguoi_danh_gia" class="form-control" value="<?= htmlspecialchars($nguoi_danh_gia) ?>">
            </div>
            <div class="col-md-3">
                <label for="nguoi_duoc_danh_gia">Người được đánh giá:</label>
                <input type="text" name="nguoi_duoc_danh_gia" id="nguoi_duoc_danh_gia" class="form-control" value="<?= htmlspecialchars($nguoi_duoc_danh_gia) ?>">
            </div>
            <div class="col-md-3">
                <label for="diem_tu">Điểm từ:</label>
                <input type="number" step="0.01" name="diem_tu" id="diem_tu" class="form-control" value="<?= htmlspecialchars($diem_tu) ?>">
            </div>
            <div class="col-md-3">
                <label for="diem_den">Điểm đến:</label>
                <input type="number" step="0.01" name="diem_den" id="diem_den" class="form-control" value="<?= htmlspecialchars($diem_den) ?>">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary w-100">Lọc</button>
            </div>
        </div>
    </form>
</div>

        <?php if (empty($ds_danh_gia)): ?>
            <p>Không có dữ liệu đánh giá cho đợt này.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Người đánh giá</th>
                        <th>Người được đánh giá</th>
                        <th>Tuân thủ</th>
                        <th>Hợp tác</th>
                        <th>Tận tuỵ</th>
                        <th>Điểm TB</th>
                        <th>Nhận xét</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ds_danh_gia as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ten_nguoi_danh_gia']) ?> (<?= $row['nguoi_danh_gia'] ?>)</td>
                            <td><?= htmlspecialchars($row['ten_nguoi_duoc_danh_gia']) ?> (<?= $row['nguoi_duoc_danh_gia'] ?>)</td>
                            <td><?= $row['diem_tuan_thu'] ?></td>
                            <td><?= $row['diem_hop_tac'] ?></td>
                            <td><?= $row['diem_tan_tuy'] ?></td>
                            <td><?= is_null($row['diem_trung_binh']) ? '-' : $row['diem_trung_binh'] ?></td>
                            <td><?= htmlspecialchars($row['nhan_xet']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<script>
    document.getElementById('toggleFilter').addEventListener('click', function () {
        const filterForm = document.getElementById('filterForm');
        if (filterForm.style.display === 'none') {
            filterForm.style.display = 'block';
            this.textContent = 'Ẩn bộ lọc';
        } else {
            filterForm.style.display = 'none';
            this.textContent = 'Hiển thị bộ lọc';
        }
    });
</script>


</body>
</html>