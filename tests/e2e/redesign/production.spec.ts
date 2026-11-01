import AxeBuilder from '@axe-core/playwright';
import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const expectedSections = [
  { name: 'hero', top: 106, height: 716 },
  { name: 'how', top: 822, height: 598 },
  { name: 'capabilities', top: 1420, height: 1094 },
  { name: 'order', top: 2514, height: 600 },
  { name: 'repeat', top: 3114, height: 636 },
  { name: 'shipping', top: 3750, height: 766 },
  { name: 'partners', top: 4516, height: 532 },
  { name: 'faq', top: 5048, height: 810 },
  { name: 'contact', top: 5858, height: 372 },
  { name: 'photo', top: 6230, height: 543 },
] as const;

const faqQuestions = [
  'Можно ли приехать на производство?',
  'Можно ли начать с пробного изделия или небольшой партии?',
  'Что делать, если нет готового чертежа?',
  'Работаете ли вы с материалом заказчика?',
  'Можно ли изменить заказ после запуска в производство?',
  'Что делать, если при приёмке обнаружено несоответствие?',
] as const;

type BrowserDiagnostic = {
  kind: 'console.error' | 'pageerror';
  message: string;
  location?: ReturnType<ConsoleMessage['location']>;
};

function collectBrowserDiagnostics(page: Page): BrowserDiagnostic[] {
  const diagnostics: BrowserDiagnostic[] = [];

  page.on('console', (message) => {
    if (message.type() !== 'error') return;
    diagnostics.push({
      kind: 'console.error',
      message: message.text(),
      location: message.location(),
    });
  });

  page.on('pageerror', (error) => {
    diagnostics.push({ kind: 'pageerror', message: error.stack ?? error.message });
  });

  return diagnostics;
}

async function expectNoHorizontalOverflow(page: Page): Promise<void> {
  const geometry = await page.evaluate(() => ({
    clientWidth: document.documentElement.clientWidth,
    scrollWidth: document.documentElement.scrollWidth,
  }));

  expect(
    geometry.scrollWidth,
    `Unexpected horizontal overflow: ${geometry.scrollWidth}px > ${geometry.clientWidth}px`,
  ).toBeLessThanOrEqual(geometry.clientWidth + 1);
}

async function attachDiagnostics(
  testInfo: TestInfo,
  diagnostics: BrowserDiagnostic[],
): Promise<void> {
  await testInfo.attach('browser-diagnostics', {
    body: JSON.stringify(diagnostics, null, 2),
    contentType: 'application/json',
  });
}

