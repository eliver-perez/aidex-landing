<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
require SITE_ROOT . '/src/view.php';
startContactSession();

$canonical = publicUrl(setting('SITE_URL', 'https://aidex.com.mx'));
$privacyUrl = publicUrl(setting('PRIVACY_URL'));
$contactEmail = filter_var(setting('CONTACT_EMAIL'), FILTER_VALIDATE_EMAIL) ?: '';
$flash = $_SESSION['flash'] ?? [];
$old = $_SESSION['old'] ?? [];
$formMessage = trim($flash['message'] ?? '');
$formState = !empty($flash['ok']) ? 'success' : 'error';
unset($_SESSION['flash'], $_SESSION['old']);

function oldValue(string $field): string
{
    return escape($GLOBALS['old'][$field] ?? '');
}
?>
<!DOCTYPE html>
<html lang="es-MX">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#0b2233" />
        <title>AIDEX · Tus expedientes en orden. Tu información a la mano.</title>
        <meta
            name="description"
            content="Organiza, indexa y localiza tus expedientes digitales con AIDEX. Bibliotecas documentales, búsqueda por contenido y control de acceso. Solicita una demostración."
        />
        <meta property="og:type" content="website" />
        <meta property="og:locale" content="es_MX" />
        <meta property="og:site_name" content="AIDEX" />
        <meta
            property="og:title"
            content="AIDEX · Menos tiempo buscando. Más control de tu información."
        />
        <meta
            property="og:description"
            content="Una mejor forma de organizar, encontrar y controlar tus expedientes digitales. Conoce AIDEX."
        />
        <?php if ($canonical !== ''): ?>
        <link rel="canonical" href="<?= escape($canonical) ?>/" />
        <meta property="og:url" content="<?= escape($canonical) ?>/" />
        <meta property="og:image" content="<?= escape($canonical) ?>/assets/img/social-card.png" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta
            property="og:image:alt"
            content="AIDEX. Tus expedientes en orden. Tu información a la mano."
        />
        <?php endif; ?>
        <meta name="twitter:card" content="summary_large_image" />
        <link rel="icon" href="assets/img/icon.svg" type="image/svg+xml" />
        <link
            rel="preload"
            href="assets/fonts/manrope-extrabold.ttf"
            as="font"
            type="font/ttf"
            crossorigin
        />
        <link rel="stylesheet" href="assets/css/site.css" />
        <script src="assets/js/site.js" defer></script>
    </head>
    <body>
        <a class="skip-link" href="#contenido">Saltar al contenido</a>

        <div class="header-shell" id="inicio">
            <header class="site-header container">
                <a class="brand" href="#inicio" aria-label="AIDEX, inicio">
                    <img
                        src="assets/img/logo-dark-h.svg"
                        alt="AIDEX · Aplicación de Indexación Digital de Expedientes"
                        width="720"
                        height="140"
                    />
                </a>
                <button
                    class="menu-toggle"
                    type="button"
                    aria-expanded="false"
                    aria-controls="main-nav"
                    aria-label="Abrir menú"
                >
                    <?= icon('menu') ?>
                </button>
                <nav class="main-nav" id="main-nav" aria-label="Navegación principal">
                    <a href="#solucion">Por qué AIDEX</a>
                    <a href="#funcionalidades">Capacidades</a>
                    <a href="#conoce-aidex">Explora AIDEX</a>
                    <a class="button button-small button-nav" href="#contacto">
                        Solicitar demostración <?= icon('diagonal') ?>
                    </a>
                </nav>
            </header>
        </div>

        <main id="contenido">
            <div class="hero-shell">
                <section class="hero container" aria-labelledby="hero-title">
                    <div class="hero-copy">
                        <p class="eyebrow eyebrow-light">
                            <span class="status-dot"></span>
                            Tu información, en su lugar
                        </p>
                        <h1 id="hero-title">
                            Tus expedientes
                            <br />
                            en orden.
                            <br />
                            <span>
                                Tu información
                                <br />
                                a la mano.
                            </span>
                        </h1>
                        <p class="hero-description">
                            Deja atrás las búsquedas interminables. Con AIDEX, organiza, encuentra y
                            controla tus expedientes digitales desde un mismo lugar.
                        </p>
                        <div class="hero-actions">
                            <a class="button button-primary" href="#contacto">
                                Solicitar una demo <?= icon('arrow') ?>
                            </a>
                            <a class="button button-ghost" href="#conoce-aidex">
                                Explorar AIDEX <?= icon('diagonal') ?>
                            </a>
                        </div>
                        <p class="hero-footnote">
                            <?= icon('check') ?> Organización que se adapta a tus documentos.
                        </p>
                    </div>

                    <figure class="hero-figure">
                        <div class="app-window" aria-label="Ejemplo de una biblioteca documental">
                            <div class="window-bar">
                                <span class="window-dots" aria-hidden="true">
                                    <i></i>
                                    <i></i>
                                    <i></i>
                                </span>
                                <span>Tu espacio documental</span>
                                <?= icon('shield') ?>
                            </div>
                            <div class="app-layout">
                                <aside class="app-sidebar" aria-label="Navegación ilustrativa">
                                    <img
                                        src="assets/img/logo-dark-h.svg"
                                        alt="AIDEX"
                                        width="720"
                                        height="140"
                                    />
                                    <p class="sidebar-label">ESPACIO DE TRABAJO</p>
                                    <span><?= icon('grid') ?> Resumen</span>
                                    <span class="selected"><?= icon('folder') ?> Bibliotecas</span>
                                    <span><?= icon('search') ?> Buscar</span>
                                    <span><?= icon('history') ?> Auditoría</span>
                                    <div class="sidebar-bottom">
                                        <?= icon('shield') ?> Acceso por permisos
                                    </div>
                                </aside>
                                <div class="app-content">
                                    <div class="app-breadcrumb">
                                        Bibliotecas <?= icon('chevron') ?> Proyectos
                                    </div>
                                    <div class="app-title-row">
                                        <div>
                                            <h2>Todo en su lugar.</h2>
                                            <p>Biblioteca de proyectos</p>
                                        </div>
                                        <span class="app-avatar" aria-label="Usuario de ejemplo">
                                            AB
                                        </span>
                                    </div>
                                    <div class="mock-search">
                                        <?= icon('search') ?> Encuentra un documento…
                                        <span>↵</span>
                                    </div>
                                    <div class="folder-grid">
                                        <div class="mini-folder">
                                            <?= icon('folder') ?>
                                            <strong>Contratos</strong>
                                            <span>Documentos del proyecto</span>
                                        </div>
                                        <div class="mini-folder">
                                            <?= icon('folder') ?>
                                            <strong>Reportes</strong>
                                            <span>Seguimiento y avances</span>
                                        </div>
                                        <div class="mini-folder">
                                            <?= icon('folder') ?>
                                            <strong>Acuerdos</strong>
                                            <span>Información de consulta</span>
                                        </div>
                                    </div>
                                    <div class="mock-table">
                                        <div class="mock-table-head">
                                            <span>DOCUMENTO</span>
                                            <span>ESTADO</span>
                                        </div>
                                        <div class="mock-row">
                                            <span class="file-icon"><?= icon('file') ?></span>
                                            <div>
                                                <strong>Contrato de servicios.pdf</strong>
                                                <small>Expediente PR-2026-001</small>
                                            </div>
                                            <span class="tag">Indexado</span>
                                        </div>
                                        <div class="mock-row">
                                            <span class="file-icon blue"><?= icon('file') ?></span>
                                            <div>
                                                <strong>Reporte de avance.docx</strong>
                                                <small>Expediente PR-2026-001</small>
                                            </div>
                                            <span class="tag">Indexado</span>
                                        </div>
                                        <div class="mock-row">
                                            <span class="file-icon"><?= icon('file') ?></span>
                                            <div>
                                                <strong>Acuerdo de trabajo.pdf</strong>
                                                <small>Expediente PR-2026-002</small>
                                            </div>
                                            <span class="tag">Indexado</span>
                                        </div>
                                    </div>
                                    <div class="app-bottom">
                                        <span class="status-dot"></span>
                                        Información organizada, lista para consultar
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="floating-result" aria-hidden="true">
                            <span class="result-check"><?= icon('check') ?></span>
                            <div>
                                <strong>Lo que buscas, a tu alcance.</strong>
                                <span>Coincidencias dentro del documento</span>
                            </div>
                            <?= icon('search') ?>
                        </div>
                        <figcaption>
                            Representación ilustrativa de AIDEX · Datos de ejemplo
                        </figcaption>
                    </figure>
                </section>

                <div class="value-strip">
                    <div class="container value-strip-inner">
                        <span>DEL ARCHIVO A LA INFORMACIÓN</span>
                        <p><?= icon('folder') ?> Organiza con estructura</p>
                        <p><?= icon('search') ?> Encuentra por contenido</p>
                        <p><?= icon('shield') ?> Consulta con control</p>
                    </div>
                </div>
            </div>

            <section class="section solution" id="solucion" aria-labelledby="solution-title">
                <div class="container solution-grid">
                    <div class="solution-copy">
                        <p class="eyebrow">POR QUÉ AIDEX</p>
                        <h2 id="solution-title">
                            Que encontrar un documento
                            <br />
                            no detenga tu trabajo.
                        </h2>
                        <p>La información está ahí. El tiempo se pierde intentando localizarla.</p>
                        <ul class="problem-list">
                            <li>
                                <?= icon('folder') ?>
                                <span>Carpetas dispersas y archivos difíciles de ubicar.</span>
                            </li>
                            <li>
                                <?= icon('search') ?>
                                <span>Búsquedas manuales que interrumpen tus tareas.</span>
                            </li>
                            <li>
                                <?= icon('layers') ?>
                                <span>Expedientes sin una estructura clara para consultar.</span>
                            </li>
                        </ul>
                    </div>
                    <div class="solution-highlight">
                        <span class="feature-icon"><?= icon('folder-search') ?></span>
                        <h3>
                            De archivos dispersos
                            <br />
                            a información
                            <span>accesible.</span>
                        </h3>
                        <p>
                            Con AIDEX, organiza tus bibliotecas, localiza contenido indexado y
                            consulta tus documentos con los permisos que corresponden.
                        </p>
                        <a class="solution-link" href="#conoce-aidex">
                            Descubre cómo funciona <?= icon('arrow') ?>
                        </a>
                    </div>
                </div>
            </section>

            <section class="section features" id="funcionalidades" aria-labelledby="features-title">
                <div class="container">
                    <div class="section-heading">
                        <div>
                            <p class="eyebrow">CAPACIDADES PARA TU GESTIÓN DOCUMENTAL</p>
                            <h2 id="features-title">Más que guardar archivos.</h2>
                        </div>
                        <p>
                            Una forma de dar estructura a tus expedientes
                            <br class="desktop-break" />
                            y valor a la información que contienen.
                        </p>
                    </div>
                    <div class="feature-grid">
                        <article class="feature-card">
                            <div class="feature-top">
                                <span class="feature-icon"><?= icon('folder') ?></span>
                                <h3>Bibliotecas a tu medida</h3>
                            </div>
                            <p>
                                Vincula carpetas existentes, incorpora nuevos archivos o combina
                                ambas formas de trabajo en una biblioteca.
                            </p>
                        </article>
                        <article class="feature-card">
                            <div class="feature-top">
                                <span class="feature-icon"><?= icon('search') ?></span>
                                <h3>Búsqueda dentro de documentos</h3>
                            </div>
                            <p>
                                Localiza nombres, expedientes y contenido indexado. Acota los
                                resultados por biblioteca, formato y clasificación.
                            </p>
                        </article>
                        <article class="feature-card">
                            <div class="feature-top">
                                <span class="feature-icon"><?= icon('layers') ?></span>
                                <h3>Expedientes con estructura</h3>
                            </div>
                            <p>
                                Agrupa documentos relacionados y usa plantillas para consultar los
                                requisitos incorporados y pendientes.
                            </p>
                        </article>
                        <article class="feature-card">
                            <div class="feature-top">
                                <span class="feature-icon"><?= icon('scan') ?></span>
                                <h3>Extracción de texto y OCR</h3>
                            </div>
                            <p>
                                Convierte contenido en información que puedes buscar, incluso en PDF
                                escaneados, según su calidad y configuración.
                            </p>
                        </article>
                        <article class="feature-card">
                            <div class="feature-top">
                                <span class="feature-icon"><?= icon('shield') ?></span>
                                <h3>Acceso con permisos</h3>
                            </div>
                            <p>
                                Define perfiles y autorizaciones por biblioteca para organizar quién
                                puede consultar documentos y realizar acciones.
                            </p>
                        </article>
                        <article class="feature-card">
                            <div class="feature-top">
                                <span class="feature-icon"><?= icon('history') ?></span>
                                <h3>Trazabilidad documental</h3>
                            </div>
                            <p>
                                Consulta las acciones registradas sobre tus documentos y revisa la
                                actividad de las bibliotecas según tus permisos.
                            </p>
                        </article>
                    </div>
                    <p class="capability-note">
                        Las funciones disponibles dependen de los módulos habilitados, la
                        configuración y los permisos de cada usuario.
                    </p>
                </div>
            </section>

            <section class="section showcase" id="conoce-aidex" aria-labelledby="showcase-title">
                <div class="container">
                    <div class="showcase-heading">
                        <p class="eyebrow">EXPLORA AIDEX</p>
                        <h2 id="showcase-title">
                            La información correcta.
                            <br />
                            Sin recorrer cada carpeta.
                        </h2>
                        <p>
                            Explora una muestra de cómo tus documentos pueden convertirse en
                            información accesible.
                        </p>
                    </div>
                    <div
                        class="demo-tabs"
                        role="tablist"
                        aria-label="Explorar las capacidades de AIDEX"
                    >
                        <button
                            id="tab-search"
                            role="tab"
                            aria-selected="true"
                            aria-controls="panel-search"
                            tabindex="0"
                            type="button"
                        >
                            <?= icon('search') ?> Búsqueda
                        </button>
                        <button
                            id="tab-organize"
                            role="tab"
                            aria-selected="false"
                            aria-controls="panel-organize"
                            tabindex="-1"
                            type="button"
                        >
                            <?= icon('folder') ?> Bibliotecas
                        </button>
                        <button
                            id="tab-trace"
                            role="tab"
                            aria-selected="false"
                            aria-controls="panel-trace"
                            tabindex="-1"
                            type="button"
                        >
                            <?= icon('history') ?> Trazabilidad
                        </button>
                        <button
                            id="tab-indicators"
                            role="tab"
                            aria-selected="false"
                            aria-controls="panel-indicators"
                            tabindex="-1"
                            type="button"
                        >
                            <?= icon('chart') ?> Indicadores
                        </button>
                    </div>
                    <div class="demo-frame">
                        <div class="demo-notice">
                            <span class="status-dot" aria-hidden="true"></span>
                            Vista de demostración · Datos de ejemplo
                        </div>
                        <section
                            class="demo-panel"
                            id="panel-search"
                            role="tabpanel"
                            aria-labelledby="tab-search"
                            tabindex="0"
                        >
                            <div class="demo-description">
                                <span class="demo-step">BÚSQUEDA DOCUMENTAL</span>
                                <h3>
                                    No necesitas recordar
                                    <br />
                                    el nombre del archivo.
                                </h3>
                                <p>
                                    Una palabra puede ser el punto de partida. AIDEX busca en el
                                    texto que ha extraído e indexado de tus documentos.
                                </p>
                                <ul class="check-list">
                                    <li><?= icon('check') ?> Coincidencias con contexto</li>
                                    <li><?= icon('check') ?> Filtros para acotar tu búsqueda</li>
                                    <li><?= icon('check') ?> Resultados por biblioteca</li>
                                </ul>
                                <p class="demo-hint">
                                    Prueba con «contrato», «avance» o «acuerdo».
                                </p>
                            </div>
                            <div class="search-demo">
                                <div class="demo-window-title"><?= icon('search') ?> Buscar documentos</div>
                                <form class="demo-search-form" id="demo-search" role="search">
                                    <label class="sr-only" for="demo-query">
                                        Buscar entre documentos de ejemplo
                                    </label>
                                    <input
                                        type="search"
                                        id="demo-query"
                                        value="contrato"
                                        placeholder="Prueba con contrato…"
                                        maxlength="100"
                                        autocomplete="off"
                                    />
                                    <button type="submit" aria-label="Buscar en el ejemplo">
                                        <?= icon('search') ?>
                                    </button>
                                </form>
                                <div class="demo-filter-row">
                                    <label for="demo-library">Biblioteca</label>
                                    <select id="demo-library">
                                        <option value="all">Todas las bibliotecas</option>
                                        <option value="Proyectos">Proyectos</option>
                                        <option value="Administración">Administración</option>
                                    </select>
                                </div>
                                <p class="result-count" id="result-count" aria-live="polite">
                                    2 documentos de ejemplo encontrados
                                </p>
                                <div id="search-results" class="search-results"></div>
                                <noscript>
                                    <p class="no-script-note">
                                        Activa JavaScript para probar la búsqueda ilustrativa. AIDEX
                                        permite localizar coincidencias en el contenido indexado.
                                    </p>
                                </noscript>
                            </div>
                        </section>
                        <section
                            class="demo-panel"
                            id="panel-organize"
                            role="tabpanel"
                            aria-labelledby="tab-organize"
                            tabindex="0"
                            hidden
                        >
                            <div class="demo-description">
                                <span class="demo-step">BIBLIOTECAS Y EXPEDIENTES</span>
                                <h3>
                                    Cada biblioteca,
                                    <br />
                                    un espacio organizado.
                                </h3>
                                <p>
                                    Organiza tus documentos en bibliotecas. Dentro de ellas, reúne
                                    los archivos de un mismo asunto y consulta los requisitos del
                                    expediente.
                                </p>
                                <ul class="check-list">
                                    <li><?= icon('check') ?> Categorías y tipos documentales</li>
                                    <li><?= icon('check') ?> Plantillas reutilizables</li>
                                    <li><?= icon('check') ?> Avance del expediente</li>
                                </ul>
                            </div>
                            <div class="organization-demo">
                                <div class="demo-window-title">
                                    <?= icon('folder') ?> Biblioteca de proyectos
                                </div>
                                <div class="organization-content">
                                    <div class="library-context">
                                        <span><?= icon('folder') ?> Proyectos</span>
                                        <span class="tag">Biblioteca híbrida</span>
                                    </div>
                                    <p class="app-breadcrumb">Expedientes / PR-2026-001</p>
                                    <h4>Documentación del proyecto</h4>
                                    <div class="progress-caption">
                                        <span>Requisitos incorporados</span>
                                        <strong>2 de 3</strong>
                                    </div>
                                    <progress
                                        value="2"
                                        max="3"
                                        aria-label="2 de 3 requisitos incorporados"
                                    >
                                        2 de 3
                                    </progress>
                                    <ul class="requirements-list">
                                        <li>
                                            <?= icon('check') ?>
                                            <span>Contrato de servicios</span>
                                            <span class="tag">Incorporado</span>
                                        </li>
                                        <li>
                                            <?= icon('check') ?>
                                            <span>Reporte de avance</span>
                                            <span class="tag">Incorporado</span>
                                        </li>
                                        <li>
                                            <?= icon('file') ?>
                                            <span>Acta de cierre</span>
                                            <span class="tag tag-pending">Pendiente</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </section>
                        <section
                            class="demo-panel"
                            id="panel-trace"
                            role="tabpanel"
                            aria-labelledby="tab-trace"
                            tabindex="0"
                            hidden
                        >
                            <div class="demo-description">
                                <span class="demo-step">TRAZABILIDAD DOCUMENTAL</span>
                                <h3>
                                    Más contexto.
                                    <br />
                                    Mejor seguimiento.
                                </h3>
                                <p>
                                    Consulta cuándo se registró una acción, quién la realizó y a qué
                                    documento corresponde, según tus permisos.
                                </p>
                                <ul class="check-list">
                                    <li><?= icon('check') ?> Historial por documento</li>
                                    <li><?= icon('check') ?> Eventos por biblioteca</li>
                                    <li><?= icon('check') ?> Filtros de actividad</li>
                                </ul>
                            </div>
                            <div class="timeline-demo">
                                <div class="demo-window-title">
                                    <?= icon('history') ?> Historial documental
                                </div>
                                <div class="timeline-content">
                                    <h4>Contrato de servicios.pdf</h4>
                                    <p>Actividad registrada · Usuarios y fechas ficticios</p>
                                    <ol class="timeline">
                                        <li>
                                            <span class="timeline-dot"></span>
                                            <div>
                                                <strong>Documento consultado</strong>
                                                <span>Usuario de consulta · 12 oct, 10:42</span>
                                            </div>
                                            <?= icon('file') ?>
                                        </li>
                                        <li>
                                            <span class="timeline-dot"></span>
                                            <div>
                                                <strong>Documento aprobado</strong>
                                                <span>Usuario revisor · 12 oct, 09:30</span>
                                            </div>
                                            <?= icon('check') ?>
                                        </li>
                                        <li>
                                            <span class="timeline-dot"></span>
                                            <div>
                                                <strong>Documento cargado</strong>
                                                <span>Usuario de carga · 11 oct, 16:15</span>
                                            </div>
                                            <?= icon('plus') ?>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </section>
                        <section
                            class="demo-panel"
                            id="panel-indicators"
                            role="tabpanel"
                            aria-labelledby="tab-indicators"
                            tabindex="0"
                            hidden
                        >
                            <div class="demo-description">
                                <span class="demo-step">INDICADORES DOCUMENTALES</span>
                                <h3>
                                    Una vista clara
                                    <br />
                                    de tu archivo.
                                </h3>
                                <p>
                                    Consulta el volumen documental, el estado de la indexación y las
                                    incorporaciones recientes para dar seguimiento a tus procesos.
                                </p>
                                <ul class="check-list">
                                    <li><?= icon('check') ?> Documentos e índice vigente</li>
                                    <li><?= icon('check') ?> Pendientes y errores de procesamiento</li>
                                    <li><?= icon('check') ?> Actividad de incorporación</li>
                                </ul>
                            </div>
                            <?php require SITE_ROOT . '/src/views/indicators.php'; ?>
                        </section>
                    </div>
                </div>
            </section>

            <section class="section workflow" id="como-funciona" aria-labelledby="workflow-title">
                <div class="container">
                    <div class="section-heading">
                        <div>
                            <p class="eyebrow eyebrow-light">ASÍ FUNCIONA AIDEX</p>
                            <h2 id="workflow-title">Del documento a la respuesta.</h2>
                        </div>
                        <a class="text-link" href="#contacto">Veámoslo con una demo <?= icon('arrow') ?></a>
                    </div>
                    <div class="steps">
                        <article>
                            <span class="step-icon"><?= icon('folder') ?></span>
                            <h3>Organiza tus bibliotecas</h3>
                            <p>
                                Define bibliotecas y vincula tus carpetas o incorpora documentos
                                mediante cargas.
                            </p>
                        </article>
                        <article>
                            <span class="step-icon"><?= icon('scan') ?></span>
                            <h3>Clasifica e indexa</h3>
                            <p>
                                Clasifica, asocia documentos a expedientes y procesa su contenido
                                para facilitar la búsqueda.
                            </p>
                        </article>
                        <article>
                            <span class="step-icon"><?= icon('folder-search') ?></span>
                            <h3>Encuentra y consulta</h3>
                            <p>
                                Localiza información, consulta documentos y revisa su historial con
                                los permisos correspondientes.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="section contact" id="contacto" aria-labelledby="contact-title">
                <div class="container contact-grid">
                    <div class="contact-copy">
                        <p class="eyebrow">HABLEMOS DE TUS DOCUMENTOS</p>
                        <h2 id="contact-title">
                            Menos tiempo buscando.
                            <br />
                            <span>
                                Más posibilidades
                                <br />
                                para tu información.
                            </span>
                        </h2>
                        <p>
                            Cuéntanos cómo organizas tus expedientes y qué te gustaría mejorar.
                            Conoce cómo AIDEX puede ayudarte en una demostración.
                        </p>
                        <div class="contact-detail">
                            <?= icon('mail') ?>
                            <div>
                                <strong>Tu siguiente paso empieza aquí.</strong>
                                <span>Solicita información o una demostración del producto.</span>
                            </div>
                        </div>
                        <?php if ($contactEmail !== ''): ?>
                        <a class="contact-email" href="mailto:<?= escape($contactEmail) ?>"><?= escape($contactEmail) ?></a>
                        <?php endif; ?>
                    </div>
                    <form class="contact-form" id="contact-form" action="contact.php" method="post">
                        <h3>Conoce AIDEX</h3>
                        <p class="form-intro">Hagamos espacio para una mejor organización.</p>
                        <input type="hidden" name="csrf" value="<?= escape($_SESSION['csrf']) ?>" />
                        <div class="honeypot" aria-hidden="true">
                            <label for="website">Deja este campo vacío</label>
                            <input
                                type="text"
                                id="website"
                                name="website"
                                tabindex="-1"
                                autocomplete="off"
                            />
                        </div>
                        <div class="field">
                            <label for="nombre">
                                Nombre completo
                                <span aria-hidden="true">*</span>
                            </label>
                            <input
                                id="nombre"
                                name="nombre"
                                autocomplete="name"
                                maxlength="120"
                                required
                                placeholder="Tu nombre"
                                value="<?= oldValue('nombre') ?>"
                                aria-describedby="error-nombre"
                            />
                            <span class="field-error" id="error-nombre"><?= escape($flash['errors']['nombre'] ?? '') ?></span>
                        </div>
                        <div class="field">
                            <label for="empresa">
                                Empresa u organización
                                <span class="optional">Opcional</span>
                            </label>
                            <input
                                id="empresa"
                                name="empresa"
                                autocomplete="organization"
                                maxlength="150"
                                placeholder="Nombre de tu organización"
                                value="<?= oldValue('empresa') ?>"
                                aria-describedby="error-empresa"
                            />
                            <span class="field-error" id="error-empresa"><?= escape($flash['errors']['empresa'] ?? '') ?></span>
                        </div>
                        <div class="field-grid">
                            <div class="field">
                                <label for="correo">
                                    Correo electrónico
                                    <span aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="correo"
                                    name="correo"
                                    autocomplete="email"
                                    maxlength="254"
                                    required
                                    placeholder="nombre@ejemplo.com"
                                    value="<?= oldValue('correo') ?>"
                                    aria-describedby="error-correo"
                                />
                                <span class="field-error" id="error-correo"><?= escape($flash['errors']['correo'] ?? '') ?></span>
                            </div>
                            <div class="field">
                                <label for="telefono">
                                    Teléfono
                                    <span class="optional">Opcional</span>
                                </label>
                                <input
                                    type="tel"
                                    id="telefono"
                                    name="telefono"
                                    autocomplete="tel"
                                    maxlength="35"
                                    pattern="[0-9+\(\).\s#xX\-]{5,35}"
                                    placeholder="Tu número"
                                    value="<?= oldValue('telefono') ?>"
                                    aria-describedby="error-telefono"
                                />
                                <span class="field-error" id="error-telefono"><?= escape($flash['errors']['telefono'] ?? '') ?></span>
                            </div>
                        </div>
                        <div class="field">
                            <label for="mensaje">
                                ¿En qué podemos ayudarte?
                                <span aria-hidden="true">*</span>
                            </label>
                            <textarea
                                id="mensaje"
                                name="mensaje"
                                rows="4"
                                maxlength="3000"
                                required
                                placeholder="Me interesa conocer AIDEX para…"
                                aria-describedby="error-mensaje"
                            >
