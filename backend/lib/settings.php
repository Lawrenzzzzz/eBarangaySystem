<?php
declare(strict_types=1);

function barangay_profile_record(): array
{
    $row = db()->query("SELECT * FROM barangay_settings WHERE id = 1 LIMIT 1")->fetch();

    if ($row === false) {
        return [
            'barangay_name' => '',
            'municipality' => '',
            'province' => '',
            'chairman' => '',
            'term' => '',
            'puroks' => 0,
            'sitios' => 0,
            'contact_number' => '',
            'email' => '',
            'office_address' => '',
        ];
    }

    return [
        'barangay_name' => (string) $row['barangay_name'],
        'municipality' => (string) $row['municipality'],
        'province' => (string) $row['province'],
        'chairman' => (string) $row['chairman'],
        'term' => (string) $row['term'],
        'puroks' => (int) $row['puroks'],
        'sitios' => (int) $row['sitios'],
        'contact_number' => (string) ($row['contact_number'] ?? ''),
        'email' => (string) ($row['email'] ?? ''),
        'office_address' => (string) $row['office_address'],
    ];
}

function save_barangay_profile(int $userId, array $input): array
{
    $barangayName = trim((string) ($input['barangay_name'] ?? ''));
    $municipality = trim((string) ($input['municipality'] ?? ''));
    $province = trim((string) ($input['province'] ?? ''));
    $chairman = trim((string) ($input['chairman'] ?? ''));
    $term = trim((string) ($input['term'] ?? ''));
    $puroks = (int) ($input['puroks'] ?? 0);
    $sitios = (int) ($input['sitios'] ?? 0);

    if ($barangayName === '' || mb_strlen($barangayName) > 150) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter the barangay name.'];
    }
    if (mb_strlen($municipality) > 150) {
        return ['ok' => false, 'status' => 422, 'error' => 'The municipality name is too long.'];
    }
    if (mb_strlen($province) > 150) {
        return ['ok' => false, 'status' => 422, 'error' => 'The province name is too long.'];
    }
    if (mb_strlen($chairman) > 150) {
        return ['ok' => false, 'status' => 422, 'error' => 'The chairman name is too long.'];
    }
    if (mb_strlen($term) > 60) {
        return ['ok' => false, 'status' => 422, 'error' => 'The term is too long.'];
    }
    if ($puroks < 0 || $puroks > 9999 || $sitios < 0 || $sitios > 9999) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid number of puroks and sitios.'];
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $statement = $pdo->prepare(
            "INSERT INTO barangay_settings (id, barangay_name, municipality, province, chairman, term, puroks, sitios, updated_by)
             VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                barangay_name = VALUES(barangay_name),
                municipality = VALUES(municipality),
                province = VALUES(province),
                chairman = VALUES(chairman),
                term = VALUES(term),
                puroks = VALUES(puroks),
                sitios = VALUES(sitios),
                updated_by = VALUES(updated_by)"
        );
        $statement->execute([$barangayName, $municipality, $province, $chairman, $term, $puroks, $sitios, $userId > 0 ? $userId : null]);

        if ($chairman !== '') {
            $updateChairmanAccount = $pdo->prepare(
                "UPDATE users u
                 JOIN roles r ON r.id = u.role_id
                 SET u.full_name = ?
                 WHERE r.code IN ('admin', 'captain') AND u.deleted_at IS NULL"
            );
            $updateChairmanAccount->execute([$chairman]);
        }

        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }

    return [
        'ok' => true,
        'message' => 'Barangay profile saved.',
        'profile' => barangay_profile_record(),
    ];
}

function official_position_options(): array
{
    return [
        'secretary' => 'Barangay Secretary',
        'treasurer' => 'Barangay Treasurer',
        'kagawad' => 'Kagawad',
        'staff' => 'Barangay Staff',
        'official' => 'Barangay Official',
    ];
}

