<?php
class DashboardModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getTotalEmployees() {
        $sql = "SELECT COUNT(*) AS total FROM nhan_vien";
        return $this->db->query($sql)->fetch_assoc()['total'];
    }

    public function getTotalDepartments() {
        $sql = "SELECT COUNT(*) AS total FROM phong_ban";
        return $this->db->query($sql)->fetch_assoc()['total'];
    }

    public function getTotalGroups() {
        $sql = "SELECT COUNT(*) AS total FROM nhom";
        return $this->db->query($sql)->fetch_assoc()['total'];
    }

    public function getTotalEvaluations() {
        $sql = "SELECT COUNT(*) AS total FROM danh_gia";
        return $this->db->query($sql)->fetch_assoc()['total'];
    }

    public function getAverageScore() {
        $sql = "SELECT AVG((diem_tuan_thu + diem_hop_tac + diem_tan_tuy) / 3) AS avg_score FROM danh_gia";
        return round($this->db->query($sql)->fetch_assoc()['avg_score'], 2);
    }

    public function getTopEmployees($limit = 5) {
        $sql = "SELECT nv.ho_ten, AVG((dg.diem_tuan_thu + dg.diem_hop_tac + dg.diem_tan_tuy) / 3) AS avg_score
                FROM danh_gia dg
                JOIN nhan_vien nv ON dg.nguoi_duoc_danh_gia = nv.ma_nhan_vien
                GROUP BY dg.nguoi_duoc_danh_gia
                ORDER BY avg_score DESC
                LIMIT $limit";
        return $this->db->query($sql);
    }
}
?>