<?= oldValue('mensaje') ?></textarea>
                            <span class="field-error" id="error-mensaje"><?= escape($flash['errors']['mensaje'] ?? '') ?></span>
                        </div>
                        <button class="button button-dark form-submit" type="submit">
                            <span>Solicitar información</span>
                            <?= icon('arrow') ?>
                        </button>
                        <p class="form-note">
                            * Campos obligatorios. Evita incluir información confidencial en tu
                            mensaje.
                        </p>
                        <?php if ($privacyUrl !== ''): ?>
                        <p class="form-note">
                            <a href="<?= escape($privacyUrl) ?>">Consulta el aviso de privacidad</a>
                            .
                        </p>
                        <?php endif; ?>
                        <div
                            class="form-status<?= $formMessage !== '' ? ' is-visible' : '' ?>"
                            data-state="<?= escape($formState) ?>"
                            id="form-status"
                            role="status"
                            aria-live="polite"
                            aria-atomic="true"
                            tabindex="-1"
                        >
                            <?= escape($formMessage) ?>
                        </div>
                    </form>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-top">
                <a class="footer-brand" href="#inicio" aria-label="AIDEX, volver al inicio">
                    <img
                        src="assets/img/logo-dark-h.svg"
                        alt="AIDEX · Aplicación de Indexación Digital de Expedientes"
                        width="720"
                        height="140"
                        loading="lazy"
                    />
                </a>
                <p>
                    Tu biblioteca digital,
                    <br />
                    ordenada y al alcance.
                </p>
                <a class="text-link" href="#contacto">Conversemos <?= icon('diagonal') ?></a>
            </div>
            <div class="container footer-bottom">
                <span>© <?= date('Y') ?> AIDEX. Todos los derechos reservados.</span>
                <div>
                    <?php if ($privacyUrl !== ''): ?>
                    <a href="<?= escape($privacyUrl) ?>">Aviso de privacidad</a>
                    <?php endif; ?>
                    <a href="#inicio">Volver al inicio ↑</a>
                </div>
            </div>
        </footer>

        <dialog class="document-dialog" id="document-dialog" aria-labelledby="document-title">
            <div class="dialog-heading">
                <span class="eyebrow">FICHA ILUSTRATIVA</span>
                <button type="button" id="close-document" aria-label="Cerrar ficha">×</button>
            </div>
            <span class="feature-icon"><?= icon('file') ?></span>
            <h2 id="document-title"></h2>
            <p id="document-meta"></p>
            <h3>Fragmento de ejemplo</h3>
            <blockquote id="document-excerpt"></blockquote>
            <p class="dialog-note">
                Documento ficticio de esta demostración. No contiene archivos reales ni ofrece
                descargas.
            </p>
        </dialog>
    </body>
</html>
