<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/context.php';

extract(resident_page_context('../login_page/login.php'), EXTR_SKIP);

$projects = list_projects();

$projectStats = [
    'total' => count($projects),
    'planned' => 0,
    'ongoing' => 0,
    'completed' => 0,
];

$statusTones = [
    'planned' => 'info',
    'ongoing' => 'warn',
    'completed' => 'good',
];

$sectorTones = [
    'Infrastructure' => 'blue',
    'Education' => 'green',
    'Health' => 'amber',
    'Social Services' => 'green',
    'Disaster & P&O' => 'amber',
];

foreach ($projects as $project) {
    $status = (string) ($project['status'] ?? 'planned');
    if (isset($projectStats[$status])) {
        $projectStats[$status]++;
    }
}

$formatPeso = static function (float $amount): string {
    $decimals = abs($amount - round($amount)) < 0.005 ? 0 : 2;
    return '₱' . number_format($amount, $decimals);
};

$formatDate = static function ($value): string {
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    $timestamp = strtotime($value);

    return $timestamp === false ? $value : date('M j, Y', $timestamp);
};

$projects = array_map(static function (array $project) use ($formatPeso, $formatDate): array {
    $start = $formatDate($project['start_date'] ?? '');
    $target = $formatDate($project['target_date'] ?? '');

    if ($start !== '' && $target !== '') {
        $timeline = $start . ' – ' . $target;
    } elseif ($start !== '') {
        $timeline = 'Started ' . $start;
    } elseif ($target !== '') {
        $timeline = 'Target ' . $target;
    } else {
        $timeline = 'Timeline not set';
    }

    $project['budget_display'] = $formatPeso((float) $project['budget']);
    $project['timeline_display'] = $timeline;
    $project['updated_display'] = $formatDate($project['updated_at'] ?? '');

    return $project;
}, $projects);
