<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    require_login_json();

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        json_error('Method not allowed.', 405);
    }

    json_response([
        'ok' => true,
        'document_types' => document_types(),
    ]);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to load document types.', 500);
}
