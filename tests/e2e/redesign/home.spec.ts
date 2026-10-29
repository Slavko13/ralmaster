import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const expectedSections = [
  'hero',
  'work-model',
  'products',
  'projects',
  'partners',
  'production',
  'process',
  'faq',
  'order',
  'photo',
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

    diagnostics.push({
      kind: 'console.error',
      message: message.text(),
      location: message.location(),
    });
  });

  page.on('pageerror', (error) => {
    diagnostics.push({
      kind: 'pageerror',
      message: error.stack ?? error.message,
    });
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

test.describe('P0 redesigned home', () => {
  test('matches the Paper information architecture and exact hero copy', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);

    const home = page.locator('[data-rm-home]');
    await expect(home).toBeVisible();

    const heading = home.locator('[data-rm-section="hero"] h1');
    await expect(heading).toBeVisible();
    const normalizedHeading = (await heading.innerText()).replace(/\s+/g, ' ').trim();
    expect(normalizedHeading).toBe(
      'Изготовление металлических изделий и металлообработка',
    );

    const sections = await home
      .locator(':scope > [data-rm-section]')
      .evaluateAll((nodes) => nodes.map((node) => node.getAttribute('data-rm-section')));
    expect(sections).toEqual(expectedSections);
  });

  test('keeps card data dynamic and all redesigned links local', async ({ page, request }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    const home = page.locator('[data-rm-home]');

    await expect(home.locator('[data-rm-card="project"]').first()).toBeVisible();
    await expect(home.locator('[data-rm-card="partner"]').first()).toBeVisible();

    const hrefs = await home.locator('a[href]').evaluateAll((links) =>
      links
        .map((link) => link.getAttribute('href'))
        .filter((href): href is string => Boolean(href)),
    );

    expect(hrefs.length).toBeGreaterThan(0);
    expect(hrefs.join('\n')).not.toMatch(/bavrasem\.beget\.tech/i);

    const routeHrefs = [
      ...new Set(
        hrefs.filter(
          (href) =>
            href.startsWith('/') &&
            !href.startsWith('//') &&
            !href.startsWith('/ajax/') &&
            !href.startsWith('/bitrix/'),
        ),
      ),
    ];

    const representativeRoutes = ['/product/', '/services/', '/projects/'].map((prefix) => {
      const href = routeHrefs.find((candidate) => candidate.startsWith(prefix));
      expect(href, `Missing redesigned home link for ${prefix}`).toBeTruthy();
      return href!;
    });

    for (const href of representativeRoutes) {
      const response = await request.get(href, { maxRedirects: 5 });
      expect(response.status(), `Broken internal home link: ${href}`).toBeLessThan(500);
    }
  });

  test('loads every rendered image asset', async ({ page }) => {
    await page.goto('/', { waitUntil: 'networkidle' });
    const images = page.locator('[data-rm-home] img');
    const imageCount = await images.count();
    expect(imageCount).toBeGreaterThan(0);

    for (let index = 0; index < imageCount; index += 1) {
      const image = images.nth(index);
      await image.scrollIntoViewIfNeeded();
      await expect
        .poll(() =>
          image.evaluate((element) => {
            const node = element as HTMLImageElement;
            return node.complete && node.naturalWidth > 0 && node.naturalHeight > 0;
          }),
        )
        .toBe(true);
    }
  });

  test('FAQ is keyboard operable and exposes its controlled panel', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    const trigger = page
      .locator('[data-rm-section="faq"] button[aria-expanded][aria-controls]')
      .first();

    await expect(trigger).toBeVisible();
    const panelId = await trigger.getAttribute('aria-controls');
    expect(panelId).toBeTruthy();

    const panel = page.locator(`#${panelId}`);
    await expect(panel).toHaveCount(1);

    const initialState = await trigger.getAttribute('aria-expanded');
    expect(initialState === 'true' || initialState === 'false').toBe(true);

    await trigger.focus();
    await expect(trigger).toBeFocused();
    await page.keyboard.press('Enter');
    await expect(trigger).toHaveAttribute(
      'aria-expanded',
      initialState === 'true' ? 'false' : 'true',
    );

    await page.keyboard.press('Space');
    await expect(trigger).toHaveAttribute('aria-expanded', initialState!);
  });

  test('request modal opens and closes without submitting', async ({ page }) => {
    const postRequests: string[] = [];
    page.on('request', (request) => {
      if (request.method() === 'POST') postRequests.push(request.url());
    });

    await page.goto('/', { waitUntil: 'domcontentloaded' });
    const trigger = page
      .locator('[data-rm-section="hero"] [data-rm-discuss-open="home"]')
      .first();

    await expect(trigger).toBeVisible();
    await trigger.click();

    const dialog = page.locator('dialog[data-rm-discuss-variant="home"]');
    await expect(dialog).toBeVisible();
    await expect(dialog.locator('h2')).toHaveText('Обсудить заказ');
    await expect(dialog.locator('[data-SID="CONTEXT"]')).toHaveValue('home');
    await page.keyboard.press('Escape');
    await expect(dialog).toBeHidden();
    expect(postRequests).toEqual([]);
  });

  test('desktop render has no overflow or browser errors', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);

    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await page.waitForTimeout(2_000);
    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });

  test('P0 @mobile mobile render has no overflow or browser errors', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);

    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await expect(page.locator('[data-rm-home]')).toBeVisible();
    await page.waitForTimeout(2_000);
    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });
});
