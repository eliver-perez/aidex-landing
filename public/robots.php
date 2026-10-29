<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
header('Content-Type: text/plain; charset=utf-8');

$canonical = publicUrl(setting('SITE_URL', 'https://aidex.com.mx'));
echo "User-agent: *\n";

if (enabled('SITE_INDEXABLE')) {
    echo "Allow: /\nDisallow: /contact.php\n";

    if ($canonical !== '') {
        echo "Sitemap: {$canonical}/sitemap.xml\n";
    }
} else {
    echo "Disallow: /\n";
}
