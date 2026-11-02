<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    ensure_post();
    verify_csrf();
    $input = json_input();
    respond_result(sign_in_user((string) ($input['email'] ?? ''), (string) ($input['password'] ?? '')));
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to sign you in right now. Please try again.', 500);
}
