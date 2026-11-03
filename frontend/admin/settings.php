<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/admin/guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Brgy. Bucana</title>
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
            <li class="nav-item"><a class="nav-link" href="residents.php"><i class="bi bi-people"></i> Residents</a></li>
            <li class="nav-item"><a class="nav-link" href="reports.php"><i class="bi bi-file-earmark-text"></i> Reports</a></li>
            <!-- ACTIVE CLASS MOVED HERE -->
            <li class="nav-item"><a class="nav-link active" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
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
                <h3 class="fw-bold mb-1">Settings</h3>
                <p class="text-muted mb-0">Barangay profile & account</p>
            </div>
        </div>

        <!-- SETTINGS SPECIFIC CONTENT -->
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7">
                <div class="card">
                    <div class="card-header border-bottom pb-3 mb-3">
                        <h5 class="mb-0">Barangay Profile</h5>
                        <small class="text-muted">These details appear on generated reports</small>
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Barangay Name</label>
                            <input type="text" class="form-control bg-light" value="Barangay Bucana">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">City / Municipality</label>
                            <input type="text" class="form-control bg-light" value="Nasugbu">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Chairman</label>
                            <input type="text" class="form-control bg-light" value="Capt. Choi Seungcheol Batumbakal">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Terms</label>
                            <input type="text" class="form-control bg-light" value="2022-2025">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Puroks</label>
                            <input type="text" class="form-control bg-light" value="7">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Sitios</label>
                            <input type="text" class="form-control bg-light" value="8">
                        </div>
                        <button class="btn btn-primary-green px-4">Save Changes</button>
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