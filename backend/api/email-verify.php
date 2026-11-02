<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    ensure_post();
    verify_csrf();
    $input = json_input();
    respond_result(verify_email_code((int) ($input['verification_id'] ?? 0), (string) ($input['code'] ?? '')));
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to verify the code right now. Please try again.', 500);
}
