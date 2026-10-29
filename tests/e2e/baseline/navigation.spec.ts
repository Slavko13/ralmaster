import { expect, test } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const navigationItems = [
  { name: /компания|производств/i, path: '/company/' },
  { name: /каталог|продукция/i, path: '/product/' },
  { name: /услуги|металлообработк/i, path: '/services/' },
  { name: /покраска|порошков/i, path: '/painting/' },
  { name: /проекты|реализованн/i, path: '/projects/' },
  { name: /контакты/i, path: '/contacts/' },
] as const;

test.describe('P0 desktop navigation', () => {
  for (const item of navigationItems) {
    test(`header link opens ${item.path}`, async ({ page }) => {
      await page.goto('/', { waitUntil: 'domcontentloaded' });
      const link = page.locator('a:visible').filter({ hasText: item.name }).first();

      await expect(link).toBeVisible();
      await Promise.all([
        page.waitForURL((url) => url.pathname.startsWith(item.path)),
        link.click(),
      ]);
      await expectHealthyDocument(page);
    });
  }
});

test('P0 catalog exposes a detail route', async ({ page }) => {
  await page.goto('/product/', { waitUntil: 'domcontentloaded' });
  const detailLink = page
    .locator(
      'main a[href^="/product/"]:not([href="/product/"]):visible, [role="main"] a[href^="/product/"]:not([href="/product/"]):visible, .main a[href^="/product/"]:not([href="/product/"]):visible',
    )
    .first();

  await expect(detailLink).toBeVisible();
  const href = await detailLink.getAttribute('href');
  expect(href).toBeTruthy();
  const response = await page.goto(href!, { waitUntil: 'domcontentloaded' });
  expect(response?.status()).toBeLessThan(500);
  await expectHealthyDocument(page);
});

test('P0 services expose a detail route', async ({ page }) => {
  await page.goto('/services/', { waitUntil: 'domcontentloaded' });
  const detailLink = page
    .locator(
      'main a[href^="/services/"]:not([href="/services/"]):visible, [role="main"] a[href^="/services/"]:not([href="/services/"]):visible, .main a[href^="/services/"]:not([href="/services/"]):visible',
    )
    .first();

  await expect(detailLink).toBeVisible();
  const href = await detailLink.getAttribute('href');
  expect(href).toBeTruthy();
  const response = await page.goto(href!, { waitUntil: 'domcontentloaded' });
  expect(response?.status()).toBeLessThan(500);
  await expectHealthyDocument(page);
});
