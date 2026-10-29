import { expect, test } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

test('P1 @mobile mobile header is usable at 390px', async ({ page }) => {
  await page.goto('/', { waitUntil: 'domcontentloaded' });
  await expectHealthyDocument(page);

  const mobileHeader = page.locator('#mobileheader:visible');
  await expect(mobileHeader).toBeVisible();

  // The legacy Aspro control has no accessible name; this stable template
  // class is recorded as baseline debt and will be replaced during redesign.
  const menuTrigger = mobileHeader.locator('.burger:visible').first();
  await expect(menuTrigger).toBeVisible();
  await menuTrigger.click();

  await expect(
    page.locator('#mobilemenu:visible, nav:visible, [role="navigation"]:visible').first(),
  ).toBeVisible();
});

test('P1 @mobile primary request CTA opens the request form without submitting', async ({ page }) => {
  const postRequests: string[] = [];
  page.on('request', (request) => {
    if (request.method() === 'POST') postRequests.push(request.url());
  });

  await page.goto('/', { waitUntil: 'domcontentloaded' });
  const trigger = page
    .locator('[data-rm-section="hero"] [data-rm-discuss-open="home"]')
    .filter({ hasText: /рассчитать заказ/i })
    .first();

  await expect(trigger).toBeVisible();
  await trigger.click();

  const dialog = page.locator('dialog[data-rm-discuss-variant="home"][open]');
  await expect(dialog).toBeVisible();
  const geometry = await dialog.evaluate((node) => {
    const rect = node.getBoundingClientRect();
    return { left: rect.left, right: rect.right, viewport: document.documentElement.clientWidth };
  });
  expect(geometry.left).toBeGreaterThanOrEqual(0);
  expect(geometry.right).toBeLessThanOrEqual(geometry.viewport + 1);
  expect(postRequests).toEqual([]);
  await page.keyboard.press('Escape');
  await expect(dialog).toBeHidden();
});
