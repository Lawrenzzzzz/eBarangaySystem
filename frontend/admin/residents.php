<?php
declare(strict_types=1);
require __DIR__ . '/_auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Residents - Brgy. Bucana</title>
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
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="budget.php"><i class="bi bi-wallet2"></i> Budget</a></li>
            <!-- ACTIVE CLASS MOVED HERE -->
            <li class="nav-item"><a class="nav-link active" href="residents.php"><i class="bi bi-people"></i> Residents</a></li>
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
                <h3 class="fw-bold mb-1">Residents</h3>
                <p class="text-muted mb-0">Population & demographic statistics · 7 puroks, 8 sitios</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <select class="form-select form-select-sm" style="width: 100px;">
                    <option>2020</option>
                    <option selected>2021</option>
                    <option>2022</option>
                </select>
            </div>
        </div>

        <!-- RESIDENTS SPECIFIC CONTENT -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Total Residents</div>
                    <div class="kpi-value">12,480</div>
                    <div class="kpi-sub">+4.5% vs 2020</div>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Households</div>
                    <div class="kpi-value">3,142</div>
                    <div class="kpi-sub">avg 4.0 members</div>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Registered Voters</div>
                    <div class="kpi-value">8,214</div>
                    <div class="kpi-sub">65.8% of residents</div>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Puroks / Sitios</div>
                    <div class="kpi-value">7 / 8</div>
                    <div class="kpi-sub">Administrative zones</div>
                </div></div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header">Population Trend</div>
                    <div class="card-body">
                        <div style="height: 250px;"><canvas id="residentsLineChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header">Gender Split</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-primary fw-bold">48.0% Male</small>
                            <small class="text-danger fw-bold">52.0% Female</small>
                        </div>
                        <div class="progress mb-4" style="height: 10px;">
                            <div class="progress-bar bg-primary" style="width: 48%"></div>
                            <div class="progress-bar bg-danger" style="width: 52%"></div>
                        </div>
                        <div class="d-flex justify-content-between mb-2"><span>0-17 yrs</span><strong>2,746</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>18-34 yrs</span><strong>4,243</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>35-59 yrs</span><strong>3,869</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>60+ yrs</span><strong>1,622</strong></div>
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