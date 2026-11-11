<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/guard.php';

$reportActionError = '';

$currentYear = (int) date('Y');
$reportYearInput = $_GET['year'] ?? $currentYear;
$reportYear = is_scalar($reportYearInput) ? (int) $reportYearInput : $currentYear;
if ($reportYear < 1900 || $reportYear > $currentYear) {
    $reportYear = $currentYear;
}

$reportBarangayName = 'Barangay Bucana';
$reportMunicipality = 'Nasugbu';
$reportChairmanName = official_barangay_chairman_name($officialFullName);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $submittedToken = (string) ($_POST['csrf_token'] ?? '');
    $action = is_scalar($_POST['action'] ?? null) ? (string) $_POST['action'] : '';
    $reportInput = is_scalar($_POST['report'] ?? null) ? (string) $_POST['report'] : '';

    $reportDefinitions = [
        'residents' => [
            'title' => 'Residents Report',
            'data' => static fn (): array => list_residents_for_report(),
        ],
        'payments' => [
            'title' => 'Payments Report',
            'data' => static fn (): array => list_payments_for_report(),
        ],
    ];

    if (!hash_equals($csrf, $submittedToken)) {
        $reportActionError = 'Your session security token expired. Please try again.';
    } elseif ($action !== 'generate' || !isset($reportDefinitions[$reportInput])) {
        $reportActionError = 'Unknown report action.';
    } else {
        $definition = $reportDefinitions[$reportInput];
        $generatedAt = new DateTimeImmutable('now');
        $generatedYear = (int) $generatedAt->format('Y');

        $reportId = save_generated_report([
            'report_type' => $reportInput,
            'title' => $definition['title'],
            'report_year' => $generatedYear,
            'barangay_name' => $reportBarangayName,
            'municipality' => $reportMunicipality,
            'chairman_name' => $reportChairmanName,
            'generated_by' => (int) current_user_id(),
            'generated_at' => $generatedAt->format('Y-m-d H:i:s'),
            'data' => $definition['data'](),
        ]);

        redirect('official/reports?year=' . $generatedYear . '&report_id=' . $reportId . '&generated=1');
    }
}

$reportIdInput = $_GET['report_id'] ?? 0;
$reportId = is_scalar($reportIdInput) ? (int) $reportIdInput : 0;
$selectedReport = $reportId > 0 ? find_generated_report($reportId) : null;
if ($selectedReport !== null && $selectedReport['report_year'] !== $reportYear) {
    $selectedReport = null;
}

$savedReports = list_generated_reports($reportYear);
