<?php
declare(strict_types=1);

function payment_amount_label(float $amount): string
{
    return '₱' . number_format($amount, $amount === floor($amount) ? 0 : 2);
}

function payment_status_label(string $status): string
{
    return match ($status) {
        'received' => 'Received',
        'pending' => 'Pending',
        'void' => 'Void',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
}

function payment_status_badge_class(string $status): string
{
    return match ($status) {
        'received' => 'text-bg-success',
        'pending' => 'text-bg-warning',
        'void' => 'text-bg-secondary',
        default => 'text-bg-secondary',
    };
}

function payment_method_label(string $method): string
{
    return match ($method) {
        'cash' => 'Cash',
        'online' => 'Online payment',
        default => ucfirst(str_replace('_', ' ', $method)),
    };
}

function payment_details(array $row): array
{
    $status = (string) $row['status'];
    $amount = (float) $row['amount'];
    $payee = trim((string) ($row['payee'] ?? ''));
    if ($payee === '') {
        $payee = 'Barangay Official';
    }
    $documentName = trim((string) ($row['document_name'] ?? ''));
    if ($documentName === '') {
        $documentName = 'Document request';
    }

    return [
        'id' => (int) $row['id'],
        'reference_no' => (string) $row['reference_no'],
        'request_reference' => (string) ($row['request_reference'] ?? ''),
        'payee' => $payee,
        'document_name' => $documentName,
        'payment_method' => (string) $row['payment_method'],
        'method_label' => payment_method_label((string) $row['payment_method']),
        'amount' => $amount,
        'amount_label' => payment_amount_label($amount),
        'status' => $status,
        'status_label' => payment_status_label($status),
        'badge_class' => payment_status_badge_class($status),
        'paid_at' => (string) $row['paid_at'],
        'search_text' => mb_strtolower(implode(' ', array_filter([
            (string) $row['reference_no'],
            (string) ($row['request_reference'] ?? ''),
            $payee,
            $documentName,
            payment_method_label((string) $row['payment_method']),
            payment_status_label($status),
        ], static fn ($value): bool => $value !== ''))),
    ];
}

function official_payment_query(): string
{
    return "SELECT p.*, dr.reference_no AS request_reference,
                   dt.name AS document_name
            FROM payments p
            LEFT JOIN document_requests dr ON dr.id = p.document_request_id
            LEFT JOIN document_types dt ON dt.id = dr.document_type_id";
}

function list_official_payments(?string $search = null, int $limit = 200): array
{
    $limit = max(1, min($limit, 500));
    $sql = official_payment_query();
    $params = [];

    $search = trim((string) $search);
    if ($search !== '') {
        $sql .= " WHERE p.reference_no LIKE ? OR dr.reference_no LIKE ? OR p.payee LIKE ?
                      OR dr.full_name LIKE ? OR dt.name LIKE ?";
        $like = '%' . $search . '%';
        array_push($params, $like, $like, $like, $like, $like);
    }

    $sql .= " ORDER BY p.paid_at DESC, p.id DESC LIMIT " . $limit;

    $statement = db()->prepare($sql);
    $statement->execute($params);

    return array_map('payment_details', $statement->fetchAll());
}

function official_payment_stats(): array
{
    $statement = db()->query(
        "SELECT
            COALESCE(SUM(CASE WHEN status = 'received' THEN amount ELSE 0 END), 0) AS collected_total,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) AS pending_total,
            SUM(status = 'pending') AS pending_count,
            SUM(status = 'received' AND paid_at >= DATE_FORMAT(NOW(), '%Y-%m-01')) AS month_count,
            SUM(paid_at >= (NOW() - INTERVAL WEEKDAY(NOW()) DAY)) AS week_count
         FROM payments"
    );
    $row = $statement->fetch() ?: [];

    $unpaidStatement = db()->query(
        "SELECT COALESCE(SUM(dr.fee), 0) AS unpaid_total, COUNT(*) AS unpaid_count
         FROM document_requests dr
         LEFT JOIN payments p ON p.document_request_id = dr.id
                              AND p.status IN ('received', 'pending')
         WHERE dr.fee > 0
           AND dr.status IN ('pending', 'processing', 'for_payment', 'ready')
           AND p.id IS NULL"
    );
    $unpaid = $unpaidStatement->fetch() ?: [];

    $freeStatement = db()->query(
        "SELECT COUNT(*)
         FROM document_requests
         WHERE fee <= 0
           AND status NOT IN ('rejected', 'cancelled')"
    );

    $collectedTotal = (float) ($row['collected_total'] ?? 0);
    $unpaidTotal = (float) ($unpaid['unpaid_total'] ?? 0);
    $unpaidCount = (int) ($unpaid['unpaid_count'] ?? 0);
    $pendingTotal = (float) ($row['pending_total'] ?? 0) + $unpaidTotal;

    return [
        'collected_total' => $collectedTotal,
        'collected_label' => payment_amount_label($collectedTotal),
        'pending_total' => $pendingTotal,
        'pending_label' => payment_amount_label($pendingTotal),
        'pending_count' => (int) ($row['pending_count'] ?? 0) + $unpaidCount,
        'unpaid_count' => $unpaidCount,
        'month_count' => (int) ($row['month_count'] ?? 0),
        'week_count' => (int) ($row['week_count'] ?? 0),
        'free_count' => (int) $freeStatement->fetchColumn(),
    ];
}
