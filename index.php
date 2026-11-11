<?php
declare(strict_types=1);

/*
 * e-Barangay front controller.
 *
 * Every request is routed through this file (see .htaccess). URLs stay clean
 * and never expose the folder layout, e.g.:
 *
 *   /login                 -> frontend/login_page/login.php
 *   /dashboard             -> frontend/user_pages/index.php
 *   /admin/residents       -> frontend/admin/residents.php
 *   /official/blotter     -> frontend/barangay_officials/blotter.php
 *   /api/login             -> backend/api/login.php
 */

require_once __DIR__ . '/backend/bootstrap.php';

$requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
$requestPath = (string) (parse_url($requestUri, PHP_URL_PATH) ?: '/');
if (BASE_PATH !== '' && str_starts_with($requestPath, BASE_PATH)) {
    $requestPath = substr($requestPath, strlen(BASE_PATH));
}
$requestPath = trim(rawurldecode($requestPath), '/');
if ($requestPath === 'index.php') {
    $requestPath = '';
}

$routes = [
    // Public
    '' => '/frontend/login_page/index.php',
    'login' => '/frontend/login_page/login.php',
    'signup' => '/frontend/login_page/signup.php',
    'reset-password' => '/frontend/login_page/reset-password.php',

    // Resident portal
    'dashboard' => '/frontend/user_pages/index.php',
    'documents' => '/frontend/user_pages/documents.php',
    'track' => '/frontend/user_pages/track.php',
    'announcements' => '/frontend/user_pages/announcements.php',
    'projects' => '/frontend/user_pages/projects.php',
    'blotter' => '/frontend/user_pages/blotter.php',
    'blotter-report' => '/frontend/user_pages/blotter-report.php',
    'settings' => '/frontend/user_pages/settings.php',

    // Admin portal
    'admin' => '/frontend/admin/index.php',
    'admin/dashboard' => '/frontend/admin/dashboard.php',
    'admin/residents' => '/frontend/admin/residents.php',
    'admin/attendance' => '/frontend/admin/attendance.php',
    'admin/budget' => '/frontend/admin/budget.php',
    'admin/reports' => '/frontend/admin/reports.php',
    'admin/projects' => '/frontend/admin/projects.php',
    'admin/settings' => '/frontend/admin/settings.php',
    'admin/change-password' => '/frontend/admin/change-password.php',
    'admin/add-official-account' => '/frontend/admin/add-official-account.php',

    // Barangay officials portal
    'official/dashboard' => '/frontend/barangay_officials/dashboard.php',
    'official/residents' => '/frontend/barangay_officials/residents.php',
    'official/document-requests' => '/frontend/barangay_officials/document-requests.php',
    'official/document-fees' => '/frontend/barangay_officials/document-fees.php',
    'official/blotter' => '/frontend/barangay_officials/blotter.php',
    'official/announcements' => '/frontend/barangay_officials/announcements.php',
    'official/payments' => '/frontend/barangay_officials/payments.php',
    'official/reports' => '/frontend/barangay_officials/reports.php',
    'official/change-password' => '/frontend/barangay_officials/change-password.php',
    'official/settings' => '/frontend/barangay_officials/settings.php',

    // Attendance portal
    'attendance' => '/frontend/attendance/index.php',
    'attendance/login' => '/frontend/attendance/login.php',
];

if ($requestPath !== '' && str_starts_with($requestPath, 'api/')) {
    $endpoint = substr($requestPath, strlen('api/'));
    $endpointFile = __DIR__ . '/backend/api/' . $endpoint . '.php';
    if (preg_match('/^[a-z0-9-]+$/', $endpoint) === 1 && is_file($endpointFile)) {
        require $endpointFile;
        exit;
    }
    json_error('The requested endpoint does not exist.', 404);
}

$routeFile = $routes[$requestPath] ?? null;
if ($routeFile === null || !is_file(__DIR__ . $routeFile)) {
    http_response_code(404);
    require __DIR__ . '/frontend/login_page/404.php';
    exit;
}

require __DIR__ . $routeFile;
