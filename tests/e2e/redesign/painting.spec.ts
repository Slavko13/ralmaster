import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const routes = ['/painting/', '/painting/poroshkovaya-pokraska-metalla/'] as const;
const expectedSections = [
  'hero',
  'accepted',
  'preparation',
  'requirements',
  'examples',
  'orders',
  'estimate',
  'photo',
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

test.describe('P0 redesigned powder coating page', () => {
  test('keeps both public routes on one reusable Paper layout', async ({ page, request }) => {
    for (const route of routes) {
      const response = await request.get(route, { maxRedirects: 5 });
      expect(response.status(), `Broken powder-coating route: ${route}`).toBeLessThan(400);

      await page.goto(route, { waitUntil: 'domcontentloaded' });
      await expectHealthyDocument(page);
      await expect(page.locator('[data-rm-powder-page]')).toBeVisible();
      await expect(page.locator('[data-rm-powder-page] h1')).toHaveText(
        'Порошковая окраска металлических изделий',
      );

      const sections = await page
        .locator('[data-rm-powder-page] > [data-rm-powder-section]')
        .evaluateAll((nodes) => nodes.map((node) => node.getAttribute('data-rm-powder-section')));
      expect(sections).toEqual(expectedSections);
    }
  });

  test('shows Paper content without presenting draft limits as confirmed facts', async ({ page }) => {
    await page.goto('/painting/', { waitUntil: 'domcontentloaded' });

    await expect(page.getByRole('heading', { name: 'Какие изделия принимаем на окраску' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Подготовка поверхности перед окраской' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Согласуем требования к покрытию' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Примеры порошковой окраски' })).toBeVisible();
    await expect(page.locator('.rm-powder-dimensions__note')).toContainText(
      'требуют подтверждения производством',
    );
    await expect(page.locator('.rm-powder-example small')).toHaveCount(3);
    await expect(page.locator('.rm-powder-example small').first()).toContainText(
      'Демонстрационный пример',
    );
  });

  test('places accepted-product titles above images and descriptions below', async ({
    page,
  }) => {
    await page.goto('/painting/', { waitUntil: 'networkidle' });

    const geometry = await page.locator('.rm-powder-accepted').evaluate((section) => {
      const heading = section.querySelector<HTMLElement>('h2')!;
      const grid = section.querySelector<HTMLElement>('.rm-powder-accepted__cards')!;
      const card = grid.querySelector<HTMLElement>('.rm-powder-accepted__card')!;
      const cardHeading = card.querySelector<HTMLElement>('.rm-powder-accepted__card-heading')!;
      const image = card.querySelector<HTMLElement>('img')!;
      const description = card.querySelector<HTMLElement>(
        '.rm-powder-accepted__card-description',
      )!;
      const measure = (element: HTMLElement) => {
        const box = element.getBoundingClientRect();
        return { width: Math.round(box.width), height: Math.round(box.height) };
      };
      return {
        section: measure(section as HTMLElement),
        heading: measure(heading),
        headingPaddingBottom: getComputedStyle(heading).paddingBottom,
        grid: measure(grid),
        card: measure(card),
        cardHeading: measure(cardHeading),
        image: measure(image),
        imageFit: getComputedStyle(image).objectFit,
        description: measure(description),
        childOrder: Array.from(card.children).map((child) =>
          child instanceof HTMLImageElement ? child.tagName : child.className,
        ),
      };
    });

    expect(geometry).toEqual({
      section: { width: 1440, height: 878 },
      heading: { width: 1312, height: 88 },
      headingPaddingBottom: '32px',
      grid: { width: 1312, height: 401 },
      card: { width: 421, height: 401 },
      cardHeading: { width: 421, height: 74 },
      image: { width: 421, height: 254 },
      imageFit: 'cover',
      description: { width: 421, height: 73 },
      childOrder: [
        'rm-powder-accepted__card-heading',
        'IMG',
        'rm-powder-accepted__card-description',
      ],
    });

    const card = page.locator('.rm-powder-accepted__card').first();
    const topBeforeHover = await card.evaluate(
      (node) => node.getBoundingClientRect().top + window.scrollY,
    );
    await card.hover();
    await page.waitForTimeout(220);
    const topAfterHover = await card.evaluate(
      (node) => node.getBoundingClientRect().top + window.scrollY,
    );
    await expect(card).toHaveCSS('transform', 'matrix(1.1, 0, 0, 1.1, 0, 0)');
    await expect(card).toHaveCSS('box-shadow', /rgba\(17, 30, 53, 0\.16\).*18px 40px/);
    expect(Math.abs(topAfterHover - topBeforeHover)).toBeLessThanOrEqual(1);
  });

  test('loads all Powder Paper images from local template assets', async ({ page }) => {
    await page.goto('/painting/', { waitUntil: 'networkidle' });
    const images = page.locator('[data-rm-powder-page] img');
    expect(await images.count()).toBeGreaterThanOrEqual(10);

    for (let index = 0; index < await images.count(); index += 1) {
      const image = images.nth(index);
      await image.scrollIntoViewIfNeeded();
      await expect
        .poll(() =>
          image.evaluate((node) => {
            const element = node as HTMLImageElement;
            return element.complete && element.naturalWidth > 0 && element.naturalHeight > 0;
          }),
        )
        .toBe(true);
      await expect(image).toHaveAttribute('src', /\/images\/ralmaster\//);
    }
  });

  test('opens the Paper coating calculation dialog and validates without submitting', async ({ page }) => {
    const postRequests: string[] = [];
    page.on('request', (request) => {
      if (request.method() === 'POST') postRequests.push(request.url());
    });

    await page.goto('/painting/', { waitUntil: 'domcontentloaded' });
    const cta = page.locator(
      '.rm-powder-hero [data-rm-discuss-open="powder-coating"]',
    );
    await expect(cta).toBeVisible();
    await cta.click();

    const dialog = page.locator('dialog[data-rm-discuss-variant="powder-coating"]');
    await expect(dialog).toBeVisible();
    await expect(dialog.locator('h2')).toHaveText('Расчёт порошковой окраски');
    await expect(dialog.locator('[data-SID="CONTEXT"]')).toHaveValue(
      'powder-coating; contact=telegram',
    );
    await dialog.locator('input[type="submit"]').click();
    await expect(dialog.locator('[data-SID="CONTACT"] .rm-estimate-error')).toBeVisible();
    await expect(dialog.locator('[data-SID="TASK"] .rm-estimate-error')).toBeVisible();
    await page.keyboard.press('Escape');
    await expect(dialog).toBeHidden();

    await expect(page.locator('.rm-powder-estimate__form form')).toBeVisible();
    await expect(page.locator('.rm-powder-estimate__form input[type="submit"]')).toBeVisible();
    expect(postRequests).toEqual([]);
  });

  test('opens estimate CTAs in a popup without scrolling to the inline form', async ({ page }) => {
    await page.goto('/painting/', { waitUntil: 'networkidle' });
    const dialog = page.locator('dialog[data-rm-discuss-variant="powder-coating"]');
    const selectors = [
      '.rm-powder-dimensions__action [data-rm-discuss-open="powder-coating"]',
      '.rm-powder-example [data-rm-discuss-open="powder-coating"]',
      '.rm-powder-orders [data-rm-discuss-open="powder-coating"]',
    ];

    for (const selector of selectors) {
      const trigger = page.locator(selector).first();
      await trigger.scrollIntoViewIfNeeded();
      const scrollBefore = await page.evaluate(() => window.scrollY);
      await trigger.click();
      await expect(dialog).toBeVisible();
      expect(await page.evaluate(() => window.scrollY)).toBe(scrollBefore);
      await page.keyboard.press('Escape');
      await expect(dialog).toBeHidden();
    }
  });

  test('keeps every desktop estimate control inside the Paper form panel', async ({ page }) => {
    await page.goto('/painting/', { waitUntil: 'networkidle' });

    const geometry = await page.locator('.rm-powder-estimate__form').evaluate((root) => {
      const rootBox = root.getBoundingClientRect();
      const measure = (selector: string) => {
        const element = root.querySelector<HTMLElement>(selector);
        if (!element) return null;
        const box = element.getBoundingClientRect();
        return {
          height: Math.round(box.height),
          clipped:
            box.top < rootBox.top - 0.5 ||
            box.right > rootBox.right + 0.5 ||
            box.bottom > rootBox.bottom + 0.5 ||
            box.left < rootBox.left - 0.5,
        };
      };

      const duplicateTitleBoxes = Array.from(root.querySelectorAll('.form-group > .input'))
        .flatMap((input) => Array.from(input.childNodes))
        .filter((node) => node.nodeType === Node.TEXT_NODE && node.textContent?.trim())
        .map((node) => {
          const range = document.createRange();
          range.selectNode(node);
          const box = range.getBoundingClientRect();
          return { width: Math.round(box.width), height: Math.round(box.height) };
        });

      return {
        root: { clientHeight: root.clientHeight, scrollHeight: root.scrollHeight },
        form: measure('form'),
        task: measure('.row[data-SID="TASK"]'),
        textarea: measure('[data-SID="TASK"] textarea'),
        uploader: measure('.uploader'),
        consent: measure('.licence_block'),
        submit: measure('input[type="submit"]'),
        duplicateTitleBoxes,
      };
    });

    expect(geometry.root).toEqual({ clientHeight: 738, scrollHeight: 738 });
    expect(geometry.form?.clipped).toBe(false);
    expect(geometry.task).toEqual({ height: 140, clipped: false });
    expect(geometry.textarea).toEqual({ height: 112, clipped: false });
    expect(geometry.uploader).toEqual({ height: 56, clipped: false });
    expect(geometry.consent?.clipped).toBe(false);
    expect(geometry.submit).toEqual({ height: 60, clipped: false });
    expect(geometry.duplicateTitleBoxes.every((box) => box.width === 0 && box.height === 0)).toBe(true);
  });

  test('matches the desktop Paper section heights', async ({ page }) => {
    await page.goto('/painting/', { waitUntil: 'networkidle' });

    const heights = await page
      .locator('[data-rm-powder-page] > [data-rm-powder-section]')
      .evaluateAll((nodes) =>
        Object.fromEntries(
          nodes.map((node) => [
            node.getAttribute('data-rm-powder-section'),
            Math.round(node.getBoundingClientRect().height),
          ]),
        ),
      );

    expect(heights).toEqual({
      hero: 574,
      accepted: 878,
      preparation: 777,
      requirements: 714,
      examples: 920,
      orders: 604,
      estimate: 850,
      photo: 543,
    });

    const preparationBoundary = await page
      .locator('.rm-powder-preparation__layout')
      .evaluate((layout) => {
        const image = layout.querySelector<HTMLElement>('.rm-powder-cut-image')!;
        const steps = layout.querySelector<HTMLElement>('.rm-powder-preparation__steps')!;
        const lastStep = steps.querySelector<HTMLElement>('li:last-child')!;
        const lastDescription = lastStep.querySelector<HTMLElement>('p')!;
        const imageBox = image.getBoundingClientRect();
        const stepsBox = steps.getBoundingClientRect();
        const lastStepBox = lastStep.getBoundingClientRect();
        const lastDescriptionBox = lastDescription.getBoundingClientRect();
        return {
          imageBottom: Math.round(imageBox.bottom),
          stepsBottom: Math.round(stepsBox.bottom),
          lastStepBottom: Math.round(lastStepBox.bottom),
          lastDescriptionBottom: Math.round(lastDescriptionBox.bottom),
          lastStepMarginBottom: getComputedStyle(lastStep).marginBottom,
        };
      });

    expect(preparationBoundary.stepsBottom - preparationBoundary.imageBottom).toBe(1);
    expect(preparationBoundary.lastStepBottom).toBeLessThanOrEqual(
      preparationBoundary.stepsBottom,
    );
    expect(preparationBoundary.lastDescriptionBottom).toBeLessThan(
      preparationBoundary.stepsBottom - 20,
    );
    expect(preparationBoundary.lastStepMarginBottom).toBe('0px');
  });

  test('desktop render has no overflow or browser errors', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);
    await page.goto('/painting/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await page.waitForTimeout(1_500);
    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });

  test('keeps Paper sections and estimate form inside an intermediate desktop viewport', async ({
    page,
  }) => {
    await page.setViewportSize({ width: 1288, height: 1000 });
    await page.goto('/painting/', { waitUntil: 'networkidle' });

    await expectNoHorizontalOverflow(page);
    const geometry = await page.evaluate(() => {
      const viewportWidth = document.documentElement.clientWidth;
      const selectors = [
        '[data-rm-powder-page]',
        '.rm-powder-preparation__layout',
        '.rm-powder-requirements__layout',
        '.rm-powder-estimate__layout',
        '.rm-powder-estimate__form',
      ];

      return selectors.map((selector) => {
        const element = document.querySelector<HTMLElement>(selector);
        const box = element?.getBoundingClientRect();
        return {
          selector,
          left: Math.round(box?.left ?? -1),
          right: Math.round(box?.right ?? viewportWidth + 1),
          viewportWidth,
        };
      });
    });

    for (const item of geometry) {
      expect(item.left, `${item.selector} is clipped on the left`).toBeGreaterThanOrEqual(0);
      expect(item.right, `${item.selector} is clipped on the right`).toBeLessThanOrEqual(
        item.viewportWidth,
      );
    }

    await expect(page.locator('.rm-powder-requirements')).toHaveCSS(
      'background-color',
      'rgb(239, 241, 244)',
    );
  });

  test('P0 @mobile mobile render has no overflow or browser errors', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);
    await page.goto('/painting/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await expect(page.locator('[data-rm-powder-page]')).toBeVisible();
    await page.waitForTimeout(1_500);
    const mobileUploader = page.locator('.rm-powder-estimate__form .uploader');
    await expect(mobileUploader).toBeVisible();
    await expect(mobileUploader.locator('.action')).toBeHidden();
    const uploaderGeometry = await mobileUploader.evaluate((node) => {
      const frame = node.getBoundingClientRect();
      const filename = node.querySelector<HTMLElement>('.filename')!.getBoundingClientRect();
      return {
        filenameLeft: Math.round(filename.left),
        filenameRight: Math.round(filename.right),
        frameLeft: Math.round(frame.left),
        frameRight: Math.round(frame.right),
      };
    });
    expect(uploaderGeometry.filenameLeft).toBeGreaterThanOrEqual(uploaderGeometry.frameLeft);
    expect(uploaderGeometry.filenameRight).toBeLessThanOrEqual(uploaderGeometry.frameRight);
    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });
});
