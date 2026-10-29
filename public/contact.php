<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
require SITE_ROOT . '/src/contact.php';
startContactSession();

function respond(int $status, string $message, array $errors = [], array $data = []): never
{
    $result = [
        'ok' => $status === 200,
        'message' => $message,
        'errors' => $errors,
        'csrf' => $_SESSION['csrf'],
    ];

    if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    } else {
        $_SESSION['flash'] = $result;
        $_SESSION['old'] = $data;
        header('Location: ./#contacto', true, 303);
    }

    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respond(405, 'Utiliza el formulario para enviar tu solicitud.');
}

if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 20000) {
    respond(413, 'La solicitud es demasiado larga. Reduce el mensaje e inténtalo de nuevo.');
}

$token = $_POST['csrf'] ?? '';

if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
    respond(403, 'La sesión del formulario venció. Recarga la página e inténtalo de nuevo.');
}

if (!empty($_POST['website']) || time() - ($_SESSION['form_started'] ?? time()) < 2) {
    respond(422, 'No se pudo validar la solicitud. Espera unos segundos e inténtalo de nuevo.');
}

if (!consumeContactAttempt($_SERVER['REMOTE_ADDR'] ?? 'unknown')) {
    header('Retry-After: ' . max(60, (int) setting('CONTACT_RATE_WINDOW', '900')));
    respond(429, 'Se alcanzó el límite de intentos. Inténtalo más tarde.');
}

[$data, $errors] = validateContact($_POST);

if ($errors !== []) {
    respond(422, 'Revisa los campos señalados antes de enviar.', $errors, $data);
}

if (!mailIsConfigured()) {
    respond(503, 'El envío de correo aún no está configurado. Tu solicitud no se ha enviado.', [], $data);
}

try {
    sendContactMail($data);
} catch (Throwable $exception) {
    // No registrar el cuerpo, las direcciones ni la respuesta privada del proveedor.
    error_log('AIDEX landing: envío SMTP no confirmado. Revisar configuración o proveedor.');
    respond(502, 'No pudimos confirmar el envío. Inténtalo más tarde.', [], $data);
}

$_SESSION['csrf'] = bin2hex(random_bytes(32));
$_SESSION['form_started'] = time();
respond(200, 'Tu solicitud se envió correctamente. Gracias por tu interés en AIDEX.');
