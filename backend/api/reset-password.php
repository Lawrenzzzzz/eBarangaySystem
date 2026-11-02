<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    ensure_post();
    verify_csrf();
    $input = json_input();
    respond_result(complete_password_reset(
        (string) ($input['token'] ?? ''),
        (string) ($input['password'] ?? ''),
        (string) ($input['confirmPassword'] ?? '')
    ));
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to reset your password right now. Please try again.', 500);
}
