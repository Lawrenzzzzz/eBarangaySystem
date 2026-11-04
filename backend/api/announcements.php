<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $userId = require_login_json();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        json_response(['ok' => true, 'announcements' => list_announcements()]);
    }

    if ($method === 'POST') {
        $account = account_details($userId);
        $officialRoles = ['official', 'staff', 'kagawad', 'secretary', 'treasurer', 'admin', 'captain', 'super_admin'];

        if ($account === null || !in_array($account['role_code'] ?? '', $officialRoles, true)) {
            json_error('You do not have permission to post announcements.', 403);
        }

        ensure_post();
        verify_csrf();
        respond_result(create_announcement(json_input(), $userId));
    }

    json_error('Method not allowed.', 405);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to manage announcements right now.', 500);
}
