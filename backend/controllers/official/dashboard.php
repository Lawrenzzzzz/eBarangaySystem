<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/guard.php';

$residentStats = official_resident_stats();
$documentRequestStats = official_document_request_stats();
$blotterStats = official_blotter_stats();
$paymentStats = official_payment_stats();

$actionableStatuses = ['pending', 'processing', 'for_payment', 'ready'];
$documentRequestQueue = [];
foreach (list_official_document_requests('all', null, 50) as $request) {
    if (in_array($request['status'], $actionableStatuses, true)) {
        $documentRequestQueue[] = $request;
    }
    if (count($documentRequestQueue) >= 6) {
        break;
    }
}
$documentRequestQueueCount = $documentRequestStats['pending'] + $documentRequestStats['processing'] + $documentRequestStats['ready'];

$recentActivity = [];
foreach (list_official_document_requests('all', null, 5) as $request) {
    $recentActivity[] = [
        'at' => $request['created_at'],
        'icon' => 'bi-file-earmark-text',
        'title' => 'Document request ' . strtolower($request['status_label']),
        'meta' => $request['full_name'] . ' · ' . $request['document_name'],
        'url' => 'document-requests.php?q=' . urlencode($request['reference_no']),
    ];
}
foreach (list_official_blotter_reports(null, 5) as $report) {
    $recentActivity[] = [
        'at' => $report['created_at'],
        'icon' => 'bi-shield-exclamation',
        'title' => 'Blotter report ' . strtolower($report['status_label']),
        'meta' => $report['filed_by'] . ' · ' . $report['subject'],
        'url' => 'blotter.php?q=' . urlencode($report['reference_no']),
    ];
}
foreach (list_official_payments(null, 5) as $payment) {
    $recentActivity[] = [
        'at' => $payment['paid_at'],
        'icon' => 'bi-cash-coin',
        'title' => 'Payment ' . strtolower($payment['status_label']),
        'meta' => $payment['payee'] . ' · ' . $payment['amount_label'],
        'url' => 'payments.php?q=' . urlencode($payment['reference_no']),
    ];
}
foreach (list_announcements(3) as $announcement) {
    $recentActivity[] = [
        'at' => $announcement['created_at'],
        'icon' => 'bi-megaphone',
        'title' => 'Announcement posted',
        'meta' => $announcement['type_label'] . ' · ' . $announcement['title'],
        'url' => 'announcements.php',
    ];
}

usort($recentActivity, static fn (array $a, array $b): int => strtotime($b['at']) <=> strtotime($a['at']));
$recentActivity = array_slice($recentActivity, 0, 6);

$relativeTime = static function (string $timestamp): string {
    $time = strtotime($timestamp);
    if ($time === false) {
        return '';
    }

    $diff = time() - $time;
    if ($diff < 60) {
        return 'Just now';
    }
    if ($diff < 3600) {
        return (int) floor($diff / 60) . 'm ago';
    }
    if ($diff < 86400) {
        return (int) floor($diff / 3600) . 'h ago';
    }
    if ($diff < 604800) {
        return (int) floor($diff / 86400) . 'd ago';
    }

    return date('M j', $time);
};

$blotterResolvedRate = $blotterStats['total'] > 0
    ? (int) round(($blotterStats['resolved'] / $blotterStats['total']) * 100)
    : 0;
