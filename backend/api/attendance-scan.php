<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $operatorId = require_login_json();
    $operator = account_details($operatorId);
    if ($operator === null || !in_array($operator['role_code'] ?? '', ATTENDANCE_OPERATOR_ROLES, true)) {
        json_error('The attendance portal is for administrator accounts only.', 403);
    }

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
        json_response([
            'ok' => true,
            'date_label' => date('l, F j, Y'),
            'records' => attendance_today(),
        ]);
    }

    ensure_post();
    verify_csrf();
    $input = json_input();

    $payload = trim((string) ($input['payload'] ?? ''));
    if ($payload === '') {
        json_error('Scan a QR code to record attendance.', 422);
    }

    $scannedUserId = attendance_verify($payload);
    if ($scannedUserId === null) {
        json_error('This QR code is not a valid e-Barangay attendance code.', 422);
    }

    respond_result(attendance_scan($scannedUserId, $operatorId));
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to record attendance right now. Please try again.', 500);
}
