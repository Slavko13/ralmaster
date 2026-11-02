import { expect, test } from '@playwright/test';

const navigation = [
  ['Услуги', '/services/'],
  ['Продукция', '/product/'],
  ['О компании', '/company/'],
  ['Проекты', '/projects/'],
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

  test('desktop header exposes five primary links and the services submenu', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    const header = page.locator('.rm-header');
    const links = header.locator('.rm-flat-menu__link');
    await expect(links).toHaveCount(5);
    await expect(links).toHaveText(navigation.map(([label]) => label));
    await expect(header.locator('.dropdown-menu, .dropdown-submenu, .table-menu')).toHaveCount(0);
    const services = header.locator('.rm-flat-menu__item--services');
    await expect(services.locator('.rm-flat-menu__submenu a')).toHaveText([
      'Металлообработка',
      'Порошковая окраска',
    ]);
    await expect(services.locator('.rm-flat-menu__submenu a').first()).toHaveAttribute(
      'href',
      '/services/',
    );
    await expect(services.locator('.rm-flat-menu__submenu a').last()).toHaveAttribute(
      'href',
      '/painting/',
    );
  });

  test('every desktop navigation item is a direct working link', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    for (const [label, path] of navigation) {
      await expect(
        page.locator('.rm-header').getByRole('link', { name: label, exact: true }),
      ).toHaveAttribute('href', path);
    }
  });

  test('partners page does not inherit the production active state', async ({ page }) => {
    await page.goto('/company/partners/', { waitUntil: 'domcontentloaded' });

    const productionItem = page
      .locator('.rm-header .rm-flat-menu__item')
      .filter({ hasText: 'О компании' });
    await expect(productionItem).not.toHaveClass(/\bactive\b/);
    await expect(productionItem.locator('a')).not.toHaveAttribute('aria-current', 'page');
  });

  test('P0 @mobile mobile menu remains accessible with visible service choices', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    const trigger = page.locator('#mobileheader button.burger');
    await expect(trigger).toHaveAttribute('aria-label', 'Открыть меню');
    await expect(trigger).toHaveAttribute('aria-expanded', 'false');
    await trigger.click();
    await expect(trigger).toHaveAttribute('aria-expanded', 'true');
    await expect(trigger).toHaveAttribute('aria-label', 'Закрыть меню');

    const menu = page.locator('#mobilemenu');
    await expect(menu.locator('.rm-flat-menu__link')).toHaveCount(5);
    await expect(menu.locator('.rm-flat-menu__submenu a')).toHaveText([
      'Металлообработка',
      'Порошковая окраска',
    ]);
    await expect(menu.locator('.dropdown, .dropdown-menu, .parent')).toHaveCount(0);
  });

  test('desktop basket reflects an item added from the live catalog', async ({ page }) => {
    await page.goto('/product/pannier/', { waitUntil: 'networkidle' });
    const item = page.locator('[data-rm-product-category] .catalog_table_2 .item').first();
    await expect(item).toBeVisible();
    const productName = (await item.locator('.title').innerText()).trim();
    const basketRequest = page.waitForResponse(
      (response) =>
        response.request().method() === 'POST' &&
        response.url().includes('/include/footer/site-basket.php'),
    );
    await item.locator('.to_cart').click();
    await basketRequest;

    const basket = page.locator('.rm-header .rm-header__basket');
    await expect(basket).toBeVisible();
    await expect(basket.locator('.basket-link')).toHaveAttribute('href', '/cart/');
    await expect(basket.locator('.count')).not.toHaveText('0');

    await basket.locator('.basket-link').click();
    await expect(page).toHaveURL(/\/cart\/$/);
    await expect(page.getByText(productName, { exact: false }).first()).toBeVisible();
  });
});
