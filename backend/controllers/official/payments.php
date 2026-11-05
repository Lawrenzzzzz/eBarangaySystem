<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/guard.php';

$paymentSearch = trim((string) ($_GET['q'] ?? ''));
$payments = list_official_payments($paymentSearch);
$paymentStats = official_payment_stats();
