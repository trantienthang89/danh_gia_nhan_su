<?php
include '../../config/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Kiểm tra đăng nhập
if (!isset($_SESSION['user']['ma_nhan_vien'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['ma_nhan_vien'];
// Lấy đợt đánh giá đang diễn ra
$sql_dot = "SELECT ma_dot, ten_dot FROM dot_danh_gia WHERE trang_thai = 'Dang Dien Ra' LIMIT 1";
$dot_stmt = $conn->prepare($sql_dot);
$dot_stmt->execute();
$dot_info = $dot_stmt->fetch(PDO::FETCH_ASSOC);
$ma_dot = $dot_info['ma_dot'] ?? 'Không có';
$ten_dot = $dot_info['ten_dot'] ?? 'Không xác định';



// Nếu được gọi bởi AJAX để lấy dữ liệu đánh giá cũ
if (
    isset($_GET['edit_id']) &&
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) {
    $nguoi_duoc_danh_gia = $_GET['edit_id'];

    $sql_danh_gia_cu = "
        SELECT diem_tuan_thu, diem_hop_tac, diem_tan_tuy, nhan_xet 
        FROM danh_gia
        WHERE nguoi_danh_gia = :user_id AND nguoi_duoc_danh_gia = :nguoi_duoc_danh_gia
    ";
    $stmt = $conn->prepare($sql_danh_gia_cu);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':nguoi_duoc_danh_gia', $nguoi_duoc_danh_gia);
    $stmt->execute();
    $danh_gia_cu = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($danh_gia_cu);
    exit();
}

// Xử lý đánh giá (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nguoi_danh_gia = $_POST['nguoi_danh_gia'];
    $nguoi_duoc_danh_gia = $_POST['nguoi_duoc_danh_gia'];
    $diem_tuan_thu = $_POST['diem_tuan_thu'];
    $diem_hop_tac = $_POST['diem_hop_tac'];
    $diem_tan_tuy = $_POST['diem_tan_tuy'];
    $nhan_xet = $_POST['nhan_xet'];

    $diem_trung_binh = round(($diem_tuan_thu + $diem_hop_tac + $diem_tan_tuy) / 3, 2);

    $check = $conn->prepare("SELECT COUNT(*) FROM danh_gia WHERE nguoi_danh_gia = :ndg AND nguoi_duoc_danh_gia = :nddg AND ma_dot = :ma_dot");
    $check->execute([
        ':ndg' => $nguoi_danh_gia,
        ':nddg' => $nguoi_duoc_danh_gia,
        ':ma_dot' => $ma_dot
    ]);

    if ($check->fetchColumn() > 0) {
        $sql = "UPDATE danh_gia 
                SET diem_tuan_thu = :tuan_thu, diem_hop_tac = :hop_tac, diem_tan_tuy = :tan_tuy, 
                    diem_trung_binh = :tb, nhan_xet = :nhan_xet
                WHERE nguoi_danh_gia = :ndg AND nguoi_duoc_danh_gia = :nddg AND ma_dot = :ma_dot";
    } else {
        $sql = "INSERT INTO danh_gia 
                (nguoi_danh_gia, nguoi_duoc_danh_gia, ma_dot, diem_tuan_thu, diem_hop_tac, diem_tan_tuy, diem_trung_binh, nhan_xet)
                VALUES 
                (:ndg, :nddg, :ma_dot, :tuan_thu, :hop_tac, :tan_tuy, :tb, :nhan_xet)";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':ndg' => $nguoi_danh_gia,
        ':nddg' => $nguoi_duoc_danh_gia,
        ':ma_dot' => $ma_dot,
        ':tuan_thu' => $diem_tuan_thu,
        ':hop_tac' => $diem_hop_tac,
        ':tan_tuy' => $diem_tan_tuy,
        ':tb' => $diem_trung_binh,
        ':nhan_xet' => $nhan_xet
    ]);

    header('Location: admin_danh_gia.php');
    exit();
}

// Truy vấn danh sách nhân viên trừ bản thân


$sql = "
    SELECT nv.ma_nhan_vien, nv.ho_ten, cv.ten_chuc_vu, pb.ten_phong_ban,
           (SELECT COUNT(*) 
            FROM danh_gia dg 
            WHERE dg.nguoi_duoc_danh_gia = nv.ma_nhan_vien 
              AND dg.nguoi_danh_gia = :user_id 
              AND dg.ma_dot = :ma_dot) AS da_danh_gia
    FROM nhan_vien nv
    JOIN chuc_vu cv ON nv.chuc_vu_id = cv.ma_chuc_vu
    JOIN phong_ban pb ON nv.phong_ban_id = pb.ma_phong_ban
    WHERE nv.ma_nhan_vien != :user_id
";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':ma_dot', $ma_dot);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Danh sách đã đánh giá
$sql_danh_gia = "SELECT nguoi_duoc_danh_gia FROM danh_gia WHERE nguoi_danh_gia = :user_id";
$stmt = $conn->prepare($sql_danh_gia);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$da_danh_gia_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

$da_danh_gia = [];
$chua_danh_gia = [];

