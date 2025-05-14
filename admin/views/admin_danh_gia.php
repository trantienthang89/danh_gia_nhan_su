<?php

include '../../config/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Hàm icon chức vụ
function chucVuIcon($ten_chuc_vu) {
    if (stripos($ten_chuc_vu, 'trưởng phòng') !== false) return '🧑‍💼';
    if (stripos($ten_chuc_vu, 'giám đốc') !== false) return '👔';
    if (stripos($ten_chuc_vu, 'nhân viên') !== false) return '👨‍💻';
    return '👤';
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

    header('Location: admin_danh_gia.php?success=1');
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f5f7fa;
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
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            margin: 28px auto;
            max-width: 1200px;
        }
        .nhan-vien-container {
            display: flex;
            gap: 40px;
            justify-content: space-between;
            width: 100%;
            margin-top: 18px;
        }
        .cot {
            flex: 1;
        }
        .nhan-vien-box {
            border: 1px solid #e3e8ee;
            border-radius: 12px;
            padding: 20px 18px;
            background-color: #f8fafc;
            width: 100%;
            max-width: 340px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 22px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .nhan-vien-box:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
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
        .btn-danh-gia {
            background: #22c55e;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 7px 18px;
            font-size: 15px;
            margin-top: 12px;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 6px rgba(34,197,94,0.08);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-danh-gia:hover {
            background: #16a34a;
        }
        .btn-sua-danh-gia {
            background: #f59e42;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 7px 18px;
            font-size: 15px;
            margin-top: 12px;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 6px rgba(245,158,66,0.08);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-sua-danh-gia:hover {
            background: #ea580c;
        }
        #formContainer {
            display: none;
            margin-top: 28px;
            border: 1px solid #e3e8ee;
            padding: 24px 20px;
            background-color: #f8fafc;
            max-width: 420px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        label {
            display: block;
            margin: 12px 0 6px 0;
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
               <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="max-width:500px;margin:0 auto 16px;">
            <i class="bi bi-check-circle-fill"></i> Gửi đánh giá thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    <?php endif; ?>
            <p><strong>Đợt đánh giá hiện tại:</strong> <?= htmlspecialchars($ten_dot) ?> (Mã: <?= htmlspecialchars($ma_dot) ?>)</p>

            <h2>Danh sách nhân viên cần đánh giá</h2>

            <div class="nhan-vien-container">
                <div class="cot">
                    <h3>Chưa đánh giá</h3>
                    <?php foreach ($chua_danh_gia as $row): ?>
                        <div class="nhan-vien-box">
                            <h4><?= htmlspecialchars($row['ho_ten']) ?></h4>
                            <p><strong>Mã NV:</strong> <?= htmlspecialchars($row['ma_nhan_vien']) ?></p>
                            <p>
                                <strong>Chức vụ:</strong>
                                <span style="font-size:18px"><?= chucVuIcon($row['ten_chuc_vu']) ?></span>
                                <?= htmlspecialchars($row['ten_chuc_vu']) ?>
                            </p>
                            <p><strong>Phòng ban:</strong> <?= htmlspecialchars($row['ten_phong_ban']) ?></p>
                            <button class="btn-danh-gia" onclick="hienForm(<?= $row['ma_nhan_vien'] ?>, '<?= htmlspecialchars($row['ho_ten'], ENT_QUOTES) ?>')">
                                <i class="bi bi-pencil-square"></i> Đánh giá
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cot">
                    <h3>Đã đánh giá</h3>
                    <?php foreach ($da_danh_gia as $row): ?>
                        <div class="nhan-vien-box">
                            <h4><?= htmlspecialchars($row['ho_ten']) ?></h4>
                            <p><strong>Mã NV:</strong> <?= htmlspecialchars($row['ma_nhan_vien']) ?></p>
                            <p>
                                <strong>Chức vụ:</strong>
                                <span style="font-size:18px"><?= chucVuIcon($row['ten_chuc_vu']) ?></span>
                                <?= htmlspecialchars($row['ten_chuc_vu']) ?>
                            </p>
                            <p><strong>Phòng ban:</strong> <?= htmlspecialchars($row['ten_phong_ban']) ?></p>
                            <button class="btn-sua-danh-gia" onclick="hienForm(<?= $row['ma_nhan_vien'] ?>, '<?= htmlspecialchars($row['ho_ten'], ENT_QUOTES) ?>', true)">
                                <i class="bi bi-pencil"></i> Sửa đánh giá
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="modal fade" id="formDanhGiaModal" tabindex="-1" aria-labelledby="formDanhGiaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius: 12px;">
      <div class="modal-header">
        <h5 class="modal-title" id="formDanhGiaLabel">Đánh giá cho <span id="tenNhanVien"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <form method="post" action="">
        <div class="modal-body">
          <input type="hidden" name="nguoi_danh_gia" value="<?= $user_id ?>">
          <input type="hidden" id="nguoiDuocDanhGia" name="nguoi_duoc_danh_gia">

          <label class="mt-2">Điểm tuân thủ:
            <input type="number" name="diem_tuan_thu" min="1" max="10" step="0.5" class="form-control" required>
          </label>
          <label class="mt-2">Điểm hợp tác:
            <input type="number" name="diem_hop_tac" min="1" max="10" step="0.5" class="form-control" required>
          </label>
          <label class="mt-2">Điểm tận tuỵ:
            <input type="number" name="diem_tan_tuy" min="1" max="10" step="0.5" class="form-control" required>
          </label>
          <label class="mt-2">Nhận xét:
            <textarea name="nhan_xet" rows="3" class="form-control"></textarea>
          </label>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Gửi đánh giá</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
        </div>
      </form>
    </div>
  </div>
</div>
        </main>
    </div>

    <script>
function hienForm(maNV, tenNV, isEdit = false) {
    // Hiện overlay loading
    document.getElementById('loadingOverlay').style.display = 'flex';

    document.getElementById('nguoiDuocDanhGia').value = maNV;
    document.getElementById('tenNhanVien').textContent = tenNV;

    // Reset trước
    document.querySelector('[name="diem_tuan_thu"]').value = '';
    document.querySelector('[name="diem_hop_tac"]').value = '';
    document.querySelector('[name="diem_tan_tuy"]').value = '';
    document.querySelector('[name="nhan_xet"]').value = '';

    function showModalAndHideLoading() {
        var modal = new bootstrap.Modal(document.getElementById('formDanhGiaModal'));
        modal.show();
        document.getElementById('loadingOverlay').style.display = 'none';
    }

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
            showModalAndHideLoading();
        };
        xhr.onerror = showModalAndHideLoading;
        xhr.send();
    } else {
        setTimeout(showModalAndHideLoading, 350);
    }
}

function anForm() {
    document.getElementById('formContainer').style.display = 'none';
}
</script>
    <!-- Loading overlay -->
<div id="loadingOverlay" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:3000;background:rgba(0,0,0,0.2);align-items:center;justify-content:center;">
    <div style="background:#fff;padding:24px 36px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.15);display:flex;flex-direction:column;align-items:center;">
        <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div style="margin-top:12px;font-size:16px;">Đang tải dữ liệu...</div>
    </div>
</div>
</body>
</html>