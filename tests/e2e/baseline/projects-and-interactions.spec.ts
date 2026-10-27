import { expect, test } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

test('P0 projects expose a working detail route', async ({ page }) => {
  await page.goto('/projects/', { waitUntil: 'domcontentloaded' });
  await expectHealthyDocument(page);

  const detailLink = page
    .locator(
      'main a[href^="/projects/"]:not([href="/projects/"]):visible, [role="main"] a[href^="/projects/"]:not([href="/projects/"]):visible, .main a[href^="/projects/"]:not([href="/projects/"]):visible',
    )
    .first();

  await expect(detailLink).toBeVisible();
  const href = await detailLink.getAttribute('href');
  expect(href).toBeTruthy();

  const response = await page.goto(href!, { waitUntil: 'domcontentloaded' });
  expect(response?.status()).toBeLessThan(500);
  await expectHealthyDocument(page);
  await expect(page.locator('a[href="/projects/"]:visible').first()).toBeVisible();
});

test('P0 request modal closes with Escape without submitting data', async ({ page }) => {
  await page.goto('/', { waitUntil: 'domcontentloaded' });

  const trigger = page
    .locator('[data-event="jqm"]:visible')
    .filter({ hasText: /заказ|рассчит|заявк|обсудить|вопрос/i })
    .first();

  await expect(trigger).toBeVisible();
  await trigger.click();

  const dialog = page.locator('.jqmWindow:visible, [role="dialog"]:visible').first();
  await expect(dialog).toBeVisible();
  await page.keyboard.press('Escape');
  await expect(dialog).toBeHidden();
});
