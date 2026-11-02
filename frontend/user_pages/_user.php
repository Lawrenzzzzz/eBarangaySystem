<?php
declare(strict_types=1);

$currentUser = account_details((int) current_user_id());
if ($currentUser === null) {
    unset($_SESSION['user_id']);
    header('Location: ../login_page/login.php');
    exit;
}

$esc = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$userFullName = trim((string) ($currentUser['full_name'] ?? ''));
$nameParts = $userFullName === '' ? [] : (preg_split('/\s+/', $userFullName) ?: []);
$userFirstName = $nameParts[0] ?? 'Resident';

$userInitials = '';
foreach ($nameParts as $namePart) {
    if ($namePart === '') {
        continue;
    }
    $userInitials .= strtoupper(substr($namePart, 0, 1));
    if (strlen($userInitials) >= 2) {
        break;
    }
}
if ($userInitials === '') {
    $userInitials = 'R';
}

$userResidentCode = trim((string) ($currentUser['resident_code'] ?? ''));
$userHousehold = trim((string) ($currentUser['household_number'] ?? ''));
$userAddress = trim((string) ($currentUser['address_line'] ?? ''));
$userEmail = trim((string) ($currentUser['email'] ?? ''));
