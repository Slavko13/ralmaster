import { expect, test } from '@playwright/test';

test.describe('P0 redesigned desktop header menus', () => {
  for (const route of ['/', '/painting/poroshkovaya-pokraska-metalla/']) {
    test(`legacy fixed header stays disabled after scrolling ${route}`, async ({ page }) => {
      await page.goto(route, { waitUntil: 'domcontentloaded' });

      await expect(page.locator('.rm-header[data-rm-shell="header"]')).toBeVisible();
      await expect(page.locator('#headerfixed')).toHaveCount(0);

      await page.evaluate(() => window.scrollTo(0, 900));
      await page.waitForTimeout(450);

      await expect(page.locator('#headerfixed')).toHaveCount(0);
      await expect(page.locator('#headerfixed.fixed')).toHaveCount(0);

      if (route === '/painting/poroshkovaya-pokraska-metalla/') {
        await page.screenshot({
          path: 'test-results/sticky-audit-painting-after-fix-scroll900.png',
        });
      }
    });
  }

  test('wide product menu is laid out as a usable panel', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    const header = page.locator('.rm-header');
    const middle = header.locator('.rm-header__middle');
    const productLink = header.getByRole('link', { name: 'Продукция', exact: true }).first();
    const productItem = productLink.locator('xpath=ancestor::td[contains(@class,"menu-item")][1]');
    const productMenu = productItem.locator(':scope > .wrap > .dropdown-menu');

    await productLink.hover();
    await expect(productMenu).toBeVisible();
    await expect(productMenu.locator(':scope > li')).toHaveCount(7);
    await expect(middle).toHaveCSS('background-color', 'rgba(0, 0, 0, 0)');

    const geometry = await productMenu.evaluate((node) => {
      const rect = node.getBoundingClientRect();
      const firstItem = node.querySelector<HTMLElement>(':scope > li');
      const firstRect = firstItem?.getBoundingClientRect();

      return {
        left: Math.round(rect.left),
        right: Math.round(rect.right),
        top: Math.round(rect.top),
        width: Math.round(rect.width),
        firstItemWidth: firstRect ? Math.round(firstRect.width) : 0,
        viewportWidth: document.documentElement.clientWidth,
      };
    });

    expect(geometry.left).toBeGreaterThanOrEqual(0);
    expect(geometry.right).toBeLessThanOrEqual(geometry.viewportWidth + 1);
    expect(geometry.top).toBeGreaterThanOrEqual(100);
    expect(geometry.width).toBeGreaterThan(700);
    expect(geometry.firstItemWidth).toBeGreaterThan(150);

    await productLink.focus();
    await expect(productMenu).toBeVisible();
  });

  test('metalworking dropdown remains open while moving to metal bending', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    const header = page.locator('.rm-header');
    const serviceLink = header.getByRole('link', { name: 'Металлообработка', exact: true }).first();
    const serviceItem = serviceLink.locator('xpath=ancestor::td[contains(@class,"menu-item")][1]');
    const serviceMenu = serviceItem.locator(':scope > .wrap > .dropdown-menu');
    const bendingLink = serviceMenu.getByRole('link', { name: /Гибка металла/i }).first();

    await serviceLink.hover();
    await expect(serviceMenu).toBeVisible();
    await expect(bendingLink).toBeVisible();

    const target = await bendingLink.boundingBox();
    expect(target).not.toBeNull();
    await page.mouse.move(target!.x + target!.width / 2, target!.y + target!.height / 2, {
      steps: 12,
    });

    await expect(serviceMenu).toBeVisible();
    await expect(bendingLink).toBeVisible();

    await page.mouse.click(target!.x + target!.width / 2, target!.y + target!.height / 2);
    await page.waitForURL('**/services/metal-bending/**');
    await expect(page).toHaveURL(/\/services\/metal-bending\/$/);
  });
});
