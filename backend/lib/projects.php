<?php
declare(strict_types=1);

function project_sectors(): array
{
    return [
        'Infrastructure',
        'Education',
        'Health',
        'Social Services',
        'Disaster & P&O',
    ];
}

function project_statuses(): array
{
    return ['planned', 'ongoing', 'completed'];
}

function project_status_meta(string $status): array
{
    return match ($status) {
        'ongoing' => ['label' => 'Ongoing', 'class' => 'status-pending', 'action' => 'Complete', 'progress' => 50],
        'completed' => ['label' => 'Completed', 'class' => 'status-completed', 'action' => '', 'progress' => 100],
        default => ['label' => 'Planned', 'class' => 'status-released', 'action' => 'Start Project', 'progress' => 0],
    };
}

function project_details(array $row): array
{
    $status = strtolower((string) $row['status']);
    if (!in_array($status, project_statuses(), true)) {
        $status = 'planned';
    }
    $meta = project_status_meta($status);

    $start = trim((string) ($row['start_date'] ?? ''));
    $target = trim((string) ($row['target_date'] ?? ''));
    $timeline = $start !== '' && $target !== '' ? $start . ' - ' . $target : ($start !== '' ? $start : ($target !== '' ? $target : 'Not set'));

    $report = null;
    if (isset($row['report_data']) && $row['report_data'] !== null && $row['report_data'] !== '') {
        $decoded = json_decode((string) $row['report_data'], true);
        $report = is_array($decoded) ? $decoded : null;
    }

    return [
        'id' => (int) $row['id'],
        'title' => (string) $row['title'],
        'sector' => (string) $row['sector'],
        'description' => (string) ($row['description'] ?? ''),
        'budget' => (float) $row['budget'],
        'status' => $status,
        'status_label' => $meta['label'],
        'status_class' => $meta['class'],
        'action_label' => $meta['action'],
        'progress' => (int) $row['progress'],
        'start_date' => $start,
        'target_date' => $target,
        'timeline' => $timeline,
        'report' => $report,
        'created_at' => (string) $row['created_at'],
        'updated_at' => (string) $row['updated_at'],
    ];
}

function list_projects(): array
{
    $rows = db()->query('SELECT * FROM projects ORDER BY created_at DESC, id DESC')->fetchAll();

    return array_map('project_details', $rows);
}

function find_project(int $projectId): ?array
{
    if ($projectId < 1) {
        return null;
    }

    $statement = db()->prepare('SELECT * FROM projects WHERE id = ? LIMIT 1');
    $statement->execute([$projectId]);
    $row = $statement->fetch();

    return $row === false ? null : project_details($row);
}

function project_budget_sector(string $sector): ?string
{
    return match ($sector) {
        'Health' => 'Healthcare',
        'Disaster & P&O' => 'Disaster and P&O',
        default => in_array($sector, budget_sectors(), true) ? $sector : null,
    };
}

function adjust_project_disbursement(array $project, float $delta, ?int $userId = null): void
{
    if (abs($delta) < 0.001) {
        return;
    }

    $sector = project_budget_sector((string) $project['sector']);
    if ($sector === null) {
        return;
    }

    $budget = ensure_budget((int) date('Y'), $userId);
    $statement = db()->prepare(
        'INSERT INTO budget_allocations (budget_id, sector, amount, disbursed_amount)
         VALUES (?, ?, 0, ?)
         ON DUPLICATE KEY UPDATE disbursed_amount = GREATEST(disbursed_amount + VALUES(disbursed_amount), 0)'
    );
    $statement->execute([(int) $budget['id'], $sector, $delta]);
}

function create_project(array $input, int $userId): array
{
    $title = trim((string) ($input['title'] ?? ''));
    $sector = trim((string) ($input['sector'] ?? ''));
    $description = trim((string) ($input['description'] ?? ''));
    $budget = $input['budget'] ?? 0;

    if (strlen($title) < 3 || strlen($title) > 160) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a project name between 3 and 160 characters.'];
    }
    if (!in_array($sector, project_sectors(), true)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Select a valid sector.'];
    }
    if (strlen($description) > 5000) {
        return ['ok' => false, 'status' => 422, 'error' => 'The description is too long (up to 5000 characters).'];
    }
    if (!is_numeric($budget) || (float) $budget < 0) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid budget amount.'];
    }

    $statement = db()->prepare(
        'INSERT INTO projects (title, sector, description, budget, status, progress, created_by)
         VALUES (?, ?, ?, ?, ?, 0, ?)'
    );
    $statement->execute([$title, $sector, $description, round((float) $budget, 2), 'planned', $userId]);

    $project = find_project((int) db()->lastInsertId());
    if ($project === null) {
        return ['ok' => false, 'status' => 500, 'error' => 'Unable to save the project.'];
    }

    return [
        'ok' => true,
        'project' => $project,
        'message' => 'Project created successfully.',
    ];
}

