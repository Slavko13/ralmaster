import AxeBuilder from '@axe-core/playwright';
import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const expectedSections = [
  { name: 'title', top: 106, height: 190 },
  { name: 'summary', top: 296, height: 510 },
  { name: 'route', top: 806, height: 884 },
  { name: 'requisites', top: 1690, height: 1011 },
  { name: 'visit', top: 2701, height: 446 },
  { name: 'photo', top: 3147, height: 543 },
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
    diagnostics.push({ kind: 'console.error', message: message.text(), location: message.location() });
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
  expect(geometry.scrollWidth).toBeLessThanOrEqual(geometry.clientWidth + 1);
}

async function attachDiagnostics(testInfo: TestInfo, diagnostics: BrowserDiagnostic[]) {
  await testInfo.attach('browser-diagnostics', {
    body: JSON.stringify(diagnostics, null, 2),
    contentType: 'application/json',
  });
}

test.describe('P0 redesigned contacts and discuss-order dialog', () => {
  test('keeps the public contact route and CMS contact sources', async ({ page, request }) => {
    const response = await request.get('/contacts/', { maxRedirects: 5 });
    expect(response.status()).toBeLessThan(400);

    await page.goto('/contacts/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await expect(page.locator('[data-rm-contacts-page]')).toBeVisible();
    await expect(page.locator('[data-rm-contacts-page] h1')).toHaveText('Контакты');
    await expect(page.locator('.rm-contacts-summary__phone a[href^="tel:"]').first()).toBeVisible();
    await expect(page.locator('.rm-contacts-summary__email a[href^="mailto:"]').first()).toBeVisible();
    await expect(page.locator('.rm-contacts-route__map')).toBeVisible();
    await expect(page.locator('.rm-contacts-requisites__row')).toHaveCount(9);
  });

  test('uses a local Paper image and no runtime design hotlinks', async ({ page }) => {
    await page.goto('/contacts/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('[data-rm-contacts-page]')).toBeVisible();
    const image = page.locator('.rm-contacts-photo img');
    await image.scrollIntoViewIfNeeded();
    await expect(image).toHaveAttribute(
      'src',
      '/local/templates/aspro-allcorp2/images/ralmaster/home/production-powder-line.png',
    );
    await expect
      .poll(() =>
        image.evaluate((node) => {
          const element = node as HTMLImageElement;
          return element.complete && element.naturalWidth > 0 && element.naturalHeight > 0;
        }),
      )
      .toBe(true);
    expect(await page.locator('[src*="paper.design"], [style*="paper.design"]').count()).toBe(0);
  });

  test('opens the reusable 748 × 850 Paper dialog and validates without POST', async ({ page }) => {
    const postRequests: string[] = [];
    page.on('request', (request) => {
      if (request.method() === 'POST') postRequests.push(request.url());
    });

    await page.goto('/contacts/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('[data-rm-contacts-page]')).toBeVisible();
    await page.locator('[data-rm-discuss-open]').click();

    const dialog = page.locator('[data-rm-discuss-dialog]');
    await expect(dialog).toBeVisible();
    await expect(dialog.locator('input[data-sid="CONTEXT"]')).toHaveValue('contacts');
    await expect(dialog.locator('[data-SID="TASK"] textarea')).toHaveAttribute(
      'placeholder',
      /нужны корзины для кондиционеров/,
    );
    await expect(dialog.locator('.uploader .filename')).toHaveAttribute(
      'data-rm-upload-label',
      'Прикрепить чертежи, фотографии или ТЗ',
    );
    await expect(dialog.locator('#contacs_form')).toHaveCount(0);
    const brokenLabelReferences = await dialog.evaluate((root) =>
      Array.from(root.querySelectorAll<HTMLLabelElement>('label[for]'))
        .map((label) => label.htmlFor)
        .filter((id) => id && !root.querySelector(`#${CSS.escape(id)}`)),
    );
    expect(brokenLabelReferences).toEqual([]);

    const geometry = await dialog.evaluate((root) => {
      const box = root.getBoundingClientRect();
      const measure = (selector: string) => {
        const node = root.querySelector<HTMLElement>(selector);
        if (!node) return null;
        const rect = node.getBoundingClientRect();
        return {
          width: Math.round(rect.width),
          height: Math.round(rect.height),
          clipped:
            rect.top < box.top - 0.5 ||
            rect.left < box.left - 0.5 ||
            rect.right > box.right + 0.5 ||
            rect.bottom > box.bottom + 0.5,
        };
      };
      return {
        dialog: { width: Math.round(box.width), height: Math.round(box.height) },
        header: measure('.rm-discuss__header'),
        intro: measure('.rm-discuss__intro'),
        form: measure('.rm-discuss__form'),
        name: measure('.row[data-SID="NAME"]'),
        contact: measure('.row[data-SID="CONTACT"]'),
        task: measure('.row[data-SID="TASK"]'),
        file: measure('.row[data-SID="FILE"]'),
        consent: measure('.licence_block'),
        submit: measure('input[type="submit"]'),
        note: measure('.rm-discuss__note'),
      };
    });

    expect(geometry.dialog).toEqual({ width: 748, height: 850 });
    expect(geometry.header).toEqual({ width: 668, height: 64, clipped: false });
    expect(geometry.intro).toEqual({ width: 668, height: 76, clipped: false });
    expect(geometry.form).toEqual({ width: 668, height: 590, clipped: false });
    expect(geometry.name?.height).toBe(88);
    expect(geometry.contact?.height).toBe(88);
    expect(geometry.task?.height).toBe(148);
    expect(geometry.file?.height).toBe(86);
    expect(geometry.consent?.height).toBe(20);
    expect(geometry.submit?.height).toBe(60);
    expect(geometry.note).toEqual({ width: 668, height: 40, clipped: false });
    for (const value of Object.values(geometry).slice(1)) expect(value?.clipped).not.toBe(true);

    await dialog.locator('input[type="submit"]').click();
    await expect(dialog.locator('.rm-estimate-error').first()).toBeVisible();
    expect(postRequests).toEqual([]);
    await dialog.locator('[data-rm-discuss-close]').click();
    await expect(dialog).toBeHidden();
  });

  test('matches the exact 1440 × 4120 Paper geometry', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);
    await page.goto('/contacts/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await page.waitForTimeout(1_200);

    const geometry = await page.locator('[data-rm-contacts-page]').evaluate((root) => {
      const absoluteTop = (element: Element) =>
        Math.round(element.getBoundingClientRect().top + window.scrollY);
      const sections = Array.from(
        root.querySelectorAll<HTMLElement>(':scope > [data-rm-contacts-section]'),
      );
      const footer = document.querySelector<HTMLElement>('.rm-footer')!;
      return {
        rootTop: absoluteTop(root),
        width: Math.round(root.getBoundingClientRect().width),
        documentHeight: document.documentElement.scrollHeight,
        footerTop: absoluteTop(footer),
        footerHeight: Math.round(footer.getBoundingClientRect().height),
        sections: sections.map((section) => ({
          name: section.dataset.rmContactsSection,
          top: absoluteTop(section),
          height: Math.round(section.getBoundingClientRect().height),
        })),
      };
    });

    expect(geometry).toEqual({
      rootTop: 106,
      width: 1440,
      documentHeight: 4120,
      footerTop: 3690,
      footerHeight: 430,
      sections: expectedSections,
    });
    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });

  test('has no critical axe violations', async ({ page }, testInfo) => {
    await page.goto('/contacts/', { waitUntil: 'domcontentloaded' });
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
      await page.goto('/contacts/', { waitUntil: 'domcontentloaded' });
      await expect(page.locator('[data-rm-contacts-page]')).toBeVisible();
      await expectNoHorizontalOverflow(page);
    }
  });

  test('P0 @mobile 390 layout and dialog are usable without browser errors', async ({
    page,
  }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);
    await page.goto('/contacts/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await expect(page.locator('[data-rm-contacts-page]')).toBeVisible();
    await expectNoHorizontalOverflow(page);

    await page.locator('[data-rm-discuss-open]').click();
    await expect(page.locator('[data-rm-discuss-dialog]')).toBeVisible();
    await expectNoHorizontalOverflow(page);
    await page.locator('[data-rm-discuss-close]').click();

    await page.waitForTimeout(1_000);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });
});
