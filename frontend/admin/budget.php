<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/admin/guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget - Brgy. Bucana</title>
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
            <!-- ACTIVE CLASS MOVED HERE -->
            <li class="nav-item"><a class="nav-link active" href="budget.php"><i class="bi bi-wallet2"></i> Budget</a></li>
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
                <h3 class="fw-bold mb-1">Budget</h3>
                <p class="text-muted mb-0">Sector allocations and disbursements - FY 2021</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <select class="form-select form-select-sm" style="width: 100px;">
                    <option>2020</option>
                    <option selected>2021</option>
                    <option>2022</option>
                </select>
            </div>
        </div>

        <!-- BUDGET SPECIFIC CONTENT -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Total Budget</div>
                    <div class="kpi-value">₱24,800,000</div>
                    <div class="kpi-sub">FY 2021 enacted</div>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Disbursed</div>
                    <div class="kpi-value">₱19,600,000</div>
                    <div class="kpi-sub">79.0% utilization</div>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Remaining</div>
                    <div class="kpi-value text-warning">₱5,200,000</div>
                    <div class="kpi-sub">Unspent allocation</div>
                </div></div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-7">
                <div class="card h-100">
                    <div class="card-header">Allocated vs. Disbursed</div>
                    <div class="card-body">
                        <div style="height: 300px;"><canvas id="budgetHorizontalBarChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card h-100">
                    <div class="card-header">Sector Breakdown</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="bi bi-circle-fill text-success small"></i> Infrastructure</span>
                            <span class="text-muted">₱7.8M (91% disb.)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="bi bi-circle-fill text-danger small"></i> Healthcare</span>
                            <span class="text-muted">₱5.5M (89% disb.)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="bi bi-circle-fill text-warning small"></i> Education</span>
                            <span class="text-muted">₱4.4M (62% disb.)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="bi bi-circle-fill text-primary small"></i> Social Services</span>
                            <span class="text-muted">₱3.8M (76% disb.)</span>
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