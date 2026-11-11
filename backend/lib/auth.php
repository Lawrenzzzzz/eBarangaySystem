<?php
declare(strict_types=1);

function send_email_verification(string $rawEmail): array
{
    $email = strtolower(trim($rawEmail));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid email address.'];
    }

    $pdo = db();
    $recent = $pdo->prepare("SELECT TIMESTAMPDIFF(SECOND, created_at, NOW()) AS elapsed FROM auth_tokens WHERE type = 'email_verification' AND destination = ? ORDER BY id DESC LIMIT 1");
    $recent->execute([$email]);
    $elapsed = $recent->fetchColumn();
    if ($elapsed !== false && $elapsed !== null && (int) $elapsed < 60) {
        return [
            'ok' => false,
            'status' => 429,
            'error' => 'Please wait before requesting another code.',
            'retry_after' => max(1, 60 - (int) $elapsed),
        ];
    }

    $hourly = $pdo->prepare("SELECT COUNT(*) FROM auth_tokens WHERE type = 'email_verification' AND destination = ? AND created_at > (NOW() - INTERVAL 1 HOUR)");
    $hourly->execute([$email]);
    if ((int) $hourly->fetchColumn() >= 5) {
        return ['ok' => false, 'status' => 429, 'error' => 'Too many verification requests. Please try again later.'];
    }

    $pdo->prepare("UPDATE auth_tokens SET revoked_at = NOW() WHERE type = 'email_verification' AND destination = ? AND used_at IS NULL AND revoked_at IS NULL")->execute([$email]);

    $code = (string) random_int(100000, 999999);
    $expiresAt = date('Y-m-d H:i:s', time() + 600);
    $statement = $pdo->prepare("INSERT INTO auth_tokens (user_id, type, destination, token_hash, expires_at, requested_ip, user_agent, metadata) VALUES (NULL, 'email_verification', ?, ?, ?, ?, ?, ?)");
    $statement->execute([
        $email,
        password_hash($code, PASSWORD_BCRYPT),
        $expiresAt,
        client_ip(),
        user_agent(),
        json_encode(['purpose' => 'signup_email']),
    ]);
    $verificationId = (int) $pdo->lastInsertId();

    $text = "Your e-Barangay Bucana verification code is {$code}. It expires in 10 minutes.";
    $html = '<p>Your e-Barangay Bucana verification code is:</p>'
        . '<p style="font-size:28px;font-weight:700;letter-spacing:6px">' . $code . '</p>'
        . '<p>This code expires in 10 minutes. If you did not request this, you can ignore this email.</p>';
    $mail = send_email($email, 'Your e-Barangay verification code', $text, $html);
    if (!($mail['sent'] ?? false)) {
        if (APP_ENV !== 'production') {
            log_line('mail.log', "Development email OTP for {$email}: {$code}");
        }
        if (APP_ENV === 'production') {
            $pdo->prepare("UPDATE auth_tokens SET revoked_at = NOW() WHERE id = ?")->execute([$verificationId]);
            return ['ok' => false, 'status' => 503, 'error' => 'The email service is unavailable right now. Please try again later.'];
        }
    }

    $result = [
        'ok' => true,
        'verification_id' => $verificationId,
        'email' => $email,
        'expires_in' => 600,
        'email_sent' => (bool) ($mail['sent'] ?? false),
    ];

    if (APP_ENV !== 'production' && !($mail['sent'] ?? false)) {
        $result['debug_code'] = $code;
    }

    return $result;
}

