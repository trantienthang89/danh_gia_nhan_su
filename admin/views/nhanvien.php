<?php
include '../../config/db.php';

$sql = "SELECT nv.ma_nhan_vien, nv.ho_ten, cv.ten_chuc_vu, pb.ten_phong_ban 
        FROM nhan_vien nv
        JOIN chuc_vu cv ON nv.chuc_vu_id = cv.ma_chuc_vu
        JOIN phong_ban pb ON nv.phong_ban_id = pb.ma_phong_ban
        WHERE 1=1";

$params = [];
if (!empty($_GET['ma_nv'])) {
    $sql .= " AND nv.ma_nhan_vien LIKE ?";
    $params[] = "%" . $_GET['ma_nv'] . "%";
}
if (!empty($_GET['ho_ten'])) {
    $sql .= " AND nv.ho_ten LIKE ?";
    $params[] = "%" . $_GET['ho_ten'] . "%";
}
if (!empty($_GET['chuc_vu'])) {
    $sql .= " AND cv.ten_chuc_vu LIKE ?";
    $params[] = "%" . $_GET['chuc_vu'] . "%";
}
if (!empty($_GET['phong_ban'])) {
    $sql .= " AND pb.ten_phong_ban LIKE ?";
    $params[] = "%" . $_GET['phong_ban'] . "%";
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$nhanviens = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý nhân viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
         .content {
            flex-grow: 1;
            padding: 20px;
        }
        .main {
            display: flex;
            flex-grow: 1;
        }
        .filter-dropdown {
            position: relative;
            display: inline-block;
        }
        .filter-menu {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 250px;
            border: 1px solid #ddd;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
            border-radius: 8px;
            z-index: 1000;
        }
        .filter-dropdown:hover .filter-menu {
            display: block;
        }
        .filter-menu input {
            margin-bottom: 8px;
        }
    </style>
</head>

<div class="main">
    <?php include './siderbar.php'; ?>

    <div class="content">
    <div class="container mt-4">
        <h2>Danh sách nhân viên</h2>
        <div class="d-flex align-items-center mb-3">
        <div class="filter-dropdown me-2">
            <button class="btn btn-primary">
                <i class="fas fa-filter"></i> Lọc
            </button>
            <!-- Menu lọc -->
            <div class="filter-menu p-3">
            <form id="filterForm">
    <div class="mb-2">
        <label class="form-label">Mã NV:</label>
        <input type="text" name="ma_nv" class="form-control" value="<?= $_GET['ma_nv'] ?? '' ?>">
    </div>
    <div class="mb-2">
        <label class="form-label">Họ tên:</label>
        <input type="text" name="ho_ten" class="form-control" value="<?= $_GET['ho_ten'] ?? '' ?>">
    </div>
    <div class="mb-2">
        <label class="form-label">Chức vụ:</label>
        <select name="chuc_vu" class="form-control">
            <option value="">-- Chọn chức vụ --</option>
            <?php
            $stmt = $conn->query("SELECT ma_chuc_vu, ten_chuc_vu FROM chuc_vu");
            $chuc_vus = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($chuc_vus as $cv) {
                $selected = ($_GET['chuc_vu'] ?? '') == $cv['ten_chuc_vu'] ? "selected" : "";
                echo "<option value='{$cv['ten_chuc_vu']}' $selected>{$cv['ten_chuc_vu']}</option>";
            }
            ?>
        </select>
    </div>
    <div class="mb-2">
        <label class="form-label">Phòng ban:</label>
        <select name="phong_ban" class="form-control">
            <option value="">-- Chọn phòng ban --</option>
            <?php
            $stmt = $conn->query("SELECT ma_phong_ban, ten_phong_ban FROM phong_ban");
            $phong_bans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($phong_bans as $pb) {
                $selected = ($_GET['phong_ban'] ?? '') == $pb['ten_phong_ban'] ? "selected" : "";
                echo "<option value='{$pb['ten_phong_ban']}' $selected>{$pb['ten_phong_ban']}</option>";
            }
            ?>
        </select>
    </div>
    <button type="submit" class="btn btn-success w-100">Áp dụng</button>
</form>

            </div>
        </div>

        <!-- Nút Reset -->
        <button class="btn btn-secondary" id="resetFilter">
            <i class="fas fa-sync-alt"></i> Reset
        </button>
    </div>
        <a href="../controllers//them_nhanvien.php" class="btn btn-success mb-2">Thêm nhân viên</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã NV</th>
                    <th>Họ tên</th>
                    <th>Chức vụ</th>
                    <th>Phòng ban</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($nhanviens as $row) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['ma_nhan_vien']) ?></td>
                    <td><?= htmlspecialchars($row['ho_ten']) ?></td>
                    <td><?= htmlspecialchars($row['ten_chuc_vu']) ?></td>
                    <td><?= htmlspecialchars($row['ten_phong_ban']) ?></td>
                    <td>
                        <a href="../controllers//sua_nhanvien.php?id=<?= $row['ma_nhan_vien'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                        <a href="../controllers//xoa_nhanvien.php?id=<?= $row['ma_nhan_vien'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
    // Xử lý khi nhấn nút "Áp dụng" lọc
    document.getElementById("filterForm").addEventListener("submit", function(event) {
        event.preventDefault();
        const params = new URLSearchParams(new FormData(this)).toString();
        window.location.href = "?" + params; // Chuyển hướng trang với tham số lọc
    });

    // Xử lý khi nhấn nút "Reset" để trở lại danh sách ban đầu
    document.getElementById("resetFilter").addEventListener("click", function() {
        window.location.href = window.location.pathname; // Xóa tất cả tham số lọc
    });
</script>
    </div>
</div>


    
</body>
</html>
