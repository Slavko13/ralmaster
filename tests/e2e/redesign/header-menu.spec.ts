import { expect, test } from '@playwright/test';

const navigation = [
  ['Услуги', '/services/'],
  ['Продукция', '/product/'],
  ['О производстве', '/company/'],
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

    await services.hover();
    const submenuRows = services.locator('.rm-flat-menu__submenu a');
    const geometry = await services.locator('.rm-flat-menu__submenu').evaluate((menu) => {
      const menuBox = menu.getBoundingClientRect();
      const rows = Array.from(menu.querySelectorAll('a')).map((row) => {
        const box = row.getBoundingClientRect();
        return { left: box.left, right: box.right };
      });
      return { left: menuBox.left, right: menuBox.right, rows };
    });
    expect(geometry.rows.every((row) => Math.abs(row.left - geometry.left) <= 1)).toBe(true);
    expect(geometry.rows.every((row) => Math.abs(row.right - geometry.right) <= 1)).toBe(true);

    await submenuRows.first().hover();
    await page.waitForTimeout(220);
    const backgrounds = await submenuRows.evaluateAll((rows) =>
      rows.map((row) => getComputedStyle(row).backgroundColor),
    );
    expect(backgrounds[0]).not.toBe(backgrounds[1]);
  });

  test('every desktop navigation item is a direct working link', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    for (const [label, path] of navigation) {
      await expect(
        page.locator('.rm-header').getByRole('link', { name: label, exact: true }),
      ).toHaveAttribute('href', path);
    }
  });

  test('desktop shell uses the approved vector logos and linked footer credit', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    const darkLogo = page.locator('.rm-header__logo-image--dark');
    const lightLogo = page.locator('.rm-header__logo-image--light');
    await expect(darkLogo).toHaveAttribute('src', /ralmaster-logo-dark\.svg$/);
    await expect(lightLogo).toHaveAttribute('src', /ralmaster-logo-light\.svg$/);
    await expect(darkLogo).toHaveCSS('opacity', '0');
    await expect(lightLogo).toHaveCSS('opacity', '1');

    const footer = page.locator('.rm-footer');
    await expect(footer.locator('.rm-footer__logo img')).toHaveAttribute(
      'src',
      /ralmaster-logo-light\.svg$/,
    );
    const dualweb = footer.getByRole('link', { name: 'Dualweb', exact: true });
    await expect(dualweb).toHaveAttribute('href', 'https://dualweb.ru/');
    await expect(dualweb).toHaveAttribute('target', '_blank');
    await expect(dualweb).toHaveAttribute('rel', /noopener/);
    await expect(footer.locator('.rm-footer__social')).toHaveCSS('margin-top', '28px');

    await page.goto('/company/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('.rm-header__logo-image--dark')).toHaveCSS('opacity', '1');
    await expect(page.locator('.rm-header__logo-image--light')).toHaveCSS('opacity', '0');
  });

  test('partners page does not inherit the production active state', async ({ page }) => {
    await page.goto('/company/partners/', { waitUntil: 'domcontentloaded' });

    const productionItem = page
      .locator('.rm-header .rm-flat-menu__item')
      .filter({ hasText: 'О производстве' });
    await expect(productionItem).not.toHaveClass(/\bactive\b/);
    await expect(productionItem.locator('a')).not.toHaveAttribute('aria-current', 'page');
  });

  test('P0 @mobile mobile menu opens services on intent and highlights a full submenu row', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    const trigger = page.locator('#mobileheader button.burger');
    await expect(trigger).toHaveAttribute('aria-label', 'Открыть меню');
    await expect(trigger).toHaveAttribute('aria-expanded', 'false');
    await trigger.click();
    await expect(trigger).toHaveAttribute('aria-expanded', 'true');
    await expect(trigger).toHaveAttribute('aria-label', 'Закрыть меню');

    const menu = page.locator('#mobilemenu');
    await expect(menu.locator('.rm-flat-menu__link')).toHaveCount(5);
    const services = menu.locator('.rm-flat-menu__item--services');
    const servicesTrigger = services.locator(':scope > .rm-flat-menu__link');
    const chevron = servicesTrigger.locator('.rm-flat-menu__chevron');
    const submenu = services.locator('.rm-flat-menu__submenu');
    await expect(submenu).toBeHidden();
    await expect(servicesTrigger).toHaveAttribute('aria-expanded', 'false');
    const closedChevronTransform = await chevron.evaluate((node) => getComputedStyle(node).transform);
    await servicesTrigger.click();
    await expect(servicesTrigger).toHaveAttribute('aria-expanded', 'true');
    await expect(submenu).toBeVisible();
    const openChevronTransform = await chevron.evaluate((node) => getComputedStyle(node).transform);
    expect(openChevronTransform).not.toBe(closedChevronTransform);
    await expect(submenu.locator('a')).toHaveText([
      'Металлообработка',
      'Порошковая окраска',
    ]);
    const rowGeometry = await submenu.locator('a').first().evaluate((row) => {
      const rowBox = row.getBoundingClientRect();
      const menuBox = row.closest('ul')!.getBoundingClientRect();
      return {
        leftDelta: Math.round(rowBox.left - menuBox.left),
        rightDelta: Math.round(menuBox.right - rowBox.right),
      };
    });
    expect(rowGeometry).toEqual({ leftDelta: 0, rightDelta: 0 });
    await expect(menu.locator('.dropdown, .dropdown-menu, .parent')).toHaveCount(0);
  });

  test('P0 @mobile mobile header uses the approved dark vector logo', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('#mobileheader .rm-mobile-header__logo img')).toHaveAttribute(
      'src',
      /ralmaster-logo-dark\.svg$/,
    );
  });

  test('P0 @mobile cart icon stays black and aligned without search on the cart route', async ({ page }) => {
    await page.goto('/cart/', { waitUntil: 'domcontentloaded' });
    const basket = page.locator('#mobileheader .rm-mobile-basket');
    const link = basket.locator('.basket-link');
    await expect(link).toBeVisible();
    await expect(link).toHaveAttribute('aria-current', 'page');
    await expect(link).toHaveCSS('color', 'rgb(24, 35, 55)');
    await expect(link).toHaveCSS('background-color', 'rgba(0, 0, 0, 0)');
    await expect(basket.locator('.count')).toBeHidden();
    await expect(page.locator('#mobileheader .wrap_search, #mobileheader .search_wrap')).toHaveCount(0);

    const alignment = await page.locator('#mobileheader .rm-mobile-header').evaluate((header) => {
      const basketBox = header.querySelector('.rm-mobile-basket')!.getBoundingClientRect();
      const headerBox = header.getBoundingClientRect();
      return {
        centerDelta: Math.round(Math.abs((basketBox.top + basketBox.bottom) / 2 - (headerBox.top + headerBox.bottom) / 2)),
        rightGap: Math.round(headerBox.right - basketBox.right),
      };
    });
    expect(alignment.centerDelta).toBeLessThanOrEqual(1);
    expect(alignment.rightGap).toBeGreaterThanOrEqual(16);
  });

  test('P0 @mobile mobile menu uses the blue basket badge for live catalog items', async ({ page }) => {
    await page.goto('/product/pannier/', { waitUntil: 'networkidle' });
    const item = page.locator('[data-rm-product-category] .catalog_table_2 .item').first();
    await expect(item).toBeVisible();
    const basketRequest = page.waitForResponse(
      (response) =>
        response.request().method() === 'POST' &&
        response.url().includes('/include/footer/site-basket.php'),
    );
    await item.locator('.to_cart').click();
    await basketRequest;

    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await page.locator('#mobileheader button.burger').click();
    const badge = page.locator('#mobilemenu .menu > ul > li.counters .count');
    await expect(badge).toBeVisible();
    await expect(badge).not.toHaveText('0');
    await expect(badge).toHaveCSS('background-color', 'rgb(23, 91, 239)');
    await expect(badge).toHaveCSS('color', 'rgb(255, 255, 255)');
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
    const catalogBasketAppearance = await basket.locator('.basket-link').evaluate((node) => {
      const style = getComputedStyle(node);
      return { color: style.color, background: style.backgroundColor };
    });
    expect(catalogBasketAppearance).toEqual({
      color: 'rgb(24, 35, 55)',
      background: 'rgba(0, 0, 0, 0)',
    });

    await basket.locator('.basket-link').click();
    await expect(page).toHaveURL(/\/cart\/$/);
    await expect(page.getByText(productName, { exact: false }).first()).toBeVisible();
    const cartBasket = page.locator('.rm-header .rm-header__basket');
    const cartBasketAppearance = await cartBasket.locator('.basket-link').evaluate((node) => {
      const style = getComputedStyle(node);
      return { color: style.color, background: style.backgroundColor };
    });
    expect(cartBasketAppearance).toEqual(catalogBasketAppearance);
    await expect(cartBasket.locator('.count')).toHaveCSS('background-color', 'rgb(23, 91, 239)');
    await expect(cartBasket.locator('.count')).toHaveCSS('color', 'rgb(255, 255, 255)');
  });

  test('keeps the cart icon visible while hiding a zero badge', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    const homeBasket = page.locator('.rm-header .rm-header__basket');
    await expect(homeBasket.locator('.basket-link')).toBeVisible();
    await expect(homeBasket.locator('.count')).toBeHidden();
    const homeIconPath = await homeBasket.locator('.rm-header__basket-icon path').getAttribute('d');

    await page.goto('/cart/', { waitUntil: 'domcontentloaded' });
    const cartBasket = page.locator('.rm-header .rm-header__basket');
    await expect(cartBasket.locator('.basket-link')).toBeVisible();
    await expect(cartBasket.locator('.rm-header__basket-icon')).toBeVisible();
    await expect(cartBasket.locator('.count')).toBeHidden();
    await expect(cartBasket.locator('.rm-header__basket-icon path')).toHaveAttribute('d', homeIconPath!);
  });

  test('basket uses one icon and transparent blue-hover treatment on dark and white headers', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    const homeBasket = page.locator('.rm-header .rm-header__basket');
    const homeBasketLink = homeBasket.locator('.basket-link');
    await expect(homeBasket).toHaveCSS('background-color', 'rgba(0, 0, 0, 0)');
    await expect(homeBasketLink).toHaveCSS('color', 'rgb(255, 255, 255)');
    await homeBasketLink.hover();
    await expect(homeBasket).toHaveCSS('background-color', 'rgba(0, 0, 0, 0)');
    await expect(homeBasketLink).toHaveCSS('color', 'rgb(23, 91, 239)');
    await expect(homeBasketLink).toHaveCSS('background-color', 'rgba(0, 0, 0, 0)');

    await page.goto('/cart/', { waitUntil: 'domcontentloaded' });
    const cartBasket = page.locator('.rm-header .rm-header__basket');
    const cartBasketLink = cartBasket.locator('.basket-link');
    await expect(cartBasket).toHaveCSS('background-color', 'rgba(0, 0, 0, 0)');
    await expect(cartBasketLink).toHaveCSS('color', 'rgb(24, 35, 55)');
    await cartBasketLink.hover();
    await expect(cartBasket).toHaveCSS('background-color', 'rgba(0, 0, 0, 0)');
    await expect(cartBasketLink).toHaveCSS('color', 'rgb(23, 91, 239)');
    await expect(cartBasketLink).toHaveCSS('background-color', 'rgba(0, 0, 0, 0)');
  });
});
