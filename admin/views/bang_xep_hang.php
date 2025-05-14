<!-- filepath: d:\xampp\htdocs\dacs3\admin\views\bang_xep_hang.php -->
<?php
ob_start(); // Bật bộ đệm đầu ra

include '../../config/db.php';

// Lấy danh sách tất cả các đợt
$sql_all_dots = "SELECT ma_dot, ten_dot FROM dot_danh_gia ORDER BY ma_dot DESC";
$stmt_all_dots = $conn->query($sql_all_dots);
$all_dots = $stmt_all_dots->fetchAll(PDO::FETCH_ASSOC);

// Cập nhật mã đợt từ GET nếu có
$ma_dot = $_GET['ma_dot'] ?? null;
$action = $_GET['action'] ?? 'view'; // Mặc định là xem bảng xếp hạng

if ($ma_dot) {
    $limit = 10;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Đếm tổng số nhân viên được đánh giá
    $sql_count = "
        SELECT COUNT(*) FROM (
            SELECT dg.nguoi_duoc_danh_gia
            FROM danh_gia dg
            WHERE dg.ma_dot = :ma_dot
            GROUP BY dg.nguoi_duoc_danh_gia
        ) AS temp
    ";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bindParam(':ma_dot', $ma_dot);
    $stmt_count->execute();
    $total = $stmt_count->fetchColumn();
    $total_pages = ceil($total / $limit);

    // Nếu xuất file thì lấy toàn bộ dữ liệu, không phân trang
    if ($action === 'export') {
        $sql_export = "
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
        $stmt_export = $conn->prepare($sql_export);
        $stmt_export->bindParam(':ma_dot', $ma_dot);
        $stmt_export->execute();
        $bang_xep_hang_export = $stmt_export->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="bang_xep_hang_dot_' . $ma_dot . '.xls"');
        echo "<table border='1'>";
        echo "<tr>
                <th>Hạng</th>
                <th>Người được đánh giá</th>
                <th>Điểm Tuân Thủ (TB)</th>
                <th>Điểm Hợp Tác (TB)</th>
                <th>Điểm Tận Tụy (TB)</th>
                <th>Điểm Trung Bình (TB)</th>
              </tr>";
        $rank = 1;
        foreach ($bang_xep_hang_export as $row) {
            echo "<tr>";
            echo "<td>" . $rank++ . "</td>";
            echo "<td>" . htmlspecialchars($row['ten_nguoi_duoc_danh_gia']) . "</td>";
            echo "<td>" . number_format($row['diem_tuan_thu_tb'], 2) . "</td>";
            echo "<td>" . number_format($row['diem_hop_tac_tb'], 2) . "</td>";
            echo "<td>" . number_format($row['diem_tan_tuy_tb'], 2) . "</td>";
            echo "<td>" . number_format($row['diem_trung_binh_tb'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }

    // Truy vấn dữ liệu bảng xếp hạng có phân trang (chỉ cho hiển thị web)
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
        LIMIT $limit OFFSET $offset
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ma_dot', $ma_dot);
    $stmt->execute();
    $bang_xep_hang = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Xử lý xuất file Excel
    if ($action === 'export' && !empty($bang_xep_hang)) {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="bang_xep_hang_dot_' . $ma_dot . '.xls"');
        echo "<table border='1'>";
        echo "<tr>
                <th>Hạng</th>
                <th>Người được đánh giá</th>
                <th>Điểm Tuân Thủ (TB)</th>
                <th>Điểm Hợp Tác (TB)</th>
                <th>Điểm Tận Tụy (TB)</th>
                <th>Điểm Trung Bình (TB)</th>
              </tr>";
        $rank = 1 + $offset;
        foreach ($bang_xep_hang as $row) {
            echo "<tr>";
            echo "<td>" . $rank++ . "</td>";
            echo "<td>" . htmlspecialchars($row['ten_nguoi_duoc_danh_gia']) . "</td>";
            echo "<td>" . number_format($row['diem_tuan_thu_tb'], 2) . "</td>";
            echo "<td>" . number_format($row['diem_hop_tac_tb'], 2) . "</td>";
            echo "<td>" . number_format($row['diem_tan_tuy_tb'], 2) . "</td>";
            echo "<td>" . number_format($row['diem_trung_binh_tb'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }

    // Xử lý khi nhấn nút "Đăng bảng xếp hạng"
if ($action === 'post') {
    // Có thể lưu trạng thái "đã đăng" vào DB nếu cần ở đây
    $thong_bao_dang = true;
    // KHÔNG chuyển hướng sang dua_top.php
}
}
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
<body>
<div class="main">
    <?php include './siderbar.php'; ?>

    <div class="content">
        <?php if (!empty($thong_bao_dang)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> Đăng bảng xếp hạng thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    <?php endif; ?>
        <?php
        // Tìm tên đợt được chọn
        $ten_dot_duoc_chon = 'Không xác định';
        foreach ($all_dots as $dot) {
            if ($dot['ma_dot'] == $ma_dot) {
                $ten_dot_duoc_chon = $dot['ten_dot'];
                break;
            }
        }
        ?>
        <h2>Bảng xếp hạng đợt đánh giá: <?= htmlspecialchars($ten_dot_duoc_chon) ?></h2>

        <!-- Dropdown chọn đợt đánh giá -->
        <form method="get" action="">
            <label for="ma_dot">Chọn đợt:</label>
            <select name="ma_dot" id="ma_dot" class="form-select" style="width: 300px;">
                <?php foreach ($all_dots as $dot): ?>
                    <option value="<?= $dot['ma_dot'] ?>" <?= ($dot['ma_dot'] == $ma_dot) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dot['ten_dot']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="action" value="view" class="btn btn-primary mt-2">Xem bảng xếp hạng</button>
            <button type="submit" name="action" value="export" class="btn btn-success mt-2">Xuất file</button>
            <button type="submit" name="action" value="post" class="btn btn-warning mt-2">Đăng bảng xếp hạng</button>
        </form>

        <?php if (!empty($bang_xep_hang) && $action === 'view'): ?>
            <table>
                <thead>
                    <tr>
                        <th>Hạng</th>
                        <th>Người được đánh giá</th>
                        <th>Điểm Tuân Thủ (TB)</th>
                        <th>Điểm Hợp Tác (TB)</th>
                        <th>Điểm Tận Tụy (TB)</th>
                        <th>Điểm Trung Bình (TB)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1 + $offset; ?>

                    <?php foreach ($bang_xep_hang as $row): ?>
                        <tr>
                            <td><?= $rank++ ?></td>
                            <td><?= htmlspecialchars($row['ten_nguoi_duoc_danh_gia']) ?></td>
                            <td><?= number_format($row['diem_tuan_thu_tb'], 2) ?></td>
                            <td><?= number_format($row['diem_hop_tac_tb'], 2) ?></td>
                            <td><?= number_format($row['diem_tan_tuy_tb'], 2) ?></td>
                            <td><?= number_format($row['diem_trung_binh_tb'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <!-- PHÂN TRANG -->
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php
            $query = $_GET;
            for ($i = 1; $i <= $total_pages; $i++):
                $query['page'] = $i;
                $link = '?' . http_build_query($query);
            ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $link ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
<?php elseif ($action === 'view'): ?>
    <p>Không có dữ liệu đánh giá cho đợt này.</p>
<?php endif; ?>
    </div>
</div>
</body>
</html>