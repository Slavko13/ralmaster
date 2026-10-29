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
      diagnostics.push({
        kind: 'console.error',
        message: message.text(),
        location: message.location(),
      });
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

  expect(
    geometry.scrollWidth,
    `Unexpected horizontal overflow: ${geometry.scrollWidth}px > ${geometry.clientWidth}px`,
  ).toBeLessThanOrEqual(geometry.clientWidth + 1);
}

async function attachDiagnostics(
  testInfo: TestInfo,
  diagnostics: BrowserDiagnostic[],
): Promise<void> {
  await testInfo.attach('browser-diagnostics', {
    body: JSON.stringify(diagnostics, null, 2),
    contentType: 'application/json',
  });
}

test.describe('Redesigned metalworking root', () => {
  test('renders the Paper sections and dynamic service routes', async ({ page, request }) => {
    await page.goto('/services/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);

    const root = page.locator('[data-rm-metalworking]');
    await expect(root).toBeVisible();
    await expect(root.locator('h1')).toHaveText('Металлообработка на заказ');

    const sections = await root
      .locator(':scope > [data-rm-section]')
      .evaluateAll((nodes) => nodes.map((node) => node.getAttribute('data-rm-section')));
    expect(sections).toEqual(['hero', 'services', 'operations', 'examples', 'estimate', 'photo']);

    const expectedCodes = ['laser-cutting', 'metal-bending', 'laser-welding'];
    const cards = root.locator('[data-rm-service-code]');
    await expect(cards).toHaveCount(expectedCodes.length);

    for (const code of expectedCodes) {
      const card = root.locator(`[data-rm-service-code="${code}"]`);
      await expect(card).toBeVisible();
      const href = await card.locator('h3 a').getAttribute('href');
      expect(href, `Missing dynamic route for ${code}`).toBeTruthy();
      expect(href).toMatch(/^\/services\//);

      const response = await request.get(href!, { maxRedirects: 5 });
      expect(response.status(), `Broken service route: ${href}`).toBeLessThan(400);
    }
  });

  test('uses local assets and every rendered image loads', async ({ page }) => {
    await page.goto('/services/', { waitUntil: 'networkidle' });

    const images = page.locator('[data-rm-metalworking] img');
    const count = await images.count();
    expect(count).toBeGreaterThanOrEqual(8);

    for (let index = 0; index < count; index += 1) {
      const image = images.nth(index);
      const source = await image.getAttribute('src');
      expect(source).toMatch(/^\/local\/templates\/aspro-allcorp2\/images\/ralmaster\//);
      expect(source).not.toMatch(/paper\.design|bavrasem\.beget\.tech/i);

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

  test('dedicated estimate form validates safely without a POST', async ({ page }) => {
    const postRequests: string[] = [];
    page.on('request', (request) => {
      if (request.method() === 'POST') postRequests.push(request.url());
    });

    await page.goto('/services/', { waitUntil: 'domcontentloaded' });
    const formRoot = page.locator(
      '[data-rm-estimate-form][data-rm-estimate-context="metalworking"]',
    );
    await expect(formRoot).toBeVisible();

    await expect(formRoot.locator('[data-SID="CONTACT"] input')).toHaveCount(1);
    await expect(formRoot.locator('[data-SID="TASK"] textarea')).toHaveCount(1);
    await expect(formRoot.locator('[data-SID="FILE"] input[type="file"]')).toHaveCount(1);
    await expect(formRoot.locator('input[data-sid="CONTEXT"]')).toHaveValue('metalworking');

    await formRoot.locator('input[type="submit"]').click();
    await expect(formRoot.locator('.rm-estimate-error')).toHaveCount(2);
    expect(postRequests).toEqual([]);
  });

  test('desktop geometry follows the 1440 Paper frame without browser errors', async ({
    page,
  }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);

    await page.goto('/services/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await page.waitForTimeout(2_000);

    const geometry = await page.locator('[data-rm-metalworking]').evaluate((root) => {
      const sections = Array.from(root.querySelectorAll<HTMLElement>(':scope > [data-rm-section]'));
      return {
        rootTop: Math.round((root as HTMLElement).getBoundingClientRect().top + window.scrollY),
        width: Math.round((root as HTMLElement).getBoundingClientRect().width),
        documentHeight: document.documentElement.scrollHeight,
        footerTop: Math.round(
          (document.querySelector('.rm-footer')?.getBoundingClientRect().top ?? 0) + window.scrollY,
        ),
        sections: sections.map((section) => ({
          name: section.dataset.rmSection,
          height: Math.round(section.getBoundingClientRect().height),
        })),
      };
    });

    expect(geometry.width).toBe(1440);
    expect(geometry.rootTop).toBe(106);
    expect(geometry.footerTop).toBe(5311);
    expect(geometry.documentHeight).toBe(5741);
    expect(geometry.sections).toEqual([
      { name: 'hero', height: 302 },
      { name: 'services', height: 1760 },
      { name: 'operations', height: 862 },
      { name: 'examples', height: 888 },
      { name: 'estimate', height: 850 },
      { name: 'photo', height: 543 },
    ]);

    await expect(page.locator('#rm-metal-estimate form')).toBeVisible();
    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });

  test('has no critical axe violations', async ({ page }, testInfo) => {
    await page.goto('/services/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);

    const results = await new AxeBuilder({ page })
      .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
      .analyze();

    await testInfo.attach('axe-results', {
      body: JSON.stringify(results.violations, null, 2),
      contentType: 'application/json',
    });

    expect(results.violations.filter((violation) => violation.impact === 'critical')).toEqual([]);
  });

  test('P0 @mobile mobile layout is usable and has no overflow or browser errors', async ({
    page,
  }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);

    await page.goto('/services/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await expect(page.locator('[data-rm-metalworking]')).toBeVisible();
    await expect(page.locator('[data-rm-service-code]')).toHaveCount(3);
    await page.waitForTimeout(2_000);

    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });
});
