<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

try {
    $userId = require_login_json();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        $year = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');
        if ($year < 2000 || $year > 2100) {
            json_error('Please provide a valid fiscal year.');
        }

        json_response([
            'ok' => true,
            'budget' => get_budget($year),
        ]);
    }

    if ($method === 'POST') {
        verify_csrf();
        $input = json_input();
        $currentYear = (int) date('Y');
        $year = (int) ($input['year'] ?? $currentYear);

        if ($year !== $currentYear) {
            json_error('Budget can only be added for the current year.', 422);
        }

        $amount = $input['amount'] ?? null;
        if (!is_numeric($amount)) {
            json_error('Enter an amount greater than zero.', 422);
        }

        $budget = add_budget_total($year, (float) $amount, $userId);
        json_response([
            'ok' => true,
            'budget' => $budget,
        ]);
    }

    json_error('Method not allowed.', 405);
} catch (InvalidArgumentException $exception) {
    json_error($exception->getMessage(), 422);
} catch (Throwable $exception) {
    log_line('app.log', $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    json_error('Unable to save the budget.', 500);
}
