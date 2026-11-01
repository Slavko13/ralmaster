import AxeBuilder from '@axe-core/playwright';
import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const routes = [
  { path: '/product/kapelnik/', marker: '[data-rm-product-category]', title: 'Капельник' },
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

async function expectProductBasketFlow(page: Page): Promise<void> {
  await page.goto('/product/pannier/', { waitUntil: 'networkidle' });

  const catalog = page.locator('[data-rm-product-category]');
  await expect(catalog).toBeVisible();
  const products = catalog.locator('.catalog_table_2 .item');
  await expect(products).toHaveCount(3);

  const firstProduct = products.first();
  const productName = (await firstProduct.locator('.title').innerText()).trim();
  const addButton = firstProduct.locator('.to_cart');
  await expect(firstProduct.locator('.footer-button')).toHaveCSS('opacity', '1');
  await expect(addButton).toBeVisible();
  const basketRequest = page.waitForResponse(
    (response) =>
      response.request().method() === 'POST' &&
      response.url().includes('/include/footer/site-basket.php'),
  );
  await addButton.click();
  await basketRequest;

  await page.goto('/cart/', { waitUntil: 'networkidle' });
  await expect(page.locator('[data-rm-system-page="cart"]')).toBeVisible();
  await expect(page.getByText(productName, { exact: false }).first()).toBeVisible();
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
      if (route.path.startsWith('/product/')) {
        await expect(page.locator('.main-catalog-wrapper')).toBeVisible();
      } else {
        await expect(
          page.locator('.main-catalog-wrapper, .main-section-wrapper, .alert-warning'),
        ).toHaveCount(0);
      }
      await expect(page.locator('.ajax_basket > .fixed_wrapper')).toBeHidden();
      expect(await page.locator('[src*="paper.design"], [style*="paper.design"]').count()).toBe(0);
    });
  }

  test('product and service detail templates keep local imagery and safe estimate dialogs', async ({ page }) => {
    for (const route of routes.slice(1, 2)) {
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

  test('redesigned product category keeps live Bitrix products and basket flow', async ({
    page,
  }) => {
    await expectProductBasketFlow(page);
  });

  test('product categories preserve the legacy navigation and controls in the new style', async ({
    page,
  }) => {
    for (const category of [
      { path: '/product/pannier/', title: 'Короба', count: 3 },
      { path: '/product/bracket/', title: 'Кронштейны', count: 1 },
    ]) {
      await page.goto(category.path, { waitUntil: 'networkidle' });
      const root = page.locator('[data-rm-product-category]');
      await expect(root.locator('h1')).toHaveText(category.title);
      await expect(root.locator('.rm-detail-hero')).toHaveCount(0);
      await expect(root.getByText('Рассчитать изготовление', { exact: false })).toHaveCount(0);
      await expect(root.locator('.sidebar .side-menu > li')).toHaveCount(7);
      await expect(root.locator('.sidebar .side-menu > li.active')).toContainText(category.title);
      await expect(root.locator('.bx_filter')).toBeAttached();
      await expect(root.locator('select.sort')).toBeVisible();
      await expect(root.locator('.display-type, .view-button')).toHaveCount(0);
      await expect(root.locator('.ajax_load.table .catalog.item-views.table')).toHaveCount(1);
      await expect(root.locator('.catalog_table_2 .item')).toHaveCount(category.count);

      const firstProduct = root.locator('.catalog_table_2 .item').first();
      await expect(firstProduct.locator('.props_wrapper')).toBeHidden();
      await firstProduct.hover();
      await expect(firstProduct.locator('.props_wrapper')).toBeHidden();
      const productImage = firstProduct.locator('.image img');
      await productImage.scrollIntoViewIfNeeded();
      await expect
        .poll(() =>
          productImage.evaluate((image) => {
            const element = image as HTMLImageElement;
            return element.complete && element.naturalWidth > 0 && element.naturalHeight > 0;
          }),
        )
        .toBe(true);
      const imageGeometry = await firstProduct.evaluate((card) => {
        const image = card.querySelector<HTMLImageElement>('.image img')!;
        const frame = card.querySelector<HTMLElement>('.image')!;
        const imageBox = image.getBoundingClientRect();
        const frameBox = frame.getBoundingClientRect();
        return {
          imageWidth: Math.round(imageBox.width),
          imageHeight: Math.round(imageBox.height),
          frameWidth: Math.round(frameBox.width),
          centered: Math.abs(imageBox.left + imageBox.width / 2 - (frameBox.left + frameBox.width / 2)),
        };
      });
      expect(imageGeometry.imageWidth).toBeGreaterThan(imageGeometry.frameWidth * 0.75);
      expect(imageGeometry.imageHeight).toBeGreaterThan(250);
      expect(imageGeometry.centered).toBeLessThanOrEqual(1);
      await expect(firstProduct.locator('.counter')).toBeVisible();
      await expect(firstProduct.locator('.to_cart')).toBeVisible();
      await expect(firstProduct.locator('.slice_price, .price_val')).toHaveCount(0);
    }
  });

  test('catalog controls use the blue system and keep flat product actions', async ({ page }) => {
    await page.goto('/product/pannier/', { waitUntil: 'networkidle' });
    const root = page.locator('[data-rm-product-category]');
    const applyButton = root.locator('.bx_filter_search_button');
    const resetButton = root.locator('.bx_filter_search_reset');

    await expect(applyButton).toHaveCSS('background-color', 'rgb(23, 91, 239)');
    await expect(resetButton).toHaveCSS('color', 'rgb(23, 91, 239)');
    await expect(resetButton).toHaveCSS('border-color', 'rgb(23, 91, 239)');
    await expect(root.locator('.bx_ui_slider_pricebar_V')).toHaveCSS(
      'background-color',
      'rgb(23, 91, 239)',
    );
    await expect(root.locator('.bx_ui_slider_handle').first()).toHaveCSS(
      'border-color',
      'rgb(23, 91, 239)',
    );

    const filterButtonGeometry = await root.locator('.bx_filter_button_box').evaluate((box) => {
      const buttons = Array.from(box.querySelectorAll<HTMLElement>('.btn'));
      return buttons.map((button) => {
        const bounds = button.getBoundingClientRect();
        return {
          width: Math.round(bounds.width),
          top: Math.round(bounds.top),
          height: Math.round(bounds.height),
        };
      });
    });
    expect(filterButtonGeometry).toHaveLength(2);
    expect(filterButtonGeometry[0].width).toBe(filterButtonGeometry[1].width);
    expect(filterButtonGeometry[0].top).toBe(filterButtonGeometry[1].top);
    expect(filterButtonGeometry[0].height).toBeLessThanOrEqual(42);

    const statusCheckbox = root.locator('.bx_filter input[type="checkbox"]').first();
    const statusLabel = root.locator(`label[for="${await statusCheckbox.getAttribute('id')}"]`);
    await statusLabel.click();
    await expect(statusCheckbox).toBeChecked();
    await expect(root.locator('#modef')).toBeVisible();
    await expect(root.locator('#modef')).toHaveCSS('background-color', 'rgb(23, 91, 239)');
    const checkedColor = await statusLabel.evaluate((label) =>
      getComputedStyle(label, '::after').backgroundColor,
    );
    expect(checkedColor).toBe('rgb(23, 91, 239)');

    const firstProduct = root.locator('.catalog_table_2 .item').first();
    await firstProduct.hover();
    await expect(firstProduct).toHaveCSS('box-shadow', 'none');
    await expect(firstProduct.locator('.button_block')).toHaveCSS('box-shadow', 'none');
    await expect(firstProduct.locator('.counter .wrap')).toHaveCSS('box-shadow', 'none');
    await expect(firstProduct.locator('.to_cart')).toHaveCSS('box-shadow', 'none');
  });

  test('P0 @mobile redesigned product category keeps live products and basket flow', async ({
    page,
  }) => {
    await expectProductBasketFlow(page);
  });

  test('@mobile catalog remains a single tile view without view controls', async ({ page }) => {
    await page.goto('/product/pannier/?display=list', { waitUntil: 'networkidle' });
    const root = page.locator('[data-rm-product-category]');
    await expect(root.locator('.display-type, .view-button')).toHaveCount(0);
    await expect(root.locator('.ajax_load.table .catalog.item-views.table')).toHaveCount(1);
  });

  test('desktop dialog remains reachable in a short viewport', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 720 });
    await page.goto('/services/laser-cutting/', { waitUntil: 'networkidle' });
    await page.locator('.rm-detail-hero [data-rm-discuss-open]').click();

    const dialog = page.locator('dialog[data-rm-discuss-dialog]');
    await expect(dialog).toBeVisible();
    const geometry = await dialog.evaluate((node) => {
      const box = node.getBoundingClientRect();
      return {
        top: Math.round(box.top),
        bottom: Math.round(box.bottom),
        viewportHeight: window.innerHeight,
        clientHeight: node.clientHeight,
        scrollHeight: node.scrollHeight,
        overflowY: getComputedStyle(node).overflowY,
      };
    });

    expect(geometry.top).toBeGreaterThanOrEqual(8);
    expect(geometry.bottom).toBeLessThanOrEqual(geometry.viewportHeight - 8);
    expect(geometry.scrollHeight).toBeGreaterThan(geometry.clientHeight);
    expect(geometry.overflowY).toBe('auto');

    const submit = dialog.locator('input[type="submit"]');
    await submit.scrollIntoViewIfNeeded();
    await expect(submit).toBeVisible();
    await page.keyboard.press('Escape');
    await expect(dialog).toBeHidden();
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

    const externalLinks = root.locator('.rm-partner-card a[href^="http"]');
    expect(await externalLinks.count()).toBeGreaterThan(0);
    expect(
      await externalLinks.evaluateAll((links) =>
        links.every(
          (link) =>
            link.getAttribute('target') === '_blank' &&
            (link.getAttribute('rel') ?? '').includes('noopener'),
        ),
      ),
    ).toBe(true);
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

