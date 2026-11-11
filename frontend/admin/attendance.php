<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/admin/attendance.php';

$attendanceInitials = static function (string $name): string {
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
    <meta name="csrf-token" content="<?= $esc($settingsCsrf) ?>">
    <title>Attendance - <?= $esc($settingsBarangayName) ?></title>
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
            <li class="nav-item"><a class="nav-link active" href="attendance.php"><i class="bi bi-calendar-check"></i> Attendance</a></li>
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
    <div id="content">
        <div class="page-header">
            <div>
                <h3 class="fw-bold mb-1">Attendance</h3>
                <p class="text-muted mb-0">Hours worked by each barangay official</p>
            </div>
            <form method="get" class="d-flex flex-column gap-2" style="min-width: 260px;">
                <div class="d-flex align-items-center gap-2">
                    <label for="attendance-date" class="text-muted small fw-semibold mb-0" style="min-width: 50px;">Date</label>
                    <input type="date" class="form-control form-control-sm" id="attendance-date" name="date" value="<?= $esc($attendanceDateInput) ?>" onchange="this.form.submit()">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="attendance-official" class="text-muted small fw-semibold mb-0" style="min-width: 50px;">Official</label>
                    <select class="form-select form-select-sm" id="attendance-official" name="official" onchange="this.form.submit()">
                        <option value="">All officials</option>
                        <?php foreach ($attendanceOfficialOptions as $officialOption): ?>
                            <option value="<?= (int) $officialOption['user_id'] ?>"<?= $attendanceSelectedOfficial === $officialOption['user_id'] ? ' selected' : '' ?>><?= $esc($officialOption['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Official Attendance</span>
                <small class="text-muted fw-normal">Green: 6 hours or more &middot; Red: under 6 hours</small>
            </div>
            <div class="card-body pt-2">
                <div class="table-responsive">
                    <table class="table table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Date</th>
                                <th>Time in</th>
                                <th>Time out</th>
                                <th class="text-end">Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($attendanceOfficials === []): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">No officials logged in on this date.</td></tr>
                        <?php else: ?>
                            <?php foreach ($attendanceOfficials as $official): ?>
                                <?php
                                $hasHours = $official['hours_label'] !== null;
                                $hoursClass = !$hasHours
                                    ? 'attendance-hours-none'
                                    : ((float) $official['hours'] >= 6 ? 'attendance-hours-good' : 'attendance-hours-low');
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="official-avatar"><?= $esc($attendanceInitials($official['name'])) ?></span>
                                            <strong><?= $esc($official['name']) ?></strong>
                                        </div>
                                    </td>
                                    <td><?= $esc($official['position']) ?></td>
                                    <td class="text-muted"><?= $esc($attendanceDateShort) ?></td>
                                    <td>
                                        <?php if ($official['time_in'] !== null): ?>
                                            <span class="status-pill status-released"><?= $esc($official['time_in']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($official['time_out'] !== null): ?>
                                            <span class="status-pill status-pending"><?= $esc($official['time_out']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($hasHours): ?>
                                            <span class="attendance-hours-badge <?= $hoursClass ?>"><?= $esc($official['hours_label']) ?></span>
                                        <?php else: ?>
                                            <span class="attendance-hours-badge attendance-hours-none">No record</span>
                                        <?php endif; ?>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
