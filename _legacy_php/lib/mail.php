<?php
/**
 * ── lib/mail.php ──────────────────────────────────────────────
 * SVI – SMTP Mail Utility
 * Uses Spacemail SMTP to ensure deliverability.
 */

require_once __DIR__ . '/env.php';

/**
 * Internal helper for SMTP commands
 */
function _svi_smtp_cmd($socket, $cmd, $expected) {
    if ($cmd) fputs($socket, $cmd . "\r\n");
    $resp = "";
    while ($line = fgets($socket, 512)) {
        $resp .= $line;
        if (substr($line, 3, 1) === ' ') break;
    }
    if (substr($resp, 0, 3) !== (string)$expected) {
        error_log("[SVI MAIL] SMTP error: Expected $expected, got: " . trim($resp));
        return false;
    }
    return true;
}

/**
 * Send an email via SMTP.
 */
function svi_mail(string $to, string $subject, string $message, array $extraHeaders = []): bool {
    $host = env('SMTP_HOST', 'mail.spacemail.com');
    $port = (int)env('SMTP_PORT', 465);
    $user = env('SMTP_USER', 'security@roqsafe.com');
    $pass = env('SMTP_PASS');
    $auth = env('SMTP_AUTH', 'true') === 'true';
    $secure = env('SMTP_SECURE', 'ssl');

    // Create the message with headers
    $boundary = md5(uniqid(time()));
    $headers  = [
        "From: Roqsafe SVI <{$user}>",
        "Reply-To: " . ($extraHeaders['Reply-To'] ?? $user),
        "MIME-Version: 1.0",
        "Content-Type: text/plain; charset=UTF-8; boundary=\"{$boundary}\"",
        "X-Mailer: Roqsafe-SVI-Mailer"
    ];

    foreach ($extraHeaders as $k => $v) {
        if ($k !== 'Reply-To' && $k !== 'From') {
            $headers[] = "{$k}: {$v}";
        }
    }

    $fullMessage = implode("\r\n", $headers) . "\r\n\r\n" . $message;

    try {
        $isSecurePort = ($port === 465);
        $prefix = $isSecurePort ? 'ssl://' : '';
        $socket = @fsockopen($prefix . $host, $port, $errno, $errstr, 15);

        if (!$socket) {
            error_log("[SVI MAIL] Failed to connect to $host:$port. Error: $errstr ($errno)");
            return false;
        }

        // ── Handshake ──
        if (!_svi_smtp_cmd($socket, null, 220)) { @fclose($socket); return false; }
        if (!_svi_smtp_cmd($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'), 250)) { @fclose($socket); return false; }

        // ── STARTTLS (for 587) ──
        if (!$isSecurePort) {
            if (!_svi_smtp_cmd($socket, "STARTTLS", 220)) { @fclose($socket); return false; }
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                error_log("[SVI MAIL] TLS Encryption failed.");
                @fclose($socket); return false;
            }
            // Re-identify after TLS
            if (!_svi_smtp_cmd($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'), 250)) { @fclose($socket); return false; }
        }

        // ── Auth ──
        if ($auth) {
            if (!_svi_smtp_cmd($socket, "AUTH LOGIN", 334)) { @fclose($socket); return false; }
            if (!_svi_smtp_cmd($socket, base64_encode($user), 334)) { @fclose($socket); return false; }
            if (!_svi_smtp_cmd($socket, base64_encode($pass), 235)) { @fclose($socket); return false; }
        }

        // ── Transaction ──
        if (!_svi_smtp_cmd($socket, "MAIL FROM: <$user>", 250)) { @fclose($socket); return false; }
        if (!_svi_smtp_cmd($socket, "RCPT TO: <$to>", 250)) { @fclose($socket); return false; }
        if (!_svi_smtp_cmd($socket, "DATA", 354)) { @fclose($socket); return false; }

        // Send headers and body
        fputs($socket, "To: $to\r\nSubject: $subject\r\n$fullMessage\r\n.\r\n");
        if (!_svi_smtp_cmd($socket, null, 250)) { @fclose($socket); return false; }

        // ── Quit ──
        fputs($socket, "QUIT\r\n");
        @fclose($socket);

        return true;
    } catch (Throwable $t) {
        error_log("[SVI MAIL] Throwable Error: " . $t->getMessage());
        return false;
    }
}