function create_official_account(int $createdByUserId, array $input): array
{
    $fullName = trim((string) ($input['full_name'] ?? ''));
    $email = strtolower(trim((string) ($input['email'] ?? '')));
    $phoneRaw = trim((string) ($input['phone'] ?? ''));
    $phone = $phoneRaw === '' ? null : normalize_phone($phoneRaw);
    $position = strtolower(trim((string) ($input['position'] ?? '')));
    $password = (string) ($input['password'] ?? '');
    $confirmPassword = (string) ($input['confirm_password'] ?? '');

    $positions = official_position_options();

    if (strlen($fullName) < 2 || strlen($fullName) > 150 || !preg_match("/^[\p{L}\s.'-]+$/u", $fullName)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter the complete name using letters only.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid email address.'];
    }
    if ($phoneRaw !== '' && $phone === null) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid Philippine mobile number (09XXXXXXXXX).'];
    }
    if (!array_key_exists($position, $positions)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Select a valid position.'];
    }

    $passwordError = validate_password_error($password);
    if ($passwordError !== null) {
        return ['ok' => false, 'status' => 422, 'error' => $passwordError];
    }
    if ($password !== $confirmPassword) {
        return ['ok' => false, 'status' => 422, 'error' => 'The password confirmation does not match.'];
    }

    $roleId = $position === 'captain' ? 3 : 2;

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
            "INSERT INTO users (role_id, email, full_name, phone, password_hash, account_type, status, email_verified_at, password_changed_at)
             VALUES (?, ?, ?, ?, ?, 'official', 'active', NOW(), NOW())"
        );
        $insertUser->execute([$roleId, $email, $fullName, $phone, password_hash($password, PASSWORD_BCRYPT)]);
        $userId = (int) $pdo->lastInsertId();

        $employeeNumber = 'EMP-' . str_pad((string) $userId, 4, '0', STR_PAD_LEFT);
        $insertApplication = $pdo->prepare(
            "INSERT INTO official_applications (user_id, requested_position, employee_number, status, reviewed_by, reviewed_at)
             VALUES (?, ?, ?, 'approved', ?, NOW())"
        );
        $insertApplication->execute([$userId, $positions[$position], $employeeNumber, $createdByUserId > 0 ? $createdByUserId : null]);

        $pdo->commit();

        return [
            'ok' => true,
            'user_id' => $userId,
            'employee_number' => $employeeNumber,
            'message' => $fullName . ' has been added as ' . $positions[$position] . '.',
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

function change_account_password(int $userId, array $input): array
{
    $currentPassword = (string) ($input['current_password'] ?? '');
    $newPassword = (string) ($input['new_password'] ?? '');
    $confirmPassword = (string) ($input['confirm_password'] ?? '');

    if ($currentPassword === '') {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter your current password.'];
    }

    $statement = db()->prepare("SELECT password_hash FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
    $statement->execute([$userId]);
    $user = $statement->fetch();

    if ($user === false) {
        return ['ok' => false, 'status' => 404, 'error' => 'Account was not found.'];
    }
    if (!password_verify($currentPassword, (string) $user['password_hash'])) {
        return ['ok' => false, 'status' => 422, 'error' => 'Your current password is incorrect.'];
    }

    $passwordError = validate_password_error($newPassword);
    if ($passwordError !== null) {
        return ['ok' => false, 'status' => 422, 'error' => $passwordError];
    }
    if ($newPassword !== $confirmPassword) {
        return ['ok' => false, 'status' => 422, 'error' => 'The password confirmation does not match.'];
    }
    if (password_verify($newPassword, (string) $user['password_hash'])) {
        return ['ok' => false, 'status' => 422, 'error' => 'Choose a password that is different from your current one.'];
    }

    $update = db()->prepare(
        "UPDATE users SET password_hash = ?, password_changed_at = NOW(), failed_login_attempts = 0, locked_until = NULL
         WHERE id = ? AND deleted_at IS NULL"
    );
    $update->execute([password_hash($newPassword, PASSWORD_BCRYPT), $userId]);

    return ['ok' => true, 'message' => 'Your password has been updated.'];
}

function update_account_profile(int $userId, array $input): array
{
    $fullName = trim((string) ($input['full_name'] ?? ''));
    $email = strtolower(trim((string) ($input['email'] ?? '')));
    $phoneRaw = trim((string) ($input['phone'] ?? ''));
    $photo = trim((string) ($input['photo'] ?? ''));

    if (strlen($fullName) < 2 || strlen($fullName) > 150 || !preg_match("/^[\p{L}\s.'-]+$/u", $fullName)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter your complete name using letters only.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid email address.'];
    }

    $phone = null;
    if ($phoneRaw !== '') {
        $phone = normalize_phone($phoneRaw);
        if ($phone === null) {
            return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid Philippine mobile number (09XXXXXXXXX).'];
        }
    }

    $duplicate = db()->prepare(
        "SELECT id FROM users
         WHERE id <> ? AND deleted_at IS NULL AND (email = ? OR (? IS NOT NULL AND phone = ?))
         LIMIT 1"
    );
    $duplicate->execute([$userId, $email, $phone, $phone]);
    if ($duplicate->fetch() !== false) {
        return ['ok' => false, 'status' => 409, 'error' => 'Another account already uses this email address or mobile number.'];
    }

    $newAvatar = null;
    $directory = APP_ROOT . '/frontend/assets/uploads/avatars';

    if ($photo !== '') {
        if (!preg_match('#^data:image/(png|jpe?g|webp);base64,([A-Za-z0-9+/=\s]+)$#', $photo, $matches)) {
            return ['ok' => false, 'status' => 422, 'error' => 'Upload a JPG, PNG, or WebP image.'];
        }

        $binary = base64_decode(preg_replace('/\s+/', '', $matches[2]) ?? '', true);
        if ($binary === false || $binary === '') {
            return ['ok' => false, 'status' => 422, 'error' => 'The selected image could not be read. Try another file.'];
        }
        if (strlen($binary) > 2 * 1024 * 1024) {
            return ['ok' => false, 'status' => 422, 'error' => 'The photo must be 2 MB or smaller.'];
        }

        $imageInfo = @getimagesizefromstring($binary);
        $mime = (string) ($imageInfo['mime'] ?? '');
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensions[$mime])) {
            return ['ok' => false, 'status' => 422, 'error' => 'Upload a JPG, PNG, or WebP image.'];
        }

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            return ['ok' => false, 'status' => 500, 'error' => 'Unable to store the photo right now.'];
        }

        $newAvatar = 'user-' . $userId . '-' . bin2hex(random_bytes(8)) . '.' . $extensions[$mime];
        if (file_put_contents($directory . '/' . $newAvatar, $binary) === false) {
            return ['ok' => false, 'status' => 500, 'error' => 'Unable to store the photo right now.'];
        }
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $current = $pdo->prepare("SELECT avatar_filename FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1 FOR UPDATE");
        $current->execute([$userId]);
        $currentRow = $current->fetch();
        if ($currentRow === false) {
            $pdo->rollBack();
            if ($newAvatar !== null) {
                @unlink($directory . '/' . $newAvatar);
            }
            return ['ok' => false, 'status' => 404, 'error' => 'Account was not found.'];
        }

        $oldAvatar = (string) ($currentRow['avatar_filename'] ?? '');
        $avatarFilename = $newAvatar ?? $oldAvatar;

        $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, avatar_filename = ? WHERE id = ? AND deleted_at IS NULL")
            ->execute([$fullName, $email, $phone, $avatarFilename !== '' ? $avatarFilename : null, $userId]);

        $resident = $pdo->prepare("SELECT id FROM residents WHERE user_id = ? LIMIT 1 FOR UPDATE");
        $resident->execute([$userId]);
        if ($resident->fetch() !== false) {
            $parts = preg_split('/\s+/', $fullName) ?: [$fullName];
            $firstName = $parts[0];
            $lastName = count($parts) > 1 ? (string) end($parts) : null;
            $middleName = count($parts) > 2 ? implode(' ', array_slice($parts, 1, -1)) : null;
            $pdo->prepare("UPDATE residents SET first_name = ?, middle_name = ?, last_name = ?, full_name = ? WHERE user_id = ?")
                ->execute([$firstName, $middleName, $lastName, $fullName, $userId]);
        }

        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($newAvatar !== null) {
            @unlink($directory . '/' . $newAvatar);
        }
        if ($exception instanceof PDOException && (string) $exception->getCode() === '23000') {
            return ['ok' => false, 'status' => 409, 'error' => 'Another account already uses this email address or mobile number.'];
        }
        throw $exception;
    }

    if ($newAvatar !== null && $oldAvatar !== '' && $oldAvatar !== $newAvatar) {
        $oldPath = $directory . '/' . basename($oldAvatar);
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }

    return [
        'ok' => true,
        'message' => 'Your profile has been updated.',
        'profile' => account_details($userId),
    ];
}
