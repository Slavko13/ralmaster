import { expect, type Page, type TestInfo } from '@playwright/test';

const fatalMarkup = [
  /fatal error/i,
  /uncaught (?:error|exception)/i,
  /bitrix\\main\\db\\sqlqueryexception/i,
  /connection refused/i,
  /access denied for user/i,
];

export async function expectHealthyDocument(page: Page): Promise<void> {
  await expect(page.locator('body')).toBeVisible();
  const bodyText = (await page.locator('body').innerText()).trim();
  expect(bodyText.length).toBeGreaterThan(100);

  for (const pattern of fatalMarkup) {
    expect(bodyText, `Page contains fatal markup matching ${pattern}`).not.toMatch(pattern);
  }

  await expect(page).toHaveTitle(/\S+/);
}

export async function attachFullPageScreenshot(
  page: Page,
  testInfo: TestInfo,
  name: string,
): Promise<void> {
  await testInfo.attach(name, {
    body: await page.screenshot({ fullPage: true, animations: 'disabled' }),
    contentType: 'image/png',
  });
}
