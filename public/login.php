<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
</head>
<body>
    <h2>Đăng nhập hệ thống</h2>

    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <form method="POST" action="/app/controller/AuthController.php">
        <label>Mã nhân viên:</label>
        <input type="text" name="ma_nhan_vien" required><br><br>

        <label>Tên đăng nhập:</label>
        <input type="text" name="ten_dang_nhap" required><br><br>

        <label>Mật khẩu:</label>
        <input type="password" name="mat_khau" required><br><br>

        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>
