<?php
declare(strict_types=1);

function announcement_types(): array
{
    return [
        'health' => 'Health',
        'education' => 'Education',
        'infrastructure' => 'Infrastructure',
        'safety' => 'Safety',
        'notice' => 'Notice',
    ];
}

function announcement_type_label(string $type): string
{
    $types = announcement_types();
    $key = strtolower($type);

    return $types[$key] ?? ucfirst($key);
}

function announcement_type_tone(string $type): string
{
    return match (strtolower($type)) {
        'health' => 'blue',
        'education' => 'blue',
        'infrastructure' => 'amber',
        'safety' => 'green',
        'notice' => 'amber',
        default => 'blue',
    };
}

function announcement_badge_class(string $type): string
{
    return match (strtolower($type)) {
        'health' => 'text-bg-primary',
        'education' => 'text-bg-info',
        'infrastructure' => 'text-bg-secondary',
        'safety' => 'text-bg-success',
        'notice' => 'text-bg-warning',
        default => 'text-bg-secondary',
    };
}

function announcement_accent_color(string $type): string
{
    return match (strtolower($type)) {
        'health' => '#0d6efd',
        'education' => '#0dcaf0',
        'infrastructure' => '#6c757d',
        'safety' => '#198754',
        'notice' => '#ffc107',
        default => '#6c757d',
    };
}

function announcement_details(array $row): array
{
    $type = strtolower((string) $row['type']);

    return [
        'id' => (int) $row['id'],
        'type' => $type,
        'type_label' => announcement_type_label($type),
        'type_tone' => announcement_type_tone($type),
        'badge_class' => announcement_badge_class($type),
        'accent_color' => announcement_accent_color($type),
        'title' => (string) $row['title'],
        'content' => (string) $row['content'],
        'created_by' => (int) $row['created_by'],
        'author_name' => trim((string) ($row['author_name'] ?? '')) !== '' ? (string) $row['author_name'] : 'Barangay Official',
        'created_at' => (string) $row['created_at'],
    ];
}

function list_announcements(int $limit = 0): array
{
    $sql = "SELECT a.*, COALESCE(res.full_name, u.email, 'Barangay Official') AS author_name
            FROM announcements a
            LEFT JOIN users u ON u.id = a.created_by
            LEFT JOIN residents res ON res.user_id = a.created_by
            ORDER BY a.created_at DESC, a.id DESC";
    if ($limit > 0) {
        $sql .= ' LIMIT ' . $limit;
    }

    $rows = db()->query($sql)->fetchAll();

    return array_map('announcement_details', $rows);
}

function create_announcement(array $input, int $userId): array
{
    $type = strtolower(trim((string) ($input['type'] ?? '')));
    $title = trim((string) ($input['title'] ?? ''));
    $content = trim((string) ($input['content'] ?? ''));

    if (!array_key_exists($type, announcement_types())) {
        return ['ok' => false, 'status' => 422, 'error' => 'Select a valid announcement type.'];
    }
    if (strlen($title) < 3 || strlen($title) > 160) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a title between 3 and 160 characters.'];
    }
    if ($content === '' || strlen($content) > 5000) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter the announcement content (up to 5000 characters).'];
    }

    $statement = db()->prepare('INSERT INTO announcements (created_by, type, title, content) VALUES (?, ?, ?, ?)');
    $statement->execute([$userId, $type, $title, $content]);

    return [
        'ok' => true,
        'id' => (int) db()->lastInsertId(),
        'message' => 'Announcement posted successfully.',
    ];
}
