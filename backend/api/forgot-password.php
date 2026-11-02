<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    ensure_post();
    verify_csrf();
    $input = json_input();
    respond_result(request_password_reset((string) ($input['email'] ?? '')));
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to process your password reset request right now. Please try again.', 500);
}
