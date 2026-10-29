# AIDEX · Landing oficial

Segunda revisión local del sitio comercial de **AIDEX — Aplicación de Indexación
Digital de Expedientes**. HTML semántico, CSS, JavaScript y PHP independientes de
la aplicación y del servidor de licencias. No utiliza base de datos, frameworks
frontend, CDN, analítica ni herramientas de compilación para funcionar.

## Inicio rápido

Desde la carpeta de trabajo `aidex/` (los comandos de desarrollo no corresponden al ZIP de producción):

```sh
cd sitio
composer install --no-dev --prefer-dist --optimize-autoloader
php -S 127.0.0.1:8088 -t public tools/router.php
```

Abrir **http://127.0.0.1:8088**. Detener con `Ctrl+C`.

No se necesita `.env` para revisar la landing. Sin SMTP configurado, el formulario
valida los datos y explica que **la solicitud no se ha enviado**. Nunca simula éxito.
Los recursos visuales y tipográficos se sirven desde el propio sitio.

En este equipo, el PHP de XAMPP es **8.1.6**, por debajo del requisito del proyecto.
Para esta revisión usar `/opt/homebrew/bin/php` (8.5.7, verificado) en el comando
anterior si `php` apunta a XAMPP. No se modificó la configuración de XAMPP.

En una instalación compatible de XAMPP también puede abrirse `http://localhost/aidex/sitio/public/` si Apache
permite `.htaccess`, tiene PHP 8.2 o posterior y la extensión mbstring. Es preferible
usar el servidor de desarrollo anterior o un virtual host con raíz en `public/`.

## Organización

```text
sitio/
├── public/                 Única raíz pública
│   ├── index.php           Landing y formulario
│   ├── contact.php         Procesamiento POST (JSON o redirección)
│   ├── robots.php          robots.txt configurable mediante rewrite
│   ├── sitemap.php         sitemap.xml configurable mediante rewrite
│   └── assets/             CSS, JavaScript, logos, tarjeta social y fuentes locales
├── config/environment.php  Lectura de configuración privada
├── src/                    Sesión, cabeceras, validación, antispam, correo y vistas
├── storage/                Control de frecuencia; debe ser escribible por PHP
├── vendor/                 PHPMailer y autoload generado por Composer
├── docs/                   Configuración Nginx, criterios de contenido y pruebas
├── tests/                  Pruebas HTTP/SMTP y navegador
├── tools/                  Router local, formato, tarjeta social y empaquetado
├── dist/                   Paquete generado para despliegue
├── composer.json
├── composer.lock           Dependencia fijada para instalaciones reproducibles
├── .env.example            Todas las opciones, sin credenciales
└── README.md
```

No mover `src/`, `config/`, `vendor/`, `storage/` ni `.env` dentro de `public/`.
`.gitignore` excluye credenciales, dependencias, registros, capturas y paquetes.
`.htaccess` niega el acceso a la raíz privada bajo Apache; Nginx no lee ese archivo
por lo que debe configurarse correctamente su `root`.

## Requisitos

- PHP **8.2 o posterior** con `mbstring`, `openssl`, `session`, `filter`, `hash`,
  `ctype` y `json`. Algunas extensiones forman parte de la distribución estándar.
- Composer 2 para instalar dependencias; no se ejecuta desde las peticiones web.
- Servidor SMTP accesible desde PHP-FPM, con certificado válido si usa TLS.
- Nginx y PHP-FPM en producción; Python 3 solo para pruebas y empaquetado.
- `storage/` y el directorio de sesiones de PHP deben permitir escritura al usuario de PHP-FPM.

