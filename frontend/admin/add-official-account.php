<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/admin/add-official-account.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $esc($settingsCsrf) ?>">
    <title>Add Official Account - Brgy. Bucana</title>
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
                            <a class="nav-link active" href="add-official-account.php">
                                <i class="bi bi-person-plus"></i><span>Add Official Account</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="change-password.php">
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
                <h3 class="fw-bold mb-1">Add Official Account</h3>
                <p class="text-muted mb-0">Create an active account so a barangay official can sign in</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card">
                    <div class="card-header border-bottom pb-3 mb-3">
                        <h5 class="mb-0">Official Details</h5>
                        <small class="text-muted">The new official can sign in immediately with the credentials below</small>
                    </div>
                    <div class="card-body pt-0">
                        <div class="alert py-2 d-none" id="official-alert" role="alert"></div>
                        <form id="official-form" novalidate>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold" for="official-full-name">Full Name</label>
                                    <input type="text" class="form-control" id="official-full-name" name="full_name" maxlength="150" autocomplete="name" placeholder="e.g. Maria Santos Dela Cruz" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold" for="official-email">Email Address</label>
                                    <input type="email" class="form-control" id="official-email" name="email" maxlength="190" autocomplete="email" placeholder="official@example.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold" for="official-phone">Contact Number</label>
                                    <input type="tel" class="form-control" id="official-phone" name="phone" maxlength="20" autocomplete="tel" placeholder="09XXXXXXXXX">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold" for="official-position">Position</label>
                                    <select class="form-select" id="official-position" name="position" required>
                                        <option value="">Select position</option>
                                        <?php foreach ($settingsPositions as $positionValue => $positionLabel): ?>
                                            <option value="<?= $esc($positionValue) ?>"><?= $esc($positionLabel) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold" for="official-password">Password</label>
                                    <input type="password" class="form-control" id="official-password" name="password" autocomplete="new-password" minlength="8" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold" for="official-confirm-password">Confirm Password</label>
                                    <input type="password" class="form-control" id="official-confirm-password" name="confirm_password" autocomplete="new-password" minlength="8" required>
                                </div>
                            </div>
                            <p class="text-muted small mt-3 mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Use at least 8 characters with at least one letter and one number.
                            </p>
                            <button class="btn btn-primary-green px-4 mt-4" type="submit">
                                <i class="bi bi-person-plus me-1"></i> Create Official Account
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
