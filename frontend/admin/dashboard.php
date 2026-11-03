<?php
declare(strict_types=1);
require __DIR__ . '/_auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Brgy. Bucana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="wrapper">
    <!-- SIDEBAR -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <div class="logo-circle">M</div>
            <div><h6 class="mb-0 fw-bold">Brgy. Bucana</h6><small class="text-muted">Office of the Chairman</small></div>
        </div>
        <ul class="nav flex-column mt-3">
            <!-- NOTICE: The active class is on Dashboard -->
            <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="budget.php"><i class="bi bi-wallet2"></i> Budget</a></li>
            <li class="nav-item"><a class="nav-link" href="residents.php"><i class="bi bi-people"></i> Residents</a></li>
            <li class="nav-item"><a class="nav-link" href="reports.php"><i class="bi bi-file-earmark-text"></i> Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
        </ul>
        <div class="sidebar-footer">
            <div class="user-card">
                <small class="text-muted d-block mb-1">SIGNED IN AS</small>
                <strong class="d-block">Capt. Choi Seungcheol Batumbakal</strong>
                <small class="text-muted">Chairman - Term 2022-2025</small>
            </div>
            <button class="btn btn-outline btn-sm w-100 mt-2" id="admin-signout" type="button">Sign out</button>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div id="content">
        <div class="page-header">
            <div>
                <h3 class="fw-bold mb-1">Chairman's Dashboard</h3>
                <p class="text-muted mb-0">Financial & population overview</p>
            </div>
            <button class="btn btn-primary-green btn-sm px-3">Generate Report</button>
        </div>

        <!-- DASHBOARD SPECIFIC CONTENT -->
        <div class="row g-3 mb-4">
            <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="kpi-title">Total Budget</div><div class="kpi-value">P24.8M</div></div></div></div>
            <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="kpi-title">Disbursed</div><div class="kpi-value">P19.6M</div></div></div></div>
            <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="kpi-title">Total Residents</div><div class="kpi-value">12,480</div></div></div></div>
            <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="kpi-title">Registered Voters</div><div class="kpi-value">8,214</div></div></div></div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header">FY 2021 Budget by Sector</div>
                    <div class="card-body"><div style="height: 250px;"><canvas id="dashboardBarChart"></canvas></div></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header">Allocation by Sector</div>
                    <div class="card-body">
                        <div style="height: 190px;"><canvas id="dashboardPieChart"></canvas></div>
                        <div class="d-flex justify-content-between mt-3 mb-1">
                            <small class="fw-bold" style="color: #1f4d3a;">Male: 5,990</small>
                            <small class="fw-bold" style="color: #c07c3b;">Female: 6,490</small>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar" role="progressbar" style="width: 48%; background-color: #1f4d3a;" aria-valuenow="48" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar" role="progressbar" style="width: 52%; background-color: #c07c3b;" aria-valuenow="52" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/charts.js"></script>
<script src="js/main.js"></script>
</body>
</html>