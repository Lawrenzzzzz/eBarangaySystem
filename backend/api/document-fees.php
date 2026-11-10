<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $userId = require_login_json();
    $account = account_details($userId);
    $officialRoles = ['official', 'staff', 'kagawad', 'secretary', 'treasurer', 'admin', 'captain', 'super_admin'];
    $isOfficial = $account !== null && in_array($account['role_code'] ?? '', $officialRoles, true);

    if (!$isOfficial) {
        json_error('You do not have permission to manage document fees.', 403);
    }

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        json_response([
            'ok' => true,
            'fees' => document_fee_records(),
        ]);
    }

    if ($method === 'POST') {
        ensure_post();
        verify_csrf();

        $input = json_input();
        $fees = $input['fees'] ?? null;

        if (!is_array($fees)) {
            json_error('Enter a fee amount for each document.', 422);
        }

        respond_result(save_document_fees($fees));
    }

    json_error('Method not allowed.', 405);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to manage document fees right now.', 500);
}
