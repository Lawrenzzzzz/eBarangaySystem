<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $userId = require_login_json();
    $account = account_details($userId);
    $adminRoles = ['admin', 'captain', 'super_admin'];

    if ($account === null || !in_array($account['role_code'] ?? '', $adminRoles, true)) {
        json_error('You do not have permission to view the dashboard.', 403);
    }

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        json_error('Method not allowed.', 405);
    }

    json_response([
        'ok' => true,
        'dashboard' => admin_dashboard_data(),
    ]);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to load the dashboard right now.', 500);
}
