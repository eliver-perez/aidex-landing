<?php

// Muestra editorial basada en los apartados 14.1 y 14.2 del manual.
// Las cifras son ficticias y guardan consistencia entre tarjetas, gráficos y tablas.
?>
<div class="dashboard-demo">
    <div class="demo-window-title"><?= icon('chart') ?> Resumen documental</div>
    <div class="dashboard-content">
        <div class="dashboard-heading">
            <div>
                <h4>El estado de tus documentos</h4>
                <p>Una perspectiva general de la gestión documental</p>
            </div>
            <span class="dashboard-period">Semana de ejemplo</span>
        </div>
        <dl class="dashboard-metrics">
            <div class="metric-card">
                <dt><?= icon('file') ?> Registrados</dt>
                <dd>
                    1,280
                    <span>Total de documentos</span>
                </dd>
            </div>
            <div class="metric-card metric-card-indexed">
                <dt><?= icon('check') ?> Índice vigente</dt>
                <dd>
                    1,152
                    <span>Contenido indexado</span>
                </dd>
            </div>
            <div class="metric-card">
                <dt><?= icon('clock') ?> Pendientes</dt>
                <dd>
                    104
                    <span>Por procesar</span>
                </dd>
            </div>
            <div class="metric-card">
                <dt><?= icon('alert') ?> Con error</dt>
                <dd>
                    24
                    <span>De extracción</span>
                </dd>
            </div>
        </dl>
        <div class="dashboard-charts">
            <figure class="chart-card">
                <figcaption>Incorporaciones por día</figcaption>
                <p class="chart-subtitle">155 documentos en esta semana de ejemplo</p>
                <svg
                    class="activity-chart"
                    viewBox="0 0 260 145"
                    role="img"
                    aria-labelledby="activity-chart-title activity-chart-description"
                >
                    <title id="activity-chart-title">Incorporaciones documentales de ejemplo</title>
                    <desc id="activity-chart-description">
                        Lunes 18, martes 32, miércoles 24, jueves 45 y viernes 36 documentos.
                    </desc>
                    <g class="chart-grid">
                        <path d="M26 20h222M26 64h222M26 108h222" />
                    </g>
                    <g class="chart-axis-labels">
                        <text x="0" y="24">50</text>
                        <text x="0" y="68">25</text>
                        <text x="7" y="112">0</text>
                    </g>
                    <g class="chart-bars">
                        <rect x="37" y="76.32" width="24" height="31.68" rx="4" />
                        <rect x="80" y="51.68" width="24" height="56.32" rx="4" />
                        <rect x="123" y="65.76" width="24" height="42.24" rx="4" />
                        <rect x="166" y="28.8" width="24" height="79.2" rx="4" />
                        <rect x="209" y="44.64" width="24" height="63.36" rx="4" />
                    </g>
                    <g class="chart-value-labels">
                        <text x="49" y="69">18</text>
                        <text x="92" y="44">32</text>
                        <text x="135" y="58">24</text>
                        <text x="178" y="21">45</text>
                        <text x="221" y="37">36</text>
                    </g>
                    <g class="chart-day-labels">
                        <text x="49" y="131">Lun</text>
                        <text x="92" y="131">Mar</text>
                        <text x="135" y="131">Mié</text>
                        <text x="178" y="131">Jue</text>
                        <text x="221" y="131">Vie</text>
                    </g>
                </svg>
            </figure>
            <figure class="chart-card">
                <figcaption>Estado del procesamiento</figcaption>
                <div class="processing-chart">
                    <svg
                        class="donut-chart"
                        viewBox="0 0 120 120"
                        role="img"
                        aria-labelledby="processing-chart-title processing-chart-description"
                    >
                        <title id="processing-chart-title">Estado documental de ejemplo</title>
                        <desc id="processing-chart-description">
                            De 1,280 documentos, 1,152 tienen índice vigente, 104 están pendientes y
                            24 tienen error de extracción.
                        </desc>
                        <g transform="rotate(-90 60 60)" fill="none" stroke-width="12">
                            <circle
                                class="donut-indexed"
                                cx="60"
                                cy="60"
                                r="47"
                                pathLength="100"
                                stroke-dasharray="90 10"
                            />
                            <circle
                                class="donut-pending"
                                cx="60"
                                cy="60"
                                r="47"
                                pathLength="100"
                                stroke-dasharray="8.125 91.875"
                                stroke-dashoffset="-90"
                            />
                            <circle
                                class="donut-error"
                                cx="60"
                                cy="60"
                                r="47"
                                pathLength="100"
                                stroke-dasharray="1.875 98.125"
                                stroke-dashoffset="-98.125"
                            />
                        </g>
                        <text class="donut-value" x="60" y="60">90%</text>
                        <text class="donut-label" x="60" y="77">indexado</text>
                    </svg>
                    <ul class="chart-legend">
                        <li>
                            <span class="legend-dot legend-indexed"></span>
                            Índice vigente
                            <strong>1,152</strong>
                        </li>
                        <li>
                            <span class="legend-dot legend-pending"></span>
                            Pendientes
                            <strong>104</strong>
                        </li>
                        <li>
                            <span class="legend-dot legend-error"></span>
                            Con error
                            <strong>24</strong>
                        </li>
                    </ul>
                </div>
            </figure>
        </div>
        <details class="dashboard-data">
            <summary>Ver datos de los gráficos</summary>
            <div class="dashboard-tables">
                <table>
                    <caption>Incorporaciones de ejemplo</caption>
                    <thead>
                        <tr>
                            <th scope="col">Día</th>
                            <th scope="col">Documentos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Lunes</th>
                            <td>18</td>
                        </tr>
                        <tr>
                            <th scope="row">Martes</th>
                            <td>32</td>
                        </tr>
                        <tr>
                            <th scope="row">Miércoles</th>
                            <td>24</td>
                        </tr>
                        <tr>
                            <th scope="row">Jueves</th>
                            <td>45</td>
                        </tr>
                        <tr>
                            <th scope="row">Viernes</th>
                            <td>36</td>
                        </tr>
                    </tbody>
                </table>
                <table>
                    <caption>Estado de ejemplo</caption>
                    <thead>
                        <tr>
                            <th scope="col">Estado</th>
                            <th scope="col">Documentos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Índice vigente</th>
                            <td>1,152</td>
                        </tr>
                        <tr>
                            <th scope="row">Pendientes</th>
                            <td>104</td>
                        </tr>
                        <tr>
                            <th scope="row">Error de extracción</th>
                            <td>24</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="row">Total</th>
                            <td>1,280</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </details>
    </div>
</div>
