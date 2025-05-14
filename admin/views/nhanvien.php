<?php
include '../../config/db.php';

// Xử lý phân trang
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Xây dựng câu truy vấn lọc
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

// Đếm tổng số nhân viên sau khi lọc
$sql_count = "SELECT COUNT(*) FROM nhan_vien nv
              JOIN chuc_vu cv ON nv.chuc_vu_id = cv.ma_chuc_vu
              JOIN phong_ban pb ON nv.phong_ban_id = pb.ma_phong_ban
              WHERE 1=1";
$params_count = $params;
if (!empty($_GET['ma_nv'])) {
    $sql_count .= " AND nv.ma_nhan_vien LIKE ?";
}
if (!empty($_GET['ho_ten'])) {
    $sql_count .= " AND nv.ho_ten LIKE ?";
}
if (!empty($_GET['chuc_vu'])) {
    $sql_count .= " AND cv.ten_chuc_vu LIKE ?";
}
if (!empty($_GET['phong_ban'])) {
    $sql_count .= " AND pb.ten_phong_ban LIKE ?";
}
$stmt_count = $conn->prepare($sql_count);
$stmt_count->execute($params_count);
$total = $stmt_count->fetchColumn();
$total_pages = ceil($total / $limit);

// Lấy dữ liệu nhân viên có phân trang
$sql .= " LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$nhanviens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy dữ liệu chức vụ và phòng ban cho form thêm/sửa
$chuc_vus = $conn->query("SELECT ma_chuc_vu, ten_chuc_vu FROM chuc_vu")->fetchAll(PDO::FETCH_ASSOC);
$phong_bans = $conn->query("SELECT ma_phong_ban, ten_phong_ban FROM phong_ban")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhân viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .content {
            flex-grow: 1;
            padding: 5px;
        }
        .main {
            display: flex;
            flex-grow: 1;
        }
        #filterFormContainer {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 600px;
            background: white;
            border-radius: 6px;
            padding: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1001;
        }
        #filterFormContainer.show {
            display: block;
        }
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        #overlay.show {
            display: block;
        }
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 5px;
            margin-bottom: 5px;
        }
        .container {
            padding: 5px;
        }
        h2 {
            margin-bottom: 5px;
        }
        p {
            margin-bottom: 5px;
        }
        table {
            margin-top: 5px;
        }
        .alert {
            margin-bottom: 5px;
        }
        @media (max-width: 576px) {
            #filterFormContainer {
                width: 95%;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
<div class="main">
    <?php include './siderbar.php'; ?>

    <div class="content">
        <?php if (isset($_GET['error'])): ?>
    <?php if ($_GET['error'] == 'trung_ma'): ?>
        <div class="alert alert-danger">Mã nhân viên đã tồn tại!</div>
    <?php elseif ($_GET['error'] == 'thieu_thong_tin'): ?>
        <div class="alert alert-danger">Vui lòng nhập đầy đủ thông tin!</div>
    <?php else: ?>
        <div class="alert alert-danger">Đã xảy ra lỗi khi thêm nhân viên!</div>
    <?php endif; ?>
<?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Thêm nhân viên thành công!</div>
        <?php endif; ?>
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">Cập nhật nhân viên thành công!</div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Xóa nhân viên thành công!</div>
        <?php endif; ?>
        <div class="container mt-4">
            <h2>Danh sách nhân viên</h2>
            <p>Tìm thấy <?php echo $total; ?> nhân viên</p>

            <!-- Nút Lọc và Thêm nhân viên nằm ngang hàng, căn phải -->
            <div class="action-buttons">
                <button class="btn btn-primary" id="showFilterBtn" style="border-radius: 6px;">
                    <i class="bi bi-funnel-fill"></i> Lọc
                </button>
                <button class="btn btn-success" id="showAddNhanVienFormBtn" style="border-radius: 6px;">
                    <i class="bi bi-person-plus"></i> Thêm nhân viên
                </button>
            </div>

            <!-- Overlay để làm mờ nền -->
            <div id="overlay"></div>

            <!-- Form lọc như modal -->
            <div id="filterFormContainer">
                <div class="border rounded p-4 shadow-sm bg-light">
                    <form id="filterForm">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-person-badge"></i> Mã NV</label>
                                <input type="text" name="ma_nv" class="form-control" value="<?= htmlspecialchars($_GET['ma_nv'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-person"></i> Họ tên</label>
                                <input type="text" name="ho_ten" class="form-control" value="<?= htmlspecialchars($_GET['ho_ten'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-briefcase"></i> Chức vụ</label>
                                <select name="chuc_vu" class="form-select">
                                    <option value="">-- Chọn chức vụ --</option>
                                    <?php foreach ($chuc_vus as $cv): 
                                        $selected = ($_GET['chuc_vu'] ?? '') == $cv['ten_chuc_vu'] ? "selected" : "";
                                    ?>
                                        <option value="<?= htmlspecialchars($cv['ten_chuc_vu']) ?>" <?= $selected ?>><?= htmlspecialchars($cv['ten_chuc_vu']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-building"></i> Phòng ban</label>
                                <select name="phong_ban" class="form-select">
                                    <option value="">-- Chọn phòng ban --</option>
                                    <?php foreach ($phong_bans as $pb): 
                                        $selected = ($_GET['phong_ban'] ?? '') == $pb['ten_phong_ban'] ? "selected" : "";
                                    ?>
                                        <option value="<?= htmlspecialchars($pb['ten_phong_ban']) ?>" <?= $selected ?>><?= htmlspecialchars($pb['ten_phong_ban']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2 g-2">
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success w-100" style="border-radius: 6px;">
                                    <i class="bi bi-search"></i> Áp dụng
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-secondary w-100" id="hideFilterBtn" style="border-radius: 6px;">
                                    <i class="bi bi-x-circle"></i> Đóng
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-warning w-100" id="resetFilter" style="border-radius: 6px;">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Form thêm nhân viên (ẩn mặc định) -->
            <form method="POST" action="../controllers/them_nhanvien.php" class="mb-3" id="addNhanVienForm" style="display: none;">
                <div class="row g-1">
                    <div class="col-md-3">
                        <input type="text" name="ma_nhan_vien" class="form-control" placeholder="Mã nhân viên" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="ho_ten" class="form-control" placeholder="Họ tên" required>
                    </div>
                    <div class="col-md-3">
                        <select name="chuc_vu_id" class="form-select" required>
                            <option value="">-- Chọn chức vụ --</option>
                            <?php foreach ($chuc_vus as $cv): ?>
                                <option value="<?= htmlspecialchars($cv['ma_chuc_vu']) ?>"><?= htmlspecialchars($cv['ten_chuc_vu']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="phong_ban_id" class="form-select" required>
                            <option value="">-- Chọn phòng ban --</option>
                            <?php foreach ($phong_bans as $pb): ?>
                                <option value="<?= htmlspecialchars($pb['ma_phong_ban']) ?>"><?= htmlspecialchars($pb['ten_phong_ban']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100" style="border-radius: 6px;"><i class="bi bi-plus-circle"></i> Thêm</button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-secondary w-100" id="hideAddNhanVienFormBtn" style="border-radius: 6px;">Hủy</button>
                    </div>
                </div>
            </form>     

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
                    <?php foreach ($nhanviens as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ma_nhan_vien']) ?></td>
                        <td><?= htmlspecialchars($row['ho_ten']) ?></td>
                        <td><?= htmlspecialchars($row['ten_chuc_vu']) ?></td>
                        <td><?= htmlspecialchars($row['ten_phong_ban']) ?></td>
                        <td>
                            <!-- Nút sửa -->
                            <button 
                                class="btn btn-primary btn-sm"
                                style="background-color: #007bff; border-radius: 6px; border: none;"
                                onclick="editNhanVien('<?= htmlspecialchars($row['ma_nhan_vien']) ?>', '<?= htmlspecialchars($row['ho_ten'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['ten_chuc_vu'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['ten_phong_ban'], ENT_QUOTES) ?>')">
                                <i class="bi bi-pencil"></i> Sửa
                            </button>
                            <!-- Nút xóa -->
                            <form method="POST" action="../controllers/xoa_nhanvien.php" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên này không?');">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($row['ma_nhan_vien']) ?>">
                                <button 
                                    type="submit"
                                    class="btn btn-danger btn-sm"
                                    style="background-color: #dc3545; border-radius: 6px; border: none;">
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
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
                            <a class="page-link" href="<?= htmlspecialchars($link) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterFormContainer = document.getElementById('filterFormContainer');
    const showFilterBtn = document.getElementById('showFilterBtn');
    const hideFilterBtn = document.getElementById('hideFilterBtn');
    const overlay = document.getElementById('overlay');
    const filterForm = document.getElementById('filterForm');

    // Khôi phục dữ liệu form từ sessionStorage
    const savedFormData = sessionStorage.getItem('filterFormData');
    if (savedFormData) {
        const formData = new URLSearchParams(savedFormData);
        for (let [key, value] of formData.entries()) {
            const input = filterForm.querySelector(`[name="${key}"]`);
            if (input) input.value = value;
        }
    }

    // Hiển thị form lọc với overlay
    showFilterBtn.onclick = function () {
        filterFormContainer.classList.add('show');
        overlay.classList.add('show');
    };

    // Ẩn form lọc và overlay
    hideFilterBtn.onclick = function () {
        filterFormContainer.classList.remove('show');
        overlay.classList.remove('show');
    };

    // Xử lý submit form lọc
    filterForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(filterForm);
        sessionStorage.setItem('filterFormData', new URLSearchParams(formData).toString());
        const params = new URLSearchParams(formData).toString();
        window.location.href = '?' + params;
    });

    // Reset form
    document.getElementById('resetFilter').onclick = function () {
        sessionStorage.removeItem('filterFormData');
        filterForm.reset();
        window.location.href = window.location.pathname;
    };

    // Hiển thị/ẩn form thêm nhân viên
    document.getElementById('showAddNhanVienFormBtn').onclick = function () {
        document.getElementById('addNhanVienForm').style.display = 'block';
        this.style.display = 'none';
    };
    document.getElementById('hideAddNhanVienFormBtn').onclick = function () {
        document.getElementById('addNhanVienForm').style.display = 'none';
        document.getElementById('showAddNhanVienFormBtn').style.display = 'inline-block';
    };

    // Đóng form lọc bằng phím Esc
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && filterFormContainer.classList.contains('show')) {
            hideFilterBtn.click();
        }
    });
});

