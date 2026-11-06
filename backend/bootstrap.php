<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Manila');

define('APP_ROOT', dirname(__DIR__));
define('APP_BACKEND', __DIR__);
define('APP_STORAGE', __DIR__ . '/storage');

$envFile = __DIR__ . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        if (getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

function env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

$appKey = env('APP_KEY', '');
define('APP_ENV', env('APP_ENV', 'local'));
define('APP_KEY', $appKey === '' ? 'insecure-development-key' : $appKey);
define('APP_URL', rtrim((string) env('APP_URL', 'http://localhost/MyWebsites/eBarangay'), '/'));

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_name('ebarangay_session');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime', '28800');
    session_set_cookie_params([
        'lifetime' => 28800,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $secure,
    ]);
    session_start();
}

require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/mailer.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/documents.php';
require_once __DIR__ . '/lib/payments.php';
require_once __DIR__ . '/lib/blotter.php';
require_once __DIR__ . '/lib/residents.php';
require_once __DIR__ . '/lib/reports.php';
require_once __DIR__ . '/lib/announcements.php';
require_once __DIR__ . '/lib/budgets.php';
