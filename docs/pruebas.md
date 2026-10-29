# Informe de verificación

Revisión realizada sobre la segunda revisión local. Las pruebas no acceden a la
aplicación AIDEX ni a sus bases de datos, repositorios o servidor de licencias.

## Resultado

| Área | Verificación | Resultado |
| --- | --- | --- |
| PHP | Sintaxis de archivos de aplicación, configuración, puntos de entrada y router | Correcta con PHP 8.5.7 |
| Dependencias | `composer check-platform-reqs --no-dev` | Requisitos instalados; PHPMailer 7.1.1 |
| JavaScript | `node --check` y errores de ejecución en Chrome | Sin errores |
| Carga | HTTP 200, imágenes decodificadas, una etiqueta h1 y un main | Correcto |
| Navegación | Todas las anclas apuntan a elementos existentes | Correcto |
| Muestra | Búsqueda por palabra, filtros, cero resultados y ficha modal | Correcto |
| Pestañas | Búsqueda, Bibliotecas, Trazabilidad e Indicadores; clic, flechas, Home/End y estado ARIA | Correcto |
| Dashboard | Tarjetas, gráficos SVG, tablas desplegables y adaptación en las cuatro vistas | Correcto |
| Tarjetas | Seis capacidades; tres columnas en escritorio, dos en tablet y una en móvil; iconos y títulos centrados verticalmente | Correcto |
| Avisos del formulario | Contenedor vacío sin altura, con y sin JavaScript; advertencia real de SMTP sin configurar | Correcto |
| Estados visuales | Error y éxito mediante respuestas HTTP interceptadas solo en la prueba de navegador | Correcto; el flujo SMTP se comprueba por separado contra el receptor local |
| Menú móvil | Apertura, navegación y cierre | Correcto |
| Adaptación | 320, 375, 390, 768, 1024 y 1440 px sin desbordamiento horizontal | Correcto |
| Movimiento reducido | Desactiva desplazamiento suave y transiciones | Correcto |
| Accesibilidad automática | axe: WCAG 2 A/AA y 2.1 AA; escritorio, cuatro paneles y móvil | Sin hallazgos en los estados revisados |
| Sin JavaScript | Navegación visible, POST con redirección y conservación escapada de campos | Correcto |
| Campos | Requeridos, correo inválido, longitud, espacios vacíos y teléfono | Correcto |
| Inyección de encabezados | Correo con CR/LF y Bcc | Rechazado |
| CSRF | Token incorrecto, rotación al enviar y reutilización | Rechazado cuando corresponde |
| Antispam | Honeypot y frecuencia persistente entre sesiones | Correcto |
| SMTP sin configurar | Mensaje explícito, sin éxito y conserva campos | Correcto en navegador |
| SMTP local | Receptor ficticio acepta con 250 | Devuelve éxito; destinatario fijo y Reply-To correctos |
| SMTP local rechazado | Receptor ficticio rechaza con 550 | Devuelve error 502 sin detalles técnicos |
| XSS | HTML en campos y consulta de muestra | Se muestra como texto, no se ejecuta |
| Cabeceras | CSP, HttpOnly, SameSite y no-store | Verificadas |
| Archivos privados | .env, config, src, vendor, composer y storage solicitados al servidor local | HTTP 404 |
| SEO | URL canónica configurable, robots y sitemap | Verificados en instancia aislada |
| Identidad | SVG copiados sin alterar | Comparación binaria con los originales |
| Empaquetado | Exclusión de .env, registros, referencias y recursos de pruebas | Verificado |

## Revisión visual

Se inspeccionaron las capturas completas de escritorio y móvil. Se ajustaron
la navegación compacta, las tarjetas, el selector segmentado, el panel destacado
de solución y el proceso con iconos. La transición de pestañas conserva la
opacidad del texto para no perder contraste durante el cambio. Se revisaron
la distribución del hero, tarjetas, paneles y formulario. Los artefactos de
navegador se conservan en `tests/artifacts/`, excluidos del paquete de publicación.

El contenido comercial se contrastó con el manual. El mapa de capacidades y sus
límites se conserva en `docs/capacidades.md`. No se publicaron datos del manual
ni se reutilizaron capturas potencialmente sensibles.

## Límites de esta revisión

- La aceptación/rechazo SMTP se comprobó contra un receptor de prueba local, no
  contra el proveedor definitivo. Falta probar credenciales, autenticación TLS
  real, reputación del remitente, recepción final y respuesta desde el buzón.
- Nginx, PHP-FPM, DNS, certificado HTTPS y permisos de Ubuntu se documentaron;
  no se verificaron en un servidor de producción ni se desplegaron archivos allí.
- Se utilizó Chrome de escritorio con emulación de tamaños. No se probaron
  dispositivos físicos, Safari ni Firefox.
- La auditoría automática no sustituye una revisión manual completa con lectores
  de pantalla ni acredita una certificación de accesibilidad.
- El PHP incluido en XAMPP es 8.1.6 y tiene un aviso de extensión intl inexistente.
  La revisión se ejecutó con el PHP 8.5.7 disponible en `/opt/homebrew/bin/php`.
  No se cambió XAMPP. La aplicación requiere PHP 8.2 o posterior.
- Composer 2.8.6 mostró avisos de deprecación propios al ejecutarse con PHP 8.5;
  la instalación y comprobación de requisitos finalizaron correctamente. No se
  observaron errores de la aplicación durante las pruebas.
- Aviso de privacidad, dirección comercial y credenciales definitivas siguen
  pendientes de configuración por el responsable del sitio.

## Alcance de la segunda revisión

- Se conservan la arquitectura, los logotipos y la configuración de publicación.
- `public/contact.php`, `src/contact.php`, `src/bootstrap.php`,
  `config/environment.php`, `.env.example` y `docs/nginx.conf` no se modificaron.
- El aviso vacío se corrige con una clase de visibilidad explícita y estados de
  presentación; no depende de `:empty`, que cuenta espacios del HTML.
- Se volvió a ejecutar la integración HTTP/SMTP local, sin enviar correo externo.
- La inspección web de `https://aidex.com.mx` no estuvo disponible desde la
  herramienta de navegación. La implementación y la revisión visual se realizaron
  sobre el código local existente. No se publicaron cambios.
