<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/guard.php';

$attendanceDate = attendance_normalize_date($_GET['date'] ?? null);
$attendanceDateLabel = date('l, F j, Y', strtotime($attendanceDate));
$attendanceDateShort = date('M j, Y', strtotime($attendanceDate));
$attendanceDateInput = $attendanceDate;
$attendanceOfficialOptions = attendance_official_accounts();
$attendanceSelectedOfficial = (int) ($_GET['official'] ?? 0);
if ($attendanceSelectedOfficial <= 0) {
    $attendanceSelectedOfficial = null;
}

if ($attendanceSelectedOfficial !== null) {
    $attendanceOfficials = attendance_official_history($attendanceSelectedOfficial);
    $attendanceDateLabel = 'All recorded dates';
} else {
    $attendanceOfficials = attendance_officials_for_date($attendanceDate, null);
}

$settingsCsrf = csrf_token();
