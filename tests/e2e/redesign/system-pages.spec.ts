import AxeBuilder from '@axe-core/playwright';
import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

type BrowserDiagnostic = {
  kind: 'console.error' | 'pageerror';
  message: string;
  location?: ReturnType<ConsoleMessage['location']>;
};

function collectBrowserDiagnostics(page: Page): BrowserDiagnostic[] {
  const diagnostics: BrowserDiagnostic[] = [];
  page.on('console', (message) => {
    if (message.type() === 'error') {
      diagnostics.push({ kind: 'console.error', message: message.text(), location: message.location() });
    }
  });
  page.on('pageerror', (error) => {
    diagnostics.push({ kind: 'pageerror', message: error.stack ?? error.message });
  });
  return diagnostics;
}

async function expectNoHorizontalOverflow(page: Page): Promise<void> {
  const geometry = await page.evaluate(() => ({
    clientWidth: document.documentElement.clientWidth,
    scrollWidth: document.documentElement.scrollWidth,
  }));
  expect(geometry.scrollWidth).toBeLessThanOrEqual(geometry.clientWidth + 1);
}

async function attachDiagnostics(testInfo: TestInfo, diagnostics: BrowserDiagnostic[]) {
  await testInfo.attach('browser-diagnostics', {
    body: JSON.stringify(diagnostics, null, 2),
    contentType: 'application/json',
  });
}

test.describe('P0 redesigned system pages', () => {
  test('search keeps the Bitrix query flow in the new shared UI', async ({ page }) => {
    await page.goto('/search/?q=покраска', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    const search = page.locator('[data-rm-system-page="search"]');
    await expect(search).toBeVisible();
    await expect(search.getByLabel('Поисковый запрос')).toHaveValue('покраска');
    await expect(search.getByRole('button', { name: 'Найти' })).toBeVisible();
    await expect(page.locator('.rm-search-result, .rm-system-note').first()).toBeVisible();
  });

  test('site map renders the live Bitrix menu tree', async ({ page }) => {
    await page.goto('/search/map.php', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    const map = page.locator('[data-rm-site-map]');
    await expect(map).toBeVisible();
    expect(await map.getByRole('link').count()).toBeGreaterThan(5);
    await expect(map.getByRole('link', { name: /Продукция/i }).first()).toBeVisible();
  });

  test('cart keeps the live Aspro basket component inside the new system shell', async ({ page }) => {
    await page.goto('/cart/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await expect(page.locator('[data-rm-system-page="cart"]')).toBeVisible();
    await expect(page.locator('.basket.default')).toBeVisible();
  });

  test('login keeps the Bitrix authentication fields and recovery action', async ({ page }) => {
    await page.goto('/login/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await expect(page.locator('[data-rm-auth-page]')).toBeVisible();
    await expect(page.getByLabel(/Логин/i)).toHaveAttribute('name', 'USER_LOGIN');
    await expect(page.getByLabel(/Пароль/i)).toHaveAttribute('name', 'USER_PASSWORD');
    await expect(page.getByRole('link', { name: /Забыли пароль/i })).toBeVisible();
  });

  test('unknown route returns the redesigned 404 with a real 404 status', async ({ page }) => {
    const response = await page.goto('/__codex_redesign_missing_page__/', {
      waitUntil: 'domcontentloaded',
    });
    expect(response).not.toBeNull();
    expect(response!.status()).toBe(404);
    await expectHealthyDocument(page);
    await expect(page.locator('[data-rm-not-found]')).toBeVisible();
    await expect(page.getByRole('link', { name: 'На главную', exact: true })).toHaveAttribute('href', '/');
  });

  test('legacy duplicate sections permanently redirect to current routes', async ({ request }) => {
    const redirects = [
      ['/products/', '/product/'],
      ['/news/', '/info/news/'],
      ['/landings/', '/info/articles/'],
    ] as const;

    for (const [source, target] of redirects) {
      const response = await request.get(source, { maxRedirects: 0 });
      expect(response.status()).toBe(301);
      expect(new URL(response.headers().location).pathname).toBe(target);
    }
  });

  test('representative system pages have no overflow, browser errors, or axe violations', async ({
    page,
  }, testInfo) => {
    for (const path of ['/search/?q=покраска', '/search/map.php', '/cart/', '/__codex_redesign_missing_page__/']) {
      const diagnostics = collectBrowserDiagnostics(page);
      await page.goto(path, { waitUntil: 'domcontentloaded' });
      await page.waitForTimeout(350);
      await expectNoHorizontalOverflow(page);
      const axe = await new AxeBuilder({ page })
        .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
        .analyze();
      expect(axe.violations).toEqual([]);
      const relevantDiagnostics = path.includes('__codex_redesign_missing_page__')
        ? diagnostics.filter((item) => !/status of 404 \(Not Found\)/i.test(item.message))
        : diagnostics;
      await attachDiagnostics(testInfo, relevantDiagnostics);
      expect(relevantDiagnostics).toEqual([]);
    }
  });

  test('P0 @mobile all system pages remain usable without horizontal overflow', async ({
    page,
  }, testInfo) => {
    const routes = [
      { path: '/search/?q=покраска', marker: '[data-rm-system-page="search"]' },
      { path: '/search/map.php', marker: '[data-rm-site-map]' },
      { path: '/cart/', marker: '[data-rm-system-page="cart"]' },
      { path: '/login/', marker: '[data-rm-auth-page]' },
      { path: '/__codex_redesign_missing_page__/', marker: '[data-rm-not-found]' },
    ] as const;

    for (const route of routes) {
      const diagnostics = collectBrowserDiagnostics(page);
      await page.goto(route.path, { waitUntil: 'domcontentloaded' });
      await expect(page.locator(route.marker)).toBeVisible();
      await expectNoHorizontalOverflow(page);
      const relevantDiagnostics = route.path.includes('__codex_redesign_missing_page__')
        ? diagnostics.filter((item) => !/status of 404 \(Not Found\)/i.test(item.message))
        : diagnostics;
      await attachDiagnostics(testInfo, relevantDiagnostics);
      expect(relevantDiagnostics).toEqual([]);
    }
  });
});
