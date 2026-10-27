import { expect, test } from '@playwright/test';
import {
  attachFullPageScreenshot,
  expectHealthyDocument,
} from '../helpers/site';

const primaryRoutes = [
  { path: '/', label: 'Главная' },
  { path: '/company/', label: 'Компания' },
  { path: '/product/', label: 'Каталог' },
  { path: '/services/', label: 'Услуги' },
  { path: '/painting/', label: 'Покраска' },
  { path: '/projects/', label: 'Проекты' },
  { path: '/contacts/', label: 'Контакты' },
] as const;

const secondaryRoutes = [
  '/painting/poroshkovaya-pokraska-metalla/',
  '/product/baskets/',
  '/services/metal-bending/',
  '/projects/zhk-simvol/',
  '/search/',
  '/search/map.php',
  '/cart/',
  '/cart/order/',
  '/cabinet/',
  '/cabinet/registration/',
  '/cabinet/forgot-password/',
  '/login/',
  '/info/news/',
  '/info/articles/',
  '/info/faq/',
  '/info/sales/',
  '/company/partners/',
  '/company/reviews/',
  '/company/staff/',
  '/company/vacancy/',
  '/products/',
  '/news/',
  '/landings/',
] as const;

test.describe('P0 primary route smoke', () => {
  for (const route of primaryRoutes) {
    test(`${route.label}: ${route.path} loads without a fatal error`, async ({ page }, testInfo) => {
      const response = await page.goto(route.path, { waitUntil: 'domcontentloaded' });

      expect(response, `No main-document response for ${route.path}`).not.toBeNull();
      expect(response!.status(), `Unexpected HTTP status for ${route.path}`).toBeLessThan(500);
      await expectHealthyDocument(page);
      await attachFullPageScreenshot(page, testInfo, `baseline-${route.label}`);
    });
  }
});

test.describe('P1 secondary and legacy route smoke', () => {
  for (const path of secondaryRoutes) {
    test(`${path} remains reachable`, async ({ page }) => {
      const response = await page.goto(path, { waitUntil: 'domcontentloaded' });

      expect(response, `No main-document response for ${path}`).not.toBeNull();
      expect(response!.status(), `Unexpected HTTP status for ${path}`).toBeLessThan(400);
      await expectHealthyDocument(page);
    });
  }
});

test('P1 direct form route rejects a missing form identifier without a fatal error', async ({ page }) => {
  const response = await page.goto('/form/', { waitUntil: 'domcontentloaded' });

  expect(response).not.toBeNull();
  expect(response!.status()).toBe(404);
  await expectHealthyDocument(page);
  await expect(page.locator('body')).toContainText(/страниц[аы].*(?:не найдена|отсутствует)|404/i);
});

test('P0 unknown route renders the site not-found state', async ({ page }) => {
  const response = await page.goto('/__codex_baseline_missing_page__/', {
    waitUntil: 'domcontentloaded',
  });

  expect(response).not.toBeNull();
  expect([200, 404]).toContain(response!.status());
  await expectHealthyDocument(page);
  await expect(page.locator('body')).toContainText(/страниц[аы].*(?:не найдена|отсутствует)|404/i);
});
