<?php
include '../../config/db.php';

// Xử lý thêm tài khoản
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $ma_nhan_vien = $_POST['ma_nhan_vien'];
    $ten_dang_nhap = $_POST['ten_dang_nhap'];
    $mat_khau = password_hash($_POST['mat_khau'], PASSWORD_DEFAULT); // Mã hóa mật khẩu
    $quyen = $_POST['quyen'];

    // Kiểm tra xem mã nhân viên có tồn tại trong bảng nhan_vien không
    $sql_check = "SELECT COUNT(*) FROM nhan_vien WHERE ma_nhan_vien = :ma_nhan_vien";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([':ma_nhan_vien' => $ma_nhan_vien]);
    $exists = $stmt_check->fetchColumn();

    if ($exists == 0) {
        echo "<script>alert('Mã nhân viên không tồn tại. Vui lòng kiểm tra lại!');</script>";
    } else {
        $sql = "INSERT INTO tai_khoan (ma_nhan_vien, ten_dang_nhap, mat_khau, quyen) VALUES (:ma_nhan_vien, :ten_dang_nhap, :mat_khau, :quyen)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':ma_nhan_vien' => $ma_nhan_vien,
            ':ten_dang_nhap' => $ten_dang_nhap,
            ':mat_khau' => $mat_khau,
            ':quyen' => $quyen
        ]);
        header("Location: /admin/views/dashboard.php");
           }
}

// Xử lý sửa tài khoản
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $ma_tai_khoan = $_POST['ma_tai_khoan'];
    $ma_nhan_vien = $_POST['ma_nhan_vien'];
    $ten_dang_nhap = $_POST['ten_dang_nhap'];
    $quyen = $_POST['quyen'];

    // Nếu có mật khẩu mới, mã hóa và cập nhật
    if (!empty($_POST['mat_khau'])) {
        $mat_khau = password_hash($_POST['mat_khau'], PASSWORD_DEFAULT); // Mã hóa mật khẩu mới
        $sql = "UPDATE tai_khoan SET ma_nhan_vien = :ma_nhan_vien, ten_dang_nhap = :ten_dang_nhap, mat_khau = :mat_khau, quyen = :quyen WHERE ma_tai_khoan = :ma_tai_khoan";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':ma_nhan_vien' => $ma_nhan_vien,
            ':ten_dang_nhap' => $ten_dang_nhap,
            ':mat_khau' => $mat_khau,
            ':quyen' => $quyen,
            ':ma_tai_khoan' => $ma_tai_khoan
        ]);
    } else {
        $sql = "UPDATE tai_khoan SET ma_nhan_vien = :ma_nhan_vien, ten_dang_nhap = :ten_dang_nhap, quyen = :quyen WHERE ma_tai_khoan = :ma_tai_khoan";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':ma_nhan_vien' => $ma_nhan_vien,
            ':ten_dang_nhap' => $ten_dang_nhap,
            ':quyen' => $quyen,
            ':ma_tai_khoan' => $ma_tai_khoan
        ]);
    }
    header("Location: dashboard.php");
    exit();
}

// Xử lý xóa tài khoản
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $ma_tai_khoan = $_POST['ma_tai_khoan'];
    $sql = "DELETE FROM tai_khoan WHERE ma_tai_khoan = :ma_tai_khoan";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':ma_tai_khoan' => $ma_tai_khoan]);
    header("Location: dashboard.php");
    exit();
}

// Lấy danh sách tài khoản
$sql = "SELECT ma_tai_khoan, ma_nhan_vien, ten_dang_nhap, quyen FROM tai_khoan"; // Không lấy cột mật khẩu
$stmt = $conn->query($sql);
$ds_tai_khoan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tài khoản</title>
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
        <h2>Quản lý Tài khoản Nhân viên</h2>

        <!-- Form thêm tài khoản -->
        <form method="POST" action="" class="mb-4">
            <input type="hidden" name="action" value="add">
            <div class="row">
                <div class="col-md-2">
                    <input type="text" name="ma_nhan_vien" class="form-control" placeholder="Mã nhân viên" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="ten_dang_nhap" class="form-control" placeholder="Tên đăng nhập" required>
                </div>
                <div class="col-md-2">
                    <input type="password" name="mat_khau" class="form-control" placeholder="Mật khẩu" required>
                </div>
                <div class="col-md-2">
                    <select name="quyen" class="form-select" required>
                        <option value="Admin">Admin</option>
                        <option value="User">User</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">Thêm tài khoản</button>
                </div>
            </div>
        </form>

        <!-- Danh sách tài khoản -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã tài khoản</th>
                    <th>Mã nhân viên</th>
                    <th>Tên đăng nhập</th>
                    <th>Quyền</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ds_tai_khoan as $tk): ?>
                    <tr>
                        <td><?= $tk['ma_tai_khoan'] ?></td>
                        <td><?= htmlspecialchars($tk['ma_nhan_vien']) ?></td>
                        <td><?= htmlspecialchars($tk['ten_dang_nhap']) ?></td>
                        <td><?= htmlspecialchars($tk['quyen']) ?></td>
                        <td>
                            <!-- Nút sửa -->
                            <button class="btn btn-warning btn-sm" onclick="editAccount(<?= htmlspecialchars(json_encode($tk)) ?>)">Sửa</button>

                            <!-- Nút xóa -->
                            <form method="POST" action="" style="display: inline-block;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="ma_tai_khoan" value="<?= $tk['ma_tai_khoan'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>



<script>
    function editAccount(account) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        form.innerHTML = `
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="ma_tai_khoan" value="${account.ma_tai_khoan}">
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" name="ma_nhan_vien" class="form-control" value="${account.ma_nhan_vien}" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="ten_dang_nhap" class="form-control" value="${account.ten_dang_nhap}" required>
                </div>
                <div class="col-md-3">
                    <input type="password" name="mat_khau" class="form-control" placeholder="Mật khẩu mới (nếu cần)">
                </div>
                <div class="col-md-3">
                    <select name="quyen" class="form-select" required>
                        <option value="Admin" ${account.quyen === 'Admin' ? 'selected' : ''}>Admin</option>
                        <option value="User" ${account.quyen === 'User' ? 'selected' : ''}>User</option>
                    </select>
                </div>
                <div class="col-md-3 mt-2">
                    <button type="submit" class="btn btn-primary w-100">Lưu</button>
                </div>
            </div>
        `;

        document.querySelector('.content').prepend(form);
    }
</script>
</body>
</html>