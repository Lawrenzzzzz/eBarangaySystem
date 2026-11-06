<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/admin/guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Brgy. Bucana</title>
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
            <li class="nav-item"><a class="nav-link" href="projects.php"><i class="bi bi-kanban"></i> Projects</a></li>
            <li class="nav-item"><a class="nav-link" href="residents.php"><i class="bi bi-people"></i> Residents</a></li>
            <!-- ACTIVE CLASS MOVED HERE -->
            <li class="nav-item"><a class="nav-link active" href="reports.php"><i class="bi bi-file-earmark-text"></i> Reports</a></li>
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
                <h3 class="fw-bold mb-1">Reports</h3>
                <p class="text-muted mb-0">Generate and export official barangay records</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <select class="form-select form-select-sm" style="width: 100px;">
                    <option>2020</option>
                    <option selected>2021</option>
                    <option>2022</option>
                </select>
            </div>
        </div>

        <!-- REPORTS SPECIFIC CONTENT -->
        <div class="row g-4">
            <!-- Report 1 -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center d-flex flex-column">
                        <i class="bi bi-file-earmark-bar-graph fs-1 text-success mb-3"></i>
                        <h5 class="fw-bold">FY 2021 Budget Report</h5>
                        <p class="text-muted small">14 line items · 2 purok annexes</p>
                        <div class="mt-auto d-flex gap-2">
                            <button class="btn btn-primary-green w-100 btn-sm">Generate PDF</button>
                            <button class="btn btn-outline-secondary w-100 btn-sm">CSV</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Report 2 -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center d-flex flex-column">
                        <i class="bi bi-people fs-1 text-success mb-3"></i>
                        <h5 class="fw-bold">Resident Census 2021</h5>
                        <p class="text-muted small">3,142 households · 8 sitios</p>
                        <div class="mt-auto d-flex gap-2">
                            <button class="btn btn-primary-green w-100 btn-sm">Generate PDF</button>
                            <button class="btn btn-outline-secondary w-100 btn-sm">CSV</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Report 3 -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center d-flex flex-column">
                        <i class="bi bi-shield-exclamation fs-1 text-success mb-3"></i>
                        <h5 class="fw-bold">Disaster Response Log</h5>
                        <p class="text-muted small">3 events · P&O summary</p>
                        <div class="mt-auto d-flex gap-2">
                            <button class="btn btn-primary-green w-100 btn-sm">Generate PDF</button>
                            <button class="btn btn-outline-secondary w-100 btn-sm">CSV</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Report 4 -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center d-flex flex-column">
                        <i class="bi bi-heart-pulse fs-1 text-success mb-3"></i>
                        <h5 class="fw-bold">Healthcare Utilization</h5>
                        <p class="text-muted small">Vaccination & clinic visits</p>
                        <div class="mt-auto d-flex gap-2">
                            <button class="btn btn-primary-green w-100 btn-sm">Generate PDF</button>
                            <button class="btn btn-outline-secondary w-100 btn-sm">CSV</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Report 5 -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center d-flex flex-column">
                        <i class="bi bi-mortarboard fs-1 text-success mb-3"></i>
                        <h5 class="fw-bold">Education Assistance</h5>
                        <p class="text-muted small">Scholars & feeding program</p>
                        <div class="mt-auto d-flex gap-2">
                            <button class="btn btn-primary-green w-100 btn-sm">Generate PDF</button>
                            <button class="btn btn-outline-secondary w-100 btn-sm">CSV</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Report 6 -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center d-flex flex-column">
                        <i class="bi bi-check2-square fs-1 text-success mb-3"></i>
                        <h5 class="fw-bold">Voter Registration</h5>
                        <p class="text-muted small">By purok, precinct mapped</p>
                        <div class="mt-auto d-flex gap-2">
                            <button class="btn btn-primary-green w-100 btn-sm">Generate PDF</button>
                            <button class="btn btn-outline-secondary w-100 btn-sm">CSV</button>
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