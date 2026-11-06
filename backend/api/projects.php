<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $userId = require_login_json();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        json_response(['ok' => true, 'projects' => list_projects()]);
    }

    if ($method === 'POST') {
        $account = account_details($userId);
        $adminRoles = ['admin', 'captain', 'super_admin'];
        if ($account === null || !in_array($account['role_code'] ?? '', $adminRoles, true)) {
            json_error('You do not have permission to manage projects.', 403);
        }

        ensure_post();
        verify_csrf();
        $input = json_input();

        if (isset($input['id'])) {
            respond_result(update_project($input));
        }

        respond_result(create_project($input, $userId));
    }

    json_error('Method not allowed.', 405);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to manage projects right now.', 500);
}
