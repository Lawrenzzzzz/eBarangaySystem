<?php
declare(strict_types=1);

function admin_dashboard_money_label(float $amount): string
{
    return '₱' . number_format($amount, 0);
}

function admin_dashboard_relative_time(?string $datetime): string
{
    if ($datetime === null || trim($datetime) === '') {
        return 'Never';
    }

    $timestamp = strtotime($datetime);
    if ($timestamp === false) {
        return 'Never';
    }

    $elapsed = time() - $timestamp;
    if ($elapsed < 60) {
        return 'Just now';
    }
    if ($elapsed < 3600) {
        $minutes = (int) floor($elapsed / 60);
        return $minutes . ' min' . ($minutes === 1 ? '' : 's') . ' ago';
    }
    if ($elapsed < 86400) {
        $hours = (int) floor($elapsed / 3600);
        return $hours . ' hr' . ($hours === 1 ? '' : 's') . ' ago';
    }
    if ($elapsed < 604800) {
        $days = (int) floor($elapsed / 86400);
        return $days . ' day' . ($days === 1 ? '' : 's') . ' ago';
    }

    return date('M j, Y', $timestamp);
}

function admin_recent_official_logins(int $limit = 8): array
{
    $limit = max(1, min($limit, 25));

    $statement = db()->prepare(
        "SELECT
            u.id AS user_id,
            u.email,
            u.full_name AS account_name,
            u.last_login_at,
            r.name AS role_name,
            r.code AS role_code,
            res.full_name AS resident_name,
            oa.requested_position,
            oa.employee_number
         FROM users u
         JOIN roles r ON r.id = u.role_id
         LEFT JOIN residents res ON res.user_id = u.id
         LEFT JOIN official_applications oa ON oa.id = (
             SELECT MAX(latest.id) FROM official_applications latest WHERE latest.user_id = u.id
         )
         WHERE u.account_type = 'official'
           AND u.deleted_at IS NULL
           AND u.last_login_at IS NOT NULL
         ORDER BY u.last_login_at DESC
         LIMIT {$limit}"
    );
    $statement->execute();

    return array_map(static function (array $row): array {
        $name = trim((string) ($row['account_name'] ?? ''));
        if ($name === '') {
            $name = trim((string) ($row['resident_name'] ?? ''));
        }
        if ($name === '') {
            $name = (string) $row['email'];
        }

        $position = trim((string) ($row['requested_position'] ?? ''));
        if ($position === '') {
            $position = trim((string) ($row['role_name'] ?? '')) ?: 'Barangay Official';
        }

        $lastLoginAt = (string) $row['last_login_at'];
        $lastLoginTimestamp = strtotime($lastLoginAt);

        return [
            'user_id' => (int) $row['user_id'],
            'name' => $name,
            'email' => (string) $row['email'],
            'position' => $position,
            'role_code' => (string) ($row['role_code'] ?? ''),
            'employee_number' => (string) ($row['employee_number'] ?? ''),
            'last_login_at' => $lastLoginAt,
            'last_login_label' => $lastLoginTimestamp !== false ? date('M j, Y g:i A', $lastLoginTimestamp) : '',
            'last_login_relative' => admin_dashboard_relative_time($lastLoginAt),
        ];
    }, $statement->fetchAll());
}

function admin_dashboard_data(): array
{
    $year = (int) date('Y');
    $budget = get_budget($year);
    $residents = official_resident_demographics();
    $documents = official_document_request_stats();
    $blotter = official_blotter_stats();
    $payments = official_payment_stats();
    $projects = list_projects();
    $announcements = list_announcements();
    $recentOfficialLogins = admin_recent_official_logins();

    $totalBudget = (float) $budget['total_budget'];
    $disbursed = (float) $budget['disbursed_total'];
    $remaining = max($totalBudget - $disbursed, 0.0);
    $utilization = $totalBudget > 0 ? round(($disbursed / $totalBudget) * 100, 1) : 0.0;

    $sectors = [];
    foreach (budget_sectors() as $sector) {
        $allocated = (float) ($budget['allocations'][$sector] ?? 0);
        $sectorDisbursed = (float) ($budget['disbursed_allocations'][$sector] ?? 0);
        $sectors[] = [
            'name' => $sector,
            'allocated' => $allocated,
            'disbursed' => $sectorDisbursed,
            'remaining' => max($allocated - $sectorDisbursed, 0.0),
            'utilization' => $allocated > 0 ? round(($sectorDisbursed / $allocated) * 100, 1) : 0.0,
        ];
    }

    $projectStats = [
        'total' => 0,
        'planned' => 0,
        'ongoing' => 0,
        'completed' => 0,
        'budget_total' => 0.0,
        'completed_budget' => 0.0,
    ];
    foreach ($projects as $project) {
        $projectStats['total']++;
        $projectStats['budget_total'] += (float) $project['budget'];
        $status = (string) $project['status'];
        if (isset($projectStats[$status])) {
            $projectStats[$status]++;
        }
        if ($status === 'completed') {
            $projectStats['completed_budget'] += (float) $project['budget'];
        }
    }
    $projectStats['completion_rate'] = $projectStats['total'] > 0
        ? round(($projectStats['completed'] / $projectStats['total']) * 100, 1)
        : 0.0;

    $blotter['resolved_rate'] = $blotter['total'] > 0
        ? round(($blotter['resolved'] / $blotter['total']) * 100, 1)
        : 0.0;

    return [
        'generated_at' => date(DATE_ATOM),
        'generated_timestamp' => time(),
        'generated_label' => date('M j, Y g:i:s A'),
        'budget' => [
            'year' => $year,
            'total' => $totalBudget,
            'total_label' => admin_dashboard_money_label($totalBudget),
            'allocated' => (float) $budget['allocated'],
            'allocated_label' => admin_dashboard_money_label((float) $budget['allocated']),
            'disbursed' => $disbursed,
            'disbursed_label' => admin_dashboard_money_label($disbursed),
            'remaining' => $remaining,
            'remaining_label' => admin_dashboard_money_label($remaining),
            'utilization' => $utilization,
            'sectors' => $sectors,
        ],
        'residents' => $residents,
        'documents' => $documents,
        'blotter' => $blotter,
        'payments' => $payments,
        'projects' => $projectStats,
        'announcements' => [
            'total' => count($announcements),
            'latest_title' => $announcements[0]['title'] ?? '',
            'latest_at' => $announcements[0]['created_at'] ?? null,
        ],
        'recent_official_logins' => $recentOfficialLogins,
    ];
}
