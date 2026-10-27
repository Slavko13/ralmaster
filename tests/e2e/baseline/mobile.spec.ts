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

test('P1 @mobile primary request CTA reaches the contact form', async ({ page }) => {
  await page.goto('/', { waitUntil: 'domcontentloaded' });
  const trigger = page.getByRole('link', { name: /оставить заявку/i }).first();

  await expect(trigger).toBeVisible();
  await trigger.click();

  await expect(page).toHaveURL(/\/contacts\/#contacs_form$/);
  await expect(page.locator('form:visible').first()).toBeVisible();
});
