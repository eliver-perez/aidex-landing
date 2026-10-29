'use strict';

document.documentElement.classList.add('js-enabled');

// El menú conserva enlaces utilizables incluso cuando JavaScript no está disponible.
function initializeNavigation() {
    const toggle = document.querySelector('.menu-toggle');
    const navigation = document.querySelector('#main-nav');

    function closeMenu() {
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Abrir menú');
        navigation.classList.remove('is-open');
    }

    toggle.addEventListener('click', () => {
        const expanded = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!expanded));
        toggle.setAttribute('aria-label', expanded ? 'Abrir menú' : 'Cerrar menú');
        navigation.classList.toggle('is-open', !expanded);
    });

    navigation.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            closeMenu();
            toggle.focus();
        }
    });

    window.matchMedia('(min-width: 851px)').addEventListener('change', closeMenu);
}

function initializeTabs() {
    const tabs = Array.from(document.querySelectorAll('[role="tab"]'));

    function activateTab(selectedTab) {
        tabs.forEach((tab) => {
            const selected = tab === selectedTab;
            tab.setAttribute('aria-selected', String(selected));
            tab.tabIndex = selected ? 0 : -1;
            document.getElementById(tab.getAttribute('aria-controls')).hidden = !selected;
        });
    }

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => activateTab(tab));
        tab.addEventListener('keydown', (event) => {
            let nextIndex = index;

            if (event.key === 'ArrowRight') {
                nextIndex = (index + 1) % tabs.length;
            } else if (event.key === 'ArrowLeft') {
                nextIndex = (index - 1 + tabs.length) % tabs.length;
            } else if (event.key === 'Home') {
                nextIndex = 0;
            } else if (event.key === 'End') {
                nextIndex = tabs.length - 1;
            } else {
                return;
            }

            event.preventDefault();
            activateTab(tabs[nextIndex]);
            tabs[nextIndex].focus();
        });
    });
}

// Datos ficticios limitados a esta página. No se realiza ninguna consulta a AIDEX.
const sampleDocuments = [
    {
        name: 'Contrato de servicios.pdf',
        library: 'Proyectos',
        record: 'PR-2026-001',
        type: 'PDF',
        page: 'Página 2',
        excerpt: 'El contrato establece las actividades y los entregables del proyecto, así como los acuerdos para su seguimiento.'
    },
    {
        name: 'Acuerdo de trabajo.pdf',
        library: 'Administración',
        record: 'AD-2026-002',
        type: 'PDF',
        page: 'Página 1',
        excerpt: 'Este acuerdo complementa el contrato de servicios y describe las responsabilidades de cada participante.'
    },
    {
        name: 'Reporte de avance.docx',
        library: 'Proyectos',
        record: 'PR-2026-001',
        type: 'DOCX',
        page: 'Fragmento de texto',
        excerpt: 'Reporte de avance del proyecto. Se documentan las actividades realizadas y los entregables pendientes de revisión.'
    },
    {
        name: 'Acta de reunión.pdf',
        library: 'Administración',
        record: 'AD-2026-003',
        type: 'PDF',
        page: 'Página 1',
        excerpt: 'Los participantes registran el acuerdo de revisar la documentación y preparar el siguiente informe de actividades.'
    }
];

function normalizeText(value) {
    return value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
}

function createElement(tag, className, text) {
    const element = document.createElement(tag);
    element.className = className;

    if (text !== undefined) {
        element.textContent = text;
    }

    return element;
}

function appendHighlightedText(element, value, query) {
    const index = query ? normalizeText(value).indexOf(normalizeText(query)) : -1;

    if (index === -1) {
        element.textContent = value;
        return;
    }

    // Construir nodos mantiene los términos del visitante fuera de innerHTML.
    element.append(document.createTextNode(value.slice(0, index)));
    element.append(createElement('mark', '', value.slice(index, index + query.length)));
    element.append(document.createTextNode(value.slice(index + query.length)));
}

function showDocument(documentData) {
    const dialog = document.querySelector('#document-dialog');
    document.querySelector('#document-title').textContent = documentData.name;
    document.querySelector('#document-meta').textContent =
        `${documentData.library} · ${documentData.record} · ${documentData.type}`;
    document.querySelector('#document-excerpt').textContent = documentData.excerpt;
    dialog.showModal();
}

