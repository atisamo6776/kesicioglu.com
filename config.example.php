<?php
/**
 * Example configuration file.
 *
 * Copy this file to config.php and fill in your own values.
 * IMPORTANT: config.php is gitignored on purpose.
 */

declare(strict_types=1);

// ---------- Session ----------
if (session_status() !== PHP_SESSION_ACTIVE) {
    // Basic secure defaults; adjust if you run on HTTP locally.
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    // ini_set('session.cookie_secure', '1'); // enable when using HTTPS
    session_start();
}

// ---------- Database ----------
// Update these for your environment
define('DB_HOST', 'localhost');
define('DB_NAME', 'kesicioglu_db');
define('DB_USER', 'kesicioglu_user');
define('DB_PASS', 'your_password');

define('SITE_URL', 'https://kesicioglu.com');

// Upload/App settings
define('UPLOADS_DIR', __DIR__ . '/uploads');
define('APPS_DIR', __DIR__ . '/apps');

// ---------- PDO ----------
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Database connection error.';
    exit;
}

// ---------- Helpers ----------
function sanitize($value): string {
    if (is_array($value)) return '';
    return trim((string)$value);
}

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function isLoggedIn(): bool {
    return !empty($_SESSION['admin_id']);
}

// ---------- CSRF ----------
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(?string $token): bool {
    return is_string($token) && !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

function require_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['_csrf'] ?? '';
        if (!csrf_verify($token)) {
            http_response_code(403);
            echo 'CSRF verification failed.';
            exit;
        }
    }
}

// ---------- Simple rate limit (login) ----------
function rate_limit_ok(string $key, int $maxAttempts, int $windowSeconds): bool {
    $now = time();
    if (!isset($_SESSION['_rate'])) $_SESSION['_rate'] = [];

    if (!isset($_SESSION['_rate'][$key])) {
        $_SESSION['_rate'][$key] = [];
    }

    // keep only within window
    $_SESSION['_rate'][$key] = array_values(array_filter(
        $_SESSION['_rate'][$key],
        fn($ts) => ($now - (int)$ts) <= $windowSeconds
    ));

    return count($_SESSION['_rate'][$key]) < $maxAttempts;
}

function rate_limit_hit(string $key): void {
    if (!isset($_SESSION['_rate'])) $_SESSION['_rate'] = [];
    if (!isset($_SESSION['_rate'][$key])) $_SESSION['_rate'][$key] = [];
    $_SESSION['_rate'][$key][] = time();
}
