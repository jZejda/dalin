// Run: node tests/Frontend/terrain-event.cjs (Sail web server required).
const { chromium } = require('playwright');
const assert = require('node:assert/strict');

(async () => {
    const browser = await chromium.launch({ headless: true });
    try {
        const page = await browser.newPage({ colorScheme: 'light' });
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        const url = process.env.TERRAIN_EVENT_URL || 'http://localhost/akce/322';
        let title;
        for (const width of [390, 1440]) {
            await page.setViewportSize({ width, height: 1000 });
            assert.equal((await page.goto(url)).status(), 200);
            await page.waitForFunction(() => window.Livewire && window.Alpine);
            title = await page.locator('h1').textContent();
            assert.ok(title.trim());
            for (const theme of ['light', 'dark']) {
                if (width === 390) await page.getByRole('button', { name: 'Menu' }).click();
                await page.selectOption('[data-terrain-theme]', theme);
                if (width === 390) await page.keyboard.press('Escape');
                assert.equal(await page.locator('body').evaluate(el => getComputedStyle(el).backgroundColor), theme === 'dark' ? 'rgb(23, 27, 29)' : 'rgb(250, 250, 248)');
                assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false);
                const secondary = page.locator('.terrain-button-secondary').first();
                if (await secondary.count()) {
                    await page.waitForFunction(({ theme }) => {
                        const el = document.querySelector('.terrain-button-secondary');
                        return getComputedStyle(el).color === (theme === 'dark' ? 'rgb(243, 244, 239)' : 'rgb(32, 36, 38)');
                    }, { theme });
                    const colors = await secondary.evaluate(el => ({ fg: getComputedStyle(el).color, bg: getComputedStyle(el).backgroundColor }));
                    assert.equal(colors.fg, theme === 'dark' ? 'rgb(243, 244, 239)' : 'rgb(32, 36, 38)');
                    assert.equal(colors.bg, theme === 'dark' ? 'rgb(32, 38, 40)' : 'rgb(255, 255, 255)');
                }
                const entry = page.locator('[data-event-entry]');
                if (await entry.count()) {
                    assert.ok((await entry.getAttribute('href')).includes('/admin/sport-events/'));
                    assert.ok((await entry.getAttribute('href')).endsWith('/entry'));
                    assert.equal(await entry.evaluate(el => getComputedStyle(el).color), 'rgb(32, 36, 38)');
                }
                await page.screenshot({ path: `/tmp/dalin-event-${theme}-${width}.png`, fullPage: true });
            }
            if (await page.locator('#map').count()) {
                assert.equal(await page.locator('#map.leaflet-container').count(), 1);
                const zoom = await page.evaluate(() => map.getZoom());
                await page.locator('#map .leaflet-control-zoom-in').click();
                await page.waitForFunction(previous => map.getZoom() > previous, zoom);
                assert.ok(await page.locator('#map .leaflet-marker-icon').count());
            }
            const links = page.locator('#event-documents a');
            for (const link of await links.all()) {
                assert.equal(await link.getAttribute('target'), '_blank');
                assert.equal(await link.getAttribute('rel'), 'noopener noreferrer');
            }
        }
        assert.deepEqual(errors, []);
        console.log(`Terrain event: ${title.trim()}, mobile/desktop light/dark, entry links, documents and map passed.`);
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exit(1); });
