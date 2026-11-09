<?php
declare(strict_types=1);

function admin_dashboard_money_label(float $amount): string
{
    return '₱' . number_format($amount, 0);
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
    ];
}
