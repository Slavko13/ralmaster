import { expect, test } from '@playwright/test';

const navigation = [
  ['Продукция', '/product/'],
  ['Металлообработка', '/services/'],
  ['Порошковая окраска', '/painting/'],
  ['О производстве', '/company/'],
  ['Реализованные проекты', '/projects/'],
  ['Контакты', '/contacts/'],
] as const;

test.describe('P0 Paper flat navigation', () => {
  for (const route of ['/', '/painting/poroshkovaya-pokraska-metalla/']) {
    test(`legacy fixed header stays disabled after scrolling ${route}`, async ({ page }) => {
      await page.goto(route, { waitUntil: 'domcontentloaded' });

      await expect(page.locator('.rm-header[data-rm-shell="header"]')).toBeVisible();
      await expect(page.locator('#headerfixed')).toHaveCount(0);

      await page.evaluate(() => window.scrollTo(0, 900));
      await page.waitForTimeout(450);

      await expect(page.locator('#headerfixed')).toHaveCount(0);
      await expect(page.locator('#headerfixed.fixed')).toHaveCount(0);
    });
  }

  test('desktop header exposes exactly six direct links without dropdown markup', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    const header = page.locator('.rm-header');
    const links = header.locator('.rm-flat-menu__link');
    await expect(links).toHaveCount(6);
    await expect(links).toHaveText(navigation.map(([label]) => label));
    await expect(header.locator('.dropdown-menu, .dropdown-submenu, .table-menu')).toHaveCount(0);
  });

  test('every desktop navigation item is a direct working link', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    for (const [label, path] of navigation) {
      await expect(
        page.locator('.rm-header').getByRole('link', { name: label, exact: true }),
      ).toHaveAttribute('href', path);
    }
  });

  test('P0 @mobile mobile menu remains flat and accessible', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    const trigger = page.locator('#mobileheader button.burger');
    await expect(trigger).toHaveAttribute('aria-label', 'Открыть меню');
    await expect(trigger).toHaveAttribute('aria-expanded', 'false');
    await trigger.click();
    await expect(trigger).toHaveAttribute('aria-expanded', 'true');
    await expect(trigger).toHaveAttribute('aria-label', 'Закрыть меню');

    const menu = page.locator('#mobilemenu');
    await expect(menu.locator('.rm-flat-menu__link')).toHaveCount(6);
    await expect(menu.locator('.dropdown, .dropdown-menu, .parent, [class*="submenu"]')).toHaveCount(0);
  });
});
