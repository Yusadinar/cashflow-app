<?php
// config/app.php — auto-detect base URL (works on localhost/subfolder & live server)

function base_url(string $path = ''): string
{
    // Detect protocol
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Detect the subfolder this app lives in (e.g. /cashflow-app)
    // __DIR__ is  /…/cashflow-app/config  → go up one level to get the app root
    $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
    $appRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');

    // Subfolder relative to document root
    $baseDir = str_replace($docRoot, '', $appRoot);
    $baseDir = rtrim($baseDir, '/');

    // Normalise leading slash on $path
    $path = '/' . ltrim($path, '/');

    return $protocol . '://' . $host . $baseDir . $path;
}

// Shorthand for HTML attributes
function url(string $path = ''): string
{
    return htmlspecialchars(base_url($path), ENT_QUOTES, 'UTF-8');
}

// Redirect helper
function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}
