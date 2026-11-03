<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/context.php';

extract(resident_page_context('../login_page/login.php'), EXTR_SKIP);

$reference = trim((string) ($_GET['ref'] ?? ''));
$report = blotter_report_by_reference((int) current_user_id(), $reference);
if ($report === null) {
    http_response_code(404);
}

$closedStatuses = ['resolved', 'dismissed', 'referred', 'cancelled'];
$isReviewed = $report !== null && $report['reviewed_at'] !== null;
$isClosed = $report !== null && ($report['resolved_at'] !== null || in_array($report['status'], $closedStatuses, true));
$progress = $isClosed ? 3 : ($isReviewed ? 2 : 1);
$filedDate = $report !== null ? date('F j, Y g:i A', strtotime($report['created_at'])) : '';
$updatedDate = $report !== null ? date('F j, Y g:i A', strtotime($report['updated_at'])) : '';
