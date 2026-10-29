<?php

declare(strict_types=1);

define('SITE_ROOT', dirname(__DIR__));

// Los errores internos se registran sin exponer rutas ni credenciales al visitante.
ini_set('display_errors', '0');
ini_set('log_errors', '1');

set_exception_handler(function (Throwable $exception): void {
    error_log('AIDEX landing: fallo interno (' . get_class($exception) . ').');
    http_response_code(503);
    header('Cache-Control: no-store');

    $message = 'El servicio no está disponible en este momento. Inténtalo más tarde.';

    if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => $message]);
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="es"><meta charset="utf-8">';
        echo '<title>AIDEX · Servicio no disponible</title><p>' . $message . '</p></html>';
    }
});

require SITE_ROOT . '/config/environment.php';
$GLOBALS['environment'] = loadEnvironment(SITE_ROOT . '/.env');

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: DENY');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; "
    . "img-src 'self'; font-src 'self'; connect-src 'self'; form-action 'self'; "
    . "frame-ancestors 'none'; base-uri 'self'; object-src 'none'");

if (!enabled('SITE_INDEXABLE')) {
    header('X-Robots-Tag: noindex, nofollow');
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function startContactSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('aidex_contact');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => setting('APP_ENV', 'local') === 'production'
            || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    header('Cache-Control: no-store, private');

    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        $_SESSION['form_started'] = time();
    }
}