function verify_email_code(int $verificationId, string $rawCode): array
{
    $code = preg_replace('/\D+/', '', $rawCode);
    if ($code === null || strlen($code) !== 6) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter the 6-digit verification code.'];
    }

    $pdo = db();
    $pdo->beginTransaction();

    $statement = $pdo->prepare("SELECT * FROM auth_tokens WHERE id = ? AND type = 'email_verification' AND used_at IS NULL AND revoked_at IS NULL FOR UPDATE");
    $statement->execute([$verificationId]);
    $token = $statement->fetch();

    if (!$token) {
        $pdo->rollBack();
        return ['ok' => false, 'status' => 422, 'error' => 'This verification request has expired. Request a new code.'];
    }

    if (strtotime((string) $token['expires_at']) < time()) {
        $pdo->prepare("UPDATE auth_tokens SET revoked_at = NOW() WHERE id = ?")->execute([$verificationId]);
        $pdo->commit();
        return ['ok' => false, 'status' => 422, 'error' => 'The verification code has expired. Request a new code.'];
    }

    if ((int) $token['attempts'] >= 5) {
        $pdo->prepare("UPDATE auth_tokens SET revoked_at = NOW() WHERE id = ?")->execute([$verificationId]);
        $pdo->commit();
        return ['ok' => false, 'status' => 429, 'error' => 'Too many incorrect attempts. Request a new code.'];
    }

    if (!password_verify($code, (string) $token['token_hash'])) {
        $attempts = (int) $token['attempts'] + 1;
        $revokedAt = $attempts >= 5 ? date('Y-m-d H:i:s') : null;
        $pdo->prepare("UPDATE auth_tokens SET attempts = ?, revoked_at = ? WHERE id = ?")->execute([$attempts, $revokedAt, $verificationId]);
        $pdo->commit();
        return ['ok' => false, 'status' => 422, 'error' => 'Incorrect verification code. Please try again.'];
    }

    $proof = random_token(32);
    $pdo->prepare("UPDATE auth_tokens SET token_hash = ?, expires_at = ?, attempts = 0, metadata = ? WHERE id = ?")->execute([
        token_hash($proof),
        date('Y-m-d H:i:s', time() + 900),
        json_encode(['purpose' => 'signup_email', 'verified' => true, 'verified_at' => date('c')]),
        $verificationId,
    ]);
    $pdo->commit();

    return [
        'ok' => true,
        'email' => $token['destination'],
        'email_proof' => $proof,
        'expires_in' => 900,
    ];
}

