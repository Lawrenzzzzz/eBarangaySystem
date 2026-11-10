<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $userId = require_login_json();
    ensure_post();
    verify_csrf();

    $input = json_input();
    $year = (int) ($input['year'] ?? date('Y'));
    if ($year < 2000 || $year > 2100) {
        json_error('Please provide a valid fiscal year.');
    }

    $allocations = normalize_budget_allocations($input['allocations'] ?? []);
    $budget = get_budget($year);
    $total = (float) $budget['total_budget'];
    $allocated = array_sum($allocations);
    $remaining = $total - $allocated;

    if (!empty($input['save'])) {
        $saved = save_budget_allocations($year, $allocations, $userId);
        json_response([
            'ok' => true,
            'saved' => true,
            'allocation_locked' => $saved['allocation_locked'],
            'allocation_locked_at' => $saved['allocation_locked_at'],
            'total_budget' => $saved['total_budget'],
            'allocated' => $saved['allocated'],
            'remaining' => $saved['remaining'],
            'over_allocated' => $saved['remaining'] < 0,
            'allocations' => $saved['allocations'],
        ]);
    }

    json_response([
        'ok' => true,
        'saved' => false,
        'allocation_locked' => $budget['allocation_locked'],
        'allocation_locked_at' => $budget['allocation_locked_at'],
        'total_budget' => $total,
        'allocated' => $allocated,
        'remaining' => $remaining,
        'over_allocated' => $remaining < 0,
        'allocations' => $allocations,
    ]);
} catch (InvalidArgumentException $exception) {
    json_error($exception->getMessage(), 422);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to compute the budget division.', 500);
}
