<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $userId = require_login_json();
    $user = account_details($userId);
    if ($user === null) {
        unset($_SESSION['user_id']);
        json_error('Your account could not be found. Please sign in again.', 401);
    }
    json_response(['ok' => true, 'user' => $user]);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to load your account.', 500);
}
