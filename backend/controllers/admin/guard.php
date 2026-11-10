<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

$currentUser = account_details((int) current_user_id());
$adminRoles = ['admin', 'captain', 'super_admin'];
if ($currentUser === null || !in_array($currentUser['role_code'] ?? '', $adminRoles, true)) {
    header('Location: ../login_page/login.php');
    exit;
}

$settingsProfile = barangay_profile_record();
$settingsBarangayName = $settingsProfile['barangay_name'] !== '' ? $settingsProfile['barangay_name'] : 'Barangay Bucana';
$settingsChairmanName = $settingsProfile['chairman'] !== '' ? $settingsProfile['chairman'] : 'Barangay Chairman';
$settingsTerm = (string) $settingsProfile['term'];

if (!isset($esc)) {
    $esc = static function ($value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    };
}
