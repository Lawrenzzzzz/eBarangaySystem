<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $userId = require_login_json();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        $search = isset($_GET['q']) ? (string) $_GET['q'] : null;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
        $requests = list_document_requests($userId, $search, $limit);

        json_response([
            'ok' => true,
            'requests' => $requests,
            'count' => count($requests),
            'stats' => document_request_stats($userId),
        ]);
    }

    if ($method === 'POST') {
        ensure_post();
        verify_csrf();
        respond_result(create_document_request($userId, json_input()));
    }

    json_error('Method not allowed.', 405);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to process your document request right now. Please try again.', 500);
}
