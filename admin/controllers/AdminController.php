<?php
require_once 'models/DashboardModel.php';

class AdminController {
    private $dashboardModel;

    public function __construct($db) {
        $this->dashboardModel = new DashboardModel($db);
    }

    public function showDashboard() {
        $totalEmployees = $this->dashboardModel->getTotalEmployees();
        $totalDepartments = $this->dashboardModel->getTotalDepartments();
        $totalGroups = $this->dashboardModel->getTotalGroups();
        $totalEvaluations = $this->dashboardModel->getTotalEvaluations();
        $averageScore = $this->dashboardModel->getAverageScore();
        $topEmployees = $this->dashboardModel->getTopEmployees();

        require 'views/admin/dashboard.php';
    }
}
?>
