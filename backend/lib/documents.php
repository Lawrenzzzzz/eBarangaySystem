<?php
declare(strict_types=1);

function document_types(): array
{
    $statement = db()->query(
        "SELECT id, code, name, description, fee, processing_days
         FROM document_types
         WHERE is_active = 1
         ORDER BY sort_order, id"
    );

    return array_map('document_type_details', $statement->fetchAll());
}

function document_type_by_code(string $code): ?array
{
    $statement = db()->prepare(
        "SELECT id, code, name, description, fee, processing_days
         FROM document_types
         WHERE code = ? AND is_active = 1
         LIMIT 1"
    );
    $statement->execute([$code]);
    $row = $statement->fetch();

    return $row ? document_type_details($row) : null;
}

function document_type_details(array $row): array
{
    $fee = (float) $row['fee'];
    $processingDays = (int) $row['processing_days'];
    $feeLabel = $fee <= 0 ? 'Free' : '₱' . number_format($fee, $fee === floor($fee) ? 0 : 2);
    $processingLabel = match (true) {
        $processingDays <= 0 => 'same day',
        $processingDays === 1 => '1 business day',
        default => $processingDays . ' business days',
    };

    return [
        'id' => (int) $row['id'],
        'code' => (string) $row['code'],
        'name' => (string) $row['name'],
        'description' => (string) $row['description'],
        'fee' => $fee,
        'fee_label' => $feeLabel,
        'processing_days' => $processingDays,
        'processing_label' => $processingLabel,
        'summary' => $feeLabel . ' · ' . $processingLabel,
    ];
}