Se utiliza [PHPMailer](https://github.com/PHPMailer/PHPMailer), instalado mediante
Composer y fijado en `composer.lock`. La implementación usa SMTP explícito; no usa
`mail()` ni desactiva la verificación de certificados TLS.

## Configuración única de correo

```sh
cp .env.example .env
chmod 600 .env
```

Editar **solo `sitio/.env`** con los valores reales. Guardar un valor por línea.
Puede envolverse un valor en comillas simples o dobles. Las contraseñas pueden
contener `#`, espacios o `=`; no se interpretan variables ni secuencias de escape.
Los comentarios deben ir en su propia línea.

| Variable | Uso |
| --- | --- |
| `SMTP_ENABLED` | `true` para activar el envío una vez completada la configuración. |
| `SMTP_HOST` | Nombre del servidor SMTP, sin prefijo `smtp://`. |
| `SMTP_PORT` | Puerto indicado por el proveedor, normalmente 587 o 465. |
| `SMTP_ENCRYPTION` | `tls` para STARTTLS o `ssl` para TLS implícito. |
| `SMTP_AUTH` | `true` si el servidor requiere autenticación. |
| `SMTP_USERNAME` / `SMTP_PASSWORD` | Credenciales reales del proveedor. |
| `SMTP_FROM_EMAIL` | Dirección remitente autorizada por el proveedor. |
| `SMTP_FROM_NAME` | Nombre visible del remitente. |
| `SMTP_TO_EMAIL` | Único destinatario comercial fijo. |
| `SMTP_REPLY_TO` | Vacío: responde al correo validado del visitante. Con valor: dirección fija de respuesta. |
| `SMTP_TIMEOUT` | Tiempo de conexión SMTP en segundos, limitado a 5–30. |

El visitante nunca puede elegir el destinatario ni el remitente. Su correo va en
Reply-To y en el cuerpo de texto, no en From. No hay autorespuesta al visitante.
No registrar ni mostrar el contenido del correo, contraseñas o respuesta técnica
privada del proveedor en producción.

`SMTP_ENCRYPTION=none` únicamente se admite en `APP_ENV=local`, para receptores de
prueba locales. En producción es obligatorio usar `tls` o `ssl`. Un SMTP sin
credenciales solo es válido si el servidor administrado permite el relay de esa
instalación y se configura explícitamente `SMTP_AUTH=false`.

Las variables del proceso prevalecen sobre `.env`. Es opcional usarlas en el pool
PHP-FPM; para una administración sencilla, usar exclusivamente el archivo privado.

### Probar el correo real

1. Configurar remitente, destinatario y autenticación en `.env`.
2. Activar `SMTP_ENABLED=true`.
3. Abrir la página, completar los campos y enviar una solicitud de prueba identificada.
4. El formulario solo confirma éxito si PHPMailer obtiene la aceptación SMTP.
5. Verificar la recepción en el buzón y probar Responder. La aceptación SMTP no garantiza entrega en bandeja de entrada; el proveedor puede aplicar filtros o generar rebotes.
6. Si falla, revisar conectividad, puerto, TLS, autorización del remitente y registros del proveedor. La web muestra un error genérico y conserva los campos.
7. Revisar SPF, DKIM y DMARC con quien administre el dominio de correo.

La prueba SMTP real queda pendiente hasta disponer de credenciales y del buzón
comercial definitivo. No se ha enviado correo externo durante el desarrollo.

## Protecciones del formulario

- Campos requeridos y límites tanto en cliente como en servidor; solo UTF-8 válido.
- CSRF por sesión, cookie HttpOnly y SameSite=Lax; Secure en producción/HTTPS.
- Honeypot y espera mínima de dos segundos desde que se abre la sesión del formulario.
- Cinco intentos por IP cada quince minutos, compartidos entre sesiones y procesos,
  configurables en `.env`. Incluye intentos con errores de validación.
- Archivo privado con bloqueo `flock`, vencimiento de entradas y límite de tamaño lógico.
- No se confía en `X-Forwarded-For`. Si se instala detrás de un proxy, configurar en
  Nginx la IP real **solo para proxies conocidos** antes de usar `REMOTE_ADDR`.
- Respuesta sin detalles internos; texto escapado al volver a mostrar datos.
- Bloqueo de controles y saltos de línea en campos de encabezado.
- Doble envío evitado en el navegador y rotación de CSRF después de aceptación.
- Solicitudes de hasta 20 KB en PHP/Nginx; nunca se aceptan archivos adjuntos.
- Cabeceras CSP, protección frente a iframes y MIME sniffing.
- Sin persistencia de contactos en base de datos. Los valores de un error sin
  JavaScript permanecen temporalmente en la sesión hasta volver a renderizarse.
- `storage/contact-rate.json` contiene hashes de IP, contadores y expiraciones;
  no contiene el mensaje ni correos. Las entradas vencidas se purgan al recibir
  solicitudes. Este control está pensado para un servidor; múltiples nodos
  necesitarían almacenamiento compartido o un límite de acceso común.

## Contenido y personalización

`public/index.php` contiene textos, secciones y estructura del formulario.
`public/assets/css/site.css` contiene la identidad y los puntos de adaptación.
`public/assets/js/site.js` contiene menú, pestañas, búsqueda ilustrativa y envío.
`src/views/indicators.php` contiene el dashboard conceptual con SVG y tablas
accesibles. No se añadió una dependencia de gráficos ni se cambió el backend SMTP.

La landing incluye presentación, problema/solución, capacidades, muestra
interactiva con Búsqueda, Bibliotecas, Trazabilidad e Indicadores, proceso de tres
pasos, contacto y pie de página. Las capacidades
publicadas están contrastadas en `docs/capacidades.md`.

El componente conserva una etiqueta discreta «Vista de demostración · Datos de
ejemplo». La búsqueda solo consulta registros ficticios del JavaScript local.
El dashboard usa cifras fijas coherentes con sus gráficos, no estadísticas de
clientes. Los enlaces «Ver datos» despliegan las tablas de esos mismos valores. Las imágenes de marca son copias intactas de los SVG oficiales.
El archivo `tools/social-card.html` conserva la fuente editable de la tarjeta Open
Graph, ya generada en `public/assets/img/social-card.png` (1200 × 630).

### Opciones públicas en `.env`

- `SITE_URL`: URL canónica, sin barra final; por defecto `https://aidex.com.mx`.
  También alimenta Open Graph, robots y sitemap. No derivarla del encabezado Host.
- `SITE_INDEXABLE=false`: revisión sin indexación, cabecera `noindex`, robots
  disallow y sitemap vacío. Cambiar a `true` al publicar la versión final.
- `CONTACT_EMAIL`: dirección comercial visible opcional; vacía no se inventa una.
- `PRIVACY_URL`: URL del aviso definitivo; vacía no se dibuja un enlace sin destino.
- `APP_ENV=production`: activa cookie Secure y prohíbe SMTP sin cifrado.
- `STORAGE_PATH`: opcional, ruta absoluta privada para el control de frecuencia;
  por defecto se utiliza `sitio/storage/`.

No se ha redactado un aviso legal provisional ni se ha añadido consentimiento
ficticio. Antes de recibir datos reales, incorporar el aviso definitivo aprobado
por el responsable del sitio y revisar cualquier requisito de consentimiento.
La revisión local puede hacerse con datos ficticios.

## Publicación en Ubuntu + Nginx + PHP-FPM

Esta entrega **no modifica ni publica en ningún servidor**. Los siguientes pasos
son para la persona que realice el despliegue.

1. Instalar Nginx, Composer, PHP-FPM y mbstring. El ejemplo usa PHP 8.3; adaptar
   el nombre del paquete y del socket a la versión instalada (8.2 o posterior).

   ```sh
   sudo apt update
   sudo apt install nginx php8.3-fpm php8.3-mbstring php8.3-cli php8.3-curl unzip composer
   ```

2. Copiar el proyecto a `/var/www/aidex/sitio/` o extraer allí el paquete generado.
   Publicar `public/`, `src/`, `config/`, `vendor/`, `storage/`, `composer.json`,
   `composer.lock` y `.env.example`. Mantener `.env` privado y crearlo en destino.
   No copiar `referencias/`, tests, capturas de revisión ni herramientas de desarrollo.

3. Si no se incluyó `vendor/` en el traslado, instalar desde el archivo de bloqueo:

   ```sh
   cd /var/www/aidex/sitio
   composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
   composer check-platform-reqs --no-dev
   cp .env.example .env
   ```

4. Configurar Nginx con `docs/nginx.conf`. La raíz pública es exclusivamente
   **`/var/www/aidex/sitio/public`**. Adaptar `server_name`, rutas y
   `/run/php/php8.3-fpm.sock` al servidor.

   ```sh
   sudo cp docs/nginx.conf /etc/nginx/sites-available/aidex
   sudo ln -s /etc/nginx/sites-available/aidex /etc/nginx/sites-enabled/aidex
   sudo nginx -t
   sudo systemctl reload nginx
   ```

5. Apuntar los registros DNS al servidor y configurar HTTPS antes de habilitar
   el contacto real. El ejemplo inicial escucha HTTP para emitir el certificado.
   Una opción, con DNS y puertos 80/443 ya disponibles, es:

   ```sh
   sudo apt install certbot python3-certbot-nginx
   sudo certbot --nginx -d aidex.com.mx -d www.aidex.com.mx --redirect
   ```

   Incluir `www` solo si ese dominio se va a utilizar y su DNS ya apunta al servidor.
   Verificar la renovación y repetir `nginx -t`. El resultado final debe redirigir
   HTTP a HTTPS. No activar `APP_ENV=production` mientras se revise solo por HTTP:
   el navegador no enviaría la cookie Secure y fallaría CSRF.

6. Completar `.env`: `APP_ENV=production`, SMTP real, contacto, URL canónica,
   aviso de privacidad y finalmente `SITE_INDEXABLE=true`.

7. Permisos recomendados: código propiedad del usuario de despliegue; Nginx/PHP
   solo necesitan lectura. El único directorio de esta aplicación que requiere
   escritura es `storage/` (además del directorio de sesiones gestionado por PHP).

   ```sh
   sudo chgrp -R www-data /var/www/aidex/sitio
   sudo find /var/www/aidex/sitio -type d -exec chmod 750 {} \;
   sudo find /var/www/aidex/sitio -type f -exec chmod 640 {} \;
   sudo chown -R www-data:www-data /var/www/aidex/sitio/storage
   sudo chmod 700 /var/www/aidex/sitio/storage
   sudo chmod 640 /var/www/aidex/sitio/.env
   ```

   `.env` debe pertenecer al usuario de despliegue y al grupo de PHP-FPM para ser
   legible con 640. No usar permisos 777. Adaptar `www-data` si el pool utiliza
   otro usuario. La protección debe incluir los directorios padres.

8. Configurar PHP con `display_errors=Off`, `log_errors=On`, sesiones escribibles
   y límites adecuados de petición. No activar caché de página/FastCGI en
   `index.php` ni `contact.php`: el CSRF pertenece a cada sesión. Los assets
   pueden almacenarse en caché; purgarla cuando se reemplacen archivos con el
   mismo nombre. No incluir reglas que sirvan el contenido PHP como archivo estático.

9. Revisar logo, móvil, navegación y formulario con HTTPS. Comprobar `/robots.txt`,
   `/sitemap.xml` y el aviso. Confirmar que `/.env`, `/src/`, `/vendor/`, `/config/`
   y `/storage/` devuelven 403/404. Probar aceptación SMTP y recepción final.

## Paquete de entrega

```sh
python3 tools/package.py
```

Genera `dist/aidex-sitio.zip`, con una carpeta superior `sitio/`. Incluye dependencias
SMTP ya instaladas y `storage/.gitkeep`. Excluye `.env`, almacenamiento de ejecución,
manual, ZIP de referencia, pruebas, capturas, herramientas y repositorios.
Extraer bajo `/var/www/aidex/` para obtener la ruta del ejemplo Nginx. Configurar las
credenciales y permisos después de extraer. No sobrescribir un `.env` existente
con el archivo de ejemplo.

## Verificación y pruebas

Sintaxis, requisitos y prueba de servidor, desde `sitio/`:

```sh
find config src public tools -name '*.php' -exec php -l {} \;
node --check public/assets/js/site.js
composer check-platform-reqs --no-dev
python3 tests/integration.py
```

La prueba de integración inicia un PHP y un SMTP **solo en loopback**, con
configuración de prueba en variables de proceso y almacenamiento temporal privado.
Comprueba aceptación 250 y rechazo 550; nunca envía a un destinatario real ni
modifica `.env`. Necesita permisos del entorno para abrir puertos locales.

Para la revisión opcional de navegador, instalar Playwright y axe fuera del código
público; no son dependencias de producción:

```sh
npm install --prefix /tmp/aidex-qa playwright @axe-core/playwright
NODE_PATH=/tmp/aidex-qa/node_modules node tests/browser.cjs
```

Requiere el sitio servido en `http://127.0.0.1:8088`. Configurar `TEST_URL` y
`CHROME_PATH` si son distintos. La ruta por defecto de Chrome corresponde a macOS.
En Linux puede usarse el ejecutable de Chromium instalado. Esta prueba espera
SMTP sin configurar y hace un intento de envío; consume un intento del límite.
Usar una instancia de revisión separada de producción.

Los resultados y capturas quedan en `tests/artifacts/` y no se publican. El informe
de esta entrega está en `docs/pruebas.md`.

## Pendientes antes de producción

- Credenciales SMTP, remitente autorizado, destinatario y prueba de recepción real.
- Dirección comercial visible, si se desea mostrar una.
- Aviso de privacidad definitivo y su URL válida.
- DNS, certificado HTTPS, versión/socket de PHP-FPM y permisos del servidor.
- Revisar la URL canónica y activar indexación cuando se publique la versión final.
- Revisión visual/comercial por el responsable del producto.
