<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $user = null;
    $userId = current_user_id();
    if ($userId !== null) {
        $user = account_details($userId);
        if ($user === null) {
            unset($_SESSION['user_id']);
        }
    }

    json_response([
        'ok' => true,
        'authenticated' => $user !== null,
        'csrf' => csrf_token(),
        'user' => $user,
    ]);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to load your session.', 500);
}