function document_request_status_label(string $status): string
{
    return match ($status) {
        'pending' => 'Pending review',
        'processing' => 'Processing',
        'for_payment' => 'Ready for payment',
        'ready' => 'Ready for pickup',
        'released' => 'Released',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
}

function document_request_status_tone(string $status): string
{
    return match ($status) {
        'pending', 'for_payment' => 'warn',
        'processing' => 'info',
        'ready', 'released' => 'good',
        default => '',
    };
}

function document_request_progress(string $status): int
{
    return match ($status) {
        'pending' => 1,
        'processing', 'for_payment' => 2,
        'ready', 'released' => 3,
        default => 0,
    };
}

function document_request_details(array $row): array
{
    $status = (string) $row['status'];

    return [
        'id' => (int) $row['id'],
        'reference_no' => (string) $row['reference_no'],
        'document_type' => (string) $row['document_type_code'],
        'document_name' => (string) $row['document_name'],
        'document_description' => (string) $row['document_description'],
        'full_name' => (string) $row['full_name'],
        'resident_code' => (string) $row['resident_code'],
        'address_line' => (string) $row['address_line'],
        'household_number' => $row['household_number'] !== null ? (string) $row['household_number'] : null,
        'purpose' => (string) $row['purpose'],
        'fee' => (float) $row['fee'],
        'processing_days' => (int) $row['processing_days'],
        'status' => $status,
        'status_label' => document_request_status_label($status),
        'status_tone' => document_request_status_tone($status),
        'progress' => document_request_progress($status),
        'remarks' => $row['remarks'] !== null ? (string) $row['remarks'] : null,
        'created_at' => (string) $row['created_at'],
        'updated_at' => (string) $row['updated_at'],
        'reviewed_at' => $row['reviewed_at'] !== null ? (string) $row['reviewed_at'] : null,
        'released_at' => $row['released_at'] !== null ? (string) $row['released_at'] : null,
    ];
}

function document_request_query(): string
{
    return "SELECT dr.*, dt.code AS document_type_code, dt.name AS document_name,
                   dt.description AS document_description, dt.processing_days
            FROM document_requests dr
            JOIN document_types dt ON dt.id = dr.document_type_id";
}

function document_request_by_id(int $requestId): ?array
{
    $statement = db()->prepare(document_request_query() . " WHERE dr.id = ? LIMIT 1");
    $statement->execute([$requestId]);
    $row = $statement->fetch();

    return $row ? document_request_details($row) : null;
}

function list_document_requests(int $userId, ?string $search = null, int $limit = 50): array
{
    $limit = max(1, min($limit, 100));
    $sql = document_request_query() . " WHERE dr.user_id = ?";
    $params = [$userId];

    $search = trim((string) $search);
    if ($search !== '') {
        $sql .= " AND (dr.reference_no LIKE ? OR dt.name LIKE ? OR dr.purpose LIKE ?)";
        $like = '%' . $search . '%';
        array_push($params, $like, $like, $like);
    }

    $sql .= " ORDER BY dr.created_at DESC, dr.id DESC LIMIT " . $limit;

    $statement = db()->prepare($sql);
    $statement->execute($params);

    return array_map('document_request_details', $statement->fetchAll());
}

function document_request_stats(int $userId): array
{
    $statement = db()->prepare(
        "SELECT
            COUNT(*) AS total,
            SUM(status IN ('pending', 'processing', 'for_payment')) AS active,
            SUM(status = 'processing') AS processing,
            SUM(status IN ('ready', 'released')) AS completed
         FROM document_requests
         WHERE user_id = ?"
    );
    $statement->execute([$userId]);
    $row = $statement->fetch() ?: [];

    return [
        'total' => (int) ($row['total'] ?? 0),
        'active' => (int) ($row['active'] ?? 0),
        'processing' => (int) ($row['processing'] ?? 0),
        'completed' => (int) ($row['completed'] ?? 0),
    ];
}

function document_request_tab(string $tab): string
{
    $tab = strtolower(trim($tab));

    return in_array($tab, ['all', 'pending', 'processing', 'ready', 'rejected', 'archived'], true) ? $tab : 'all';
}

function official_document_request_status_filter(string $tab): ?string
{
    return match ($tab) {
        'pending' => "dr.status = 'pending'",
        'processing' => "dr.status IN ('processing', 'for_payment')",
        'ready' => "dr.status = 'ready'",
        'rejected' => "dr.status IN ('rejected', 'cancelled')",
        'archived' => "dr.status = 'released'",
        default => "dr.status <> 'released'",
    };
}

function official_document_request_badge_class(string $status): string
{
    return match ($status) {
        'pending' => 'text-bg-warning',
        'processing', 'for_payment' => 'text-bg-info',
        'ready' => 'text-bg-primary',
        'released' => 'text-bg-success',
        'rejected' => 'text-bg-danger',
        default => 'text-bg-secondary',
    };
}

function official_document_request_transitions(string $status): array
{
    return match ($status) {
        'pending' => ['processing', 'rejected'],
        'processing' => ['ready'],
        'ready' => ['released'],
        default => [],
    };
}

function official_document_request_action_label(string $status): string
{
    return match ($status) {
        'processing' => 'Processing',
        'ready' => 'Ready for pick up',
        'released' => 'Claimed',
        'rejected' => 'Reject',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
}

function official_document_request_action_class(string $status): string
{
    return match ($status) {
        'processing' => 'btn-primary',
        'ready' => 'btn-success',
        'released' => 'btn-success',
        'rejected' => 'btn-danger',
        default => 'btn-outline-secondary',
    };
}

function generate_cash_reference(): string
{
    return 'CASH-' . date('Ymd') . '-' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function official_document_request_details(array $row): array
{
    $details = document_request_details($row);
    $fee = (float) $details['fee'];

    $details['email'] = (string) ($row['email'] ?? '');
    $details['phone'] = (string) ($row['phone'] ?? '');
    $details['initial'] = resident_initial((string) $row['full_name']);
    $details['fee_label'] = $fee <= 0
        ? 'Free'
        : '₱' . number_format($fee, $fee === floor($fee) ? 0 : 2);
    $details['badge_class'] = official_document_request_badge_class($details['status']);
    $details['requires_payment'] = $fee > 0;
    $details['cash_reference'] = $fee > 0 ? generate_cash_reference() : '';
    $details['actions'] = array_map(
        static fn (string $status): array => [
            'status' => $status,
            'label' => official_document_request_action_label($status),
            'class' => official_document_request_action_class($status),
        ],
        official_document_request_transitions($details['status'])
    );
    $details['search_text'] = mb_strtolower(implode(' ', array_filter([
        $details['reference_no'],
        $details['full_name'],
        $details['resident_code'],
        $details['document_name'],
        $details['purpose'],
        $details['status_label'],
        $details['email'],
    ], static fn ($value): bool => $value !== '')));

    return $details;
}

function official_document_request_query(): string
{
    return "SELECT dr.*, dt.code AS document_type_code, dt.name AS document_name,
                   dt.description AS document_description, dt.processing_days,
                   u.email, u.phone
            FROM document_requests dr
            JOIN document_types dt ON dt.id = dr.document_type_id
            JOIN users u ON u.id = dr.user_id
            WHERE u.deleted_at IS NULL";
}

function list_official_document_requests(string $tab = 'all', ?string $search = null, int $limit = 200): array
{
    $limit = max(1, min($limit, 500));
    $sql = official_document_request_query();
    $params = [];

    $filter = official_document_request_status_filter(document_request_tab($tab));
    if ($filter !== null) {
        $sql .= ' AND ' . $filter;
    }

    $search = trim((string) $search);
    if ($search !== '') {
        $sql .= " AND (dr.reference_no LIKE ? OR dr.full_name LIKE ? OR dr.resident_code LIKE ?
                       OR dt.name LIKE ? OR dr.purpose LIKE ? OR u.email LIKE ?)";
        $like = '%' . $search . '%';
        array_push($params, $like, $like, $like, $like, $like, $like);
    }

    $sql .= " ORDER BY dr.created_at DESC, dr.id DESC LIMIT " . $limit;

    $statement = db()->prepare($sql);
    $statement->execute($params);

    return array_map('official_document_request_details', $statement->fetchAll());
}

function official_document_request_stats(): array
{
    $statement = db()->query(
        "SELECT
            SUM(status <> 'released') AS total,
            SUM(status = 'pending') AS pending,
            SUM(status IN ('processing', 'for_payment')) AS processing,
            SUM(status = 'ready') AS ready,
            SUM(status IN ('rejected', 'cancelled')) AS rejected,
            SUM(status = 'released') AS archived
         FROM document_requests"
    );
    $row = $statement->fetch() ?: [];

    return [
        'total' => (int) ($row['total'] ?? 0),
        'pending' => (int) ($row['pending'] ?? 0),
        'processing' => (int) ($row['processing'] ?? 0),
        'ready' => (int) ($row['ready'] ?? 0),
        'rejected' => (int) ($row['rejected'] ?? 0),
        'archived' => (int) ($row['archived'] ?? 0),
    ];
}

function update_official_document_request_status(int $requestId, string $newStatus, int $officialUserId): array
{
    $newStatus = strtolower(trim($newStatus));

    if ($requestId < 1 || $officialUserId < 1) {
        return ['ok' => false, 'status' => 422, 'error' => 'Invalid document request or official account.'];
    }

    $statement = db()->prepare(
        "SELECT id, reference_no, status
         FROM document_requests
         WHERE id = ?
         LIMIT 1"
    );
    $statement->execute([$requestId]);
    $request = $statement->fetch();

    if ($request === false) {
        return ['ok' => false, 'status' => 404, 'error' => 'Document request was not found.'];
    }

    $currentStatus = (string) $request['status'];
    if ($newStatus === 'released') {
        return ['ok' => false, 'status' => 409, 'error' => 'Use the Claimed button to release this request.'];
    }
    if (!in_array($newStatus, official_document_request_transitions($currentStatus), true)) {
        return ['ok' => false, 'status' => 409, 'error' => 'This request can no longer be moved to that status.'];
    }

    $update = db()->prepare(
        "UPDATE document_requests
         SET status = ?, reviewed_by = ?, reviewed_at = COALESCE(reviewed_at, NOW())
         WHERE id = ? AND status = ?"
    );
    $update->execute([$newStatus, $officialUserId, $requestId, $currentStatus]);

    if ($update->rowCount() === 0) {
        return ['ok' => false, 'status' => 409, 'error' => 'This request was already updated by another user. Please refresh the page.'];
    }

    return [
        'ok' => true,
        'status' => $newStatus,
        'message' => $request['reference_no'] . ' is now ' . strtolower(document_request_status_label($newStatus)) . '.',
    ];
}

function claim_official_document_request(int $requestId, int $officialUserId, array $input): array
{
    if ($requestId < 1 || $officialUserId < 1) {
        return ['ok' => false, 'status' => 422, 'error' => 'Invalid document request or official account.'];
    }

    $official = account_details($officialUserId);
    if ($official === null) {
        return ['ok' => false, 'status' => 403, 'error' => 'Your official account could not be verified.'];
    }

    $payee = trim((string) ($official['full_name'] ?? ''));
    if ($payee === '') {
        $payee = trim((string) ($official['email'] ?? '')) ?: 'Barangay Official';
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $statement = $pdo->prepare(
            "SELECT id, reference_no, user_id, fee, status
             FROM document_requests
             WHERE id = ?
             LIMIT 1
             FOR UPDATE"
        );
        $statement->execute([$requestId]);
        $request = $statement->fetch();

        if ($request === false) {
            $pdo->rollBack();
            return ['ok' => false, 'status' => 404, 'error' => 'Document request was not found.'];
        }

        if ((string) $request['status'] !== 'ready') {
            $pdo->rollBack();
            return ['ok' => false, 'status' => 409, 'error' => 'Only requests that are ready for pickup can be claimed.'];
        }

        $fee = (float) $request['fee'];

        if ($fee > 0) {
            $method = strtolower(trim((string) ($input['payment_method'] ?? 'cash')));
            if (!in_array($method, ['cash', 'online'], true)) {
                $pdo->rollBack();
                return ['ok' => false, 'status' => 422, 'error' => 'Select a valid payment method.'];
            }

            $reference = trim((string) ($input['reference_no'] ?? ''));
            if ($method === 'online') {
                $referenceLength = function_exists('mb_strlen') ? mb_strlen($reference) : strlen($reference);
                if ($referenceLength < 3 || $referenceLength > 80) {
                    $pdo->rollBack();
                    return ['ok' => false, 'status' => 422, 'error' => 'Enter the online payment reference number.'];
                }
            } elseif ($reference === '' || strlen($reference) > 80) {
                $reference = generate_cash_reference();
            }

            $insert = $pdo->prepare(
                "INSERT INTO payments
                    (document_request_id, user_id, received_by, payee, payment_method, reference_no, amount, status, paid_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'received', NOW())"
            );

            try {
                $insert->execute([
                    $requestId,
                    (int) $request['user_id'],
                    $officialUserId,
                    $payee,
                    $method,
                    $reference,
                    $fee,
                ]);
            } catch (PDOException $exception) {
                if ((string) $exception->getCode() !== '23000' || $method !== 'cash') {
                    throw $exception;
                }
                $insert->execute([
                    $requestId,
                    (int) $request['user_id'],
                    $officialUserId,
                    $payee,
                    $method,
                    generate_cash_reference(),
                    $fee,
                ]);
            }
        }

        $update = $pdo->prepare(
            "UPDATE document_requests
             SET status = 'released', released_at = NOW(),
                 reviewed_by = ?, reviewed_at = COALESCE(reviewed_at, NOW())
             WHERE id = ? AND status = 'ready'"
        );
        $update->execute([$officialUserId, $requestId]);

        if ($update->rowCount() === 0) {
            $pdo->rollBack();
            return ['ok' => false, 'status' => 409, 'error' => 'This request was already claimed by another user.'];
        }

        $pdo->commit();

        return [
            'ok' => true,
            'message' => $request['reference_no'] . ' has been claimed and archived.',
        ];
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($exception instanceof PDOException && (string) $exception->getCode() === '23000') {
            return ['ok' => false, 'status' => 409, 'error' => 'That payment reference number is already recorded. Please check and try again.'];
        }
        throw $exception;
    }
}

function create_document_request(int $userId, array $input): array
{
    $typeCode = strtolower(trim((string) ($input['document_type'] ?? $input['type'] ?? '')));
    $purpose = trim((string) ($input['purpose'] ?? ''));

    if ($typeCode === '') {
        return ['ok' => false, 'status' => 422, 'error' => 'Choose a document to request.'];
    }

    $documentType = document_type_by_code($typeCode);
    if ($documentType === null) {
        return ['ok' => false, 'status' => 422, 'error' => 'That document type is not available.'];
    }

    $purposeLength = function_exists('mb_strlen') ? mb_strlen($purpose) : strlen($purpose);
    if ($purposeLength < 5) {
        return ['ok' => false, 'status' => 422, 'error' => 'Describe the purpose of your request (at least 5 characters).'];
    }
    if ($purposeLength > 300) {
        return ['ok' => false, 'status' => 422, 'error' => 'The purpose must be 300 characters or fewer.'];
    }

    $pdo = db();
    $residentStatement = $pdo->prepare(
        "SELECT r.id, r.resident_code, r.full_name, r.household_number, r.address_line, u.status AS account_status
         FROM residents r
         JOIN users u ON u.id = r.user_id
         WHERE r.user_id = ? AND u.deleted_at IS NULL
         LIMIT 1"
    );
    $residentStatement->execute([$userId]);
    $resident = $residentStatement->fetch();

    if (!$resident) {
        return ['ok' => false, 'status' => 422, 'error' => 'Complete your resident profile before requesting a document.'];
    }
    if ((string) $resident['account_status'] !== 'active') {
        return ['ok' => false, 'status' => 403, 'error' => 'Your account must be active to request a document.'];
    }

    $recentStatement = $pdo->prepare(
        "SELECT COUNT(*) FROM document_requests
         WHERE user_id = ? AND created_at > (NOW() - INTERVAL 24 HOUR)"
    );
    $recentStatement->execute([$userId]);
    if ((int) $recentStatement->fetchColumn() >= 10) {
        return ['ok' => false, 'status' => 429, 'error' => 'You have reached the daily request limit. Please try again tomorrow.'];
    }

    $pdo->beginTransaction();

    try {
        $insert = $pdo->prepare(
            "INSERT INTO document_requests
                (reference_no, user_id, resident_id, document_type_id, full_name, resident_code,
                 address_line, household_number, purpose, fee, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
        );

        $reference = '';
        $created = false;
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $reference = 'BI-' . date('Y') . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            try {
                $insert->execute([
                    $reference,
                    $userId,
                    (int) $resident['id'],
                    (int) $documentType['id'],
                    (string) $resident['full_name'],
                    (string) $resident['resident_code'],
                    (string) $resident['address_line'],
                    $resident['household_number'] !== null ? (string) $resident['household_number'] : null,
                    $purpose,
                    $documentType['fee'],
                ]);
                $created = true;
                break;
            } catch (PDOException $exception) {
                if ((string) $exception->getCode() !== '23000' || $attempt === 9) {
                    throw $exception;
                }
            }
        }

        if (!$created) {
            throw new RuntimeException('Unable to generate a unique document request reference.');
        }

        $requestId = (int) $pdo->lastInsertId();
        $pdo->commit();

        $request = document_request_by_id($requestId);
        if ($request === null) {
            throw new RuntimeException('The document request could not be loaded after saving.');
        }

        return [
            'ok' => true,
            'request' => $request,
            'message' => 'Your document request has been submitted.',
        ];
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}
