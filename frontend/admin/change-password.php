<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/admin/change-password.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $esc($settingsCsrf) ?>">
    <title>Change Password - <?= $esc($settingsBarangayName) ?></title>
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
            <div><h6 class="mb-0 fw-bold"><?= $esc($settingsBarangayName) ?></h6><small class="text-muted">Office of the Chairman</small></div>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="budget.php"><i class="bi bi-wallet2"></i> Budget</a></li>
            <li class="nav-item"><a class="nav-link" href="projects.php"><i class="bi bi-kanban"></i> Projects</a></li>
            <li class="nav-item"><a class="nav-link" href="residents.php"><i class="bi bi-people"></i> Residents</a></li>
            <li class="nav-item"><a class="nav-link" href="reports.php"><i class="bi bi-file-earmark-text"></i> Reports</a></li>
            <li class="nav-item">
                <div class="settings-parent active">
                    <a class="nav-link" href="settings.php"><i class="bi bi-gear"></i><span>Settings</span></a>
                    <button class="settings-caret-btn" type="button" data-bs-toggle="collapse" data-bs-target="#settings-submenu" aria-expanded="true" aria-controls="settings-submenu" aria-label="Toggle settings options">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="settings-submenu">
                    <ul class="nav flex-column settings-submenu">
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <i class="bi bi-building"></i><span>Barangay Profile</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add-official-account.php">
                                <i class="bi bi-person-plus"></i><span>Add Official Account</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="change-password.php">
                                <i class="bi bi-key"></i><span>Change Password</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
        <div class="sidebar-footer">
            <div class="user-card">
                <small class="text-muted d-block mb-1">SIGNED IN AS</small>
                <strong class="d-block"><?= $esc($settingsChairmanName) ?></strong>
                <small class="text-muted">Chairman - Term <?= $esc($settingsTerm) ?></small>
            </div>
            <button class="btn btn-outline btn-sm w-100 mt-2" id="admin-signout" type="button">Sign out</button>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div id="content">
        <div class="page-header">
            <div>
                <h3 class="fw-bold mb-1">Change Password</h3>
                <p class="text-muted mb-0">Update the password used to sign in to your account</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-7">
                <div class="card">
                    <div class="card-header border-bottom pb-3 mb-3">
                        <h5 class="mb-0">Account Security</h5>
                        <small class="text-muted">Choose a strong password you do not use anywhere else</small>
                    </div>
                    <div class="card-body pt-0">
                        <div class="alert py-2 d-none" id="password-alert" role="alert"></div>
                        <form id="password-form" novalidate>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold" for="current-password">Current Password</label>
                                <input type="password" class="form-control" id="current-password" name="current_password" autocomplete="current-password" required>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold" for="new-password">New Password</label>
                                    <input type="password" class="form-control" id="new-password" name="new_password" autocomplete="new-password" minlength="8" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold" for="confirm-password">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm-password" name="confirm_password" autocomplete="new-password" minlength="8" required>
                                </div>
                            </div>
                            <p class="text-muted small mt-3 mb-0">
                                <i class="bi bi-shield-check me-1"></i>
                                Use at least 8 characters with at least one letter and one number.
                            </p>
                            <button class="btn btn-primary-green px-4 mt-4" type="submit">
                                <i class="bi bi-key me-1"></i> Update Password
                            </button>
                        </form>
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
<script src="js/settings.js"></script>
</body>
</html>
