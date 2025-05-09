<!-- filepath: d:\xampp\htdocs\dacs3\admin\views\dua_top.php -->
<?php
include '../../config/db.php';

// Lấy mã đợt từ URL
$ma_dot = $_GET['ma_dot'] ?? null;

if ($ma_dot) {
    // Truy vấn dữ liệu bảng xếp hạng
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
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ma_dot', $ma_dot);
    $stmt->execute();
    $bang_xep_hang = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($bang_xep_hang)) {
        error_log("Không có dữ liệu cho mã đợt: " . $ma_dot);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng xếp hạng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
        }
    </style>
</head>
<body class="bg-white min-h-screen p-6 sm:p-10">

    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-normal text-black tracking-tight leading-tight">
                See where you are!
            </h1>
        </div>
        <p class="text-gray-400 text-xs mb-6">
            Here is your Leaderboard
        </p>

        <!-- Hiển thị Top 1, 2, 3 -->
        <div class="flex justify-center items-end gap-4 mb-10">
    <!-- Top 2 -->
    <?php if (isset($bang_xep_hang[1])): ?>
        <div class="flex flex-col items-center bg-white shadow-md rounded-xl px-4 py-3 w-28">
            <img class="w-10 h-10 mb-1" src="https://twemoji.maxcdn.com/v/latest/72x72/1f948.png" alt="Silver Medal">
            <span class="text-center text-sm font-semibold"><?= htmlspecialchars($bang_xep_hang[1]['ten_nguoi_duoc_danh_gia']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Top 1 -->
    <?php if (isset($bang_xep_hang[0])): ?>
        <div class="flex flex-col items-center bg-white shadow-xl border-2 border-yellow-400 rounded-xl px-6 py-4 w-32 scale-110">
        <img src="https://twemoji.maxcdn.com/v/latest/72x72/1f947.png" alt="Huy chương vàng" width="30">
        <span class="text-center text-base font-bold"><?= htmlspecialchars($bang_xep_hang[0]['ten_nguoi_duoc_danh_gia']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Top 3 -->
    <?php if (isset($bang_xep_hang[2])): ?>
        <div class="flex flex-col items-center bg-white shadow-md rounded-xl px-4 py-3 w-28">
            <img class="w-10 h-10 mb-1" src="https://twemoji.maxcdn.com/v/latest/72x72/1f949.png" alt="Bronze Medal">
            <span class="text-center text-sm font-semibold"><?= htmlspecialchars($bang_xep_hang[2]['ten_nguoi_duoc_danh_gia']) ?></span>
        </div>
    <?php endif; ?>
</div>


        <!-- Hiển thị từ Top 4 trở đi -->
        <div class="grid grid-cols-3 gap-4 text-black font-normal text-base mb-2">
            <div>Username</div>
            <div>Rank</div>
            <div>Score</div>
        </div>
        <div class="space-y-2">
            <?php if (!empty($bang_xep_hang)): ?>
                <?php for ($i = 3; $i < count($bang_xep_hang); $i++): ?>
                    <div class="bg-gray-200 rounded-full px-4 py-2 grid grid-cols-3 text-black text-sm font-normal">
                        <div><?= htmlspecialchars($bang_xep_hang[$i]['ten_nguoi_duoc_danh_gia']) ?></div>
                        <div class="text-center"><?= $i + 1 ?></div>
                        <div class="text-right"><?= number_format($bang_xep_hang[$i]['diem_trung_binh_tb'], 2) ?></div>
                    </div>
                <?php endfor; ?>
            <?php else: ?>
                <p class="text-center text-gray-500">Không có dữ liệu đánh giá cho đợt này.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>