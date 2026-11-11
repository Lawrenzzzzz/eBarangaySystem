<?php
declare(strict_types=1);

function send_email(string $to, string $subject, string $text, string $html = ''): array
{
    $script = APP_ROOT . '/smtp_service/send_email.py';
    if (!is_file($script)) {
        log_line('mail.log', 'SMTP helper not found at ' . $script);
        return ['sent' => false, 'reason' => 'missing_helper'];
    }

    $python = (string) env('PYTHON_BIN', PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3');
    $smtpUser = (string) env('SMTP_USER', '');
    $smtpPass = (string) env('SMTP_PASS', '');
    if ($smtpUser === '' || $smtpPass === '' || $smtpUser === 'your.address@gmail.com' || str_starts_with($smtpPass, 'replace-with')) {
        log_line('mail.log', 'SMTP credentials are not configured.');
        return ['sent' => false, 'reason' => 'missing_credentials'];
    }

    $payload = json_encode([
        'to' => $to,
        'subject' => $subject,
        'text' => $text,
        'html' => $html,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $environment = getenv();
    if (!is_array($environment)) {
        $environment = [];
    }
    $environment = array_merge($environment, [
        'SMTP_HOST' => (string) env('SMTP_HOST', 'smtp.gmail.com'),
        'SMTP_PORT' => (string) env('SMTP_PORT', '587'),
        'SMTP_USER' => $smtpUser,
        'SMTP_PASS' => $smtpPass,
        'SMTP_FROM_NAME' => (string) env('SMTP_FROM_NAME', 'e-Barangay Bucana'),
        'SMTP_USE_SSL' => (string) env('SMTP_USE_SSL', '0'),
    ]);

    $command = escapeshellarg($python) . ' ' . escapeshellarg($script);
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = @proc_open($command, $descriptors, $pipes, APP_ROOT, $environment);
    if (!is_resource($process)) {
        log_line('mail.log', 'Unable to start the Python SMTP helper.');
        return ['sent' => false, 'reason' => 'spawn_failed'];
    }

    fwrite($pipes[0], (string) $payload);
    fclose($pipes[0]);

    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    $result = json_decode((string) $stdout, true);
    if (is_array($result) && ($result['sent'] ?? false) === true) {
        return ['sent' => true];
    }

    $reason = is_array($result) && isset($result['error'])
        ? (string) $result['error']
        : trim((string) $stderr);
    log_line('mail.log', 'SMTP send failed (exit ' . $exitCode . '): ' . $reason);
    return ['sent' => false, 'reason' => 'smtp_error', 'error' => $reason];
}
