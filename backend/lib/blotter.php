<?php
declare(strict_types=1);

function blotter_status_label(string $status): string
{
    return match ($status) {
        'pending' => 'Pending review',
        'under_review' => 'Under review',
        'scheduled' => 'Hearing scheduled',
        'resolved' => 'Resolved',
        'dismissed' => 'Dismissed',
        'referred' => 'Referred',
        'cancelled' => 'Cancelled',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
}

function blotter_status_tone(string $status): string
{
    return match ($status) {
        'pending' => 'warn',
        'under_review', 'scheduled' => 'info',
        'resolved' => 'good',
        default => '',
    };
}

function blotter_status_note(string $status): string
{
    return match ($status) {
        'pending' => 'Your report has been received and is waiting for review by barangay staff.',
        'under_review' => 'Barangay staff are reviewing your report.',
        'scheduled' => 'A hearing or mediation has been scheduled. Wait for the barangay notice with the date and time.',
        'resolved' => 'This report has been resolved.',
        'dismissed' => 'This report was dismissed after review.',
        'referred' => 'This report was referred to another office or authority.',
        'cancelled' => 'This report was cancelled.',
        default => 'This report is being handled by barangay staff.',
    };
}

function blotter_report_details(array $row): array
{
    $status = (string) $row['status'];

    return [
        'id' => (int) $row['id'],
        'reference_no' => (string) $row['reference_no'],
        'filed_by' => (string) $row['filed_by'],
        'address_line' => (string) $row['address_line'],
        'subject' => (string) $row['subject'],
        'details' => (string) $row['details'],
        'status' => $status,
        'status_label' => blotter_status_label($status),
        'status_tone' => blotter_status_tone($status),
        'remarks' => $row['remarks'] !== null ? (string) $row['remarks'] : null,
        'resolution' => $row['resolution'] !== null ? (string) $row['resolution'] : null,
        'created_at' => (string) $row['created_at'],
        'updated_at' => (string) $row['updated_at'],
        'reviewed_at' => $row['reviewed_at'] !== null ? (string) $row['reviewed_at'] : null,
        'resolved_at' => $row['resolved_at'] !== null ? (string) $row['resolved_at'] : null,
    ];
}

function blotter_report_by_id(int $reportId): ?array
{
    $statement = db()->prepare("SELECT * FROM blotter_reports WHERE id = ? LIMIT 1");
    $statement->execute([$reportId]);
    $row = $statement->fetch();

    return $row ? blotter_report_details($row) : null;
}

function blotter_report_by_reference(int $userId, string $reference): ?array
{
    $reference = strtoupper(trim($reference));
    if ($reference === '') {
        return null;
    }

    $statement = db()->prepare(
        "SELECT * FROM blotter_reports WHERE user_id = ? AND reference_no = ? LIMIT 1"
    );
    $statement->execute([$userId, $reference]);
    $row = $statement->fetch();

    return $row ? blotter_report_details($row) : null;
}

function list_blotter_reports(int $userId, ?string $search = null, int $limit = 50): array
{
    $limit = max(1, min($limit, 100));
    $sql = "SELECT * FROM blotter_reports WHERE user_id = ?";
    $params = [$userId];

    $search = trim((string) $search);
    if ($search !== '') {
        $sql .= " AND (reference_no LIKE ? OR subject LIKE ? OR details LIKE ?)";
        $like = '%' . $search . '%';
        array_push($params, $like, $like, $like);
    }

    $sql .= " ORDER BY created_at DESC, id DESC LIMIT " . $limit;

    $statement = db()->prepare($sql);
    $statement->execute($params);

    return array_map('blotter_report_details', $statement->fetchAll());
}

function blotter_stats(int $userId): array
{
    $statement = db()->prepare(
        "SELECT
            COUNT(*) AS total,
            SUM(status IN ('pending', 'under_review', 'scheduled')) AS active,
            SUM(status = 'resolved') AS resolved,
            SUM(YEAR(created_at) = YEAR(NOW())) AS this_year
         FROM blotter_reports
         WHERE user_id = ?"
    );
    $statement->execute([$userId]);
    $row = $statement->fetch() ?: [];

    return [
        'total' => (int) ($row['total'] ?? 0),
        'active' => (int) ($row['active'] ?? 0),
        'resolved' => (int) ($row['resolved'] ?? 0),
        'this_year' => (int) ($row['this_year'] ?? 0),
    ];
}

function official_blotter_badge_class(string $status): string
{
    return match ($status) {
        'pending' => 'text-bg-warning',
        'under_review', 'scheduled' => 'text-bg-info',
        'resolved' => 'text-bg-success',
        'referred' => 'text-bg-primary',
        'dismissed', 'cancelled' => 'text-bg-secondary',
        default => 'text-bg-secondary',
    };
}

function official_blotter_status_label(string $status): string
{
    return $status === 'under_review' ? 'Investigating' : blotter_status_label($status);
}

function official_blotter_next_action(string $status): ?array
{
    return match ($status) {
        'pending' => ['label' => 'Take Action', 'status' => 'under_review', 'class' => 'btn-orange'],
        'under_review' => ['label' => 'Resolved', 'status' => 'resolved', 'class' => 'btn-success'],
        default => null,
    };
}

function official_blotter_report_details(array $row): array
{
    $details = blotter_report_details($row);
    $details['status_label'] = official_blotter_status_label($details['status']);
    $details['badge_class'] = official_blotter_badge_class($details['status']);
    $details['action'] = official_blotter_next_action($details['status']);
    $details['initial'] = resident_initial($details['filed_by']);
    $details['date_label'] = date('M j, Y', strtotime($details['created_at']));

    $excerpt = $details['details'];
    $length = function_exists('mb_strlen') ? mb_strlen($excerpt) : strlen($excerpt);
    if ($length > 120) {
        $cut = function_exists('mb_substr') ? mb_substr($excerpt, 0, 120) : substr($excerpt, 0, 120);
        $excerpt = rtrim($cut) . '…';
    }
    $details['excerpt'] = $excerpt;

    $details['search_text'] = mb_strtolower(implode(' ', array_filter([
        $details['reference_no'],
        $details['filed_by'],
        $details['subject'],
        $details['address_line'],
        $details['status_label'],
    ], static fn ($value): bool => $value !== '')));

    return $details;
}

function list_official_blotter_reports(?string $search = null, int $limit = 200): array
{
    $limit = max(1, min($limit, 500));
    $sql = "SELECT br.*
            FROM blotter_reports br
            JOIN users u ON u.id = br.user_id
            WHERE u.deleted_at IS NULL";
    $params = [];

    $search = trim((string) $search);
    if ($search !== '') {
        $sql .= " AND (br.reference_no LIKE ? OR br.filed_by LIKE ? OR br.subject LIKE ?
                       OR br.details LIKE ? OR br.address_line LIKE ?)";
        $like = '%' . $search . '%';
        array_push($params, $like, $like, $like, $like, $like);
    }

    $sql .= " ORDER BY br.created_at DESC, br.id DESC LIMIT " . $limit;

    $statement = db()->prepare($sql);
    $statement->execute($params);

    return array_map('official_blotter_report_details', $statement->fetchAll());
}

