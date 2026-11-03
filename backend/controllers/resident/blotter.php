<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/context.php';

extract(resident_page_context('../login_page/login.php'), EXTR_SKIP);

$blotterReports = list_blotter_reports((int) current_user_id(), null, 5);
$blotterExcerpt = static function (string $text, int $limit = 140): string {
    $length = function_exists('mb_strlen') ? mb_strlen($text) : strlen($text);
    if ($length <= $limit) {
        return $text;
    }
    $cut = function_exists('mb_substr') ? mb_substr($text, 0, $limit) : substr($text, 0, $limit);
    return rtrim($cut) . '…';
};
