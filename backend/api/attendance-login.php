<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    ensure_post();
    verify_csrf();
    $input = json_input();
    $result = sign_in_user((string) ($input['email'] ?? ''), (string) ($input['password'] ?? ''));
    if (($result['ok'] ?? false) !== true) {
        respond_result($result);
    }

    $roleCode = (string) ($result['user']['role_code'] ?? '');
    if (!in_array($roleCode, ['admin', 'captain', 'super_admin'], true)) {
        unset($_SESSION['user_id']);
        json_error('The attendance portal is for admin accounts only.', 403);
    }

    $result['redirect'] = url('attendance');
    respond_result($result);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to sign you in right now. Please try again.', 500);
}