function official_blotter_stats(): array
{
    $statement = db()->query(
        "SELECT
            COUNT(*) AS total,
            SUM(status = 'resolved') AS resolved,
            SUM(status IN ('under_review', 'scheduled')) AS investigating,
            SUM(status = 'pending') AS filed
         FROM blotter_reports"
    );
    $row = $statement->fetch() ?: [];

    return [
        'total' => (int) ($row['total'] ?? 0),
        'resolved' => (int) ($row['resolved'] ?? 0),
        'investigating' => (int) ($row['investigating'] ?? 0),
        'filed' => (int) ($row['filed'] ?? 0),
    ];
}

function update_official_blotter_status(int $reportId, string $newStatus, int $officialUserId): array
{
    $newStatus = strtolower(trim($newStatus));

    if ($reportId < 1 || $officialUserId < 1) {
        return ['ok' => false, 'status' => 422, 'error' => 'Invalid blotter report or official account.'];
    }

    $statement = db()->prepare(
        "SELECT id, reference_no, status
         FROM blotter_reports
         WHERE id = ?
         LIMIT 1"
    );
    $statement->execute([$reportId]);
    $report = $statement->fetch();

    if ($report === false) {
        return ['ok' => false, 'status' => 404, 'error' => 'Blotter report was not found.'];
    }

    $currentStatus = (string) $report['status'];
    $action = official_blotter_next_action($currentStatus);

    if ($action === null || $action['status'] !== $newStatus) {
        return ['ok' => false, 'status' => 409, 'error' => 'This blotter report can no longer be moved to that status.'];
    }

    $resolvedClause = $newStatus === 'resolved' ? ', resolved_at = NOW()' : '';
    $update = db()->prepare(
        "UPDATE blotter_reports
         SET status = ?, reviewed_by = ?, reviewed_at = COALESCE(reviewed_at, NOW()){$resolvedClause}
         WHERE id = ? AND status = ?"
    );
    $update->execute([$newStatus, $officialUserId, $reportId, $currentStatus]);

    if ($update->rowCount() === 0) {
        return ['ok' => false, 'status' => 409, 'error' => 'This report was already updated by another user. Please refresh the page.'];
    }

    return [
        'ok' => true,
        'status' => $newStatus,
        'message' => $report['reference_no'] . ' is now ' . strtolower(official_blotter_status_label($newStatus)) . '.',
    ];
}

