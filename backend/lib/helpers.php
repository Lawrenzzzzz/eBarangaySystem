<?php
declare(strict_types=1);

function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function json_error(string $message, int $status = 400, array $extra = []): never
{
    json_response(array_merge(['ok' => false, 'error' => $message], $extra), $status);
}

function respond_result(array $result): never
{
    if (($result['ok'] ?? false) !== true) {
        $status = (int) ($result['status'] ?? 400);
        $extra = $result;
        unset($extra['ok'], $extra['status'], $extra['error']);
        json_error((string) ($result['error'] ?? 'Request failed.'), $status, $extra);
    }
    unset($result['ok']);
    json_response(['ok' => true] + $result);
}

function json_input(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        json_error('Invalid JSON payload.', 400);
    }
    return $data;
}

function ensure_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        json_error('Method not allowed.', 405);
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $provided = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($provided) || $provided === '' || !hash_equals(csrf_token(), $provided)) {
        json_error('Your session security token is invalid. Refresh the page and try again.', 419);
    }
}

function client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return is_string($ip) && $ip !== '' ? substr($ip, 0, 45) : '0.0.0.0';
}

function user_agent(): ?string
{
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return is_string($agent) && $agent !== '' ? substr($agent, 0, 255) : null;
}

function log_line(string $file, string $message): void
{
    $dir = APP_STORAGE . '/logs';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $line = sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), client_ip(), $message);
    file_put_contents($dir . '/' . basename($file), $line, FILE_APPEND | LOCK_EX);
}

function normalize_phone(?string $raw): ?string
{
    $digits = preg_replace('/\D+/', '', (string) $raw);
    if ($digits === null || $digits === '') {
        return null;
    }
    if (strlen($digits) === 12 && str_starts_with($digits, '63')) {
        $digits = '0' . substr($digits, 2);
    } elseif (strlen($digits) === 10 && str_starts_with($digits, '9')) {
        $digits = '0' . $digits;
    }
    if (strlen($digits) !== 11 || !str_starts_with($digits, '09')) {
        return null;
    }
    return $digits;
}

function validate_password_error(string $password): ?string
{
    $length = strlen($password);
    if ($length < 8) {
        return 'Password must be at least 8 characters long.';
    }
    if ($length > 72) {
        return 'Password must be 72 characters or fewer.';
    }
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        return 'Password must contain at least one letter and one number.';
    }
    return null;
}

function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function require_login_json(): int
{
    $userId = current_user_id();
    if ($userId === null) {
        json_error('Please sign in to continue.', 401);
    }
    return $userId;
}

function require_login_page(string $redirect): void
{
    if (current_user_id() === null) {
        header('Location: ' . $redirect);
        exit;
    }
}

function token_hash(string $token): string
{
    return hash('sha256', APP_KEY . '|' . $token);
}

function random_token(int $bytes = 32): string
{
    return bin2hex(random_bytes($bytes));
}
