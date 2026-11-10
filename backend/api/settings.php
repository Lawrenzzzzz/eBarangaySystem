<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $userId = require_login_json();
    $account = account_details($userId);
    $adminRoles = ['admin', 'captain', 'super_admin'];
    $isAdmin = $account !== null && in_array($account['role_code'] ?? '', $adminRoles, true);

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $action = (string) ($_GET['action'] ?? '');

    if ($method === 'GET') {
        if (!$isAdmin) {
            json_error('You do not have permission to manage settings.', 403);
        }
        json_response([
            'ok' => true,
            'profile' => barangay_profile_record(),
            'positions' => official_position_options(),
        ]);
    }

    if ($method === 'POST') {
        ensure_post();
        verify_csrf();

        $input = json_input();
        $action = (string) ($input['action'] ?? $action);

        if ($action === 'profile') {
            if (!$isAdmin) {
                json_error('You do not have permission to update the barangay profile.', 403);
            }
            respond_result(save_barangay_profile($userId, $input));
        }

        if ($action === 'official') {
            if (!$isAdmin) {
                json_error('You do not have permission to add official accounts.', 403);
            }
            respond_result(create_official_account($userId, $input));
        }

        if ($action === 'account_profile') {
            respond_result(update_account_profile($userId, $input));
        }

        if ($action === 'password') {
            respond_result(change_account_password($userId, $input));
        }

        json_error('Unknown action.', 400);
    }

    json_error('Method not allowed.', 405);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to manage settings right now.', 500);
}
