<?php
declare(strict_types=1);

function resident_initial(string $name, string $fallback = 'R'): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    foreach ($parts as $part) {
        if ($part !== '') {
            return strtoupper(substr($part, 0, 1));
        }
    }

    return $fallback;
}

function official_resident_details(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'resident_code' => (string) $row['resident_code'],
        'full_name' => (string) $row['full_name'],
        'initial' => resident_initial((string) $row['full_name']),
        'user_id' => (int) $row['user_id'],
        'email' => (string) ($row['email'] ?? ''),
        'phone' => (string) ($row['phone'] ?? ''),
        'role_code' => (string) ($row['role_code'] ?? ''),
        'address_line' => (string) $row['address_line'],
        'household_number' => $row['household_number'] !== null ? (string) $row['household_number'] : null,
        'created_at' => (string) $row['created_at'],
    ];
}

function list_official_residents(?string $search = null, int $limit = 200): array
{
    $limit = max(1, min($limit, 500));
    $sql = "SELECT r.id, r.user_id, r.resident_code, r.full_name, r.household_number, r.address_line, r.created_at, u.email, u.phone, ro.code AS role_code
            FROM residents r
            JOIN users u ON u.id = r.user_id
            JOIN roles ro ON ro.id = u.role_id
            WHERE u.deleted_at IS NULL";
    $params = [];

    $search = trim((string) $search);
    if ($search !== '') {
        $sql .= " AND (
            r.full_name LIKE ? OR
            r.resident_code LIKE ? OR
            r.address_line LIKE ? OR
            r.household_number LIKE ? OR
            u.email LIKE ? OR
            u.phone LIKE ?
        )";
        $like = '%' . $search . '%';
        array_push($params, $like, $like, $like, $like, $like, $like);
    }

    $sql .= " ORDER BY r.created_at DESC, r.id DESC LIMIT " . $limit;

    $statement = db()->prepare($sql);
    $statement->execute($params);

    return array_map('official_resident_details', $statement->fetchAll());
}

function delete_official_resident(int $residentId, int $officialUserId): array
{
    if ($residentId < 1 || $officialUserId < 1) {
        return ['ok' => false, 'message' => 'Invalid resident or official account.'];
    }

    $statement = db()->prepare(
        "SELECT r.id, r.full_name, r.user_id, ro.code AS role_code
         FROM residents r
         JOIN users u ON u.id = r.user_id
         JOIN roles ro ON ro.id = u.role_id
         WHERE r.id = ? AND u.deleted_at IS NULL
         LIMIT 1"
    );
    $statement->execute([$residentId]);
    $resident = $statement->fetch();

    if ($resident === false) {
        return ['ok' => false, 'message' => 'Resident record was not found.'];
    }

    if ((int) $resident['user_id'] === $officialUserId) {
        return ['ok' => false, 'message' => 'You cannot remove your own resident record.'];
    }

    if ($resident['role_code'] !== 'resident') {
        return ['ok' => false, 'message' => 'Only resident accounts can be removed from this list.'];
    }

    $update = db()->prepare(
        "UPDATE users SET status = 'deactivated', deleted_at = NOW()
         WHERE id = ? AND deleted_at IS NULL"
    );
    $update->execute([(int) $resident['user_id']]);

    if ($update->rowCount() === 0) {
        return ['ok' => false, 'message' => 'This resident has already been removed.'];
    }

    return ['ok' => true, 'message' => $resident['full_name'] . ' has been removed.'];
}

function official_resident_stats(): array
{
    $statement = db()->query(
        "SELECT COUNT(*) AS total
         FROM residents r
         JOIN users u ON u.id = r.user_id
         WHERE u.deleted_at IS NULL"
    );
    $row = $statement->fetch() ?: [];

    return [
        'total' => (int) ($row['total'] ?? 0),
    ];
}
