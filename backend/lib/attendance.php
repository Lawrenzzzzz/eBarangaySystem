<?php
declare(strict_types=1);

const ATTENDANCE_OPERATOR_ROLES = ['admin', 'captain', 'super_admin'];

function attendance_payload(int $userId): string
{
    return 'att:' . $userId . ':' . hash_hmac('sha256', 'attendance|' . $userId, APP_KEY);
}

function attendance_verify(string $payload): ?int
{
    if (preg_match('/^att:(\d+):([0-9a-f]{64})$/i', trim($payload), $matches) !== 1) {
        return null;
    }
    $expected = hash_hmac('sha256', 'attendance|' . $matches[1], APP_KEY);
    if (!hash_equals($expected, strtolower($matches[2]))) {
        return null;
    }
    return (int) $matches[1];
}

function attendance_account_eligible(array $account): bool
{
    return ($account['account_type'] ?? '') === 'official'
        && ($account['account_status'] ?? '') === 'active'
        && !in_array((string) ($account['role_code'] ?? ''), ATTENDANCE_OPERATOR_ROLES, true);
}

function attendance_account(int $userId): ?array
{
    $statement = db()->prepare(
        "SELECT v.user_id, v.role_code, v.role_name, v.account_type, v.account_status,
                v.requested_position, v.employee_number,
                COALESCE(NULLIF(v.full_name, ''), NULLIF(u.full_name, ''), u.email) AS display_name
         FROM v_user_accounts v
         JOIN users u ON u.id = v.user_id
         WHERE v.user_id = ?
         LIMIT 1"
    );
    $statement->execute([$userId]);
    $row = $statement->fetch();
    return $row === false ? null : $row;
}

function attendance_position_label(array $account): string
{
    $position = trim((string) ($account['requested_position'] ?? ''));
    if ($position !== '') {
        return $position;
    }
    $roleName = trim((string) ($account['role_name'] ?? ''));
    return $roleName !== '' ? $roleName : 'Barangay Official';
}

function attendance_scan(int $userId, int $scannedByUserId): array
{
    $account = attendance_account($userId);
    if ($account === null) {
        return ['ok' => false, 'status' => 404, 'error' => 'No barangay account matches this code.'];
    }
    if (!attendance_account_eligible($account)) {
        return [
            'ok' => false,
            'status' => 422,
            'error' => $account['display_name'] . ' is not eligible for attendance tracking.',
        ];
    }

    $today = date('Y-m-d');
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $statement = $pdo->prepare(
            "SELECT time_in, time_out FROM attendance_records WHERE user_id = ? AND work_date = ? FOR UPDATE"
        );
        $statement->execute([$userId, $today]);
        $record = $statement->fetch();

        if ($record === false) {
            $insert = $pdo->prepare(
                "INSERT INTO attendance_records (user_id, work_date, time_in, scanned_in_by)
                 VALUES (?, ?, NOW(), ?)"
            );
            $insert->execute([$userId, $today, $scannedByUserId]);
            $pdo->commit();

            return [
                'ok' => true,
                'action' => 'in',
                'name' => $account['display_name'],
                'position' => attendance_position_label($account),
                'time' => date('g:i A'),
                'message' => $account['display_name'] . ' timed in at ' . date('g:i A') . '.',
            ];
        }

        $timeIn = strtotime((string) $record['time_in']);

        if (!isset($record['time_out'])) {
            $update = $pdo->prepare(
                "UPDATE attendance_records SET time_out = NOW(), scanned_out_by = ? WHERE user_id = ? AND work_date = ?"
            );
            $update->execute([$scannedByUserId, $userId, $today]);
            $pdo->commit();

            return [
                'ok' => true,
                'action' => 'out',
                'name' => $account['display_name'],
                'position' => attendance_position_label($account),
                'time' => date('g:i A'),
                'time_in' => date('g:i A', $timeIn),
                'message' => $account['display_name'] . ' timed out at ' . date('g:i A') . '.',
            ];
        }

        $pdo->rollBack();
        return [
            'ok' => false,
            'status' => 409,
            'error' => $account['display_name'] . ' already completed attendance today ('
                . date('g:i A', $timeIn) . ' - ' . date('g:i A', strtotime((string) $record['time_out'])) . ').',
        ];
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}

