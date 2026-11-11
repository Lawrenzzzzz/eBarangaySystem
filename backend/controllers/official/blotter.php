<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/guard.php';

$blotterNotice = '';
$blotterError = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $submittedToken = (string) ($_POST['csrf_token'] ?? '');
    $blotterSearch = trim((string) ($_POST['q'] ?? ''));
    $reportId = filter_var($_POST['report_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;

    if (!hash_equals($csrf, $submittedToken)) {
        $blotterError = 'Your session security token expired. Please try again.';
    } else {
        $result = update_official_blotter_status($reportId, (string) ($_POST['status'] ?? ''), (int) current_user_id());

        if ($result['ok']) {
            $params = ['updated' => 1];
            if ($blotterSearch !== '') {
                $params['q'] = $blotterSearch;
            }
            redirect('official/blotter?' . http_build_query($params));
        }

        $blotterError = $result['error'] ?? 'Unable to update this blotter report.';
    }
} else {
    $blotterSearch = trim((string) ($_GET['q'] ?? ''));
    if (isset($_GET['updated'])) {
        $blotterNotice = 'Blotter report status updated successfully.';
    }
}

$blotterReports = list_official_blotter_reports($blotterSearch);
$blotterStats = official_blotter_stats();
