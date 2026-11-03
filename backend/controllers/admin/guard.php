<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

$currentUser = account_details((int) current_user_id());
$adminRoles = ['admin', 'captain', 'super_admin'];
if ($currentUser === null || !in_array($currentUser['role_code'] ?? '', $adminRoles, true)) {
    header('Location: ../login_page/login.php');
    exit;
}
