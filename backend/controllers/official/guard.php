<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

$officialRoles = ['official', 'staff', 'kagawad', 'secretary', 'treasurer', 'admin', 'captain', 'super_admin'];
$currentUser = account_details((int) current_user_id());
if ($currentUser === null || !in_array($currentUser['role_code'] ?? '', $officialRoles, true)) {
    header('Location: ../login_page/login.php');
    exit;
}

$esc = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$csrf = csrf_token();

$officialFullName = trim((string) ($currentUser['full_name'] ?? ''));
if ($officialFullName === '') {
    $officialFullName = trim((string) ($currentUser['email'] ?? '')) ?: 'Barangay Official';
}
$officialRoleName = trim((string) ($currentUser['role_name'] ?? '')) ?: 'Barangay Official';
