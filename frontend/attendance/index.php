<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/backend/controllers/attendance/guard.php';

$attendanceRecords = attendance_today();
$attendanceDateLabel = date('l, F j, Y');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= $esc($csrf) ?>">
    <title>Attendance | e-Barangay San Isidro</title>
    <meta name="description" content="Barangay San Isidro attendance portal for officials and staff.">
    <meta property="og:title" content="Attendance | e-Barangay San Isidro">
    <meta property="og:description" content="Barangay San Isidro attendance portal for officials and staff.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;1,9..144,400;1,9..144,600&family=Manrope:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="../login_page/assets/styles.css">
    <link rel="stylesheet" href="../login_page/assets/auth.css">
    <link rel="stylesheet" href="assets/attendance.css">
</head>
<body>
<div class="att-shell">
    <header class="att-header">
        <div class="att-brand">
            <span class="att-brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 21h18"/>
                    <path d="M5 21V7l7-4 7 4v14"/>
                    <path d="M9 21v-6h6v6"/>
                </svg>
            </span>
            <span class="att-brand-text">
                <strong>e-Barangay</strong>
                <small>San Isidro &middot; Attendance</small>
            </span>
        </div>
        <div class="att-user">
            <span class="att-user-id">Signed in as <b><?= $esc($attendanceUserName) ?></b> &middot; <?= $esc($attendanceRoleName) ?></span>
            <button class="btn btn-outline" type="button" id="att-signout">Sign out</button>
        </div>
    </header>

    <main class="att-main">
        <section class="att-card" aria-label="QR scanner">
            <h1 class="att-title">QR Scanner</h1>
            <p class="att-sub">Point an official&rsquo;s QR badge at the camera to record their time in or time out.</p>

            <div id="att-reader" class="att-reader hidden"></div>

            <div id="att-cam-hint" class="att-cam-hint">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 8l2-3h4l1 2h9a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2z"/>
                    <circle cx="12" cy="13" r="4"/>
                </svg>
                <span>Camera is off. Press <b>Start camera</b> to begin scanning badges.</span>
            </div>

            <div id="att-cam-error" class="err hidden" role="alert"></div>

            <div class="att-cam-controls">
                <button class="btn btn-primary btn-full" type="button" id="att-cam-start" style="height:50px">Start camera</button>
                <button class="btn btn-outline btn-full hidden" type="button" id="att-cam-stop" style="height:50px">Stop camera</button>
            </div>

            <div id="att-result" class="att-result hidden" aria-live="polite">
                <span class="att-result-badge" id="att-result-badge">TIME IN</span>
                <span class="att-result-name" id="att-result-name"></span>
                <span class="att-result-time" id="att-result-time"></span>
            </div>
        </section>

        <section class="att-card" aria-label="Today's attendance">
            <div class="att-log-head">
                <h1 class="att-title">Today&rsquo;s Attendance</h1>
                <span class="att-log-date"><?= $esc($attendanceDateLabel) ?></span>
            </div>
            <div class="att-table-wrap">
                <table class="att-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Time in</th>
                            <th>Time out</th>
                            <th>Hours</th>
                        </tr>
                    </thead>
                    <tbody id="att-log-rows">
                        <?php if ($attendanceRecords === []): ?>
                            <tr class="att-empty"><td colspan="5">No one has timed in yet today.</td></tr>
                        <?php else: ?>
                            <?php foreach ($attendanceRecords as $attendanceRecord): ?>
                                <tr>
                                    <td><?= $esc($attendanceRecord['name']) ?></td>
                                    <td><?= $esc($attendanceRecord['position']) ?></td>
                                    <td><span class="att-pill att-pill-in"><?= $esc($attendanceRecord['time_in']) ?></span></td>
                                    <td>
                                        <?php if ($attendanceRecord['time_out'] !== null): ?>
                                            <span class="att-pill att-pill-out"><?= $esc($attendanceRecord['time_out']) ?></span>
                                        <?php else: ?>
                                            <span class="att-muted">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $esc($attendanceRecord['hours'] ?? '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<div class="modal-bg" id="att-modal" role="dialog" aria-modal="true" aria-labelledby="att-modal-name">
    <div class="modal att-modal">
        <button class="btn btn-ghost btn-icon modal-x" type="button" id="att-modal-close" aria-label="Close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true">
                <path d="M18 6 6 18"/>
                <path d="m6 6 12 12"/>
            </svg>
        </button>
        <div class="head">
            <div class="ico" id="att-modal-ico">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 6 9 17l-5-5"/>
                </svg>
            </div>
            <span class="att-modal-badge" id="att-modal-badge">TIME IN</span>
            <h2 id="att-modal-name"></h2>
            <p id="att-modal-sub"></p>
        </div>
        <button class="btn btn-primary btn-full" type="button" id="att-modal-ok" style="height:50px">Done</button>
    </div>
</div>
<div id="toasts" aria-live="polite"></div>
<script src="assets/libs/html5-qrcode.min.js"></script>
<script src="assets/app.js"></script>
</body>
</html>