function update_project(array $input, ?int $userId = null): array
{
    $projectId = (int) ($input['id'] ?? 0);
    $project = find_project($projectId);
    if ($project === null) {
        return ['ok' => false, 'status' => 404, 'error' => 'Project not found.'];
    }

    $status = strtolower(trim((string) ($input['status'] ?? $project['status'])));
    if (!in_array($status, project_statuses(), true)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Select a valid project status.'];
    }

    $start = array_key_exists('start_date', $input)
        ? trim((string) $input['start_date'])
        : (string) $project['start_date'];
    $target = array_key_exists('target_date', $input)
        ? trim((string) $input['target_date'])
        : (string) $project['target_date'];

    $reportData = isset($input['report']) && is_array($input['report']) ? $input['report'] : null;
    $reportJson = null;
    if ($reportData !== null) {
        $reportJson = json_encode($reportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($reportJson === false) {
            $reportJson = null;
        }
    }

    $progress = max((int) $project['progress'], project_status_meta($status)['progress']);

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $statement = $pdo->prepare(
            'UPDATE projects
             SET status = ?, progress = ?, start_date = ?, target_date = ?,
                 report_data = COALESCE(?, report_data)
             WHERE id = ?'
        );
        $statement->execute([$status, $progress, $start !== '' ? $start : null, $target !== '' ? $target : null, $reportJson, $projectId]);

        if ($status === 'completed' && $project['status'] !== 'completed') {
            adjust_project_disbursement($project, (float) $project['budget'], $userId);
        } elseif ($status !== 'completed' && $project['status'] === 'completed') {
            adjust_project_disbursement($project, -(float) $project['budget'], $userId);
        }

        if ($reportData !== null) {
            save_project_implementation_report($project, $reportData, $start, $target, $userId);
        }

        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }

    $updated = find_project($projectId);
    if ($updated === null) {
        return ['ok' => false, 'status' => 500, 'error' => 'Unable to update the project.'];
    }

    return [
        'ok' => true,
        'project' => $updated,
        'message' => 'Project updated successfully.',
    ];
}

function save_project_implementation_report(array $project, array $report, string $start, string $target, ?int $userId = null): int
{
    $profile = barangay_profile_record();
    $account = $userId !== null && $userId > 0 ? account_details($userId) : null;

    $text = static function (string $key, string $fallback = '') use ($report): string {
        $value = $report[$key] ?? $fallback;
        return is_scalar($value) ? trim((string) $value) : $fallback;
    };

    $barangay = $text('barangay');
    if ($barangay === '') {
        $barangay = trim((string) $profile['barangay_name']) !== '' ? (string) $profile['barangay_name'] : 'Barangay';
    }
    $municipality = $text('municipality');
    if ($municipality === '') {
        $municipality = (string) $profile['municipality'];
    }
    $province = $text('province');
    if ($province === '') {
        $province = (string) $profile['province'];
    }

    $approvedBy = $text('approved_by');
    $chairman = $approvedBy;
    if ($chairman === '') {
        $chairman = trim((string) $profile['chairman']);
    }
    if ($chairman === '') {
        $chairman = trim((string) ($account['full_name'] ?? ''));
    }
    if ($chairman === '') {
        $chairman = 'Barangay Chairman';
    }

    $generatedAt = new DateTimeImmutable('now');
    $title = 'Project Implementation Report: ' . (string) $project['title'];
    if (mb_strlen($title) > 160) {
        $title = mb_substr($title, 0, 157) . '...';
    }

    $row = [
        'project_title' => (string) $project['title'],
        'sector' => (string) $project['sector'],
        'province' => $province,
        'municipality' => $municipality,
        'barangay' => $barangay,
        'location' => $text('location'),
        'category' => $text('category'),
        'contractor' => $text('contractor'),
        'start_date' => $start,
        'target_date' => $target,
        'description' => $text('description'),
        'objectives' => $text('objectives'),
        'total_budget' => is_numeric($report['total_budget'] ?? null) ? (float) $report['total_budget'] : (float) $project['budget'],
        'fund_source' => $text('fund_source'),
        'challenges' => $text('challenges'),
        'actions' => $text('actions'),
        'recommendations' => $text('recommendations'),
        'prepared_by' => $text('prepared_by'),
        'reviewed_by' => $text('reviewed_by'),
        'approved_by' => $approvedBy,
        'report_date' => $text('date'),
    ];

    return save_generated_report([
        'report_type' => 'projects',
        'title' => $title,
        'report_year' => (int) $generatedAt->format('Y'),
        'barangay_name' => $barangay,
        'municipality' => $municipality,
        'chairman_name' => $chairman,
        'generated_by' => $userId,
        'generated_at' => $generatedAt->format('Y-m-d H:i:s'),
        'data' => [$row],
    ]);
}
