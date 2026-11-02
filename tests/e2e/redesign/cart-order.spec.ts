import { expect, test, type Page } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

async function addProductToBasket(page: Page): Promise<string> {
  await page.goto('/product/pannier/', { waitUntil: 'networkidle' });
  const product = page.locator('[data-rm-product-category] .catalog_table_2 .item').first();
  await expect(product).toBeVisible();
  const productName = (await product.locator('.title').innerText()).trim();
  const basketRequest = page.waitForResponse(
    (response) =>
      response.request().method() === 'POST' &&
      response.url().includes('/include/footer/site-basket.php'),
  );
  await product.locator('.to_cart').click();
  await basketRequest;
  return productName;
}

async function expectNoHorizontalOverflow(page: Page): Promise<void> {
  const geometry = await page.evaluate(() => ({
    clientWidth: document.documentElement.clientWidth,
    scrollWidth: document.documentElement.scrollWidth,
  }));
  expect(geometry.scrollWidth).toBeLessThanOrEqual(geometry.clientWidth + 1);
}

test.describe('redesigned cart and checkout', () => {
  test('empty cart is a single clean state without an icon or duplicate hero', async ({ page }, testInfo) => {
    await page.goto('/cart/', { waitUntil: 'networkidle' });
    await expectHealthyDocument(page);

    const root = page.locator('[data-rm-system-page="cart"]');
    await expect(root).toHaveCount(1);
    await expect(root.locator('.rm-system-hero')).toHaveCount(1);
    await expect(root.locator('.rm-system-kicker')).toHaveCount(0);
    await expect(root.locator('.rm-system-breadcrumb > span[aria-hidden="true"]')).toHaveText(['/']);
    await expect(root.locator('.basket_empty')).toBeVisible();
    expect(
      await root.locator('.basket_empty > .wrap').evaluate((element) =>
        getComputedStyle(element, '::before').display,
      ),
    ).toBe('none');

    const heroFlow = await root.locator('.rm-system-hero__layout').evaluate((hero) => {
      const title = hero.querySelector('h1')!.getBoundingClientRect();
      const intro = hero.querySelector(':scope > p')!.getBoundingClientRect();
      return {
        introTop: Math.round(intro.top),
        titleBottom: Math.round(title.bottom),
        leftDelta: Math.round(intro.left - title.left),
      };
    });
    expect(heroFlow.introTop).toBeGreaterThan(heroFlow.titleBottom);
    expect(Math.abs(heroFlow.leftDelta)).toBeLessThanOrEqual(1);
    await expectNoHorizontalOverflow(page);
    await page.screenshot({ path: testInfo.outputPath('cart-empty-desktop.png'), fullPage: true });
  });

  test('cart uses the blue RALMASTER system and keeps live basket controls', async ({ page }, testInfo) => {
    const productName = await addProductToBasket(page);
    await page.goto('/cart/', { waitUntil: 'networkidle' });
    await expectHealthyDocument(page);

    const root = page.locator('[data-rm-system-page="cart"]');
    await expect(root).toBeVisible();
    const headerBasket = page.locator('.rm-header__basket');
    await expect(headerBasket.locator('.basket-link')).toBeVisible();
    await expect(headerBasket.locator('.count')).toBeVisible();
    await expect(headerBasket.locator('.count')).toHaveText(/^[1-9]\d*$/);
    await expect(headerBasket.locator('.basket-link')).toHaveCSS('color', 'rgb(23, 91, 239)');
    await expect(headerBasket.locator('.count')).toHaveCSS('background-color', 'rgb(23, 91, 239)');
    await expect(headerBasket.locator('.count')).toHaveCSS('color', 'rgb(255, 255, 255)');
    await expect(root.locator('.rm-system-breadcrumb > span[aria-hidden="true"]')).toHaveText(['/']);
    await expect(root.getByText(productName, { exact: false }).first()).toBeVisible();
    await expect(root.locator('.basket.default .counter')).toBeVisible();

    const continueButton = root.locator('.basket.default .buttons .btn-transparent-bg');
    const printButton = root.locator('.basket.default .buttons .print');
    const orderButton = root.locator('.basket.default .buttons .to-order');
    await expect(continueButton).toHaveCSS('color', 'rgb(23, 91, 239)');
    await expect(printButton).toHaveCSS('color', 'rgb(255, 255, 255)');
    await expect(printButton).toHaveCSS('background-color', 'rgb(11, 31, 54)');
    await printButton.hover();
    await expect(printButton).toHaveCSS('color', 'rgb(255, 255, 255)');
    await expect(orderButton).toHaveCSS('background-color', 'rgb(23, 91, 239)');
    await expect(orderButton).toHaveCSS('color', 'rgb(255, 255, 255)');

    const clearButton = root.locator('.basket.default .foot .remove.all');
    await expect(clearButton).toHaveCSS('display', 'flex');
    await expect(clearButton).toHaveCSS('color', 'rgb(23, 91, 239)');
    await expect(clearButton).toHaveCSS('border-color', 'rgb(23, 91, 239)');
    await expect(clearButton).toHaveCSS('background-color', 'rgb(255, 255, 255)');
    const clearCross = await clearButton.locator(':scope > span').evaluate((label) => {
      const style = getComputedStyle(label, '::before');
      return {
        position: style.position,
        order: style.order,
        width: style.width,
        height: style.height,
        lineHeight: style.lineHeight,
        margin: style.margin,
      };
    });
    expect(clearCross).toEqual({
      position: 'static',
      order: '2',
      width: '16px',
      height: '16px',
      lineHeight: '16px',
      margin: '0px',
    });

    const actionGeometry = await root.locator('.basket.default .foot').evaluate((foot) => {
      const clear = foot.querySelector<HTMLElement>('.remove.all')!.getBoundingClientRect();
      const total = foot.querySelector<HTMLElement>('.total')!.getBoundingClientRect();
      const bounds = foot.getBoundingClientRect();
      return {
        clearLeft: Math.round(clear.left),
        totalLeft: Math.round(total.left),
        rightGap: Math.round(bounds.right - total.right),
      };
    });
    expect(actionGeometry.clearLeft).toBeLessThan(actionGeometry.totalLeft);
    expect(actionGeometry.rightGap).toBeLessThanOrEqual(8);

    const removeAlignment = await root.locator('.basket.default .items .item .remove_bl').first().evaluate((box) => {
      const control = box.querySelector<HTMLElement>('.remove')!.getBoundingClientRect();
      const bounds = box.getBoundingClientRect();
      return {
        x: Math.abs(control.left + control.width / 2 - (bounds.left + bounds.width / 2)),
        y: Math.abs(control.top + control.height / 2 - (bounds.top + bounds.height / 2)),
      };
    });
    expect(removeAlignment.x).toBeLessThanOrEqual(1);
    expect(removeAlignment.y).toBeLessThanOrEqual(1);

    const productLink = root.locator('.basket.default .description .name a').first();
    await productLink.hover();
    await expect(productLink).toHaveCSS('color', 'rgb(23, 91, 239)');
    await expectNoHorizontalOverflow(page);
    await page.screenshot({ path: testInfo.outputPath('cart-desktop.png'), fullPage: true });
  });

  test('clearing the cart keeps one system shell and returns the empty state', async ({ page }) => {
    await addProductToBasket(page);
    await page.goto('/cart/', { waitUntil: 'networkidle' });
    const root = page.locator('[data-rm-system-page="cart"]');
    const update = page.waitForResponse(
      (response) =>
        response.request().method() === 'POST' &&
        new URL(response.url()).pathname === '/cart/',
    );
    await root.locator('.basket.default .remove.all').click();
    await update;
    await expect(root.locator('.basket_empty')).toBeVisible();
    await expect(page.locator('[data-rm-system-page="cart"]')).toHaveCount(1);
    await expect(page.locator('.rm-system-hero')).toHaveCount(1);
    await expect(root.locator('.basket.default .basket_wrap')).toHaveCount(0);
  });

  test('checkout form matches the common form contract and order summary', async ({ page }, testInfo) => {
    const productName = await addProductToBasket(page);
    await page.goto('/cart/order/', { waitUntil: 'networkidle' });
    await expectHealthyDocument(page);

    const root = page.locator('[data-rm-system-page="order"]');
    await expect(root).toBeVisible();
    await expect(root.locator('.rm-system-kicker')).toHaveCount(0);
    await expect(root.locator('.rm-system-breadcrumb > span[aria-hidden="true"]')).toHaveText([
      '/',
      '/',
    ]);
    const heroFlow = await root.locator('.rm-system-hero__layout').evaluate((hero) => {
      const title = hero.querySelector('h1')!.getBoundingClientRect();
      const intro = hero.querySelector(':scope > p')!.getBoundingClientRect();
      return {
        introTop: Math.round(intro.top),
        titleBottom: Math.round(title.bottom),
        leftDelta: Math.round(intro.left - title.left),
      };
    });
    expect(heroFlow.introTop).toBeGreaterThan(heroFlow.titleBottom);
    expect(Math.abs(heroFlow.leftDelta)).toBeLessThanOrEqual(1);
    await expect(root.getByText(productName, { exact: false }).first()).toBeVisible();

    const form = root.locator('.form.order');
    await expect(form).toBeVisible();
    const firstField = form.locator('input.form-control, textarea.form-control').first();
    await expect(firstField).toHaveCSS('height', '56px');
    await expect(firstField).toHaveCSS('border-color', 'rgb(204, 214, 226)');
    expect(await firstField.evaluate((element) => getComputedStyle(element).fontFamily)).toContain('Onest');

    const submit = form.locator('input[type="submit"]');
    await expect(submit).toBeVisible();
    await expect(submit).toHaveCSS('height', '60px');
    await expect(submit).toHaveCSS('background-color', 'rgb(23, 91, 239)');
    await expect(submit).toHaveCSS('color', 'rgb(255, 255, 255)');
    await expect(root.locator('.basket_order .items')).toHaveCSS('background-color', 'rgb(255, 255, 255)');
    const summaryImage = root.locator('.basket_order .rm-order-item__image img').first();
    await expect(summaryImage).toBeVisible();
    await expect
      .poll(() =>
        summaryImage.evaluate((image) => {
          const node = image as HTMLImageElement;
          return node.complete && node.naturalWidth > 0 && node.naturalHeight > 0;
        }),
      )
      .toBe(true);

    const licenceAlignment = await form.locator('.licence_block').evaluate((block) => {
      const input = block.querySelector<HTMLInputElement>('input[type="checkbox"]')!.getBoundingClientRect();
      const label = block.querySelector<HTMLLabelElement>('label')!.getBoundingClientRect();
      return {
        display: getComputedStyle(block).display,
        centerDelta: Math.abs(input.top + input.height / 2 - (label.top + label.height / 2)),
      };
    });
    expect(licenceAlignment.display).toBe('flex');
    expect(licenceAlignment.centerDelta).toBeLessThanOrEqual(2);
    await expectNoHorizontalOverflow(page);
    await page.screenshot({ path: testInfo.outputPath('order-desktop.png'), fullPage: true });
  });

  test('@mobile cart and checkout remain readable without horizontal overflow', async ({ page }, testInfo) => {
    const productName = await addProductToBasket(page);
    await page.goto('/cart/', { waitUntil: 'networkidle' });

    const cart = page.locator('[data-rm-system-page="cart"]');
    await expect(cart.getByText(productName, { exact: false }).first()).toBeVisible();
    await expect(cart.locator('.basket.default .items .item').first()).toHaveCSS(
      'background-color',
      'rgb(255, 255, 255)',
    );
    for (const action of ['.to-order', '.print', '.btn-transparent-bg']) {
      await expect(cart.locator(`.basket.default .buttons ${action}`)).toBeVisible();
    }
    await expectNoHorizontalOverflow(page);

    await cart.locator('.basket.default .buttons .to-order').click();
    await page.waitForLoadState('networkidle');
    const order = page.locator('[data-rm-system-page="order"]');
    await expect(order.locator('.form.order')).toBeVisible();
    await expect(order.locator('.basket_order .items')).toBeVisible();
    await expect(order.locator('.basket_order .items')).toHaveCSS('position', 'static');
    await expect(order.locator('.form.order input[type="submit"]')).toBeVisible();
    await expectNoHorizontalOverflow(page);
    await page.screenshot({ path: testInfo.outputPath('order-mobile.png'), fullPage: true });
  });
});
