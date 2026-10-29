<?php

declare(strict_types=1);

/** Lee únicamente el archivo privado del sitio; nunca ejecuta su contenido. */
function loadEnvironment(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $values = [];

    foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (!preg_match('/^([A-Z][A-Z0-9_]*)\s*=(.*)$/', $line, $matches)) {
            throw new RuntimeException('Formato de configuración inválido.');
        }

        $value = trim($matches[2]);
        $quote = substr($value, 0, 1);

        if (in_array($quote, ['"', "'"], true) && str_ends_with($value, $quote)) {
            $value = substr($value, 1, -1);
        }

        $values[$matches[1]] = $value;
    }

    return $values;
}

function setting(string $key, string $default = ''): string
{
    // Las variables del proceso permiten configurar PHP-FPM sin duplicar archivos.
    $value = getenv($key);

    return $value !== false ? $value : ($GLOBALS['environment'][$key] ?? $default);
}

function enabled(string $key, bool $default = false): bool
{
    return filter_var(setting($key, $default ? 'true' : 'false'), FILTER_VALIDATE_BOOLEAN);
}

function publicUrl(string $value): string
{
    if (!filter_var($value, FILTER_VALIDATE_URL)) {
        return '';
    }

    return in_array(parse_url($value, PHP_URL_SCHEME), ['https', 'http'], true)
        ? rtrim($value, '/')
        : '';
}
