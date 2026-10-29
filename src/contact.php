<?php

declare(strict_types=1);

/** Valida sin modificar silenciosamente lo que escribió el visitante. */
function validateContact(array $input): array
{
    $limits = ['nombre' => 120, 'empresa' => 150, 'correo' => 254, 'telefono' => 35, 'mensaje' => 3000];
    $data = [];
    $errors = [];

    foreach ($limits as $field => $limit) {
        $value = $input[$field] ?? '';

        if (!is_string($value) || !mb_check_encoding($value, 'UTF-8')) {
            $errors[$field] = 'Introduce un valor válido.';
            $value = '';
        }

        $data[$field] = trim($value);

        if (mb_strlen($data[$field]) > $limit) {
            $errors[$field] = "Usa como máximo {$limit} caracteres.";
        }

        // Los saltos de línea solo se permiten en el cuerpo del mensaje.
        $pattern = $field === 'mensaje' ? '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/' : '/[\x00-\x1F\x7F]/';

        if (preg_match($pattern, $data[$field])) {
            $errors[$field] = 'El campo contiene caracteres no permitidos.';
        }
    }

    foreach (['nombre', 'correo', 'mensaje'] as $field) {
        if ($data[$field] === '') {
            $errors[$field] = 'Completa este campo obligatorio.';
        }
    }

    if ($data['correo'] !== '' && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
        $errors['correo'] = 'Introduce un correo electrónico válido.';
    }

    if ($data['telefono'] !== '' && !preg_match('/^[0-9+().\s#x-]{5,35}$/i', $data['telefono'])) {
        $errors['telefono'] = 'Revisa el teléfono; usa números y signos habituales.';
    }

    return [$data, $errors];
}

/** Archivo privado compartido entre sesiones, con escritura protegida por bloqueo. */
function consumeContactAttempt(string $ip): bool
{
    $path = rtrim(setting('STORAGE_PATH', SITE_ROOT . '/storage'), '/') . '/contact-rate.json';
    $handle = fopen($path, 'c+');

    if ($handle === false || !flock($handle, LOCK_EX)) {
        throw new RuntimeException('No se pudo bloquear el control de frecuencia.');
    }

    try {
        chmod($path, 0600);
        $entries = json_decode(stream_get_contents($handle) ?: '{}', true);
        $entries = is_array($entries) ? $entries : [];
        $now = time();
        $window = max(60, (int) setting('CONTACT_RATE_WINDOW', '900'));
        $limit = max(1, (int) setting('CONTACT_RATE_LIMIT', '5'));

        foreach ($entries as $key => $entry) {
            if (($entry['expires'] ?? 0) <= $now) {
                unset($entries[$key]);
            }
        }

        // No se confía en X-Forwarded-For enviado por el cliente.
        $key = hash('sha256', $ip);
        if (!isset($entries[$key]) && count($entries) >= 10000) {
            return false;
        }

        $entry = $entries[$key] ?? ['count' => 0, 'expires' => $now + $window];
        $allowed = $entry['count'] < $limit;
        $entry['count']++;
        $entries[$key] = $entry;
        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, json_encode($entries, JSON_THROW_ON_ERROR));
        fflush($handle);

        return $allowed;
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

function mailIsConfigured(): bool
{
    if (!enabled('SMTP_ENABLED') || !is_file(SITE_ROOT . '/vendor/autoload.php')) {
        return false;
    }

    foreach (['SMTP_HOST', 'SMTP_FROM_EMAIL', 'SMTP_TO_EMAIL'] as $key) {
        if (setting($key) === '' || preg_match('/[\r\n\x00]/', setting($key))) {
            return false;
        }
    }

    foreach (['SMTP_FROM_EMAIL', 'SMTP_TO_EMAIL', 'SMTP_REPLY_TO'] as $key) {
        if (setting($key) !== '' && !filter_var(setting($key), FILTER_VALIDATE_EMAIL)) {
            return false;
        }
    }

    $encryption = setting('SMTP_ENCRYPTION', 'tls');
    $port = filter_var(setting('SMTP_PORT', '587'), FILTER_VALIDATE_INT);

    if (!in_array($encryption, ['tls', 'ssl', 'none'], true) || !$port || $port < 1 || $port > 65535) {
        return false;
    }

    if ($encryption === 'none' && setting('APP_ENV', 'local') !== 'local') {
        return false;
    }

    return !enabled('SMTP_AUTH', true)
        || (setting('SMTP_USERNAME') !== '' && setting('SMTP_PASSWORD') !== '');
}

function sendContactMail(array $data): void
{
    require_once SITE_ROOT . '/vendor/autoload.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = setting('SMTP_HOST');
    $mail->Port = (int) setting('SMTP_PORT', '587');
    $mail->SMTPAuth = enabled('SMTP_AUTH', true);
    $mail->Username = setting('SMTP_USERNAME');
    $mail->Password = setting('SMTP_PASSWORD');
    $encryption = setting('SMTP_ENCRYPTION', 'tls');
    $mail->SMTPSecure = $encryption === 'none' ? '' : $encryption;
    $mail->SMTPAutoTLS = $encryption !== 'none';
    $mail->Timeout = max(5, min(30, (int) setting('SMTP_TIMEOUT', '15')));
    $mail->Timelimit = 30;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 0;
    $mail->setFrom(setting('SMTP_FROM_EMAIL'), setting('SMTP_FROM_NAME', 'AIDEX'));
    $mail->addAddress(setting('SMTP_TO_EMAIL'));
    $mail->addReplyTo(setting('SMTP_REPLY_TO') ?: $data['correo']);
    $mail->Subject = 'Solicitud de información · AIDEX';
    $mail->isHTML(false);
    $mail->Body = "Solicitud recibida desde el sitio de AIDEX\n\n"
        . "Nombre: {$data['nombre']}\n"
        . "Empresa u organización: {$data['empresa']}\n"
        . "Correo: {$data['correo']}\n"
        . "Teléfono: {$data['telefono']}\n\n"
        . "Mensaje:\n{$data['mensaje']}\n";

    // send() solo termina correctamente cuando SMTP acepta el mensaje.
    if (!$mail->send()) {
        throw new RuntimeException('SMTP no aceptó el mensaje.');
    }
}
