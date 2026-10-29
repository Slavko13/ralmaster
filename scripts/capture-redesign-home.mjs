import { mkdir } from 'node:fs/promises';
import { resolve } from 'node:path';
import { chromium } from '@playwright/test';

const baseUrl = process.env.BASE_URL ?? 'http://127.0.0.1:8084';
const outputRoot = resolve('test-results/redesign-home');
const viewports = [
  { name: 'desktop-1440', width: 1440, height: 1100 },
  { name: 'mobile-390', width: 390, height: 844 },
];

await mkdir(outputRoot, { recursive: true });

const browser = await chromium.launch();
try {
  for (const viewport of viewports) {
    const page = await browser.newPage({ viewport });
    const diagnostics = [];

    page.on('console', (message) => {
      if (message.type() === 'error') diagnostics.push(`console: ${message.text()}`);
    });
    page.on('pageerror', (error) => diagnostics.push(`pageerror: ${error.message}`));

    await page.goto(baseUrl, { waitUntil: 'domcontentloaded' });
    await page.waitForLoadState('networkidle').catch(() => {});
    await page.evaluate(() => document.fonts.ready);

    const images = page.locator('[data-rm-home] img');
    for (let index = 0; index < await images.count(); index += 1) {
      await images.nth(index).scrollIntoViewIfNeeded();
    }
    await page.evaluate(() => scrollTo(0, 0));

    const metrics = await page.evaluate(() => {
      const sections = Array.from(document.querySelectorAll('[data-rm-section]')).map((node) => {
        const rect = node.getBoundingClientRect();
        return {
          name: node.getAttribute('data-rm-section'),
          top: Math.round(rect.top + scrollY),
          width: Math.round(rect.width),
          height: Math.round(rect.height),
        };
      });

      return {
        viewport: { width: innerWidth, height: innerHeight },
        document: {
          clientWidth: document.documentElement.clientWidth,
          scrollWidth: document.documentElement.scrollWidth,
          scrollHeight: document.documentElement.scrollHeight,
        },
        header: Math.round(document.querySelector('.rm-header')?.getBoundingClientRect().height ?? 0),
        mobileHeader: Math.round(document.querySelector('#mobileheader')?.getBoundingClientRect().height ?? 0),
        footer: Math.round(document.querySelector('.rm-footer')?.getBoundingClientRect().height ?? 0),
        sections,
      };
    });

    console.log(JSON.stringify({ name: viewport.name, diagnostics, metrics }));

    await page.screenshot({
      path: resolve(outputRoot, `${viewport.name}-full.png`),
      fullPage: true,
    });

    for (const sectionName of ['hero', 'order']) {
      const section = page.locator(`[data-rm-section="${sectionName}"]`).first();
      if (await section.count()) {
        await section.screenshot({
          path: resolve(outputRoot, `${viewport.name}-${sectionName}.png`),
        });
      }
    }

    await page.close();
  }
} finally {
  await browser.close();
}