test.describe('Redesigned production root', () => {
  test('renders only on /company/ and leaves a representative child route intact', async ({
    page,
    request,
  }) => {
    const rootResponse = await request.get('/company/', { maxRedirects: 5 });
    expect(rootResponse.status()).toBeLessThan(400);

    await page.goto('/company/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);

    const root = page.locator('[data-rm-production]');
    await expect(root).toBeVisible();
    await expect(root.locator('h1')).toHaveText('О производстве');

    const sections = await root
      .locator(':scope > [data-rm-production-section]')
      .evaluateAll((nodes) =>
        nodes.map((node) => node.getAttribute('data-rm-production-section')),
      );
    expect(sections).toEqual(expectedSections.map(({ name }) => name));

    const childResponse = await request.get('/company/partners/', { maxRedirects: 5 });
    expect(childResponse.status()).toBeLessThan(400);

    await page.goto('/company/partners/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await expect(page.locator('[data-rm-production]')).toHaveCount(0);
  });

  test('uses local Paper assets and routes partner logos through the local partners page', async ({ page }) => {
    await page.goto('/company/', { waitUntil: 'networkidle' });

    const images = page.locator('[data-rm-production] img');
    expect(await images.count()).toBeGreaterThanOrEqual(15);

    for (let index = 0; index < (await images.count()); index += 1) {
      const image = images.nth(index);
      const source = await image.getAttribute('src');
      expect(source).toMatch(/^\/local\/templates\/aspro-allcorp2\/images\/ralmaster\//);
      expect(source).not.toMatch(/paper\.design|bavrasem\.beget\.tech/i);

      await image.scrollIntoViewIfNeeded();
      await expect
        .poll(() =>
          image.evaluate((node) => {
            const element = node as HTMLImageElement;
            return element.complete && element.naturalWidth > 0 && element.naturalHeight > 0;
          }),
        )
        .toBe(true);
    }

    const partnerCards = page.locator('.rm-production-partners__grid > *');
    await expect(partnerCards).toHaveCount(7);
    await expect(partnerCards.locator('img')).toHaveCount(7);

    const partnerLinks = page.locator('.rm-production-partners__grid > a[href]');
    await expect(partnerLinks).toHaveCount(7);
    const hrefs = await partnerLinks.evaluateAll((links) =>
      links.map((link) => (link as HTMLAnchorElement).getAttribute('href')),
    );
    expect(hrefs).toEqual(Array(7).fill('/company/partners/'));
  });

  test('uses the three Paper icons for repeat orders without legacy list bullets', async ({ page }) => {
    await page.goto('/company/', { waitUntil: 'domcontentloaded' });

    const repeatItems = page.locator('.rm-production-repeat__copy li');
    await expect(repeatItems).toHaveCount(3);
    await expect(repeatItems.locator(':scope > .rm-production-repeat__icon > svg')).toHaveCount(3);

    const iconPaths = await repeatItems.locator('svg path').evaluateAll((paths) =>
      paths.map((path) => path.getAttribute('d')),
    );
    expect(iconPaths).toEqual([
      'M4 8H17L14 5M20 16H7L10 19M17 8L14 11M7 16L10 13M20 8V12M4 16V12',
      'M14 4L20 10M5 15L4 21L10 20L21 9L15 3ZM7 13L13 19',
      'M4 6H20V21H4ZM8 3V9M16 3V9M4 11H20M8 15H11M14 15H17M8 18H11',
    ]);

    const iconGeometry = await repeatItems.locator('svg').evaluateAll((icons) =>
      icons.map((icon) => {
        const svg = icon as SVGSVGElement;
        const path = svg.querySelector('path') as SVGGraphicsElement;
        const box = path.getBBox();
        return {
          left: box.x,
          top: box.y,
          right: box.x + box.width,
          bottom: box.y + box.height,
          overflow: getComputedStyle(svg).overflow,
        };
      }),
    );
    for (const icon of iconGeometry) {
      expect(icon.left).toBeGreaterThanOrEqual(1);
      expect(icon.top).toBeGreaterThanOrEqual(1);
      expect(icon.right).toBeLessThanOrEqual(23);
      expect(icon.bottom).toBeLessThanOrEqual(23);
      expect(icon.overflow).toBe('visible');
    }

    await expect(repeatItems.locator('svg').first()).toHaveCSS('width', '28px');

    const legacyBullets = await repeatItems.evaluateAll((items) =>
      items.map((item) => getComputedStyle(item, '::before').display),
    );
    expect(legacyBullets).toEqual(['none', 'none', 'none']);
  });

  test('qualifies draft production limits instead of presenting them as certified values', async ({
    page,
  }) => {
    await page.goto('/company/', { waitUntil: 'domcontentloaded' });

    const capabilityRows = page.locator('.rm-production-capabilities tbody tr');
    await expect(capabilityRows).toHaveCount(5);
    await expect(page.locator('.rm-production-capabilities__note')).toContainText(
      'Примерные данные для согласования',
    );
    await expect(page.locator('.rm-production-capabilities__note')).toContainText(
      'подтвердим после проверки чертежа',
    );
    await expect(capabilityRows.filter({ hasText: 'Порошковая окраска' })).toContainText(
      'требует подтверждения',
    );
  });

  test('keeps the Paper capabilities table inside the desktop content frame', async ({ page }) => {
    await page.setViewportSize({ width: 1366, height: 900 });
    await page.goto('/company/', { waitUntil: 'domcontentloaded' });

    const geometry = await page.locator('.rm-production-capabilities').evaluate((section) => {
      const box = (selector: string) => {
        const rect = section.querySelector<HTMLElement>(selector)!.getBoundingClientRect();
        return { left: Math.round(rect.left), right: Math.round(rect.right), width: Math.round(rect.width) };
      };
      return {
        viewport: document.documentElement.clientWidth,
        title: box(':scope > h2'),
        table: box('table'),
        cta: box('.rm-production-capabilities__cta'),
      };
    });

    expect(geometry.title.width).toBe(1270);
    expect(geometry.table).toEqual(geometry.title);
    expect(geometry.cta).toEqual(geometry.title);
    expect(geometry.table.left).toBeGreaterThanOrEqual(0);
    expect(geometry.table.right).toBeLessThanOrEqual(geometry.viewport);
    await expectNoHorizontalOverflow(page);
  });

  test('keeps the final order divider below the last row', async ({ page }) => {
    await page.setViewportSize({ width: 1000, height: 900 });
    await page.goto('/company/', { waitUntil: 'domcontentloaded' });

    const geometry = await page.locator('.rm-production-order__steps').evaluate((list) => {
      const listBox = list.getBoundingClientRect();
      const rows = Array.from(list.querySelectorAll('li')).map((row) => {
        const box = row.getBoundingClientRect();
        return {
          top: box.top,
          bottom: box.bottom,
          marginBottom: getComputedStyle(row).marginBottom,
        };
      });
      return { listBottom: listBox.bottom, rows };
    });
    expect(geometry.rows.every((row) => row.marginBottom === '0px')).toBe(true);
    expect(Math.abs(geometry.listBottom - geometry.rows.at(-1)!.bottom)).toBeLessThanOrEqual(1);
    for (let index = 1; index < geometry.rows.length; index += 1) {
      expect(
        Math.abs(geometry.rows[index].top - geometry.rows[index - 1].bottom),
      ).toBeLessThanOrEqual(1);
    }
  });

  test('shows the Paper FAQ fallback as an accessible one-at-a-time accordion', async ({ page }) => {
    await page.goto('/company/', { waitUntil: 'domcontentloaded' });

    const faq = page.locator('.rm-production-faq');
    const details = faq.locator('details');
    await expect(details).toHaveCount(faqQuestions.length);
    await expect(details.locator('summary strong')).toHaveText([...faqQuestions]);

    await details.nth(0).locator('summary').click();
    await expect(details.nth(0)).toHaveAttribute('open', '');
    await expect(details.nth(0).locator('.rm-production-faq__answer')).toBeVisible();

    await details.nth(1).locator('summary').click();
    await expect(details.nth(1)).toHaveAttribute('open', '');
    await expect(details.nth(0)).not.toHaveAttribute('open', '');
  });

  test('opens the shared production estimate form without submitting', async ({ page }) => {
    const postRequests: string[] = [];
    page.on('request', (request) => {
      if (request.method() === 'POST') postRequests.push(request.url());
    });

    await page.goto('/company/', { waitUntil: 'domcontentloaded' });
    const trigger = page.locator(
      '.rm-production-contact [data-rm-discuss-open="production"]',
    );
    await expect(trigger).toBeVisible();
    await trigger.click();

    const dialog = page.locator('dialog[data-rm-discuss-variant="production"]');
    await expect(dialog).toBeVisible();
    await expect(dialog.locator('[data-SID="CONTEXT"]')).toHaveValue(
      'production; contact=telegram',
    );
    await page.keyboard.press('Escape');
    await expect(dialog).toBeHidden();
    expect(postRequests).toEqual([]);
  });

  test('centers the production contact card in wide viewports', async ({ page }) => {
    await page.setViewportSize({ width: 1882, height: 900 });
    await page.goto('/company/', { waitUntil: 'domcontentloaded' });

    const centers = await page.locator('.rm-production-contact').evaluate((section) => {
      const sectionBox = section.getBoundingClientRect();
      const cardBox = section.querySelector('.rm-production-contact__inner')!.getBoundingClientRect();
      return {
        section: sectionBox.left + sectionBox.width / 2,
        card: cardBox.left + cardBox.width / 2,
      };
    });
    expect(Math.abs(centers.section - centers.card)).toBeLessThanOrEqual(1);
  });

  test('matches the exact 1440 × 7203 Paper geometry', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);

    await page.goto('/company/', { waitUntil: 'networkidle' });
    await expectHealthyDocument(page);
    await page.waitForTimeout(1_500);

    const geometry = await page.locator('[data-rm-production]').evaluate((root) => {
      const sections = Array.from(
        root.querySelectorAll<HTMLElement>(':scope > [data-rm-production-section]'),
      );
      const absoluteTop = (element: Element) =>
        Math.round(element.getBoundingClientRect().top + window.scrollY);

      return {
        rootTop: absoluteTop(root),
        width: Math.round(root.getBoundingClientRect().width),
        documentHeight: document.documentElement.scrollHeight,
        footerTop: absoluteTop(document.querySelector('.rm-footer')!),
        footerHeight: Math.round(document.querySelector('.rm-footer')!.getBoundingClientRect().height),
        sections: sections.map((section) => ({
          name: section.dataset.rmProductionSection,
          top: absoluteTop(section),
          height: Math.round(section.getBoundingClientRect().height),
        })),
      };
    });

    expect(geometry).toEqual({
      rootTop: 106,
      width: 1440,
      documentHeight: 7203,
      footerTop: 6773,
      footerHeight: 430,
      sections: expectedSections,
    });

    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });

  test('has no critical axe violations', async ({ page }, testInfo) => {
    await page.goto('/company/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);

    const results = await new AxeBuilder({ page })
      .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
      .analyze();

    await testInfo.attach('axe-results', {
      body: JSON.stringify(results.violations, null, 2),
      contentType: 'application/json',
    });
    expect(results.violations.filter((violation) => violation.impact === 'critical')).toEqual([]);
  });

  test('768 and 1024 layouts remain within the viewport', async ({ page }) => {
    for (const width of [768, 1024]) {
      await page.setViewportSize({ width, height: 900 });
      await page.goto('/company/', { waitUntil: 'domcontentloaded' });
      await expect(page.locator('[data-rm-production]')).toBeVisible();
      await expectNoHorizontalOverflow(page);
    }
  });

  test('P0 @mobile 390 layout is usable and has no browser errors', async ({
    page,
  }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);

    await page.goto('/company/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await expect(page.locator('[data-rm-production]')).toBeVisible();
    await expect(page.locator('.rm-production-capabilities tbody tr')).toHaveCount(5);
    await expect(page.locator('.rm-production-faq details')).toHaveCount(6);
    await page.waitForTimeout(1_500);

    const capabilityCards = await page
      .locator('.rm-production-capabilities tbody tr')
      .evaluateAll((rows) =>
        rows.map((row) => {
          const rowBox = row.getBoundingClientRect();
          const cells = Array.from(row.children).map((cell) => {
            const box = cell.getBoundingClientRect();
            return { top: box.top, bottom: box.bottom, height: box.height };
          });
          return {
            height: rowBox.height,
            rowBottom: rowBox.bottom,
            cells,
          };
        }),
      );

    for (const card of capabilityCards) {
      expect(card.height).toBeGreaterThan(200);
      expect(card.cells.every((cell) => cell.height > 0)).toBe(true);
      expect(card.cells.at(-1)!.bottom).toBeLessThanOrEqual(card.rowBottom + 1);
      for (let index = 1; index < card.cells.length; index += 1) {
        expect(card.cells[index].top).toBeGreaterThanOrEqual(card.cells[index - 1].bottom - 1);
      }
    }

    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });
});
