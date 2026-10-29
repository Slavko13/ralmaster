import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const routes = ['/painting/', '/painting/poroshkovaya-pokraska-metalla/'] as const;
const expectedSections = [
  'hero',
  'accepted',
  'preparation',
  'requirements',
  'examples',
  'orders',
  'estimate',
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

test.describe('P0 redesigned powder coating page', () => {
  test('keeps both public routes on one reusable Paper layout', async ({ page, request }) => {
    for (const route of routes) {
      const response = await request.get(route, { maxRedirects: 5 });
      expect(response.status(), `Broken powder-coating route: ${route}`).toBeLessThan(400);

      await page.goto(route, { waitUntil: 'domcontentloaded' });
      await expectHealthyDocument(page);
      await expect(page.locator('[data-rm-powder-page]')).toBeVisible();
      await expect(page.locator('[data-rm-powder-page] h1')).toHaveText(
        'Порошковая окраска металлических изделий',
      );

      const sections = await page
        .locator('[data-rm-powder-page] > [data-rm-powder-section]')
        .evaluateAll((nodes) => nodes.map((node) => node.getAttribute('data-rm-powder-section')));
      expect(sections).toEqual(expectedSections);
    }
  });

  test('shows Paper content without presenting draft limits as confirmed facts', async ({ page }) => {
    await page.goto('/painting/', { waitUntil: 'domcontentloaded' });

    await expect(page.getByRole('heading', { name: 'Какие изделия принимаем на окраску' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Подготовка поверхности перед окраской' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Согласуем требования к покрытию' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Примеры порошковой окраски' })).toBeVisible();
    await expect(page.locator('.rm-powder-dimensions__note')).toContainText(
      'требуют подтверждения производством',
    );
    await expect(page.locator('.rm-powder-example small')).toHaveCount(3);
    await expect(page.locator('.rm-powder-example small').first()).toContainText(
      'Демонстрационный пример',
    );
  });

  test('loads all Powder Paper images from local template assets', async ({ page }) => {
    await page.goto('/painting/', { waitUntil: 'networkidle' });
    const images = page.locator('[data-rm-powder-page] img');
    expect(await images.count()).toBeGreaterThanOrEqual(10);

    for (let index = 0; index < await images.count(); index += 1) {
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
      await expect(image).toHaveAttribute('src', /\/images\/ralmaster\//);
    }
  });

  test('opens the Paper coating calculation dialog and validates without submitting', async ({ page }) => {
    const postRequests: string[] = [];
    page.on('request', (request) => {
      if (request.method() === 'POST') postRequests.push(request.url());
    });

    await page.goto('/painting/', { waitUntil: 'domcontentloaded' });
    const cta = page.locator(
      '.rm-powder-hero [data-rm-discuss-open="powder-coating"]',
    );
    await expect(cta).toBeVisible();
    await cta.click();

    const dialog = page.locator('dialog[data-rm-discuss-variant="powder-coating"]');
    await expect(dialog).toBeVisible();
    await expect(dialog.locator('h2')).toHaveText('Расчёт порошковой окраски');
    await expect(dialog.locator('[data-SID="CONTEXT"]')).toHaveValue('powder-coating');
    await dialog.locator('input[type="submit"]').click();
    await expect(dialog.locator('[data-SID="CONTACT"] .rm-estimate-error')).toBeVisible();
    await expect(dialog.locator('[data-SID="TASK"] .rm-estimate-error')).toBeVisible();
    await page.keyboard.press('Escape');
    await expect(dialog).toBeHidden();

    await expect(page.locator('.rm-powder-estimate__form form')).toBeVisible();
    await expect(page.locator('.rm-powder-estimate__form input[type="submit"]')).toBeVisible();
    expect(postRequests).toEqual([]);
  });

  test('keeps every desktop estimate control inside the Paper form panel', async ({ page }) => {
    await page.goto('/painting/', { waitUntil: 'networkidle' });

    const geometry = await page.locator('.rm-powder-estimate__form').evaluate((root) => {
      const rootBox = root.getBoundingClientRect();
      const measure = (selector: string) => {
        const element = root.querySelector<HTMLElement>(selector);
        if (!element) return null;
        const box = element.getBoundingClientRect();
        return {
          height: Math.round(box.height),
          clipped:
            box.top < rootBox.top - 0.5 ||
            box.right > rootBox.right + 0.5 ||
            box.bottom > rootBox.bottom + 0.5 ||
            box.left < rootBox.left - 0.5,
        };
      };

      const duplicateTitleBoxes = Array.from(root.querySelectorAll('.form-group > .input'))
        .flatMap((input) => Array.from(input.childNodes))
        .filter((node) => node.nodeType === Node.TEXT_NODE && node.textContent?.trim())
        .map((node) => {
          const range = document.createRange();
          range.selectNode(node);
          const box = range.getBoundingClientRect();
          return { width: Math.round(box.width), height: Math.round(box.height) };
        });

      return {
        root: { clientHeight: root.clientHeight, scrollHeight: root.scrollHeight },
        form: measure('form'),
        task: measure('.row[data-SID="TASK"]'),
        textarea: measure('[data-SID="TASK"] textarea'),
        uploader: measure('.uploader'),
        consent: measure('.licence_block'),
        submit: measure('input[type="submit"]'),
        duplicateTitleBoxes,
      };
    });

    expect(geometry.root).toEqual({ clientHeight: 738, scrollHeight: 738 });
    expect(geometry.form?.clipped).toBe(false);
    expect(geometry.task).toEqual({ height: 140, clipped: false });
    expect(geometry.textarea).toEqual({ height: 112, clipped: false });
    expect(geometry.uploader).toEqual({ height: 56, clipped: false });
    expect(geometry.consent?.clipped).toBe(false);
    expect(geometry.submit).toEqual({ height: 60, clipped: false });
    expect(geometry.duplicateTitleBoxes.every((box) => box.width === 0 && box.height === 0)).toBe(true);
  });

  test('matches the desktop Paper section heights', async ({ page }) => {
    await page.goto('/painting/', { waitUntil: 'networkidle' });

    const heights = await page
      .locator('[data-rm-powder-page] > [data-rm-powder-section]')
      .evaluateAll((nodes) =>
        Object.fromEntries(
          nodes.map((node) => [
            node.getAttribute('data-rm-powder-section'),
            Math.round(node.getBoundingClientRect().height),
          ]),
        ),
      );

    expect(heights).toEqual({
      hero: 574,
      accepted: 878,
      preparation: 777,
      requirements: 714,
      examples: 920,
      orders: 604,
      estimate: 850,
      photo: 543,
    });
  });

  test('desktop render has no overflow or browser errors', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);
    await page.goto('/painting/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await page.waitForTimeout(1_500);
    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });

  test('P0 @mobile mobile render has no overflow or browser errors', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);
    await page.goto('/painting/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await expect(page.locator('[data-rm-powder-page]')).toBeVisible();
    await page.waitForTimeout(1_500);
    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });
});
