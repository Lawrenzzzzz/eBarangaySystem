<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/guard.php';

$residentActionNotice = '';
$residentActionError = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $submittedToken = (string) ($_POST['csrf_token'] ?? '');
    $residentSearch = trim((string) ($_POST['q'] ?? ''));
    $residentId = filter_var($_POST['resident_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;

    $action = (string) ($_POST['action'] ?? 'delete');

    if (!hash_equals($csrf, $submittedToken)) {
        $residentActionError = 'Your session security token expired. Please try again.';
    } elseif ($action !== 'delete') {
        $residentActionError = 'Unknown resident action.';
    } else {
        $result = delete_official_resident($residentId, (int) current_user_id());

        if ($result['ok']) {
            $redirect = 'residents.php?deleted=1';
            if ($residentSearch !== '') {
                $redirect .= '&q=' . urlencode($residentSearch);
            }
            header('Location: ' . $redirect);
            exit;
        }
        $residentActionError = $result['message'];
    }
} else {
    $residentSearch = trim((string) ($_GET['q'] ?? ''));
    if (isset($_GET['deleted'])) {
        $residentActionNotice = 'Resident removed from the list successfully.';
    }
}

$residents = list_official_residents($residentSearch);
$residentStats = official_resident_stats();
