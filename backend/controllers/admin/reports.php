<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/guard.php';

$reportYearInput = $_GET['year'] ?? '';
$reportYear = null;
if (is_scalar($reportYearInput) && (string) $reportYearInput !== '') {
    $candidateYear = (int) $reportYearInput;
    if ($candidateYear >= 1900 && $candidateYear <= (int) date('Y') + 1) {
        $reportYear = $candidateYear;
    }
}

$reportYears = list_report_years();
$savedReports = list_generated_reports($reportYear);

$reportIdInput = $_GET['report_id'] ?? 0;
$reportId = is_scalar($reportIdInput) ? (int) $reportIdInput : 0;
$selectedReport = $reportId > 0 ? find_generated_report($reportId) : null;

$esc = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
