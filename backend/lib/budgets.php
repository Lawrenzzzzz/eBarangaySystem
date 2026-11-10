<?php
declare(strict_types=1);

function budget_sectors(): array
{
    return [
        'Infrastructure',
        'Healthcare',
        'Education',
        'Social Services',
        'Disaster and P&O',
    ];
}

function budget_row(int $year): ?array
{
    $statement = db()->prepare('SELECT id, fiscal_year, total_budget, allocation_locked_at FROM budgets WHERE fiscal_year = ? LIMIT 1');
    $statement->execute([$year]);
    $row = $statement->fetch();

    return $row === false ? null : $row;
}

function ensure_budget(int $year, ?int $userId = null): array
{
    $budget = budget_row($year);
    if ($budget !== null) {
        return $budget;
    }

    $statement = db()->prepare('INSERT INTO budgets (fiscal_year, total_budget, created_by) VALUES (?, 0, ?)');
    $statement->execute([$year, $userId]);

    $budget = budget_row($year);
    if ($budget === null) {
        throw new RuntimeException('Unable to create the budget record.');
    }

    return $budget;
}

function get_budget(int $year): array
{
    $allocations = array_fill_keys(budget_sectors(), 0.0);
    $disbursements = array_fill_keys(budget_sectors(), 0.0);
    $budget = budget_row($year);
    $total = 0.0;

    if ($budget !== null) {
        $statement = db()->prepare('SELECT sector, amount, disbursed_amount FROM budget_allocations WHERE budget_id = ?');
        $statement->execute([(int) $budget['id']]);
        foreach ($statement->fetchAll() as $row) {
            $sector = (string) $row['sector'];
            if (array_key_exists($sector, $allocations)) {
                $allocations[$sector] = (float) $row['amount'];
                $disbursements[$sector] = (float) $row['disbursed_amount'];
            }
        }
        $total = (float) $budget['total_budget'];
    }

    $allocated = array_sum($allocations);

    return [
        'year' => $year,
        'saved' => $budget !== null,
        'allocation_locked' => $budget !== null && !empty($budget['allocation_locked_at']),
        'allocation_locked_at' => $budget['allocation_locked_at'] ?? null,
        'total_budget' => $total,
        'allocations' => $allocations,
        'disbursed_allocations' => $disbursements,
        'allocated' => $allocated,
        'remaining' => $total - $allocated,
        'disbursed_total' => array_sum($disbursements),
    ];
}

function normalize_budget_allocations(array $allocations): array
{
    $normalized = [];
    foreach (budget_sectors() as $sector) {
        $amount = $allocations[$sector] ?? 0;
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException('Please provide a valid amount for ' . $sector . '.');
        }
        $amount = (float) $amount;
        if ($amount < 0) {
            throw new InvalidArgumentException('The allocation for ' . $sector . ' cannot be negative.');
        }
        $normalized[$sector] = round($amount, 2);
    }

    return $normalized;
}

function add_budget_total(int $year, float $amount, ?int $userId = null): array
{
    if ($amount <= 0) {
        throw new InvalidArgumentException('Enter an amount greater than zero.');
    }

    $budget = ensure_budget($year, $userId);
    $statement = db()->prepare('UPDATE budgets SET total_budget = total_budget + ? WHERE id = ?');
    $statement->execute([$amount, (int) $budget['id']]);

    return get_budget($year);
}

function save_budget_allocations(int $year, array $allocations, ?int $userId = null): array
{
    $allocations = normalize_budget_allocations($allocations);
    $budget = ensure_budget($year, $userId);
    if (!empty($budget['allocation_locked_at'])) {
        throw new InvalidArgumentException('The budget allocation for this year has already been saved and can no longer be edited.');
    }

    $total = (float) $budget['total_budget'];
    $allocated = array_sum($allocations);

    if ($allocated > $total + 0.001) {
        throw new InvalidArgumentException('The total allocation exceeds the available budget.');
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $lockCheck = $pdo->prepare('SELECT allocation_locked_at FROM budgets WHERE id = ? FOR UPDATE');
        $lockCheck->execute([(int) $budget['id']]);
        if (!empty($lockCheck->fetchColumn())) {
            $pdo->rollBack();
            throw new InvalidArgumentException('The budget allocation for this year has already been saved and can no longer be edited.');
        }

        $statement = $pdo->prepare(
            'INSERT INTO budget_allocations (budget_id, sector, amount)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE amount = VALUES(amount)'
        );

        foreach ($allocations as $sector => $amount) {
            $statement->execute([(int) $budget['id'], $sector, $amount]);
        }

        $pdo->prepare('UPDATE budgets SET allocation_locked_at = NOW() WHERE id = ?')->execute([(int) $budget['id']]);

        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }

    return get_budget($year);
}