function attendance_hours(?string $timeIn, ?string $timeOut): ?string
{
    if ($timeIn === null || $timeOut === null) {
        return null;
    }
    $seconds = strtotime($timeOut) - strtotime($timeIn);
    if ($seconds < 0) {
        $seconds = 0;
    }
    $hours = intdiv($seconds, 3600);
    $minutes = intdiv($seconds % 3600, 60);
    return $hours > 0 ? $hours . 'h ' . $minutes . 'm' : $minutes . 'm';
}

function attendance_normalize_date(?string $date): string
{
    $date = trim((string) $date);
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    if ($parsed === false || $parsed->format('Y-m-d') !== $date) {
        return date('Y-m-d');
    }
    return $date;
}

function attendance_official_accounts(): array
{
    $operatorPlaceholders = implode(', ', array_fill(0, count(ATTENDANCE_OPERATOR_ROLES), '?'));

    $statement = db()->prepare(
        "SELECT v.user_id, v.role_code, v.role_name, v.requested_position, v.employee_number,
                COALESCE(NULLIF(v.full_name, ''), NULLIF(u.full_name, ''), u.email) AS display_name
         FROM v_user_accounts v
         JOIN users u ON u.id = v.user_id
         WHERE v.account_type = 'official'
           AND v.account_status = 'active'
           AND v.role_code NOT IN ({$operatorPlaceholders})
         ORDER BY display_name ASC"
    );
    $statement->execute(ATTENDANCE_OPERATOR_ROLES);

    return array_map(static function (array $row): array {
        return [
            'user_id' => (int) $row['user_id'],
            'name' => (string) $row['display_name'],
            'position' => attendance_position_label($row),
            'employee_number' => (string) ($row['employee_number'] ?? ''),
        ];
    }, $statement->fetchAll());
}

function attendance_officials_for_date(string $date, ?int $officialId = null): array
{
    $date = attendance_normalize_date($date);
    $operatorPlaceholders = implode(', ', array_fill(0, count(ATTENDANCE_OPERATOR_ROLES), '?'));

    $conditions = [
        "v.account_type = 'official'",
        "v.account_status = 'active'",
        "v.role_code NOT IN ({$operatorPlaceholders})",
    ];
    $params = array_merge([$date], ATTENDANCE_OPERATOR_ROLES);

    if ($officialId !== null && $officialId > 0) {
        $conditions[] = 'v.user_id = ?';
        $params[] = $officialId;
    } else {
        $conditions[] = 'ar.time_in IS NOT NULL';
    }

    $where = implode(' AND ', $conditions);

    $statement = db()->prepare(
        "SELECT v.user_id, v.role_code, v.role_name, v.requested_position, v.employee_number,
                COALESCE(NULLIF(v.full_name, ''), NULLIF(u.full_name, ''), u.email) AS display_name,
                ar.time_in, ar.time_out
         FROM v_user_accounts v
         JOIN users u ON u.id = v.user_id
         LEFT JOIN attendance_records ar ON ar.user_id = v.user_id AND ar.work_date = ?
         WHERE {$where}
         ORDER BY display_name ASC"
    );
    $statement->execute($params);

    $officials = [];
    foreach ($statement->fetchAll() as $row) {
        $timeIn = $row['time_in'] !== null ? (string) $row['time_in'] : null;
        $timeOut = $row['time_out'] !== null ? (string) $row['time_out'] : null;
        $hours = null;
        if ($timeIn !== null && $timeOut !== null) {
            $seconds = max(strtotime($timeOut) - strtotime($timeIn), 0);
            $hours = round($seconds / 3600, 2);
        }

        $officials[] = [
            'user_id' => (int) $row['user_id'],
            'name' => (string) $row['display_name'],
            'position' => attendance_position_label($row),
            'employee_number' => (string) ($row['employee_number'] ?? ''),
            'work_date' => $date,
            'work_date_label' => date('M j, Y', strtotime($date)),
            'time_in' => $timeIn !== null ? date('g:i A', strtotime($timeIn)) : null,
            'time_out' => $timeOut !== null ? date('g:i A', strtotime($timeOut)) : null,
            'hours' => $hours,
            'hours_label' => attendance_hours($timeIn, $timeOut),
        ];
    }

    return $officials;
}

