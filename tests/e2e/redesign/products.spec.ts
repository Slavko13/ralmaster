import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const expectedProducts = [
  'Фасадные металлокассеты',
  'Корзины для кондиционеров',
  'Короба',
  'Кронштейны',
  'Отливы',
  'Капельники',
  'Экраны',
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
  expect(geometry.scrollWidth).toBeLessThanOrEqual(geometry.clientWidth + 1);
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

test.describe('P0 redesigned products root', () => {
  test('renders seven dynamic catalog sections in the Paper order', async ({ page }) => {
    await page.goto('/product/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);

    await expect(page.locator('.rm-products-intro h1')).toHaveText('Продукция');
    const cards = page.locator('.rm-product-card');
    await expect(cards).toHaveCount(7);

    const names = await cards.locator('.rm-product-card__header strong').allTextContents();
    expect(names.map((name) => name.trim())).toEqual([...expectedProducts]);

    const hrefs = await cards.evaluateAll((nodes) =>
      nodes.map((node) => (node as HTMLAnchorElement).getAttribute('href')),
    );
    expect(hrefs).toEqual([
      '/product/facadecassettes/',
      '/product/baskets/',
      '/product/pannier/',
      '/product/bracket/',
      '/product/lowtide/',
      '/product/kapelnik/',
      '/product/shield/',
    ]);
  });

  test('loads all local Paper product assets', async ({ page }) => {
    await page.goto('/product/', { waitUntil: 'networkidle' });
    const images = page.locator('.rm-product-card__image');
    await expect(images).toHaveCount(7);

    for (let index = 0; index < 7; index += 1) {
      const image = images.nth(index);
      await image.scrollIntoViewIfNeeded();
      await expect
        .poll(() =>
          image.evaluate((node) => {
            const element = node as HTMLImageElement;
            return element.complete && element.naturalWidth === 1448 && element.naturalHeight === 1086;
          }),
        )
        .toBe(true);

      await expect(image).toHaveAttribute('src', /\/images\/ralmaster\/products\//);
    }
  });

  test('keeps section and detail SEF routes available', async ({ request }) => {
    for (const route of [
      '/product/baskets/',
      '/product/baskets/korzina-dlya-konditsionera-kdk-1/',
    ]) {
      const response = await request.get(route, { maxRedirects: 5 });
      expect(response.status(), `Broken catalog route: ${route}`).toBeLessThan(400);
    }
  });

  test('opens the hero estimate dialog without scrolling or a POST', async ({ page }) => {
    const postRequests: string[] = [];
    page.on('request', (request) => {
      if (request.method() === 'POST') postRequests.push(request.url());
    });

    await page.goto('/product/', { waitUntil: 'domcontentloaded' });
    const cta = page.locator('.rm-products-intro__layout [data-rm-discuss-open="product"]');
    await expect(cta).toBeVisible();
    const scrollBefore = await page.evaluate(() => window.scrollY);
    await cta.click();

    const dialog = page.locator('dialog[data-rm-discuss-variant="product"]');
    await expect(dialog).toBeVisible();
    await expect(dialog.getByRole('heading', { name: 'Рассчитать изготовление изделий' })).toBeVisible();
    await expect(dialog.locator('[data-SID="CONTEXT"]')).toHaveValue('product');
    expect(await page.evaluate(() => window.scrollY)).toBe(scrollBefore);

    // The full inline form remains on the page as specified by the Product artboard.
    await expect(page.locator('.rm-product-estimate__form form')).toBeVisible();
    await expect(page.locator('.rm-product-estimate__form input[type="submit"]')).toBeVisible();
    expect(postRequests).toEqual([]);

    const arrows = await page.evaluate(() => {
      const hero = document.querySelector('.rm-products-button__arrow');
      const submit = document.querySelector(
        '.rm-product-estimate__form .inner-table-block:has(input[type="submit"])',
      );
      const heroStyle = hero ? getComputedStyle(hero) : null;
      const submitStyle = submit ? getComputedStyle(submit, '::after') : null;
      return {
        hero: hero?.textContent?.trim(),
        heroSize: heroStyle?.fontSize,
        submit: submitStyle?.content,
        submitSize: submitStyle?.fontSize,
      };
    });
    expect(arrows).toEqual({ hero: '↗', heroSize: '22px', submit: '"↗"', submitSize: '22px' });

    await page.keyboard.press('Escape');
    await expect(dialog).toBeHidden();
  });

  test('uses a restrained hover response on product cards', async ({ page }) => {
    await page.goto('/product/', { waitUntil: 'domcontentloaded' });
    const card = page.locator('.rm-product-card').first();
    const before = await card.evaluate((node) => ({
      transform: getComputedStyle(node).transform,
      boxShadow: getComputedStyle(node).boxShadow,
    }));

    await card.hover();
    await page.waitForTimeout(220);
    const after = await card.evaluate((node) => ({
      transform: getComputedStyle(node).transform,
      boxShadow: getComputedStyle(node).boxShadow,
    }));

    expect(before.transform).toBe('none');
    expect(after.transform).not.toBe('none');
    expect(after.boxShadow).not.toBe('none');
  });

  test('blocks an empty task and file on the shared estimate form without a POST', async ({ page }) => {
    const postRequests: string[] = [];
    page.on('request', (request) => {
      if (request.method() === 'POST') postRequests.push(request.url());
    });

    await page.goto('/product/', { waitUntil: 'domcontentloaded' });
    const formRoot = page.locator(
      '.rm-product-estimate__form[data-rm-estimate-form][data-rm-estimate-context="product"]',
    );
    await expect(formRoot).toBeVisible();

    await formRoot.locator('[data-SID="CONTACT"] input').fill('qa-product@example.test');
    await formRoot.locator('[data-SID="TASK"] textarea').fill('');

    const consent = formRoot.locator('.licence_block input[type="checkbox"]');
    if ((await consent.count()) > 0 && !(await consent.isChecked())) {
      await consent.evaluate((node) => {
        const input = node as HTMLInputElement;
        input.checked = true;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      });
    }

    await formRoot.locator('input[type="submit"]').click();
    const error = formRoot.locator('[data-SID="TASK"] .rm-estimate-error');
    await expect(error).toHaveText('Опишите задачу или прикрепите файл.');
    expect(postRequests).toEqual([]);
  });

  test('matches the desktop Paper section heights', async ({ page }) => {
    await page.goto('/product/', { waitUntil: 'networkidle' });

    const heights = await page.evaluate(() => {
      const measure = (selector: string) =>
        Math.round(document.querySelector(selector)?.getBoundingClientRect().height ?? 0);
      return {
        intro: measure('.rm-products-intro'),
        catalog: measure('.rm-products-catalog'),
        estimate: measure('.rm-product-estimate'),
        photo: measure('.rm-product-production-photo'),
      };
    });

    expect(heights.intro).toBe(274);
    expect(heights.catalog).toBe(1083);
    expect(heights.estimate).toBe(804);
    expect(heights.photo).toBe(543);
  });

  test('desktop render has no horizontal overflow or browser errors', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);
    await page.goto('/product/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await page.waitForTimeout(1_500);
    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });

  test('P0 @mobile mobile render has no horizontal overflow or browser errors', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);
    await page.goto('/product/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await expect(page.locator('.rm-product-grid')).toBeVisible();

    await page.locator('[data-rm-discuss-open="product"]').click();
    const dialog = page.locator('dialog[data-rm-discuss-variant="product"]');
    await expect(dialog).toBeVisible();
    const dialogGeometry = await dialog.evaluate((node) => {
      const box = node.getBoundingClientRect();
      return { left: box.left, right: box.right, viewportWidth: window.innerWidth };
    });
    expect(dialogGeometry.left).toBeGreaterThanOrEqual(0);
    expect(dialogGeometry.right).toBeLessThanOrEqual(dialogGeometry.viewportWidth);
    await page.keyboard.press('Escape');
    await expect(dialog).toBeHidden();

    await page.waitForTimeout(1_500);
    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });
});
