import { expect, test } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

test('P1 search safely renders a result or empty state', async ({ page }) => {
  const response = await page.goto('/search/?q=%D0%BC%D0%B5%D1%82%D0%B0%D0%BB%D0%BB', {
    waitUntil: 'domcontentloaded',
  });

  expect(response?.status()).toBeLessThan(500);
  await expectHealthyDocument(page);
  await expect(page.locator('body')).toContainText(/металл|ничего не найдено|результат/i);
});

test('P0 contacts expose communication links and feedback UI', async ({ page }) => {
  await page.goto('/contacts/', { waitUntil: 'domcontentloaded' });
  await expectHealthyDocument(page);

  await expect(page.locator('a[href^="tel:"]:visible').first()).toBeVisible();
  await expect(page.locator('a[href^="mailto:"]:visible').first()).toBeVisible();
  await expect(
    page
      .locator(
        'form:visible, [data-rm-discuss-open]:visible, [data-event="jqm"]:visible, a[href="/form/"]:visible',
      )
      .first(),
  ).toBeVisible();
});

test('P0 an empty request is rejected without a successful submission', async ({ page }) => {
  const postRequests: string[] = [];
  page.on('request', (request) => {
    if (request.method() === 'POST') postRequests.push(request.url());
  });

  await page.goto('/', { waitUntil: 'domcontentloaded' });

  const trigger = page
    .locator('[data-rm-discuss-open]:visible, [data-event="jqm"]:visible, a[href="/form/"]:visible')
    .filter({ hasText: /заказ|рассчит|заявк|обсудить|вопрос/i })
    .first();

  await expect(trigger).toBeVisible();
  await trigger.click();

  const nativeDialog = page.locator('dialog[data-rm-discuss-dialog][open]').first();
  if (await nativeDialog.isVisible().catch(() => false)) {
    const submit = nativeDialog.locator('input[type="submit"], button[type="submit"]').first();
    await expect(submit).toBeVisible();
    await submit.click();
    await expect(nativeDialog.locator('[data-SID="CONTACT"] .rm-estimate-error')).toBeVisible();
    await expect(nativeDialog.locator('[data-SID="TASK"] .rm-estimate-error')).toBeVisible();
    expect(postRequests).toEqual([]);
    return;
  }

  if (/\/form\/?$/.test(new URL(page.url()).pathname)) {
    await expect(page.locator('form').first()).toBeVisible();
    return;
  }

  const dialog = page.locator('.jqmWindow:visible, [role="dialog"]:visible').first();
  await expect(dialog).toBeVisible();

  const submit = dialog.locator('button[type="submit"], input[type="submit"]').first();
  await expect(submit).toBeVisible();
  await submit.click();

  await expect(dialog).toBeVisible();
  await expect(dialog).not.toContainText(/успешно отправлен|спасибо.*заявк/i);
  expect(postRequests).toEqual([]);
});
