<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
header('Content-Type: application/xml; charset=utf-8');
$canonical = publicUrl(setting('SITE_URL', 'https://aidex.com.mx'));

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

if (enabled('SITE_INDEXABLE') && $canonical !== '') {
    echo '    <url><loc>' . escape($canonical) . '/</loc></url>' . "\n";
}

echo '</urlset>' . "\n";
