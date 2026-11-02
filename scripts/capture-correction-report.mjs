import { mkdir } from 'node:fs/promises';
import { resolve } from 'node:path';
import { chromium } from '@playwright/test';

const baseUrl = process.env.BASE_URL ?? 'http://127.0.0.1:8084';
const outputRoot = resolve(
  process.env.REPORT_DIR ?? '../../redesign-report/2026-11-02/after',
);

await mkdir(outputRoot, { recursive: true });

const browser = await chromium.launch();
const context = await browser.newContext({
  viewport: { width: 1440, height: 1000 },
  deviceScaleFactor: 1,
});

const page = await context.newPage();

async function open(path) {
  await page.goto(`${baseUrl}${path}`, { waitUntil: 'domcontentloaded' });
  await page.waitForLoadState('networkidle').catch(() => {});
  await page.evaluate(() => document.fonts.ready);
  await page.waitForTimeout(650);
}

async function capturePage(name) {
  await page.screenshot({ path: resolve(outputRoot, name), fullPage: false });
}

async function captureElement(locator, name) {
  await locator.scrollIntoViewIfNeeded();
  await page.waitForTimeout(180);
  await locator.screenshot({ path: resolve(outputRoot, name) });
}

try {
  await open('/');
  await capturePage('01-home-hero-and-header.png');
  await captureElement(page.locator('.rm-home-product-grid'), '13-home-drawings-route.png');

  await open('/contacts/');
  await page.locator('.rm-header .rm-flat-menu__item--services').hover();
  await page.waitForTimeout(220);
  await capturePage('02-white-header-and-services-menu.png');
  await captureElement(page.locator('.rm-contacts-summary'), '03-contacts-alignment.png');
  await captureElement(
    page.locator('.rm-contacts-summary__email-row'),
    '14-contacts-copy-control.png',
  );

  await open('/company/');
  await captureElement(page.locator('.rm-production-capabilities'), '04-production-table.png');
  const discussTrigger = page.locator(
    '.rm-production-capabilities__cta [data-rm-discuss-open="production"]',
  );
  await discussTrigger.click({ force: true });
  const discussDialog = page.locator('dialog[data-rm-discuss-variant="production"][open]');
  await discussDialog.waitFor({ state: 'visible' });
  await captureElement(discussDialog, '05-unified-estimate-dialog.png');
  await page.keyboard.press('Escape');

  await open('/product/pannier/');
  const product = page.locator('[data-rm-product-category] .catalog_table_2 .item').first();
  await product.waitFor({ state: 'visible' });
  const basketResponse = page.waitForResponse(
    (response) =>
      response.request().method() === 'POST'
      && response.url().includes('/include/footer/site-basket.php'),
  );
  await product.locator('.to_cart').click();
  await basketResponse;
  await open('/cart/');
  await capturePage('06-live-catalog-cart.png');
  await captureElement(page.locator('.rm-header__basket'), '17-cart-header-count.png');
  await page.locator('.basket.default .buttons .to-order').click();
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(500);
  await capturePage('07-checkout-form.png');

  await open('/product/');
  const productCard = page.locator('.rm-product-card').first();
  await productCard.hover();
  await page.waitForTimeout(550);
  await captureElement(productCard, '15-product-card-hover.png');

  await open('/projects/krepezhnye-kronshteyny/');
  await capturePage('18-brackets-project-page.png');

  await open('/painting/');
  await captureElement(page.locator('.rm-powder-accepted'), '10-powder-products-layout.png');

  await open('/projects/');
  await captureElement(page.locator('.rm-projects-products'), '11-project-products-layout.png');

  await open('/services/');
  await captureElement(page.locator('.rm-metal-operations'), '12-metalworking-operations.png');
  await captureElement(
    page.locator('.rm-metal-estimate__intro'),
    '16-metalworking-estimate-checks.png',
  );

  await page.setViewportSize({ width: 390, height: 844 });
  await open('/');
  await page.locator('#mobileheader button.burger').click({ force: true });
  await page.locator('#mobilemenu').waitFor({ state: 'visible' });
  await capturePage('08-mobile-menu.png');

  const mobileContext = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const mobilePage = await mobileContext.newPage();
  await mobilePage.goto(`${baseUrl}/company/`, { waitUntil: 'domcontentloaded' });
  await mobilePage.waitForLoadState('networkidle').catch(() => {});
  await mobilePage.evaluate(() => document.fonts.ready);
  // The legacy off-canvas search is parked above the viewport. Element-level
  // screenshot stitching can repeat that fixed node inside a tall capture,
  // so hide it only in the report renderer.
  await mobilePage.addStyleTag({
    content: '.inline-search-block.fixed { display: none !important; }',
  });
  const mobileTable = mobilePage.locator('.rm-production-capabilities').first();
  await mobileTable.scrollIntoViewIfNeeded();
  await mobilePage.waitForTimeout(700);
  await mobileTable.screenshot({
    path: resolve(outputRoot, '09-production-table-mobile.png'),
  });
  await mobileContext.close();
} finally {
  await context.close();
  await browser.close();
}

console.log(`Correction report captured in ${outputRoot}`);
