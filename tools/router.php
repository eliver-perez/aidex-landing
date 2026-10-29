<?php

declare(strict_types=1);

// Enrutador exclusivo del servidor de desarrollo PHP, con raíz fija en public/.
$public = realpath(dirname(__DIR__) . '/public');
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$routes = [
    '/' => 'index.php',
    '/index.php' => 'index.php',
    '/contact.php' => 'contact.php',
    '/robots.txt' => 'robots.php',
    '/sitemap.xml' => 'sitemap.php',
];

if (isset($routes[$path])) {
    require $public . '/' . $routes[$path];
    return true;
}

$file = realpath($public . $path);
$extension = pathinfo($file ?: '', PATHINFO_EXTENSION);

if ($file && str_starts_with($file, $public . '/assets/') && is_file($file)
    && in_array($extension, ['css', 'js', 'svg', 'png', 'webp', 'ttf', 'woff2', 'txt'], true)) {
    return false;
}

http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo 'Página no encontrada.';
