import { mkdir } from 'node:fs/promises';
import { resolve } from 'node:path';
import { chromium } from '@playwright/test';

const baseUrl = process.env.BASE_URL ?? 'http://127.0.0.1:8084';
const outputRoot = resolve('test-results/redesign-pages');
const pageCatalog = [
  { name: 'home', path: '/' },
  { name: 'products', path: '/product/' },
  { name: 'metalworking', path: '/services/' },
  { name: 'metal-bending', path: '/services/metal-bending/' },
  { name: 'powder', path: '/painting/' },
  { name: 'production', path: '/company/' },
  { name: 'projects', path: '/projects/' },
  { name: 'project-generic', path: '/projects/zhk-belyy-grad/' },
  { name: 'project-symbol', path: '/projects/zhk-simvol/' },
  { name: 'contacts', path: '/contacts/' },
];
const requestedPage = process.argv[2] || process.env.CAPTURE_PAGE;
const pages = requestedPage
  ? pageCatalog.filter((item) => item.name === requestedPage)
  : pageCatalog;
const viewports = [
  { name: 'desktop-1440', width: 1440, height: 1100 },
  { name: 'mobile-390', width: 390, height: 844 },
];

await mkdir(outputRoot, { recursive: true });

const browser = await chromium.launch();
try {
  for (const pageSpec of pages) {
    for (const viewport of viewports) {
      const page = await browser.newPage({ viewport });
      const diagnostics = [];
      page.on('console', (message) => {
        if (message.type() === 'error') diagnostics.push(`console: ${message.text()}`);
      });
      page.on('pageerror', (error) => diagnostics.push(`pageerror: ${error.message}`));

      await page.goto(`${baseUrl}${pageSpec.path}`, { waitUntil: 'domcontentloaded' });
      await page.waitForLoadState('networkidle').catch(() => {});
      await page.evaluate(() => document.fonts.ready);

      const images = page.locator('main img, [role="main"] img, .main img');
      for (let index = 0; index < await images.count(); index += 1) {
        await images.nth(index).scrollIntoViewIfNeeded().catch(() => {});
      }
      await page.evaluate(() => scrollTo(0, 0));

      const metrics = await page.evaluate(() => {
        const visible = (node) => {
          if (!(node instanceof HTMLElement)) return false;
          const style = getComputedStyle(node);
          const rect = node.getBoundingClientRect();
          return style.display !== 'none' && style.visibility !== 'hidden' && rect.width > 0 && rect.height > 0;
        };
        const estimate = Array.from(document.querySelectorAll('[data-rm-estimate-form]'))
          .find((node) => visible(node)) ?? null;
        const sectionSelector = [
          '[data-rm-section]',
          '[data-rm-powder-section]',
          '[data-rm-production-section]',
          '[data-rm-projects-section]',
          '[data-rm-project-section]',
          '[data-rm-project-detail-section]',
          '[data-rm-contact-section]',
          '[data-rm-contacts-section]',
        ].join(',');
        const sections = Array.from(document.querySelectorAll(sectionSelector)).map((node) => {
          const rect = node.getBoundingClientRect();
          return {
            name: node.getAttribute('data-rm-section')
              ?? node.getAttribute('data-rm-powder-section')
              ?? node.getAttribute('data-rm-production-section')
              ?? node.getAttribute('data-rm-projects-section')
              ?? node.getAttribute('data-rm-project-section')
              ?? node.getAttribute('data-rm-project-detail-section')
              ?? node.getAttribute('data-rm-contact-section')
              ?? node.getAttribute('data-rm-contacts-section'),
            top: Math.round(rect.top + scrollY),
            width: Math.round(rect.width),
            height: Math.round(rect.height),
          };
        });
        const relativeBox = (root, selector) => {
          const node = root?.querySelector(selector);
          if (!(root instanceof HTMLElement) || !(node instanceof HTMLElement)) return null;
          var rootRect = root.getBoundingClientRect();
          var rect = node.getBoundingClientRect();
          return {
            top: Math.round(rect.top - rootRect.top),
            bottom: Math.round(rect.bottom - rootRect.top),
            height: Math.round(rect.height),
            clipped: rect.bottom > rootRect.bottom + 1,
          };
        };

        const contextNode = estimate?.querySelector('[data-SID="CONTEXT"]');
        const contextControl = contextNode instanceof HTMLInputElement
          ? contextNode
          : contextNode?.querySelector('input');

        return {
          viewport: { width: innerWidth, height: innerHeight },
          document: {
            clientWidth: document.documentElement.clientWidth,
            scrollWidth: document.documentElement.scrollWidth,
            scrollHeight: document.documentElement.scrollHeight,
          },
          sections,
          estimate: estimate ? {
            height: Math.round(estimate.getBoundingClientRect().height),
            contact: visible(estimate.querySelector('[data-SID="CONTACT"] input')),
            task: visible(estimate.querySelector('[data-SID="TASK"] textarea')),
            file: visible(estimate.querySelector('[data-SID="FILE"] input[type="file"]')),
            consent: visible(estimate.querySelector('.licence_block')),
            submit: visible(estimate.querySelector('input[type="submit"], button[type="submit"]')),
            context: contextControl?.value ?? null,
            nameBox: relativeBox(estimate, '[data-SID="NAME"]'),
            contactBox: relativeBox(estimate, '[data-SID="CONTACT"]'),
            taskBox: relativeBox(estimate, '[data-SID="TASK"]'),
            fileBox: relativeBox(estimate, '[data-SID="FILE"]'),
            consentBox: relativeBox(estimate, '.licence_block'),
            submitBox: relativeBox(estimate, 'input[type="submit"], button[type="submit"]'),
          } : null,
        };
      });

      console.log(JSON.stringify({ page: pageSpec.name, viewport: viewport.name, diagnostics, metrics }));

      await page.screenshot({
        path: resolve(outputRoot, `${pageSpec.name}-${viewport.name}-full.png`),
        fullPage: true,
      });

      const estimate = page.locator('[data-rm-estimate-form]').first();
      if (await estimate.count() && await estimate.isVisible()) {
        await estimate.screenshot({
          path: resolve(outputRoot, `${pageSpec.name}-${viewport.name}-estimate.png`),
        });
      }

      const dialogTrigger = page.locator('[data-rm-discuss-open]').first();
      if (await dialogTrigger.count() && await dialogTrigger.isVisible()) {
        await dialogTrigger.click();
        const dialog = page.locator('dialog[data-rm-discuss-dialog][open]').first();
        if (await dialog.count() && await dialog.isVisible()) {
          await dialog.screenshot({
            path: resolve(outputRoot, `${pageSpec.name}-${viewport.name}-dialog.png`),
          });
          if (viewport.name === 'mobile-390') {
            await dialog.evaluate((node) => {
              node.scrollTop = node.scrollHeight;
            });
            await page.waitForTimeout(100);
            await dialog.screenshot({
              path: resolve(outputRoot, `${pageSpec.name}-${viewport.name}-dialog-bottom.png`),
            });
          }
          await page.keyboard.press('Escape');
        }
      }

      await page.close();
    }
  }
} finally {
  await browser.close();
}
