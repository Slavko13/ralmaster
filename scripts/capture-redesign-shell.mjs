import { mkdir } from 'node:fs/promises';
import { resolve } from 'node:path';
import { chromium } from '@playwright/test';

const baseUrl = process.env.BASE_URL ?? 'http://127.0.0.1:8084';
const outputRoot = resolve('test-results/redesign-shell');
const viewports = [
  { name: 'desktop-1440', width: 1440, height: 1100 },
  { name: 'mobile-390', width: 390, height: 844 },
];

await mkdir(outputRoot, { recursive: true });

const browser = await chromium.launch();
try {
  for (const viewport of viewports) {
    const page = await browser.newPage({ viewport });
    await page.goto(baseUrl, { waitUntil: 'domcontentloaded' });
    await page.waitForLoadState('networkidle').catch(() => {});

    const metrics = await page.evaluate(() => {
      const read = (selector) => {
        const element = document.querySelector(selector);
        if (!element) return null;
        const rect = element.getBoundingClientRect();
        const style = getComputedStyle(element);
        return {
          selector,
          x: Math.round(rect.x * 100) / 100,
          y: Math.round(rect.y * 100) / 100,
          width: Math.round(rect.width * 100) / 100,
          height: Math.round(rect.height * 100) / 100,
          display: style.display,
          color: style.color,
          backgroundColor: style.backgroundColor,
          fontFamily: style.fontFamily,
          fontSize: style.fontSize,
        };
      };

      return {
        viewport: { width: innerWidth, height: innerHeight },
        document: {
          scrollWidth: document.documentElement.scrollWidth,
          clientWidth: document.documentElement.clientWidth,
        },
        header: read('.rm-header'),
        mobileHeader: read('#mobileheader'),
        logo: read('.rm-header__logo'),
        nav: read('.rm-header__nav'),
        tools: read('.rm-header__tools'),
        phone: read('.rm-header__phone'),
        footer: read('.rm-footer'),
      };
    });

    console.log(JSON.stringify({ name: viewport.name, metrics }));

    await page.screenshot({
      path: resolve(outputRoot, `${viewport.name}-top.png`),
      fullPage: false,
    });

    const footer = page.locator('.rm-footer').first();
    if (await footer.count()) {
      await footer.screenshot({
        path: resolve(outputRoot, `${viewport.name}-footer.png`),
      });
    }

    await page.close();
  }
} finally {
  await browser.close();
}
