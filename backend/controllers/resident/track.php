<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/context.php';

extract(resident_page_context('../login_page/login.php'), EXTR_SKIP);

$documentRequests = list_document_requests((int) current_user_id());
$documentRequestCount = count($documentRequests);
$blotterReports = list_blotter_reports((int) current_user_id());
$blotterReportCount = count($blotterReports);
$blotterExcerpt = static function (string $text, int $limit = 170): string {
    $length = function_exists('mb_strlen') ? mb_strlen($text) : strlen($text);
    if ($length <= $limit) {
        return $text;
    }
    $cut = function_exists('mb_substr') ? mb_substr($text, 0, $limit) : substr($text, 0, $limit);
    return rtrim($cut) . '…';
};
$chipLetters = ['clearance' => 'C', 'indigency' => 'I', 'residency' => 'R'];
$chipClasses = ['clearance' => 'blue', 'indigency' => 'green', 'residency' => 'amber'];
$requestNotes = [
    'pending' => 'Your request is waiting for review by barangay staff.',
    'processing' => 'Barangay staff are processing your request.',
    'for_payment' => 'Please pay the fee at the barangay hall to continue.',
    'ready' => 'Your document is ready for pickup at the barangay hall.',
    'released' => 'This document has been released.',
    'rejected' => 'This request was rejected. Visit the barangay hall for details.',
    'cancelled' => 'This request was cancelled.',
];