// Hàm hiển thị form sửa nhân viên
function editNhanVien(ma_nhan_vien, ho_ten, ten_chuc_vu, ten_phong_ban) {
    // Xóa form sửa cũ nếu có
    const oldForm = document.getElementById('editNhanVienForm');
    if (oldForm) oldForm.remove();

    // Tạo form sửa mới
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../controllers/sua_nhanvien.php';
    form.className = 'mb-3 p-3 border rounded shadow-sm';
    form.id = 'editNhanVienForm';
    form.style.background = '#f8f9fa';

    // Tạo options chức vụ
    let chucVuOptions = `<option value="">-- Chọn chức vụ --</option>`;
    <?php foreach ($chuc_vus as $cv): ?>
        chucVuOptions += `<option value="<?= htmlspecialchars($cv['ma_chuc_vu']) ?>" ${'<?= htmlspecialchars($cv['ten_chuc_vu']) ?>' === ten_chuc_vu ? 'selected' : ''}><?= htmlspecialchars($cv['ten_chuc_vu']) ?></option>`;
    <?php endforeach; ?>

    // Tạo options phòng ban
    let phongBanOptions = `<option value="">-- Chọn phòng ban --</option>`;
    <?php foreach ($phong_bans as $pb): ?>
        phongBanOptions += `<option value="<?= htmlspecialchars($pb['ma_phong_ban']) ?>" ${'<?= htmlspecialchars($pb['ten_phong_ban']) ?>' === ten_phong_ban ? 'selected' : ''}><?= htmlspecialchars($pb['ten_phong_ban']) ?></option>`;
    <?php endforeach; ?>

    form.innerHTML = `
        <input type="hidden" name="ma_nhan_vien" value="${ma_nhan_vien}">
        <h5 class="mb-2 text-primary"><i class="bi bi-pencil"></i> Sửa nhân viên</h5>
        <div class="row g-1">
            <div class="col-md-3">
                <input type="text" name="ho_ten" class="form-control" value="${ho_ten}" placeholder="Họ tên" required>
            </div>
            <div class="col-md-3">
                <select name="chuc_vu_id" class="form-select" required>
                    ${chucVuOptions}
                </select>
            </div>
            <div class="col-md-3">
                <select name="phong_ban_id" class="form-select" required>
                    ${phongBanOptions}
                </select>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100" style="border-radius: 6px;"><i class="bi bi-save"></i> Lưu</button>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-secondary w-100" style="border-radius: 6px;" onclick="this.closest('form').remove();">Hủy</button>
            </div>
        </div>
    `;
    document.querySelector('.content').prepend(form);
    form.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>
</body>
</html>