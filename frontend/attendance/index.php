<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/attendance/guard.php';
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
</head>
<body>
<main class="auth-plain">
    <div class="reset-card">
        <div class="ico">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <h1>Attendance</h1>
        <p>Signed in as <b><?= $esc($attendanceUserName) ?></b> &middot; <?= $esc($attendanceRoleName) ?></p>
        <div class="stack">
            <button class="btn btn-primary btn-full" type="button" id="att-signout" style="height:50px">Sign out</button>
        </div>
        <div class="notice">The attendance dashboard &mdash; daily time-in, time-out, and service-hour records &mdash; is coming soon.</div>
    </div>
</main>
<div id="toasts" aria-live="polite"></div>
<script src="assets/app.js"></script>
</body>
</html>
