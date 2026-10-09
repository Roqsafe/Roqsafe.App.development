<?php
// ── lib/db.php ────────────────────────────────────────────────
// PDO database connection — credentials from .env file.
// db.php itself is blocked by .htaccess; credentials live in .env
// which is also blocked from web access.

require_once __DIR__ . '/env.php';   // loads .env → $_ENV

function svi_db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $host    = env('DB_HOST',    'localhost');
    $name    = env('DB_NAME',    'svi_db');
    $user    = env('DB_USER',    'root');
    $pass    = env('DB_PASS',    '');
    $charset = env('DB_CHARSET', 'utf8mb4');

    $dsn     = "mysql:host={$host};dbname={$name};charset={$charset}";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        http_response_code(503);
        error_log('[SVI] DB connection failed: ' . $e->getMessage());
        // Load the nice 503 page and exit
        include __DIR__ . '/../app/503/page.php';
        exit;
    }

    return $pdo;
}

/**
 * Returns a PDO connection to the remote database on roqsafe.com.
 */
function svi_remote_db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $host    = env('DB_REMOTE_HOST',    'localhost');
    $name    = env('DB_REMOTE_NAME',    'erichmsx_Roqsafe');
    $user    = env('DB_REMOTE_USER',    'erichmsx_re_Database_hmsx_Roqs');
    $pass    = env('DB_REMOTE_PASS',    't0OmxbPpPb03');
    $charset = env('DB_REMOTE_CHARSET', 'utf8mb4');

    $dsn     = "mysql:host={$host};dbname={$name};charset={$charset}";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        // Log the error but don't necessarily terminate if the remote DB is secondary
        error_log('[SVI] Remote DB connection failed: ' . $e->getMessage());
        throw $e; // Rethrow to let the caller handle it
    }

    return $pdo;
}
