// Genera la tarjeta Open Graph desde HTML propio, sin alterar el logotipo.
const { chromium } = require('playwright');
const path = require('node:path');
const { pathToFileURL } = require('node:url');

(async () => {
    const browser = await chromium.launch({
        executablePath:
            process.env.CHROME_PATH ||
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        headless: true
    });

    try {
        const page = await browser.newPage({ viewport: { width: 1200, height: 630 } });
        await page.goto(pathToFileURL(path.join(__dirname, 'social-card.html')).href);
        await page.evaluate(async () => {
            await document.fonts.ready;
            await Promise.all(Array.from(document.images).map((image) => image.decode()));
        });
        await page.screenshot({
            path: path.join(__dirname, '../public/assets/img/social-card.png')
        });
        console.log('Tarjeta social creada: 1200 × 630.');
    } finally {
        await browser.close();
    }
})();
