<?php
declare(strict_types=1);

function save_generated_report(array $report): int
{
    $statement = db()->prepare(
        "INSERT INTO reports
            (report_type, title, report_year, barangay_name, municipality, chairman_name,
             generated_by, generated_at, data)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $statement->execute([
        (string) $report['report_type'],
        (string) $report['title'],
        (int) $report['report_year'],
        (string) $report['barangay_name'],
        (string) $report['municipality'],
        (string) $report['chairman_name'],
        $report['generated_by'] !== null ? (int) $report['generated_by'] : null,
        (string) $report['generated_at'],
        json_encode($report['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);

    return (int) db()->lastInsertId();
}

function list_generated_reports(?int $year = null): array
{
    $sql = "SELECT r.id, r.report_type, r.title, r.report_year, r.barangay_name, r.municipality,
                r.chairman_name, r.generated_by, r.generated_at,
                COALESCE(JSON_LENGTH(r.data), 0) AS record_count,
                COALESCE(ua.full_name, ua.email, 'Barangay Official') AS generated_by_name
         FROM reports r
         LEFT JOIN v_user_accounts ua ON ua.user_id = r.generated_by";
    $params = [];

    if ($year !== null) {
        $sql .= " WHERE r.report_year = ?";
        $params[] = $year;
    }

    $sql .= " ORDER BY r.generated_at DESC, r.id DESC";

    $statement = db()->prepare($sql);
    $statement->execute($params);

    return array_map(static function (array $row): array {
        return [
            'id' => (int) $row['id'],
            'report_type' => (string) $row['report_type'],
            'title' => (string) $row['title'],
            'report_year' => (int) $row['report_year'],
            'barangay_name' => (string) $row['barangay_name'],
            'municipality' => (string) $row['municipality'],
            'chairman_name' => (string) $row['chairman_name'],
            'generated_by' => $row['generated_by'] !== null ? (int) $row['generated_by'] : null,
            'generated_by_name' => (string) ($row['generated_by_name'] ?? 'Barangay Official'),
            'generated_at' => (string) $row['generated_at'],
            'record_count' => (int) ($row['record_count'] ?? 0),
        ];
    }, $statement->fetchAll());
}

function list_report_years(): array
{
    $statement = db()->query("SELECT DISTINCT report_year FROM reports ORDER BY report_year DESC");

    return array_map(static fn (array $row): int => (int) $row['report_year'], $statement->fetchAll());
}

function find_generated_report(int $reportId): ?array
{
    if ($reportId < 1) {
        return null;
    }

    $statement = db()->prepare("SELECT * FROM reports WHERE id = ? LIMIT 1");
    $statement->execute([$reportId]);
    $row = $statement->fetch();

    if ($row === false) {
        return null;
    }

    $data = json_decode((string) $row['data'], true);

    return [
        'id' => (int) $row['id'],
        'report_type' => (string) $row['report_type'],
        'title' => (string) $row['title'],
        'report_year' => (int) $row['report_year'],
        'barangay_name' => (string) $row['barangay_name'],
        'municipality' => (string) $row['municipality'],
        'chairman_name' => (string) $row['chairman_name'],
        'generated_by' => $row['generated_by'] !== null ? (int) $row['generated_by'] : null,
        'generated_at' => (string) $row['generated_at'],
        'data' => is_array($data) ? $data : [],
    ];
}
