import AxeBuilder from '@axe-core/playwright';
import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const routes = [
  { path: '/product/kapelnik/', marker: '[data-rm-product-detail]', title: 'Капельник' },
  {
    path: '/services/laser-cutting/',
    marker: '[data-rm-service-detail]',
    title: 'Лазерная резка металла',
  },
  { path: '/company/partners/', marker: '[data-rm-partners-page]', title: 'Партнёры' },
] as const;

type BrowserDiagnostic = {
  kind: 'console.error' | 'pageerror';
  message: string;
  location?: ReturnType<ConsoleMessage['location']>;
};

function collectBrowserDiagnostics(page: Page): BrowserDiagnostic[] {
  const diagnostics: BrowserDiagnostic[] = [];
  page.on('console', (message) => {
    if (message.type() !== 'error') return;
    diagnostics.push({ kind: 'console.error', message: message.text(), location: message.location() });
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

test.describe('P0 redesigned derived pages', () => {
  for (const route of routes) {
    test(`${route.path} uses the new Paper system without legacy body`, async ({ page, request }) => {
      const response = await request.get(route.path, { maxRedirects: 5 });
      expect(response.status()).toBeLessThan(400);

      await page.goto(route.path, { waitUntil: 'domcontentloaded' });
      await expectHealthyDocument(page);
      await expect(page.locator(route.marker)).toBeVisible();
      await expect(page.locator(`${route.marker} h1`)).toHaveText(route.title);
      await expect(
        page.locator('.main-catalog-wrapper, .main-section-wrapper, .alert-warning'),
      ).toHaveCount(0);
      await expect(page.locator('.ajax_basket > .fixed_wrapper')).toBeHidden();
      expect(await page.locator('[src*="paper.design"], [style*="paper.design"]').count()).toBe(0);
    });
  }

  test('product and service detail templates keep local imagery and safe estimate dialogs', async ({ page }) => {
    for (const route of routes.slice(0, 2)) {
      const postRequests: string[] = [];
      page.on('request', (request) => {
        if (request.method() === 'POST') postRequests.push(request.url());
      });

      await page.goto(route.path, { waitUntil: 'domcontentloaded' });
      const image = page.locator('.rm-detail-hero__visual img');
      await expect(image).toHaveAttribute('src', /^\/(?:local\/templates\/aspro-allcorp2\/images\/ralmaster|upload)\//);
      await expect
        .poll(() =>
          image.evaluate((node) => {
            const element = node as HTMLImageElement;
            return element.complete && element.naturalWidth > 0 && element.naturalHeight > 0;
          }),
        )
        .toBe(true);

      await page.locator('.rm-detail-hero [data-rm-discuss-open]').click();
      const dialog = page.locator('[data-rm-discuss-dialog]');
      await expect(dialog).toBeVisible();
      await expect(dialog.locator('form')).toBeVisible();
      expect(postRequests).toEqual([]);
      await dialog.locator('[data-rm-discuss-close]').click();
    }
  });

  test('partners remain dynamic and all available logos load', async ({ page }) => {
    await page.goto('/company/partners/', { waitUntil: 'domcontentloaded' });
    const root = page.locator('[data-rm-partners-page]');
    await expect(root).toHaveAttribute('data-rm-partner-count', '7');
    await expect(root.locator('.rm-partner-card')).toHaveCount(7);
    expect((await root.locator('.rm-partner-card h3').allTextContents()).join(' ')).not.toContain(
      '&quot;',
    );

    const images = root.locator('.rm-partner-card__logo img');
    const count = await images.count();
    expect(count).toBeGreaterThan(0);
    for (let index = 0; index < count; index += 1) {
      const image = images.nth(index);
      await image.scrollIntoViewIfNeeded();
      await expect
        .poll(() =>
          image.evaluate((node) => {
            const element = node as HTMLImageElement;
            return element.complete && element.naturalWidth > 0 && element.naturalHeight > 0;
          }),
        )
        .toBe(true);
    }
  });

  test('desktop routes have no overflow, browser errors, or critical axe violations', async ({
    page,
  }, testInfo) => {
    for (const route of routes) {
      const diagnostics = collectBrowserDiagnostics(page);
      await page.goto(route.path, { waitUntil: 'domcontentloaded' });
      await expect(page.locator(route.marker)).toBeVisible();
      await page.waitForTimeout(700);
      await expectNoHorizontalOverflow(page);

      const axe = await new AxeBuilder({ page })
        .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
        .analyze();
      expect(axe.violations.filter((violation) => violation.impact === 'critical')).toEqual([]);
      await attachDiagnostics(testInfo, diagnostics);
      expect(diagnostics).toEqual([]);
    }
  });

  test('P0 @mobile all derived routes remain usable without overflow or browser errors', async ({
    page,
  }, testInfo) => {
    for (const route of routes) {
      const diagnostics = collectBrowserDiagnostics(page);
      await page.goto(route.path, { waitUntil: 'domcontentloaded' });
      await expect(page.locator(route.marker)).toBeVisible();
      await expectNoHorizontalOverflow(page);
      await page.waitForTimeout(500);
      await attachDiagnostics(testInfo, diagnostics);
      expect(diagnostics).toEqual([]);
    }
  });
});

