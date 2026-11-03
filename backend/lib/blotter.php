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
            SUM(status = 'resolved') AS resolved
         FROM blotter_reports
         WHERE user_id = ?"
    );
    $statement->execute([$userId]);
    $row = $statement->fetch() ?: [];

    return [
        'total' => (int) ($row['total'] ?? 0),
        'active' => (int) ($row['active'] ?? 0),
        'resolved' => (int) ($row['resolved'] ?? 0),
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
