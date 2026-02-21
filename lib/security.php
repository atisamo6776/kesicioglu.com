<?php
/**
 * Security helpers (CSRF, safe uploads, safe ZIP extraction)
 *
 * This file is tracked in git. Keep secrets in config.php (gitignored).
 */

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    // If config.php did not start the session, do it here.
    @session_start();
}

if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

// ---------------- CSRF ----------------
function app_csrf_token(): string {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function app_csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . e(app_csrf_token()) . '">';
}

function app_csrf_verify(?string $token): bool {
    return is_string($token) && !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

function app_require_csrf_post(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $token = $_POST['_csrf'] ?? '';
        if (!app_csrf_verify($token)) {
            http_response_code(403);
            echo 'CSRF verification failed.';
            exit;
        }
    }
}

function app_require_csrf_get(): void {
    $token = $_GET['_csrf'] ?? '';
    if (!app_csrf_verify($token)) {
        http_response_code(403);
        echo 'CSRF verification failed.';
        exit;
    }
}

// ---------------- Login rate limit ----------------
function app_rate_limit_key(string $purpose): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    return $purpose . ':' . hash('sha256', $ip);
}

function app_rate_limit_ok(string $key, int $maxAttempts, int $windowSeconds): bool {
    $now = time();
    if (!isset($_SESSION['_rate'])) $_SESSION['_rate'] = [];
    if (!isset($_SESSION['_rate'][$key])) $_SESSION['_rate'][$key] = [];

    $_SESSION['_rate'][$key] = array_values(array_filter(
        $_SESSION['_rate'][$key],
        fn($ts) => ($now - (int)$ts) <= $windowSeconds
    ));

    return count($_SESSION['_rate'][$key]) < $maxAttempts;
}

function app_rate_limit_hit(string $key): void {
    if (!isset($_SESSION['_rate'])) $_SESSION['_rate'] = [];
    if (!isset($_SESSION['_rate'][$key])) $_SESSION['_rate'][$key] = [];
    $_SESSION['_rate'][$key][] = time();
}

// ---------------- Safe uploads ----------------
function app_ensure_dir(string $dir, int $mode = 0755): void {
    if (!is_dir($dir)) {
        mkdir($dir, $mode, true);
    }
}

function app_safe_filename(string $name): string {
    $name = basename($name);
    $name = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $name);
    $name = trim($name, '-');
    if ($name === '') $name = 'file';
    return $name;
}

function app_is_allowed_image_ext(string $ext): bool {
    return in_array(strtolower($ext), ['jpg','jpeg','png','gif','webp'], true);
}

// ---------------- Safe ZIP extraction for web apps ----------------
function app_zip_entry_is_safe(string $entryName): bool {
    if ($entryName === '') return false;
    // no absolute paths
    if ($entryName[0] === '/' || preg_match('~^[a-zA-Z]:\\\\~', $entryName)) return false;
    // no traversal
    if (strpos($entryName, '..') !== false) return false;
    return true;
}

function app_is_allowed_webapp_ext(string $ext): bool {
    $ext = strtolower($ext);
    // allow static assets only
    $allow = ['html','htm','css','js','json','txt','md','png','jpg','jpeg','gif','webp','svg','ico','map'];
    return in_array($ext, $allow, true);
}

function app_extract_zip_safely(string $zipPath, string $destDir, int $maxFiles = 800): void {
    $zip = new ZipArchive();
    if ($zip->open($zipPath) !== true) {
        throw new RuntimeException('ZIP açılamadı.');
    }

    if ($zip->numFiles > $maxFiles) {
        $zip->close();
        throw new RuntimeException('ZIP çok fazla dosya içeriyor.');
    }

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $stat = $zip->statIndex($i);
        $name = $stat['name'] ?? '';

        if (!app_zip_entry_is_safe($name)) {
            $zip->close();
            throw new RuntimeException('ZIP içinde güvenli olmayan dosya yolu bulundu.');
        }

        // directories end with /
        if (str_ends_with($name, '/')) {
            app_ensure_dir($destDir . '/' . $name);
            continue;
        }

        $ext = pathinfo($name, PATHINFO_EXTENSION);
        if ($ext !== '' && !app_is_allowed_webapp_ext($ext)) {
            $zip->close();
            throw new RuntimeException('ZIP içinde izin verilmeyen dosya uzantısı bulundu: ' . $ext);
        }

        $targetPath = $destDir . '/' . $name;
        $targetDir = dirname($targetPath);
        app_ensure_dir($targetDir);

        $stream = $zip->getStream($name);
        if (!$stream) {
            $zip->close();
            throw new RuntimeException('ZIP dosyası okunamadı.');
        }

        $out = fopen($targetPath, 'wb');
        if (!$out) {
            fclose($stream);
            $zip->close();
            throw new RuntimeException('Dosya yazılamadı.');
        }

        stream_copy_to_stream($stream, $out);
        fclose($out);
        fclose($stream);
    }

    $zip->close();
}
