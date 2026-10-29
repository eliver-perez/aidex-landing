# Criterios editoriales y verificación de capacidades

Fuente de producto: `referencias/manual-usuario-aidex.docx`, primera edición,
elaborada el 11 de octubre de 2026. Los originales permanecen fuera del sitio.

| Mensaje de la landing | Fundamento en el manual | Alcance respetado |
| --- | --- | --- |
| Bibliotecas vinculadas, administradas e híbridas | 3.1; 4.2–4.4 | Vinculada conserva originales; administrada incorpora cargas; híbrida combina ambas. |
| Buscar por nombre, expediente y contenido | 5.3; 9.1–9.4 | El contenido debe haberse extraído e indexado; no se promete encontrar todo archivo. |
| Resultados con contexto y filtros | 9.2–9.4 | Biblioteca, formato, categorías, ejercicio y expediente según ámbito. Las coincidencias por página dependen del formato. |
| Expedientes, plantillas y requisitos pendientes | 6.1–6.9 | Requisitos configurados por plantilla; no se promete completitud legal ni validación automática. |
| Extracción y OCR de PDF escaneados | 11.1–11.2; 12.5 | Reconoce texto según calidad, configuración y módulos habilitados. |
| Acceso por perfiles y permisos de biblioteca | 2.3; 3.5 | Sin afirmar certificaciones, cifrado integral ni garantías de seguridad. |
| Historial y auditoría | 10.1–10.5 | Solo acciones registradas en AIDEX; no equivale a registrar toda edición externa del archivo. |
| Cargas, clasificación y aprobación ilustrativas | 7.1–7.6; 8.1–8.6 | Aprobación cuando esté configurada y con permisos. |
| Indicadores y gráficos del dashboard | 14.1–14.2 | Total de documentos, índice vigente, estado del procesamiento e incorporaciones por día. Las cifras son ficticias. |

## Decisiones de contenido

- Público amplio: mensajes sobre necesidades documentales, sin centrar el producto en un sector.
- No se publican precios, planes, clientes, testimonios, cifras comerciales ni descargas del software.
- No se presenta como SaaS ni se afirma funcionamiento en la nube.
- Las tarjetas del hero, la búsqueda, la biblioteca, el expediente, la línea de tiempo y el dashboard son HTML/CSS conceptual.
- Los documentos, usuarios, identificadores y fechas de la demostración son ficticios.
- La muestra busca solo en cuatro registros locales. No accede a la aplicación AIDEX.
- Las cifras del dashboard son un conjunto de ejemplo: 1,280 registrados =
  1,152 con índice vigente + 104 pendientes + 24 con error de extracción.
  El gráfico de incorporaciones suma 155 (18 + 32 + 24 + 45 + 36); el 90 %
  indexado corresponde a 1,152 / 1,280. Las tablas muestran esos mismos valores.
- «2 de 3» representa requisitos de un expediente ficticio; no es una estadística del producto.
- Se incluye una nota sobre módulos, configuración y permisos.
- No se reutilizaron capturas del manual: así se evita divulgar datos y se mantiene nitidez en todas las pantallas.
- No hubo ambigüedades pendientes en las capacidades seleccionadas. Las opciones de administración, licenciamiento, respaldo e infraestructura no se promocionan en esta primera versión.

## Identidad

Se copian sin modificar `logo-h.svg`, `logo-dark-h.svg` e `icon.svg` de la carpeta
de identidad oficial. El sitio utiliza el logotipo horizontal para fondos oscuros
y el icono como favicon. La variante clara queda disponible para ajustes futuros.
Los iconos de interfaz son SVG propios y no sustituyen ni rediseñan la marca.

La composición mantiene el azul profundo y el turquesa de la propuesta original,
con espacios amplios, secciones claras y acentos oscuros en hero, proceso y pie.
La tipografía Manrope se aloja localmente bajo la licencia incluida en
`public/assets/fonts/OFL.txt`.
