<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/context.php';

extract(resident_page_context('../login_page/login.php'), EXTR_SKIP);

$homeRequests = list_document_requests((int) current_user_id(), null, 5);
$homeStats = document_request_stats((int) current_user_id());
$homeBlotterStats = blotter_stats((int) current_user_id());
$homeChipLetters = ['clearance' => 'C', 'indigency' => 'I', 'residency' => 'R'];
$homeChipClasses = ['clearance' => 'blue', 'indigency' => 'green', 'residency' => 'amber'];
$homeRequestNotes = [
    'pending' => 'Waiting for review',
    'processing' => 'Being processed',
    'for_payment' => 'For payment',
    'ready' => 'Ready for pickup',
    'released' => 'Released',
    'rejected' => 'Rejected',
    'cancelled' => 'Cancelled',
];