function create_blotter_report(int $userId, array $input): array
{
    $subject = trim((string) ($input['subject'] ?? ''));
    $details = trim((string) ($input['details'] ?? ''));
    $subjectLength = function_exists('mb_strlen') ? mb_strlen($subject) : strlen($subject);
    $detailsLength = function_exists('mb_strlen') ? mb_strlen($details) : strlen($details);

    if ($subjectLength < 5) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a subject for your report (at least 5 characters).'];
    }
    if ($subjectLength > 160) {
        return ['ok' => false, 'status' => 422, 'error' => 'The subject must be 160 characters or fewer.'];
    }
    if ($detailsLength < 10) {
        return ['ok' => false, 'status' => 422, 'error' => 'Describe what happened (at least 10 characters).'];
    }
    if ($detailsLength > 1000) {
        return ['ok' => false, 'status' => 422, 'error' => 'The description must be 1000 characters or fewer.'];
    }

    $pdo = db();
    $residentStatement = $pdo->prepare(
        "SELECT r.id, r.full_name, r.address_line, u.status AS account_status
         FROM residents r
         JOIN users u ON u.id = r.user_id
         WHERE r.user_id = ? AND u.deleted_at IS NULL
         LIMIT 1"
    );
    $residentStatement->execute([$userId]);
    $resident = $residentStatement->fetch();

    if (!$resident) {
        return ['ok' => false, 'status' => 422, 'error' => 'Complete your resident profile before filing a blotter report.'];
    }
    if ((string) $resident['account_status'] !== 'active') {
        return ['ok' => false, 'status' => 403, 'error' => 'Your account must be active to file a blotter report.'];
    }

    $recentStatement = $pdo->prepare(
        "SELECT COUNT(*) FROM blotter_reports
         WHERE user_id = ? AND created_at > (NOW() - INTERVAL 24 HOUR)"
    );
    $recentStatement->execute([$userId]);
    if ((int) $recentStatement->fetchColumn() >= 5) {
        return ['ok' => false, 'status' => 429, 'error' => 'You have reached the daily blotter limit. Please try again tomorrow.'];
    }

    $pdo->beginTransaction();

    try {
        $insert = $pdo->prepare(
            "INSERT INTO blotter_reports
                (reference_no, user_id, resident_id, filed_by, address_line, subject, details, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')"
        );

        $reference = '';
        $created = false;
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $reference = 'BL-' . date('Y') . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            try {
                $insert->execute([
                    $reference,
                    $userId,
                    (int) $resident['id'],
                    (string) $resident['full_name'],
                    (string) $resident['address_line'],
                    $subject,
                    $details,
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
            throw new RuntimeException('Unable to generate a unique blotter reference.');
        }

        $reportId = (int) $pdo->lastInsertId();
        $pdo->commit();

        $report = blotter_report_by_id($reportId);
        if ($report === null) {
            throw new RuntimeException('The blotter report could not be loaded after saving.');
        }

        return [
            'ok' => true,
            'report' => $report,
            'message' => 'Your blotter report has been submitted.',
        ];
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}
