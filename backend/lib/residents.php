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

function resident_code_for_user(int $userId): string
{
    return 'BSG-' . str_pad((string) $userId, 5, '0', STR_PAD_LEFT);
}

function next_resident_code_preview(): string
{
    $statement = db()->query(
        "SELECT AUTO_INCREMENT FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' LIMIT 1"
    );
    $nextId = (int) ($statement->fetchColumn() ?: 1);

    return resident_code_for_user(max(1, $nextId));
}

function create_official_resident(array $input): array
{
    $firstName = trim((string) ($input['first_name'] ?? ''));
    $middleName = trim((string) ($input['middle_name'] ?? ''));
    $lastName = trim((string) ($input['last_name'] ?? ''));
    $suffix = trim((string) ($input['suffix'] ?? ''));
    $birthDate = trim((string) ($input['birth_date'] ?? ''));
    $sex = strtolower(trim((string) ($input['sex'] ?? '')));
    $civilStatus = strtolower(trim((string) ($input['civil_status'] ?? '')));
    $email = strtolower(trim((string) ($input['email'] ?? '')));
    $phoneRaw = trim((string) ($input['phone'] ?? ''));
    $phone = $phoneRaw === '' ? null : normalize_phone($phoneRaw);
    $householdNumber = trim((string) ($input['household_number'] ?? ''));
    $addressLine = trim((string) ($input['address_line'] ?? ''));

    $namePattern = "/^[\p{L}\s.'-]+$/u";

    if ($firstName === '' || mb_strlen($firstName) > 80 || !preg_match($namePattern, $firstName)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid first name.'];
    }
    if ($lastName === '' || mb_strlen($lastName) > 80 || !preg_match($namePattern, $lastName)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid last name.'];
    }
    if ($middleName !== '' && (mb_strlen($middleName) > 80 || !preg_match($namePattern, $middleName))) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid middle name.'];
    }
    if ($suffix !== '' && (mb_strlen($suffix) > 20 || !preg_match("/^[\p{L}.'-]+$/u", $suffix))) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid suffix.'];
    }

    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $birthDate);
    if ($date === false || $date->format('Y-m-d') !== $birthDate) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid birth date.'];
    }
    $today = new DateTimeImmutable('today');
    if ($date > $today) {
        return ['ok' => false, 'status' => 422, 'error' => 'Birth date cannot be in the future.'];
    }
    if ($date->diff($today)->y > 130) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid birth date.'];
    }

    if (!in_array($sex, ['male', 'female', 'other'], true)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Select a sex.'];
    }
    if (!in_array($civilStatus, ['single', 'married', 'widowed', 'separated', 'annulled'], true)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Select a civil status.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid email address.'];
    }
    if ($phoneRaw !== '' && $phone === null) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid Philippine mobile number (09XXXXXXXXX).'];
    }
    if (strlen($addressLine) < 5 || strlen($addressLine) > 255) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter the complete address.'];
    }
    if (strlen($householdNumber) > 40) {
        return ['ok' => false, 'status' => 422, 'error' => 'Household number is too long.'];
    }

    $fullName = $firstName;
    if ($middleName !== '') {
        $fullName .= ' ' . $middleName;
    }
    $fullName .= ' ' . $lastName;
    if ($suffix !== '') {
        $fullName .= ' ' . $suffix;
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $existing = $pdo->prepare("SELECT email, phone FROM users WHERE email = ? OR (? IS NOT NULL AND phone = ?) LIMIT 1");
        $existing->execute([$email, $phone, $phone]);
        $existingUser = $existing->fetch();
        if ($existingUser) {
            $pdo->rollBack();
            $field = strtolower((string) $existingUser['email']) === $email ? 'email address' : 'mobile number';
            return ['ok' => false, 'status' => 409, 'error' => 'An account with this ' . $field . ' already exists.'];
        }

        $insertUser = $pdo->prepare(
            "INSERT INTO users (role_id, email, phone, password_hash, account_type, status)
             VALUES (1, ?, ?, ?, 'resident', 'active')"
        );
        $insertUser->execute([$email, $phone, password_hash(random_token(32), PASSWORD_BCRYPT)]);
        $userId = (int) $pdo->lastInsertId();
        $residentCode = resident_code_for_user($userId);

        $insertResident = $pdo->prepare(
            "INSERT INTO residents
                (user_id, resident_code, full_name, first_name, middle_name, last_name, suffix,
                 household_number, address_line, birth_date, sex, civil_status, profile_completed_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $insertResident->execute([
            $userId,
            $residentCode,
            $fullName,
            $firstName,
            $middleName !== '' ? $middleName : null,
            $lastName,
            $suffix !== '' ? $suffix : null,
            $householdNumber !== '' ? $householdNumber : null,
            $addressLine,
            $birthDate,
            $sex,
            $civilStatus,
        ]);

        $pdo->commit();

        return [
            'ok' => true,
            'user_id' => $userId,
            'resident_code' => $residentCode,
            'full_name' => $fullName,
            'message' => $fullName . ' has been added with resident code ' . $residentCode . '.',
        ];
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($exception instanceof PDOException && (string) $exception->getCode() === '23000') {
            return ['ok' => false, 'status' => 409, 'error' => 'An account with this email address or mobile number already exists.'];
        }
        throw $exception;
    }
}

function resident_profile_record(int $userId): ?array
{
    if ($userId < 1) {
        return null;
    }

    $statement = db()->prepare(
        "SELECT r.id, r.first_name, r.middle_name, r.last_name, r.suffix, r.full_name,
                r.birth_date, r.sex, r.civil_status, r.profile_completed_at,
                r.resident_code, r.household_number, r.address_line,
                u.email, u.account_type
         FROM residents r
         JOIN users u ON u.id = r.user_id
         WHERE r.user_id = ? AND u.deleted_at IS NULL
         LIMIT 1"
    );
    $statement->execute([$userId]);
    $row = $statement->fetch();

    return $row === false ? null : $row;
}

function resident_can_complete_profile(array $account): bool
{
    return (string) ($account['account_type'] ?? '') === 'resident'
        || (string) ($account['role_code'] ?? '') === 'resident';
}

function resident_needs_profile(int $userId): bool
{
    $record = resident_profile_record($userId);
    if ($record === null) {
        return resident_can_complete_profile(account_details($userId) ?? []);
    }

    if ((string) $record['account_type'] !== 'resident') {
        return false;
    }

    return $record['profile_completed_at'] === null;
}

function resident_profile_payload(int $userId): array
{
    $record = resident_profile_record($userId);
    if ($record === null) {
        $account = account_details($userId);
        if ($account === null || !resident_can_complete_profile($account)) {
            return ['needs_onboarding' => false, 'profile' => null];
        }

        return [
            'needs_onboarding' => true,
            'profile' => [
                'first_name' => '',
                'middle_name' => '',
                'last_name' => '',
                'suffix' => '',
                'birth_date' => '',
                'sex' => '',
                'civil_status' => '',
                'resident_code' => resident_code_for_user($userId),
                'household_number' => '',
                'address_line' => '',
                'email' => (string) ($account['email'] ?? ''),
            ],
        ];
    }

    return [
        'needs_onboarding' => resident_needs_profile($userId),
        'profile' => [
            'first_name' => (string) ($record['first_name'] ?? ''),
            'middle_name' => (string) ($record['middle_name'] ?? ''),
            'last_name' => (string) ($record['last_name'] ?? ''),
            'suffix' => (string) ($record['suffix'] ?? ''),
            'birth_date' => (string) ($record['birth_date'] ?? ''),
            'sex' => (string) ($record['sex'] ?? ''),
            'civil_status' => (string) ($record['civil_status'] ?? ''),
            'resident_code' => (string) ($record['resident_code'] ?? ''),
            'household_number' => $record['household_number'] !== null ? (string) $record['household_number'] : '',
            'address_line' => (string) ($record['address_line'] ?? ''),
            'email' => (string) ($record['email'] ?? ''),
        ],
    ];
}

function save_resident_profile(int $userId, array $input): array
{
    $record = resident_profile_record($userId);
    if ($record === null) {
        $account = account_details($userId);
        if ($account === null) {
            return ['ok' => false, 'status' => 404, 'error' => 'Account was not found.'];
        }
        if (!resident_can_complete_profile($account)) {
            return ['ok' => false, 'status' => 403, 'error' => 'Only resident accounts can update this profile.'];
        }
    } elseif ((string) $record['account_type'] !== 'resident') {
        return ['ok' => false, 'status' => 403, 'error' => 'Only resident accounts can update this profile.'];
    }

    $firstName = trim((string) ($input['first_name'] ?? ''));
    $middleName = trim((string) ($input['middle_name'] ?? ''));
    $lastName = trim((string) ($input['last_name'] ?? ''));
    $suffix = trim((string) ($input['suffix'] ?? ''));
    $birthDate = trim((string) ($input['birth_date'] ?? ''));
    $sex = strtolower(trim((string) ($input['sex'] ?? '')));
    $civilStatus = strtolower(trim((string) ($input['civil_status'] ?? '')));

    $namePattern = "/^[\p{L}\s.'-]+$/u";

    if ($firstName === '' || mb_strlen($firstName) > 80 || !preg_match($namePattern, $firstName)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid first name.'];
    }
    if ($lastName === '' || mb_strlen($lastName) > 80 || !preg_match($namePattern, $lastName)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid last name.'];
    }
    if ($middleName !== '' && (mb_strlen($middleName) > 80 || !preg_match($namePattern, $middleName))) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid middle name.'];
    }
    if ($suffix !== '' && (mb_strlen($suffix) > 20 || !preg_match("/^[\p{L}.'-]+$/u", $suffix))) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid suffix.'];
    }

    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $birthDate);
    if ($date === false || $date->format('Y-m-d') !== $birthDate) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter your birth date.'];
    }
    $today = new DateTimeImmutable('today');
    if ($date > $today) {
        return ['ok' => false, 'status' => 422, 'error' => 'Birth date cannot be in the future.'];
    }
    if ($date->diff($today)->y > 130) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid birth date.'];
    }

    if (!in_array($sex, ['male', 'female', 'other'], true)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Select your sex.'];
    }
    if (!in_array($civilStatus, ['single', 'married', 'widowed', 'separated', 'annulled'], true)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Select your civil status.'];
    }

    $fullName = $firstName;
    if ($middleName !== '') {
        $fullName .= ' ' . $middleName;
    }
    $fullName .= ' ' . $lastName;
    if ($suffix !== '') {
        $fullName .= ' ' . $suffix;
    }

    if ($record === null) {
        $statement = db()->prepare(
            "INSERT INTO residents
                (user_id, resident_code, full_name, first_name, middle_name, last_name, suffix,
                 household_number, address_line, birth_date, sex, civil_status, profile_completed_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NULL, '', ?, ?, ?, NOW())"
        );
        $statement->execute([
            $userId,
            resident_code_for_user($userId),
            $fullName,
            $firstName,
            $middleName !== '' ? $middleName : null,
            $lastName,
            $suffix !== '' ? $suffix : null,
            $birthDate,
            $sex,
            $civilStatus,
        ]);
    } else {
        $statement = db()->prepare(
            "UPDATE residents
             SET first_name = ?, middle_name = ?, last_name = ?, suffix = ?, full_name = ?,
                 birth_date = ?, sex = ?, civil_status = ?, profile_completed_at = NOW()
             WHERE user_id = ?"
        );
        $statement->execute([
            $firstName,
            $middleName !== '' ? $middleName : null,
            $lastName,
            $suffix !== '' ? $suffix : null,
            $fullName,
            $birthDate,
            $sex,
            $civilStatus,
            $userId,
        ]);
    }

    return [
        'ok' => true,
        'message' => 'Your profile has been saved.',
        'full_name' => $fullName,
    ];
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
