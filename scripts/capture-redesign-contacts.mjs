import { mkdir } from 'node:fs/promises';
import { resolve } from 'node:path';
import { chromium } from '@playwright/test';

const baseUrl = process.env.BASE_URL ?? 'http://127.0.0.1:8084';
const outputRoot = resolve('.cache/redesign-contacts');

await mkdir(outputRoot, { recursive: true });

const browser = await chromium.launch();
try {
  const page = await browser.newPage({ viewport: { width: 1440, height: 1100 } });
  const diagnostics = [];
  page.on('console', (message) => {
    if (message.type() === 'error') diagnostics.push(`console: ${message.text()}`);
  });
  page.on('pageerror', (error) => diagnostics.push(`pageerror: ${error.message}`));

  await page.goto(`${baseUrl}/contacts/`, { waitUntil: 'domcontentloaded', timeout: 45_000 });
  await page.waitForTimeout(1_500);
  await page.evaluate(() => document.fonts.ready);
  await page.locator('.rm-contacts-photo img').scrollIntoViewIfNeeded();
  await page.evaluate(() => scrollTo(0, 0));

  const metrics = await page.evaluate(() => ({
    documentHeight: document.documentElement.scrollHeight,
    overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
    sections: Array.from(
      document.querySelectorAll('[data-rm-contacts-page] > [data-rm-contacts-section]'),
    ).map((node) => {
      const box = node.getBoundingClientRect();
      return {
        name: node.getAttribute('data-rm-contacts-section'),
        top: Math.round(box.top + scrollY),
        height: Math.round(box.height),
      };
    }),
  }));

  await page.screenshot({ path: resolve(outputRoot, 'desktop-1440-full.png'), fullPage: true });
  await page.locator('[data-rm-discuss-open="contacts"]').click();
  await page.locator('[data-rm-discuss-dialog]').screenshot({
    path: resolve(outputRoot, 'desktop-1440-discuss-order.png'),
  });

  console.log(JSON.stringify({ diagnostics, metrics }));
  await page.close();
} finally {
  await browser.close();
}
