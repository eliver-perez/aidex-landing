/* Pruebas de navegador. Las herramientas se instalan fuera del paquete público. */
const { chromium } = require('playwright');
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');

const base = process.env.TEST_URL || 'http://127.0.0.1:8088';
const artifacts = path.join(__dirname, 'artifacts');
fs.mkdirSync(artifacts, { recursive: true });

(async () => {
    const browser = await chromium.launch({
        executablePath:
            process.env.CHROME_PATH ||
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        headless: true
    });
    const context = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
    const page = await context.newPage();
    const errors = [];
    page.on('pageerror', (error) => errors.push(error.message));
    page.on('console', (message) => {
        if (message.type() === 'error' && !/(502|503)/.test(message.text())) {
            errors.push(message.text());
        }
    });

    try {
        const response = await page.goto(base);
        assert.equal(response.status(), 200);
        await page.evaluate(async () => {
            await document.fonts.ready;
            await Promise.all(
                Array.from(document.images).map((image) => {
                    image.loading = 'eager';
                    return image.decode();
                })
            );
        });
        await page.screenshot({
            animations: 'disabled',
            path: path.join(artifacts, 'desktop.png'),
            fullPage: true
        });
        assert.equal(await page.locator('h1').count(), 1);
        assert.equal(await page.locator('main').count(), 1);
        assert.equal(await page.locator('#form-status').isVisible(), false);
        assert.equal(
            await page
                .locator('#form-status')
                .evaluate((element) => element.getBoundingClientRect().height),
            0
        );
        assert.equal(await page.locator('.feature-card').count(), 6);
        assert.deepEqual(
            await page
                .locator('[role="tab"]')
                .allTextContents()
                .then((labels) => labels.map((text) => text.trim())),
            ['Búsqueda', 'Bibliotecas', 'Trazabilidad', 'Indicadores']
        );
        assert.deepEqual(
            await page
                .locator('#main-nav a')
                .allTextContents()
                .then((labels) => labels.map((text) => text.trim())),
            ['Por qué AIDEX', 'Capacidades', 'Explora AIDEX', 'Solicitar demostración']
        );
        assert.equal(await page.locator('.demo-caption').count(), 0);
        assert.equal(await page.locator('.step-number').count(), 0);
        assert.equal(
            await page
                .locator('img')
                .evaluateAll((images) =>
                    images.every((img) => img.complete && img.naturalWidth > 0)
                ),
            true
        );
        const brokenAnchors = await page
            .locator('a[href^="#"]')
            .evaluateAll((links) =>
                links
                    .filter((link) => !document.querySelector(link.getAttribute('href')))
                    .map((link) => link.href)
            );
        assert.deepEqual(brokenAnchors, []);

        await page.locator('#demo-query').fill('avance');
        assert.equal(await page.locator('.result-card').count(), 1);
        await page.locator('.result-card button').click();
        assert.equal(await page.locator('#document-dialog').isVisible(), true);
        await page.keyboard.press('Escape');
        assert.equal(await page.locator('#document-dialog').isVisible(), false);
        await page.locator('#demo-library').selectOption('Administración');
        assert.equal(await page.locator('.result-card').count(), 0);
        await page.locator('#demo-library').selectOption('all');
        await page.locator('#demo-query').fill('contrato');
        assert.equal(await page.locator('.result-card').count(), 2);
        await page.locator('#demo-query').fill('<img src=x onerror=alert(1)>');
        assert.equal(await page.locator('#search-results img').count(), 0);
        await page.locator('#demo-query').fill('contrato');

        await page.locator('#tab-organize').click();
        assert.equal(await page.locator('#panel-organize').isVisible(), true);
        await page.keyboard.press('ArrowRight');
        assert.equal(await page.locator('#panel-trace').isVisible(), true);
        await page.keyboard.press('End');
        assert.equal(await page.locator('#panel-indicators').isVisible(), true);
        await page.locator('.dashboard-data summary').click();
        assert.equal(await page.locator('.dashboard-tables').isVisible(), true);
        assert.equal(await page.locator('.dashboard-tables table').count(), 2);
        await page.locator('.dashboard-data summary').click();
        await page.locator('.demo-tabs').scrollIntoViewIfNeeded();
        await page
            .locator('#conoce-aidex')
            .screenshot({
                animations: 'disabled',
                path: path.join(artifacts, 'indicators-desktop.png')
            });
        await page.locator('#tab-indicators').focus();
        await page.keyboard.press('Home');
        assert.equal(await page.locator('#panel-search').isVisible(), true);

        if (process.env.SKIP_CONTACT !== 'true') {
            await page.locator('#nombre').fill('Prueba de navegador');
            await page.locator('#correo').fill('prueba@example.test');
            await page.locator('#telefono').fill('+52 (81) 1234-5678');
            assert.equal(
                await page.locator('#telefono').evaluate((field) => field.checkValidity()),
                true
            );
            await page.locator('#mensaje').fill('Solicitud de prueba local sin entrega externa.');
            await page.locator('#contact-form [type="submit"]').click();
            await page.waitForFunction(() =>
                document.querySelector('#form-status').textContent.includes('no está configurado')
            );
            assert.equal(
                await page
                    .locator('#form-status')
                    .evaluate((element) => element.dataset.state === 'success'),
                false
            );
            assert.equal(await page.locator('#nombre').inputValue(), 'Prueba de navegador');
            assert.equal(await page.locator('#form-status').isVisible(), true);
            assert.equal(await page.locator('#form-status').getAttribute('data-state'), 'warning');

            // Estados visuales aislados; la prueba SMTP real está en integration.py.
            for (const [code, state, message] of [
                [502, 'error', 'Fallo de prueba controlado.'],
                [200, 'success', 'Aceptación de prueba controlada.']
            ]) {
                await page.route('**/contact.php', (route) =>
                    route.fulfill({
                        status: code,
                        contentType: 'application/json',
                        body: JSON.stringify({ ok: code === 200, message, errors: {} })
                    })
                );
                await page.locator('#contact-form [type="submit"]').click();
                await page.waitForFunction(
                    (expected) => document.querySelector('#form-status').textContent === expected,
                    message
                );
                assert.equal(await page.locator('#form-status').getAttribute('data-state'), state);
                assert.equal(await page.locator('#form-status').isVisible(), true);
                await page.unroute('**/contact.php');
            }
        }

        const AxeBuilder = require('@axe-core/playwright').default;
        const accessibility = await new AxeBuilder({ page })
            .withTags(['wcag2a', 'wcag2aa', 'wcag21aa'])
            .analyze();
        fs.writeFileSync(
            path.join(artifacts, 'accessibility.json'),
            JSON.stringify(accessibility.violations, null, 4)
        );
        console.log(
            'Accesibilidad:',
            accessibility.violations.map((item) => ({ id: item.id, nodes: item.nodes.length }))
        );
        for (const tab of ['#tab-organize', '#tab-trace', '#tab-indicators']) {
            await page.locator(tab).click();
            await page
                .locator('.demo-panel:not([hidden])')
                .evaluate((panel) =>
                    Promise.all(panel.getAnimations().map((animation) => animation.finished))
                );
            const panelAudit = await new AxeBuilder({ page })
                .withTags(['wcag2a', 'wcag2aa', 'wcag21aa'])
                .analyze();
            accessibility.violations.push(...panelAudit.violations);
        }
        await page.locator('#tab-search').click();

        for (const width of [320, 375, 390, 768, 1024, 1440]) {
            await page.setViewportSize({ width, height: 900 });
            for (const tab of ['search', 'organize', 'trace', 'indicators']) {
                await page.locator(`#tab-${tab}`).click();
                assert.equal(
                    await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth),
                    true,
                    `Desbordamiento a ${width}px en ${tab}`
                );
            }
            const cardLayout = await page.locator('.feature-grid').evaluate((grid) => ({
                columns: getComputedStyle(grid).gridTemplateColumns.split(' ').length,
                aligned: Array.from(grid.querySelectorAll('.feature-top')).every((row) => {
                    const icon = row.querySelector('.feature-icon').getBoundingClientRect();
                    const title = row.querySelector('h3').getBoundingClientRect();
                    return Math.abs(icon.y + icon.height / 2 - title.y - title.height / 2) < 1;
                })
            }));
            assert.equal(cardLayout.columns, width > 850 ? 3 : width > 560 ? 2 : 1);
            assert.equal(cardLayout.aligned, true, `Alineación de tarjetas a ${width}px`);
            if (width === 768) {
                await page
                    .locator('#conoce-aidex')
                    .screenshot({
                        animations: 'disabled',
                        path: path.join(artifacts, 'indicators-tablet.png')
                    });
            }
        }

        await page.setViewportSize({ width: 390, height: 844 });
        await page.goto(base);
        await page.evaluate(() => document.fonts.ready);
        await page.locator('.menu-toggle').click();
        assert.equal(await page.locator('.menu-toggle').getAttribute('aria-expanded'), 'true');
        await page.locator('#main-nav a[href="#conoce-aidex"]').click();
        assert.equal(await page.locator('.menu-toggle').getAttribute('aria-expanded'), 'false');
        await page.screenshot({
            animations: 'disabled',
            path: path.join(artifacts, 'mobile.png'),
            fullPage: true
        });
        await page.locator('#tab-indicators').click();
        await page
            .locator('#conoce-aidex')
            .screenshot({
                animations: 'disabled',
                path: path.join(artifacts, 'indicators-mobile.png')
            });
        await page.locator('.dashboard-data summary').click();
        await page.evaluate(() => {
            document.documentElement.style.scrollBehavior = 'auto';
            window.scrollTo(0, 0);
        });
        await page.screenshot({
            animations: 'disabled',
            path: path.join(artifacts, 'mobile-hero.png')
        });
        const mobileAudit = await new AxeBuilder({ page })
            .withTags(['wcag2a', 'wcag2aa', 'wcag21aa'])
            .analyze();
        accessibility.violations.push(...mobileAudit.violations);
        fs.writeFileSync(
            path.join(artifacts, 'accessibility.json'),
            JSON.stringify(accessibility.violations, null, 4)
        );
        await page.emulateMedia({ reducedMotion: 'reduce' });
        assert.equal(
            await page.evaluate(() => getComputedStyle(document.documentElement).scrollBehavior),
            'auto'
        );

        const noScript = await browser.newPage({ javaScriptEnabled: false });
        await noScript.goto(base);
        assert.equal(await noScript.locator('#main-nav').isVisible(), true);
        assert.equal(await noScript.locator('#contact-form').getAttribute('method'), 'post');
        assert.equal(await noScript.locator('#form-status').isVisible(), false);
        await noScript.close();
        assert.deepEqual(errors, []);
        assert.equal(
            accessibility.violations.length,
            0,
            'Hay hallazgos de accesibilidad pendientes.'
        );
        console.log(
            'OK: navegación, imágenes, anclas, búsqueda, pestañas, ficha, formulario, móvil, movimiento reducido y JavaScript.'
        );
    } finally {
        await browser.close();
    }
})().catch((error) => {
    console.error(error);
    process.exitCode = 1;
});