function register_account(array $input): array
{
    $fullName = trim((string) ($input['fullName'] ?? ''));
    $email = strtolower(trim((string) ($input['email'] ?? '')));
    $address = trim((string) ($input['address'] ?? ''));
    $householdNumber = trim((string) ($input['householdNumber'] ?? ''));
    $password = (string) ($input['password'] ?? '');
    $emailProof = trim((string) ($input['emailProof'] ?? ''));
    $phone = normalize_phone((string) ($input['phone'] ?? ''));

    if (strlen($fullName) < 2 || strlen($fullName) > 150 || !preg_match("/^[\p{L}\s.'-]+$/u", $fullName)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter your complete name using letters only.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid email address.'];
    }
    if ($phone === null) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid Philippine mobile number (09XXXXXXXXX).'];
    }
    if (strlen($address) < 5 || strlen($address) > 255) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter your complete address.'];
    }
    if (strlen($householdNumber) > 40) {
        return ['ok' => false, 'status' => 422, 'error' => 'Household number is too long.'];
    }
    $passwordError = validate_password_error($password);
    if ($passwordError !== null) {
        return ['ok' => false, 'status' => 422, 'error' => $passwordError];
    }
    if ($emailProof === '') {
        return ['ok' => false, 'status' => 422, 'error' => 'Verify your email address before creating your account.'];
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $existing = $pdo->prepare("SELECT email, phone FROM users WHERE email = ? OR phone = ? LIMIT 1");
        $existing->execute([$email, $phone]);
        $existingUser = $existing->fetch();
        if ($existingUser) {
            $pdo->rollBack();
            $field = $existingUser['email'] === $email ? 'email address' : 'mobile number';
            return ['ok' => false, 'status' => 409, 'error' => 'An account with this ' . $field . ' already exists.'];
        }

        $proofStatement = $pdo->prepare("SELECT id, destination FROM auth_tokens WHERE token_hash = ? AND type = 'email_verification' AND used_at IS NULL AND revoked_at IS NULL AND expires_at > NOW() FOR UPDATE");
        $proofStatement->execute([token_hash($emailProof)]);
        $proof = $proofStatement->fetch();
        if (!$proof || strtolower((string) $proof['destination']) !== $email) {
            $pdo->rollBack();
            return ['ok' => false, 'status' => 422, 'error' => 'Email verification has expired. Please verify your email address again.'];
        }

        $insertUser = $pdo->prepare("INSERT INTO users (role_id, email, phone, password_hash, account_type, status, email_verified_at, password_changed_at) VALUES (1, ?, ?, ?, 'resident', 'active', NOW(), NOW())");
        $insertUser->execute([$email, $phone, password_hash($password, PASSWORD_BCRYPT)]);
        $userId = (int) $pdo->lastInsertId();
        $residentCode = 'BSG-' . str_pad((string) $userId, 5, '0', STR_PAD_LEFT);

        $parts = preg_split('/\s+/', $fullName) ?: [$fullName];
        $firstName = $parts[0];
        $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : null;

        $insertResident = $pdo->prepare("INSERT INTO residents (user_id, resident_code, full_name, first_name, last_name, household_number, address_line) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertResident->execute([
            $userId,
            $residentCode,
            $fullName,
            $firstName,
            $lastName,
            $householdNumber !== '' ? $householdNumber : null,
            $address,
        ]);

        $pdo->prepare("UPDATE auth_tokens SET used_at = NOW() WHERE id = ?")->execute([(int) $proof['id']]);
        $pdo->commit();

        return [
            'ok' => true,
            'user_id' => $userId,
            'email' => $email,
            'account_type' => 'resident',
            'resident_code' => $residentCode,
            'message' => 'Account created. You can now sign in.',
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

function sign_in_user(string $rawEmail, string $password): array
{
    $email = strtolower(trim($rawEmail));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        return ['ok' => false, 'status' => 401, 'error' => 'Incorrect email or password.'];
    }

    $pdo = db();
    $statement = $pdo->prepare("SELECT u.*, r.code AS role_code FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.email = ? AND u.deleted_at IS NULL LIMIT 1");
    $statement->execute([$email]);
    $user = $statement->fetch();

    if (!$user) {
        return ['ok' => false, 'status' => 401, 'error' => 'Incorrect email or password.'];
    }

    if (!empty($user['locked_until']) && strtotime((string) $user['locked_until']) > time()) {
        return [
            'ok' => false,
            'status' => 429,
            'error' => 'Too many failed sign-in attempts. Please try again later.',
            'retry_after' => strtotime((string) $user['locked_until']) - time(),
        ];
    }

    if (!password_verify($password, (string) $user['password_hash'])) {
        $attempts = (int) $user['failed_login_attempts'] + 1;
        $lockedUntil = $attempts >= 5 ? date('Y-m-d H:i:s', time() + 900) : null;
        $pdo->prepare("UPDATE users SET failed_login_attempts = ?, locked_until = ? WHERE id = ?")->execute([$attempts, $lockedUntil, (int) $user['id']]);

        if ($lockedUntil !== null) {
            return ['ok' => false, 'status' => 429, 'error' => 'Too many failed sign-in attempts. Your account is locked for 15 minutes.', 'retry_after' => 900];
        }
        return ['ok' => false, 'status' => 401, 'error' => 'Incorrect email or password.'];
    }

    if ($user['status'] !== 'active') {
        return ['ok' => false, 'status' => 403, 'error' => 'This account is not active. Please contact the barangay office.'];
    }

    $pdo->prepare("UPDATE users SET failed_login_attempts = 0, locked_until = NULL, last_login_at = NOW() WHERE id = ?")->execute([(int) $user['id']]);

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    $roleCode = (string) ($user['role_code'] ?? 'resident');
    $redirect = match ($roleCode) {
        'admin', 'captain', 'super_admin' => url('admin/dashboard'),
        'official', 'staff', 'kagawad', 'secretary', 'treasurer' => url('official/dashboard'),
        default => url('dashboard'),
    };

    return [
        'ok' => true,
        'redirect' => $redirect,
        'user' => [
            'user_id' => (int) $user['id'],
            'email' => $user['email'],
            'account_type' => $user['account_type'],
            'role_code' => $roleCode,
            'status' => $user['status'],
        ],
    ];
}

function sign_out_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
    session_destroy();
}

function account_details(int $userId): ?array
{
    $statement = db()->prepare(
        "SELECT v.*, u.full_name AS account_full_name, u.avatar_filename
         FROM v_user_accounts v
         JOIN users u ON u.id = v.user_id
         WHERE v.user_id = ?
         LIMIT 1"
    );
    $statement->execute([$userId]);
    $row = $statement->fetch();
    if (!$row) {
        return null;
    }

    $viewFullName = trim((string) ($row['full_name'] ?? ''));
    $accountFullName = trim((string) ($row['account_full_name'] ?? ''));

    return [
        'user_id' => (int) $row['user_id'],
        'email' => $row['email'],
        'phone' => $row['phone'],
        'avatar_filename' => (string) ($row['avatar_filename'] ?? ''),
        'account_type' => $row['account_type'],
        'account_status' => $row['account_status'],
        'last_login_at' => $row['last_login_at'],
        'role_code' => $row['role_code'],
        'role_name' => $row['role_name'],
        'resident_code' => $row['resident_code'],
        'full_name' => $viewFullName !== '' ? $viewFullName : $accountFullName,
        'account_full_name' => $accountFullName,
        'household_number' => $row['household_number'],
        'address_line' => $row['address_line'],
        'requested_position' => $row['requested_position'],
        'employee_number' => $row['employee_number'],
        'official_application_status' => $row['official_application_status'],
        'official_application_created_at' => $row['official_application_created_at'],
    ];
}