function attendance_official_history(int $officialId): array
{
    if ($officialId < 1) {
        return [];
    }

    $operatorPlaceholders = implode(', ', array_fill(0, count(ATTENDANCE_OPERATOR_ROLES), '?'));

    $statement = db()->prepare(
        "SELECT v.user_id, v.role_code, v.role_name, v.requested_position, v.employee_number,
                COALESCE(NULLIF(v.full_name, ''), NULLIF(u.full_name, ''), u.email) AS display_name,
                ar.work_date, ar.time_in, ar.time_out
         FROM attendance_records ar
         JOIN v_user_accounts v ON v.user_id = ar.user_id
         JOIN users u ON u.id = ar.user_id
         WHERE ar.user_id = ?
           AND ar.time_in IS NOT NULL
           AND v.account_type = 'official'
           AND v.account_status = 'active'
           AND v.role_code NOT IN ({$operatorPlaceholders})
         ORDER BY ar.work_date DESC, ar.time_in DESC"
    );
    $statement->execute(array_merge([$officialId], ATTENDANCE_OPERATOR_ROLES));

    $officials = [];
    foreach ($statement->fetchAll() as $row) {
        $timeIn = $row['time_in'] !== null ? (string) $row['time_in'] : null;
        $timeOut = $row['time_out'] !== null ? (string) $row['time_out'] : null;
        $hours = null;
        if ($timeIn !== null && $timeOut !== null) {
            $seconds = max(strtotime($timeOut) - strtotime($timeIn), 0);
            $hours = round($seconds / 3600, 2);
        }

        $workDate = (string) $row['work_date'];

        $officials[] = [
            'user_id' => (int) $row['user_id'],
            'name' => (string) $row['display_name'],
            'position' => attendance_position_label($row),
            'employee_number' => (string) ($row['employee_number'] ?? ''),
            'work_date' => $workDate,
            'work_date_label' => date('M j, Y', strtotime($workDate)),
            'time_in' => $timeIn !== null ? date('g:i A', strtotime($timeIn)) : null,
            'time_out' => $timeOut !== null ? date('g:i A', strtotime($timeOut)) : null,
            'hours' => $hours,
            'hours_label' => attendance_hours($timeIn, $timeOut),
        ];
    }

    return $officials;
}

function attendance_today(): array
{
    $statement = db()->prepare(
        "SELECT ar.user_id, ar.time_in, ar.time_out,
                COALESCE(NULLIF(v.full_name, ''), NULLIF(u.full_name, ''), u.email) AS display_name,
                v.requested_position, v.role_name
         FROM attendance_records ar
         JOIN v_user_accounts v ON v.user_id = ar.user_id
         JOIN users u ON u.id = ar.user_id
         WHERE ar.work_date = ?
         ORDER BY ar.time_in ASC"
    );
    $statement->execute([date('Y-m-d')]);

    $records = [];
    foreach ($statement->fetchAll() as $row) {
        $records[] = [
            'user_id' => (int) $row['user_id'],
            'name' => (string) $row['display_name'],
            'position' => attendance_position_label($row),
            'time_in' => date('g:i A', strtotime((string) $row['time_in'])),
            'time_out' => isset($row['time_out']) ? date('g:i A', strtotime((string) $row['time_out'])) : null,
            'hours' => attendance_hours(
                isset($row['time_in']) ? (string) $row['time_in'] : null,
                isset($row['time_out']) ? (string) $row['time_out'] : null
            ),
        ];
    }

    return $records;
}
