<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $userId = require_login_json();

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        verify_csrf();
        respond_result(save_resident_profile($userId, json_input()));
    }

    json_response(['ok' => true] + resident_profile_payload($userId));
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to load your profile right now.', 500);
}
