<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

$attendanceRoles = ['admin', 'captain', 'super_admin'];
$currentUser = account_details((int) current_user_id());

if ($currentUser === null) {
    header('Location: login.php');
    exit;
}

$attendanceRoleCode = (string) ($currentUser['role_code'] ?? '');
if (!in_array($attendanceRoleCode, $attendanceRoles, true)) {
    $attendanceHome = in_array($attendanceRoleCode, ['official', 'staff', 'kagawad', 'secretary', 'treasurer'], true)
        ? '../barangay_officials/dashboard.php'
        : '../user_pages/index.php';
    header('Location: ' . $attendanceHome);
    exit;
}

$esc = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$csrf = csrf_token();

$attendanceUserName = trim((string) ($currentUser['account_full_name'] ?? ''));
if ($attendanceUserName === '') {
    $attendanceUserName = trim((string) ($currentUser['full_name'] ?? ''));
}
if ($attendanceUserName === '') {
    $attendanceUserName = trim((string) ($currentUser['email'] ?? '')) ?: 'Barangay Official';
}
$attendanceRoleName = trim((string) ($currentUser['role_name'] ?? '')) ?: 'Barangay Official';
