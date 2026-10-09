<?php
// ── lib/env.php ───────────────────────────────────────────────
// Simple .env loader — no Composer needed.
// Parses KEY=VALUE lines, strips quotes and comments.

function svi_load_env(?string $path = null): void {
    $path = $path ?? dirname(__DIR__) . '/.env';

    if (!is_file($path) || !is_readable($path)) {
        return; // fail silently; server env vars may already be set
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);

        // Skip comments and blank lines
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        // Must contain exactly one = sign (after the key)
        if (!str_contains($line, '=')) {
            continue;
        }

        [$rawKey, $rawVal] = explode('=', $line, 2);
        $key = trim($rawKey);
        $val = trim($rawVal);

        // Strip surrounding quotes ("value" or 'value')
        if (strlen($val) >= 2) {
            $first = $val[0];
            $last  = $val[strlen($val) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $val = substr($val, 1, -1);
            }
        }

        // Strip inline comment (e.g.  VALUE=foo # comment)
        if (str_contains($val, ' #')) {
            $val = trim(explode(' #', $val, 2)[0]);
        }

        // Only set if not already set (server env takes priority)
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $val;
        }
        if (getenv($key) === false) {
            putenv("{$key}={$val}");
        }
    }
}

// Auto-load on require
svi_load_env();

/**
 * Get an env variable with optional default fallback.
 */
function env(string $key, mixed $default = null): mixed {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}
