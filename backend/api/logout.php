<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    ensure_post();
    verify_csrf();
    sign_out_user();
    json_response(['ok' => true, 'redirect' => APP_URL . '/frontend/login_page/index.php']);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to sign you out right now. Please try again.', 500);
}
