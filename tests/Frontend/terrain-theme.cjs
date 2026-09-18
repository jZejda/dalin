// Run: node tests/Frontend/terrain-theme.cjs (Sail web server required).
const { chromium } = require('playwright');
const assert = require('node:assert/strict');

(async () => {
    const browser = await chromium.launch({ headless: true });
    try {
        const context = await browser.newContext({ colorScheme: 'dark' });
        const page = await context.newPage();
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        const isDark = () => page.evaluate(() => document.documentElement.classList.contains('dark'));
        const bodyColor = () => page.locator('body').evaluate(el => getComputedStyle(el).backgroundColor);
        const url = process.env.TERRAIN_TEST_URL || 'http://localhost/design-system';
        assert.equal((await page.goto(url)).status(), 200);
        assert.equal(await isDark(), true);
        assert.equal(await bodyColor(), 'rgb(23, 27, 29)');
        assert.equal(await page.locator('[data-terrain-theme]').inputValue(), 'system');

        for (const width of [390, 1440]) {
            await page.setViewportSize({ width, height: 1000 });
            for (const theme of ['light', 'dark']) {
                await page.selectOption('[data-terrain-theme]', theme);
                assert.equal(await isDark(), theme === 'dark');
                assert.equal(await bodyColor(), theme === 'dark' ? 'rgb(23, 27, 29)' : 'rgb(250, 250, 248)');
                assert.equal(await page.locator('.terrain-button-primary').first().evaluate(el => getComputedStyle(el).color), 'rgb(32, 36, 38)');
                assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false);
                await page.screenshot({ path: `/tmp/dalin-terrain-${theme}-${width}.png`, fullPage: true });
            }
        }
        await page.reload();
        assert.equal(await isDark(), true);
        await page.emulateMedia({ colorScheme: 'light' });
        assert.equal(await isDark(), true); // Explicit preference beats OS theme.
        await page.selectOption('[data-terrain-theme]', 'system');
        assert.equal(await isDark(), false);
        assert.equal(await page.evaluate(() => localStorage.getItem('color-theme')), null);
        await page.emulateMedia({ colorScheme: 'dark' });
        await page.waitForFunction(() => document.documentElement.classList.contains('dark'));

        const other = await context.newPage();
        await other.goto(url);
        await other.selectOption('[data-terrain-theme]', 'light');
        await page.waitForFunction(() => !document.documentElement.classList.contains('dark'));

        const blocked = await browser.newContext({ colorScheme: 'dark' });
        await blocked.addInitScript(() => {
            Object.defineProperty(window, 'localStorage', { get() { throw new Error('Storage unavailable'); } });
        });
        const blockedPage = await blocked.newPage();
        await blockedPage.goto(url);
        await blockedPage.selectOption('[data-terrain-theme]', 'light');
        assert.equal(await blockedPage.evaluate(() => document.documentElement.classList.contains('dark')), false);
        assert.deepEqual(errors, []);
        console.log('Terrain: mobile/desktop light/dark, system changes, persistence, cross-tab sync and unavailable storage passed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exit(1); });
