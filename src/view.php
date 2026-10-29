<?php

declare(strict_types=1);

/** Iconos propios de interfaz; la identidad oficial se sirve sin modificaciones. */
function icon(string $name, string $class = ''): string
{
    $paths = [
        'arrow' => '<path d="M5 12h14m-5-5 5 5-5 5"/>',
        'diagonal' => '<path d="M6 18 18 6M6 6h12v12"/>',
        'search' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/>',
        'folder' => '<path d="M3 7V5a1 1 0 0 1 1-1h5l3 3h8a1 1 0 0 1 1 1v11H3Z"/>',
        'file' => '<path d="M14 3H5v18h14V8Zm0 0v5h5M8 12h8m-8 4h6"/>',
        'shield' => '<path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6Zm-4 9 3 3 5-6"/>',
        'layers' => '<path d="m12 3 10 5-10 5L2 8Zm-10 9 10 5 10-5M2 16l10 5 10-5"/>',
        'scan' => '<path d="M8 3H3v5m13-5h5v5M3 16v5h5m8 0h5v-5M2 12h20M8 8h8M8 16h8"/>',
        'history' => '<path d="M3 10a9 9 0 1 1 1 7M3 4v6h6m3-4v6l4 2"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'chevron' => '<path d="m9 5 7 7-7 7"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 6 9 7 9-7"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
    ];

    return '<svg class="icon ' . escape($class) . '" width="24" height="24" viewBox="0 0 24 24" '
        . 'fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" '
        . 'stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? $paths['file']) . '</svg>';
}
