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
  test('cart uses the blue RALMASTER system and keeps live basket controls', async ({ page }, testInfo) => {
    const productName = await addProductToBasket(page);
    await page.goto('/cart/', { waitUntil: 'networkidle' });
    await expectHealthyDocument(page);

    const root = page.locator('[data-rm-system-page="cart"]');
    await expect(root).toBeVisible();
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

    const productLink = root.locator('.basket.default .description .name a').first();
    await productLink.hover();
    await expect(productLink).toHaveCSS('color', 'rgb(23, 91, 239)');
    await expectNoHorizontalOverflow(page);
    await page.screenshot({ path: testInfo.outputPath('cart-desktop.png'), fullPage: true });
  });

  test('checkout form matches the common form contract and order summary', async ({ page }, testInfo) => {
    const productName = await addProductToBasket(page);
    await page.goto('/cart/order/', { waitUntil: 'networkidle' });
    await expectHealthyDocument(page);

    const root = page.locator('[data-rm-system-page="order"]');
    await expect(root).toBeVisible();
    await expect(root.locator('.rm-system-breadcrumb > span[aria-hidden="true"]')).toHaveText([
      '/',
      '/',
    ]);
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
