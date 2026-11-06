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

function update_project(array $input): array
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

    $reportJson = null;
    if (isset($input['report']) && is_array($input['report'])) {
        $reportJson = json_encode($input['report'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($reportJson === false) {
            $reportJson = null;
        }
    }

    $progress = max((int) $project['progress'], project_status_meta($status)['progress']);

    $statement = db()->prepare(
        'UPDATE projects
         SET status = ?, progress = ?, start_date = ?, target_date = ?,
             report_data = COALESCE(?, report_data)
         WHERE id = ?'
    );
    $statement->execute([$status, $progress, $start !== '' ? $start : null, $target !== '' ? $target : null, $reportJson, $projectId]);

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