function request_password_reset(string $rawEmail): array
{
    $email = strtolower(trim($rawEmail));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'status' => 422, 'error' => 'Enter a valid email address.'];
    }

    $message = 'If an account exists for that email, a password reset link has been sent.';
    $pdo = db();
    $statement = $pdo->prepare("SELECT id, email FROM users WHERE email = ? AND status = 'active' AND deleted_at IS NULL LIMIT 1");
    $statement->execute([$email]);
    $user = $statement->fetch();

    if (!$user) {
        return ['ok' => true, 'message' => $message];
    }

    $recent = $pdo->prepare("SELECT COUNT(*) FROM auth_tokens WHERE type = 'password_reset' AND destination = ? AND created_at > (NOW() - INTERVAL 15 MINUTE)");
    $recent->execute([$email]);
    if ((int) $recent->fetchColumn() >= 3) {
        return ['ok' => true, 'message' => $message];
    }

    $pdo->prepare("UPDATE auth_tokens SET revoked_at = NOW() WHERE user_id = ? AND type = 'password_reset' AND used_at IS NULL AND revoked_at IS NULL")->execute([(int) $user['id']]);

    $token = random_token(32);
    $insert = $pdo->prepare("INSERT INTO auth_tokens (user_id, type, destination, token_hash, expires_at, requested_ip, user_agent, metadata) VALUES (?, 'password_reset', ?, ?, ?, ?, ?, ?)");
    $insert->execute([
        (int) $user['id'],
        $email,
        token_hash($token),
        date('Y-m-d H:i:s', time() + 1800),
        client_ip(),
        user_agent(),
        json_encode(['purpose' => 'password_reset']),
    ]);

    $resetUrl = APP_URL . url('reset-password') . '?token=' . urlencode($token);
    log_line('mail.log', "Password reset link for {$email}: {$resetUrl}");

    $result = ['ok' => true, 'message' => $message];
    if (APP_ENV !== 'production') {
        $result['debug_reset_url'] = $resetUrl;
    }
    return $result;
}

function complete_password_reset(string $token, string $password, string $confirmPassword): array
{
    if (strlen($token) < 32) {
        return ['ok' => false, 'status' => 422, 'error' => 'This password reset link is invalid or has expired.'];
    }
    $passwordError = validate_password_error($password);
    if ($passwordError !== null) {
        return ['ok' => false, 'status' => 422, 'error' => $passwordError];
    }
    if ($password !== $confirmPassword) {
        return ['ok' => false, 'status' => 422, 'error' => 'The password confirmation does not match.'];
    }

    $pdo = db();
    $pdo->beginTransaction();

    $statement = $pdo->prepare("SELECT * FROM auth_tokens WHERE token_hash = ? AND type = 'password_reset' AND used_at IS NULL AND revoked_at IS NULL AND expires_at > NOW() FOR UPDATE");
    $statement->execute([token_hash($token)]);
    $reset = $statement->fetch();

    if (!$reset) {
        $pdo->rollBack();
        return ['ok' => false, 'status' => 422, 'error' => 'This password reset link is invalid or has expired.'];
    }

    $userId = (int) $reset['user_id'];
    $pdo->prepare("UPDATE users SET password_hash = ?, password_changed_at = NOW(), failed_login_attempts = 0, locked_until = NULL WHERE id = ?")->execute([
        password_hash($password, PASSWORD_BCRYPT),
        $userId,
    ]);
    $pdo->prepare("UPDATE auth_tokens SET used_at = NOW() WHERE id = ?")->execute([(int) $reset['id']]);
    $pdo->prepare("UPDATE auth_tokens SET revoked_at = NOW() WHERE user_id = ? AND type = 'password_reset' AND used_at IS NULL AND revoked_at IS NULL")->execute([$userId]);
    $pdo->commit();

    return ['ok' => true, 'message' => 'Your password has been updated. You can now sign in.'];
}
