<style>
    .sidebar {
        width: 250px;
        height: 100vh;
        background: #343a40;
        padding: 15px;
        color: white;
    }
    .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 10px;
        border-radius: 5px;
    }
    .sidebar a:hover {
        background: #495057;
    }
    .sidebar ul li ul {
    display: none;
    list-style: none;
    padding-left: 15px;
}

.sidebar ul li.active ul {
    display: block;
}

.sidebar ul li ul li a {
    
    font-size: 14px;
}

.sidebar ul li ul li a:hover {
    background: #555c63;
}
.sidebar ul,
.sidebar ul li {
    list-style: none;
    padding: 0;
    margin: 0;
}


    
</style>

<!-- Sidebar -->
<div class="sidebar">
<ul>
        <li><a href="./dashboard.php">Dashboard</a></li>
        <li><a href="./nhanvien.php">Quản lý Nhân viên</a></li>
        <li><a href="./admin_danh_gia.php">Đánh Giá</a></li>
        <li><a href="./quan_li_danh_gia.php">Quản lý Đánh giá</a></li>

        <!-- Dropdown mục Quản lý mã đợt -->
        <li onclick="toggleDropdown(this)">
        <a href="javascript:void(0)">Quản lý mã đợt </a>
            <ul>
                <li><a href="./quan_li_dot.php">Tạo đợt mới</a></li>
                <li><a href="./bang_xep_hang.php">Bảng xếp hạng</a></li>
            </ul>
        </li>
    </ul>
    
</div>
<script>
    function toggleDropdown(el) {
        el.classList.toggle("active");
    }
</script>
