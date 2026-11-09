<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/guard.php';

$dashboardData = admin_dashboard_data();
$dashboardMoney = static fn (float $amount): string => '₱' . number_format($amount, 0);