function createSearchResult(documentData, query) {
    const article = createElement('article', 'result-card');
    const heading = createElement('div', 'result-card-heading');
    const fileIcon = document.querySelector('.mock-row .file-icon').cloneNode(true);
    const description = createElement('div', '');
    const button = createElement('button', '', documentData.name);
    button.type = 'button';
    button.setAttribute('aria-label', `Ver ficha de ejemplo: ${documentData.name}`);
    button.addEventListener('click', () => showDocument(documentData));

    description.append(button);
    description.append(createElement(
        'p',
        'result-card-meta',
        `${documentData.library} · ${documentData.record} · ${documentData.type}`
    ));
    heading.append(fileIcon, description);

    const excerpt = createElement('p', '');
    appendHighlightedText(excerpt, `${documentData.page} — ${documentData.excerpt}`, query);
    article.append(heading, excerpt);

    return article;
}

function initializeSearchDemo() {
    const searchForm = document.querySelector('#demo-search');
    const queryInput = document.querySelector('#demo-query');
    const librarySelect = document.querySelector('#demo-library');
    const resultContainer = document.querySelector('#search-results');
    const resultCount = document.querySelector('#result-count');

    function renderResults() {
        const query = queryInput.value.trim();
        const normalizedQuery = normalizeText(query);
        const library = librarySelect.value;
        const documents = sampleDocuments.filter((item) => {
            const text = normalizeText(`${item.name} ${item.record} ${item.excerpt}`);
            return text.includes(normalizedQuery) && (library === 'all' || item.library === library);
        });

        resultContainer.replaceChildren();
        resultCount.textContent = documents.length === 1
            ? '1 documento de ejemplo encontrado'
            : `${documents.length} documentos de ejemplo encontrados`;

        if (documents.length === 0) {
            resultContainer.append(createElement(
                'p',
                'empty-results',
                'No hay coincidencias en esta muestra. Prueba con «contrato», «avance» o «acuerdo».'
            ));
        }

        documents.forEach((item) => {
            resultContainer.append(createSearchResult(item, query));
        });
    }

    searchForm.addEventListener('submit', (event) => {
        event.preventDefault();
        renderResults();
    });
    queryInput.addEventListener('input', renderResults);
    librarySelect.addEventListener('change', renderResults);
    document.querySelector('#close-document').addEventListener('click', () => {
        document.querySelector('#document-dialog').close();
    });

    renderResults();
}

function initializeContactForm() {
    const form = document.querySelector('#contact-form');
    const status = document.querySelector('#form-status');
    const submit = form.querySelector('[type="submit"]');
    const label = submit.querySelector('span');
    const fields = ['nombre', 'empresa', 'correo', 'telefono', 'mensaje'];
    let submitting = false;

    function clearErrors() {
        fields.forEach((name) => {
            document.querySelector(`#error-${name}`).textContent = '';
            form.elements[name].removeAttribute('aria-invalid');
        });
    }

    function showErrors(errors) {
        fields.forEach((name) => {
            if (typeof errors[name] === 'string') {
                document.querySelector(`#error-${name}`).textContent = errors[name];
                form.elements[name].setAttribute('aria-invalid', 'true');
            }
        });
    }

    form.addEventListener('input', (event) => {
        event.target.setCustomValidity?.('');
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (submitting) {
            return;
        }

        clearErrors();

        for (const name of ['nombre', 'correo', 'mensaje']) {
            const field = form.elements[name];
            field.setCustomValidity(field.value.trim() ? '' : 'Completa este campo obligatorio.');
        }

        if (!form.reportValidity()) {
            return;
        }

        submitting = true;
        submit.disabled = true;
        form.setAttribute('aria-busy', 'true');
        label.textContent = 'Enviando solicitud…';
        status.classList.remove('is-success');
        status.textContent = 'Estamos enviando tu solicitud.';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                headers: { Accept: 'application/json' }
            });
            const result = await response.json();

            if (typeof result.message !== 'string') {
                throw new Error('Respuesta inesperada');
            }

            status.textContent = result.message;
            showErrors(result.errors || {});

            if (response.ok && result.ok === true) {
                form.reset();
                status.classList.add('is-success');
            }

            if (typeof result.csrf === 'string') {
                form.elements.csrf.value = result.csrf;
            }

            const invalidField = form.querySelector('[aria-invalid="true"]');
            (invalidField || status).focus();
        } catch {
            status.textContent = 'No pudimos confirmar el envío. Revisa tu conexión e inténtalo más tarde.';
            status.focus();
        } finally {
            submitting = false;
            submit.disabled = false;
            form.removeAttribute('aria-busy');
            label.textContent = 'Solicitar información';
        }
    });
}

initializeNavigation();
initializeTabs();
initializeSearchDemo();
initializeContactForm();
