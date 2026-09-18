// Run: node tests/Frontend/terrain-homepage.cjs (Sail web server required).
const { chromium } = require('playwright');
const assert = require('node:assert/strict');

(async () => {
    const browser = await chromium.launch({ headless: true });
    try {
        const page = await browser.newPage({ colorScheme: 'light' });
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        for (const width of [390, 1440]) {
            await page.setViewportSize({ width, height: 1000 });
            assert.equal((await page.goto(process.env.TERRAIN_HOME_URL || 'http://localhost/')).status(), 200);
            await page.waitForFunction(() => window.Livewire && window.Alpine);
            if (width === 390) {
                const menu = page.getByRole('button', { name: 'Menu' });
                assert.equal(await menu.getAttribute('aria-expanded'), 'false');
                assert.equal(await page.locator('#terrain-navigation').isVisible(), false);
                await menu.click();
                assert.equal(await menu.getAttribute('aria-expanded'), 'true');
                assert.equal(await page.locator('#terrain-navigation').isVisible(), true);
            }
            for (const theme of ['light', 'dark']) {
                await page.selectOption('[data-terrain-theme]', theme);
                assert.equal(await page.locator('body').evaluate(el => getComputedStyle(el).backgroundColor), theme === 'dark' ? 'rgb(23, 27, 29)' : 'rgb(250, 250, 248)');
                assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false);
                for (const date of await page.locator('#kalendar time').all()) {
                    const tile = await date.evaluate(el => ({ width: el.getBoundingClientRect().width, height: el.getBoundingClientRect().height, bg: getComputedStyle(el).backgroundColor }));
                    assert.equal(tile.width, 72);
                    assert.equal(tile.height, tile.width);
                    assert.equal(tile.bg, 'rgb(255, 211, 41)');
                    assert.match(await date.locator('[aria-hidden]').first().textContent(), /^\d{2}\.$/);
                }
                await page.screenshot({ path: `/tmp/dalin-homepage-${theme}-${width}.png`, fullPage: true });
            }
            if (width === 390) {
                await page.keyboard.press('Escape');
                assert.equal(await page.locator('#terrain-navigation').isVisible(), false);
            }
            assert.equal(await page.locator('#map.leaflet-container').count(), 1);
            assert.equal(await page.locator('#map .leaflet-control-zoom-in').count(), 1);
            const zoom = await page.evaluate(() => map.getZoom());
            await page.locator('#map .leaflet-control-zoom-in').click();
            await page.waitForFunction(previous => map.getZoom() > previous, zoom);
        }
        assert.deepEqual(errors, []);
        console.log('Terrain homepage: desktop/mobile light/dark, menu, Escape, map initialization and zoom passed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exit(1); });
