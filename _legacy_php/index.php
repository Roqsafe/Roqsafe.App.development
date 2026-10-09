<?php
// ── index.php ─────────────────────────────────────────────────
// This file is here to ensure compatibility with development
// servers (like PHP's built-in server `php -S`) that do not
// support .htaccess URL rewriting.
// All requests are forwarded to the Next.js-style app router.

ob_start(function($content) {
    $uts7Script = '<script src="/UTS7/app.js"></script>';
    if (stripos($content, '</body>') !== false) {
        return str_ireplace('</body>', $uts7Script . "\n</body>", $content);
    } else {
        // Only append if it looks like HTML, else leave as is (e.g. for JSON API)
        if (!empty($content) && stripos($content, '<html') !== false) {
            return $content . "\n" . $uts7Script;
        }
    }
    return $content;
});

require_once __DIR__ . '/router.php';