foreach ($result as $nv) {
    if ($nv['da_danh_gia'] > 0) {
        $da_danh_gia[] = $nv;
    } else {
        $chua_danh_gia[] = $nv;
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
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f7faf9;
        }
        .container {
            display: flex;
            flex: 1;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .main-content {
            flex: 1;
            background-color: #ffffff;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin: 20px auto;
            max-width: 1200px;
        }
        .nhan-vien-container {
            display: flex;
            gap: 30px;
            justify-content: space-between;
            width: 100%;
        }
        .cot {
            flex: 1;
        }
        .nhan-vien-box {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            background-color: #f1f1f1;
            width: 100%;
            max-width: 300px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }
        .nhan-vien-box:hover {
            transform: translateY(-5px);
        }
        .nhan-vien-box h4 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #333;
        }
        .nhan-vien-box p {
            margin: 5px 0;
            font-size: 14px;
        }
        .nhan-vien-box button {
            margin-top: 10px;
            padding: 6px 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .nhan-vien-box button:hover {
            background-color: #0056b3;
        }
        #formContainer {
            display: none;
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 20px;
            background-color: #f9f9f9;
            max-width: 400px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin: 10px 0;
        }
        h2, h3 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include './siderbar.php'; ?>

        <!-- Main content -->
        <main class="main-content">
            <p><strong>Đợt đánh giá hiện tại:</strong> <?= htmlspecialchars($ten_dot) ?> (Mã: <?= htmlspecialchars($ma_dot) ?>)</p>

            <h2>Danh sách nhân viên cần đánh giá</h2>

            <div class="nhan-vien-container">
                <div class="cot">
                    <h3>Chưa đánh giá</h3>
                    <?php foreach ($chua_danh_gia as $row): ?>
                        <div class="nhan-vien-box">
                            <h4><?= htmlspecialchars($row['ho_ten']) ?></h4>
                            <p><strong>Mã NV:</strong> <?= htmlspecialchars($row['ma_nhan_vien']) ?></p>
                            <p><strong>Chức vụ:</strong> <?= htmlspecialchars($row['ten_chuc_vu']) ?></p>
                            <p><strong>Phòng ban:</strong> <?= htmlspecialchars($row['ten_phong_ban']) ?></p>
                            <button onclick="hienForm(<?= $row['ma_nhan_vien'] ?>, '<?= htmlspecialchars($row['ho_ten'], ENT_QUOTES) ?>')">Đánh giá</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cot">
                    <h3>Đã đánh giá</h3>
                    <?php foreach ($da_danh_gia as $row): ?>
                        <div class="nhan-vien-box">
                            <h4><?= htmlspecialchars($row['ho_ten']) ?></h4>
                            <p><strong>Mã NV:</strong> <?= htmlspecialchars($row['ma_nhan_vien']) ?></p>
                            <p><strong>Chức vụ:</strong> <?= htmlspecialchars($row['ten_chuc_vu']) ?></p>
                            <p><strong>Phòng ban:</strong> <?= htmlspecialchars($row['ten_phong_ban']) ?></p>
                            <button onclick="hienForm(<?= $row['ma_nhan_vien'] ?>, '<?= htmlspecialchars($row['ho_ten'], ENT_QUOTES) ?>', true)">Sửa đánh giá</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="formContainer">
                <h3>Đánh giá cho <span id="tenNhanVien"></span></h3>
                <form method="post" action="">
                    <input type="hidden" name="nguoi_danh_gia" value="<?= $user_id ?>">
                    <input type="hidden" id="nguoiDuocDanhGia" name="nguoi_duoc_danh_gia">

                    <label>Điểm tuân thủ:
                        <input type="number" name="diem_tuan_thu" min="1" max="10" step="0.5" required>
                    </label>

                    <label>Điểm hợp tác:
                        <input type="number" name="diem_hop_tac" min="1" max="10" step="0.5" required>
                    </label>

                    <label>Điểm tận tuỵ:
                        <input type="number" name="diem_tan_tuy" min="1" max="10" step="0.5" required>
                    </label>

                    <label>Nhận xét:
                        <textarea name="nhan_xet" rows="3" cols="40"></textarea>
                    </label>

                    <button type="submit">Gửi đánh giá</button>
                    <button type="button" onclick="anForm()">Huỷ</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        function hienForm(maNV, tenNV, isEdit = false) {
            document.getElementById('nguoiDuocDanhGia').value = maNV;
            document.getElementById('tenNhanVien').textContent = tenNV;
            document.getElementById('formContainer').style.display = 'block';

            // Reset trước
            document.querySelector('[name="diem_tuan_thu"]').value = '';
            document.querySelector('[name="diem_hop_tac"]').value = '';
            document.querySelector('[name="diem_tan_tuy"]').value = '';
            document.querySelector('[name="nhan_xet"]').value = '';

            if (isEdit) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'admin_danh_gia.php?edit_id=' + maNV, true);
                xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        var data = JSON.parse(xhr.responseText);
                        document.querySelector('[name="diem_tuan_thu"]').value = data.diem_tuan_thu;
                        document.querySelector('[name="diem_hop_tac"]').value = data.diem_hop_tac;
                        document.querySelector('[name="diem_tan_tuy"]').value = data.diem_tan_tuy;
                        document.querySelector('[name="nhan_xet"]').value = data.nhan_xet;
                    }
                };
                xhr.send();
            }

            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }

        function anForm() {
            document.getElementById('formContainer').style.display = 'none';
        }
    </script>
</body>
</html>


