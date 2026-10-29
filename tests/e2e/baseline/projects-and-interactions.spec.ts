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
  const postRequests: string[] = [];
  page.on('request', (request) => {
    if (request.method() === 'POST') postRequests.push(request.url());
  });

  await page.goto('/', { waitUntil: 'domcontentloaded' });

  const candidateTriggers = page
    .locator('[data-rm-discuss-open]:visible, [data-event="jqm"]:visible')
    .filter({ hasText: /заказ|рассчит|заявк|обсудить|вопрос|консультац/i });
  const viewport = page.viewportSize();
  const findViewportTrigger = async () => {
    for (let index = 0; index < (await candidateTriggers.count()); index += 1) {
      const box = await candidateTriggers.nth(index).boundingBox();
      if (
        box &&
        viewport &&
        box.x < viewport.width &&
        box.y < viewport.height &&
        box.x + box.width > 0 &&
        box.y + box.height > 0
      ) {
        return index;
      }
    }

    return -1;
  };

  let triggerIndex = await findViewportTrigger();

  // The legacy homepage rotates CTAs between FlexSlider slides. Exercise the
  // real carousel controls until the request action is actually in view.
  if (triggerIndex < 0) {
    const heroControls = page.locator('.flex-control-nav').first().locator('a');
    for (let index = 0; index < (await heroControls.count()); index += 1) {
      await heroControls.nth(index).click();
      await page.waitForTimeout(400);
      triggerIndex = await findViewportTrigger();
      if (triggerIndex >= 0) break;
    }
  }

  expect(triggerIndex).toBeGreaterThanOrEqual(0);
  const trigger = candidateTriggers.nth(triggerIndex);

  await expect(trigger).toBeVisible();
  await trigger.click();

  const dialog = page
    .locator('dialog[data-rm-discuss-dialog][open], .jqmWindow:visible, [role="dialog"]:visible')
    .first();
  await expect(dialog).toBeVisible();
  await page.keyboard.press('Escape');
  await expect(dialog).toBeHidden();
  expect(postRequests).toEqual([]);
});
