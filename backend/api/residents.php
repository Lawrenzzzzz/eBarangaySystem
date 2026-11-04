<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $userId = require_login_json();
    $account = account_details($userId);
    $officialRoles = ['official', 'staff', 'kagawad', 'secretary', 'treasurer', 'admin', 'captain', 'super_admin'];

    if ($account === null || !in_array($account['role_code'] ?? '', $officialRoles, true)) {
        json_error('You do not have permission to manage residents.', 403);
    }

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        if (($_GET['action'] ?? '') !== 'next-code') {
            json_error('Unknown action.', 400);
        }

        json_response(['ok' => true, 'resident_code' => next_resident_code_preview()]);
    }

    if ($method === 'POST') {
        ensure_post();
        verify_csrf();
        respond_result(create_official_resident(json_input()));
    }

    json_error('Method not allowed.', 405);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to manage residents right now.', 500);
}
