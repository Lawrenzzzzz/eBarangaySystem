<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/admin/dashboard.php';

$budget = $dashboardData['budget'];
$residents = $dashboardData['residents'];
$blotter = $dashboardData['blotter'];
$recentOfficialLogins = $dashboardData['recent_official_logins'] ?? [];
$number = static fn (int|float $value): string => number_format((float) $value);
$initials = static function (string $name): string {
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $letters = '';
    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }
        $letters .= strtoupper(substr($part, 0, 1));
        if (strlen($letters) >= 2) {
            break;
        }
    }
    return $letters !== '' ? $letters : 'O';
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= $esc($settingsBarangayName) ?></title>
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
            <!-- NOTICE: The active class is on Dashboard -->
            <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="budget.php"><i class="bi bi-wallet2"></i> Budget</a></li>
            <li class="nav-item"><a class="nav-link" href="projects.php"><i class="bi bi-kanban"></i> Projects</a></li>
            <li class="nav-item"><a class="nav-link" href="residents.php"><i class="bi bi-people"></i> Residents</a></li>
            <li class="nav-item"><a class="nav-link" href="reports.php"><i class="bi bi-file-earmark-text"></i> Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
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
    <div id="content" data-dashboard-endpoint="../../backend/api/admin-dashboard.php">
        <div class="page-header">
            <div>
                <h3 class="fw-bold mb-1">Chairman's Dashboard</h3>
                <p class="text-muted mb-0">Financial &amp; population overview</p>
            </div>
        </div>

        <!-- DASHBOARD SPECIFIC CONTENT -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Total Budget</div>
                    <div class="kpi-value" id="kpi-total-budget"><?= htmlspecialchars($budget['total_label'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="kpi-sub" id="kpi-total-budget-sub">FY <?= (int) $budget['year'] ?> enacted</div>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Disbursed</div>
                    <div class="kpi-value" id="kpi-disbursed"><?= htmlspecialchars($budget['disbursed_label'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="kpi-sub" id="kpi-disbursed-sub"><?= number_format((float) $budget['utilization'], 1) ?>% utilization</div>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Total Residents</div>
                    <div class="kpi-value" id="kpi-total-residents"><?= $number($residents['total']) ?></div>
                    <div class="kpi-sub" id="kpi-total-residents-sub"><?= $number($residents['new_this_month']) ?> new this month</div>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Registered Voters</div>
                    <div class="kpi-value" id="kpi-voters"><?= $number($residents['voters']) ?></div>
                    <div class="kpi-sub" id="kpi-voters-sub"><?= number_format((float) $residents['voter_pct'], 1) ?>% of residents (18+)</div>
                </div></div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header">FY <?= (int) $budget['year'] ?> Budget by Sector</div>
                    <div class="card-body"><div style="height: 250px;"><canvas id="dashboardBarChart"></canvas></div></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header">Allocation by Sector</div>
                    <div class="card-body">
                        <div style="height: 190px;"><canvas id="dashboardPieChart"></canvas></div>
                        <div class="d-flex justify-content-between mt-3 mb-1">
                            <small class="fw-bold" style="color: #1f4d3a;">Male: <span id="gender-male-count"><?= $number($residents['male']) ?></span> (<span id="gender-male-pct"><?= (int) $residents['male_pct'] ?></span>%)</small>
                            <small class="fw-bold" style="color: #c07c3b;">Female: <span id="gender-female-count"><?= $number($residents['female']) ?></span> (<span id="gender-female-pct"><?= (int) $residents['female_pct'] ?></span>%)</small>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar" role="progressbar" id="gender-male-bar" style="width: <?= (int) $residents['male_pct'] ?>%; background-color: #1f4d3a;" aria-valuenow="<?= (int) $residents['male_pct'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar" role="progressbar" id="gender-female-bar" style="width: <?= (int) $residents['female_pct'] ?>%; background-color: #c07c3b;" aria-valuenow="<?= (int) $residents['female_pct'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-3 small text-muted">
                            <span><?= $number($blotter['total']) ?> blotter reports</span>
                            <span><?= $number($blotter['resolved']) ?> resolved (<?= number_format((float) $blotter['resolved_rate'], 0) ?>%)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history me-2"></i>Recent Official Logins</span>
                        <small class="text-muted fw-normal">Official accounts only</small>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-custom align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Official</th>
                                        <th>Position</th>
                                        <th>Email</th>
                                        <th class="text-end">Last Login</th>
                                    </tr>
                                </thead>
                                <tbody id="official-login-rows">
                                <?php if ($recentOfficialLogins === []): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">No official logins recorded yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentOfficialLogins as $login): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="official-avatar"><?= $esc($initials($login['name'])) ?></span>
                                                <div>
                                                    <strong class="d-block"><?= $esc($login['name']) ?></strong>
                                                    <small class="text-muted"><?= $esc($login['employee_number'] !== '' ? $login['employee_number'] : 'Official account') ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="status-pill status-completed"><?= $esc($login['position']) ?></span></td>
                                        <td class="text-muted"><?= $esc($login['email']) ?></td>
                                        <td class="text-end">
                                            <div class="fw-semibold"><?= $esc($login['last_login_relative']) ?></div>
                                            <small class="text-muted"><?= $esc($login['last_login_label']) ?></small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>window.DASHBOARD_DATA = <?= json_encode($dashboardData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/charts.js"></script>
<script src="js/main.js"></script>
</body>
</html>
