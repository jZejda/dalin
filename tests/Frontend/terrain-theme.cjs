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
        const toggle = '[data-terrain-theme-toggle]';
        const setTheme = async (target, theme) => {
            if (await target.evaluate(() => document.documentElement.classList.contains('dark')) !== (theme === 'dark')) {
                await target.click(toggle);
            }
        };
        const url = process.env.TERRAIN_TEST_URL || 'http://localhost/design-system';
        assert.equal((await page.goto(url)).status(), 200);
        assert.equal(await isDark(), true);
        assert.equal(await bodyColor(), 'rgb(23, 27, 29)');
        assert.equal(await page.locator(toggle).getAttribute('aria-label'), 'Přepnout na světlý režim');
        assert.equal(await page.evaluate(() => localStorage.getItem('color-theme')), null); // Follows the OS until clicked.

        for (const width of [390, 1440]) {
            await page.setViewportSize({ width, height: 1000 });
            for (const theme of ['light', 'dark']) {
                await setTheme(page, theme);
                assert.equal(await page.evaluate(() => localStorage.getItem('color-theme')), theme);
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
        await page.click(toggle);
        assert.equal(await isDark(), false);
        assert.equal(await page.locator(toggle).getAttribute('aria-label'), 'Přepnout na tmavý režim');
        await page.emulateMedia({ colorScheme: 'dark' });
        assert.equal(await isDark(), false); // The toggled choice stays pinned.

        const other = await context.newPage();
        await other.goto(url);
        await other.click(toggle);
        await page.waitForFunction(() => document.documentElement.classList.contains('dark'));

        const blocked = await browser.newContext({ colorScheme: 'dark' });
        await blocked.addInitScript(() => {
            Object.defineProperty(window, 'localStorage', { get() { throw new Error('Storage unavailable'); } });
        });
        const blockedPage = await blocked.newPage();
        await blockedPage.goto(url);
        await blockedPage.click(toggle);
        assert.equal(await blockedPage.evaluate(() => document.documentElement.classList.contains('dark')), false);
        assert.deepEqual(errors, []);
        console.log('Terrain: mobile/desktop light/dark toggle, OS default, persistence, cross-tab sync and unavailable storage passed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exit(1); });
