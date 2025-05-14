<!-- filepath: d:\xampp\htdocs\dacs3\admin\views\siderbar.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<aside class="sidebar" style="width: 240px; background-color: #f8f9fa; border-right: 1px solid #e6e9eb; padding: 24px; border-radius: 16px 0 0 16px; font-family: Arial, sans-serif;">
    <div>
        <h1 style="color: #2a9d8f; font-weight: bold; font-size: 1.25rem; margin-bottom: 32px; text-align: center; cursor: pointer; user-select: none;">
            Welcome to
        </h1>
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 32px;">
            <img alt="User profile" style="border-radius: 50%; width: 50px; height: 50px;" src="https://storage.googleapis.com/a1aa/image/2c2c1fca-c940-434b-3f64-b4dc1ea0d0dc.jpg" />
            <div style="font-size: 0.875rem; color: #495057;">
                <p style="font-weight: bold; color: #212529; margin: 0;">
                    <?php
                    // Hiển thị tên đăng nhập từ session
                    if (isset($_SESSION['user']['ho_ten'])) {
        echo htmlspecialchars($_SESSION['user']['ho_ten']);
                    } else {
                        echo "Khách";
                    }
                    ?>
                </p>
                <p style="margin: 0; font-size: 0.75rem; color: #6c757d;">
                    <?php
                    // Hiển thị quyền của người dùng từ session
                    if (isset($_SESSION['user']['quyen'])) {
                        echo htmlspecialchars($_SESSION['user']['quyen']);
                    } else {
                        echo "Không xác định";
                    }
                    ?>
                </p>
            </div>
        </div>
        <nav style="font-size: 0.875rem; color: #495057; line-height: 1.5;">
            <div>
                <p style="font-weight: bold; margin-bottom: 12px; color: #212529;">Quản lý hệ thống</p>
                <ul style="margin-left: 0; list-style: none; padding: 0;">
    <li style="margin-bottom: 8px;">
        <a style="display: flex; align-items: center; gap: 8px; color: #495057; font-size: 0.875rem; text-decoration: none; padding: 8px 12px; border-radius: 5px; transition: background-color 0.3s;" href="./dashboard.php">
            <span style="font-size: 1rem;">🏠</span>
            <span>Dashboard</span>
        </a>
    </li>
    <li style="margin-bottom: 8px;">
        <a style="display: flex; align-items: center; gap: 8px; color: #495057; font-size: 0.875rem; text-decoration: none; padding: 8px 12px; border-radius: 5px; transition: background-color 0.3s;" href="./nhanvien.php">
            <span style="font-size: 1rem;">👨‍💼</span>
            <span>Quản lý Nhân viên</span>
        </a>
    </li>
    <li style="margin-bottom: 8px;">
        <a style="display: flex; align-items: center; gap: 8px; color: #495057; font-size: 0.875rem; text-decoration: none; padding: 8px 12px; border-radius: 5px; transition: background-color 0.3s;" href="./admin_danh_gia.php">
            <span style="font-size: 1rem;">📝</span>
            <span>Đánh Giá</span>
        </a>
    </li>
    <li style="margin-bottom: 8px;">
        <a style="display: flex; align-items: center; gap: 8px; color: #495057; font-size: 0.875rem; text-decoration: none; padding: 8px 12px; border-radius: 5px; transition: background-color 0.3s;" href="./quan_li_danh_gia.php">
            <span style="font-size: 1rem;">📋</span>
            <span>Quản lý Đánh giá</span>
        </a>
    </li>
</ul>
            </div>
            <div>
                <p style="font-weight: bold; margin-bottom: 12px; color: #212529;">Quản lý mã đợt</p>
               <ul style="margin-left: 0; list-style: none; padding: 0;">
    <li style="margin-bottom: 8px;">
        <a style="display: flex; align-items: center; gap: 8px; color: #495057; font-size: 0.875rem; text-decoration: none; padding: 8px 12px; border-radius: 5px; transition: background-color 0.3s;" href="./quan_li_dot.php">
            <span style="font-size: 1rem;">🕒</span>
            <span>Tạo đợt mới</span>
        </a>
    </li>
    <li style="margin-bottom: 8px;">
        <a style="display: flex; align-items: center; gap: 8px; color: #495057; font-size: 0.875rem; text-decoration: none; padding: 8px 12px; border-radius: 5px; transition: background-color 0.3s;" href="./bang_xep_hang.php">
            <span style="font-size: 1rem;">🏆</span>
            <span>Bảng xếp hạng</span>
        </a>
    </li>
</ul>
            </div>
        </nav>
        <!-- Nút Đăng xuất -->
        <div style="margin-top: 20px; text-align: center;">
            <a href="/app/view/login.php" class="logout-btn" style="display: inline-block; padding: 10px 20px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px; font-size: 0.875rem; font-weight: bold; transition: background-color 0.3s;">
                Đăng xuất
            </a>
        </div>
    </div>
</aside>