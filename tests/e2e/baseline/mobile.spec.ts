import { expect, test } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

test('P1 @mobile mobile header is usable at 390px', async ({ page }) => {
  await page.goto('/', { waitUntil: 'domcontentloaded' });
  await expectHealthyDocument(page);

  const mobileHeader = page.locator('#mobileheader:visible');
  await expect(mobileHeader).toBeVisible();

  const menuTrigger = mobileHeader.locator('button.burger');
  await expect(menuTrigger).toBeVisible();
  await expect(menuTrigger).toHaveAttribute('aria-label', 'Открыть меню');
  await expect(menuTrigger).toHaveAttribute('aria-expanded', 'false');
  await menuTrigger.click();

  const mobileMenu = page.locator('#mobilemenu:visible');
  await expect(mobileMenu).toBeVisible();
  await expect(menuTrigger).toHaveAttribute('aria-expanded', 'true');
  await expect(menuTrigger).toHaveAttribute('aria-label', 'Закрыть меню');
  await expect(mobileMenu.locator('.rm-flat-menu__link')).toHaveCount(6);
  await expect(mobileMenu.locator('.dropdown, .dropdown-menu, .parent')).toHaveCount(0);
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
