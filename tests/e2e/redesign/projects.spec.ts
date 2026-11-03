import AxeBuilder from '@axe-core/playwright';
import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const expectedProjects = [
  {
    code: 'zhk-simvol',
    href: '/projects/zhk-simvol/',
    image: 'project-symbol.webp',
    width: 1920,
    height: 1280,
  },
  {
    code: 'zhk-belyy-grad',
    href: '/projects/zhk-belyy-grad/',
    image: 'project-white-city.webp',
    width: 1920,
    height: 1535,
  },
  {
    code: 'zhk-simvol-2',
    href: '/projects/zhk-simvol-2/',
    image: 'project-symbol-2.webp',
    width: 1536,
    height: 1024,
  },
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
  expect(geometry.scrollWidth).toBeLessThanOrEqual(geometry.clientWidth + 1);
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

async function sectionHeights(
  page: Page,
  selector: string,
  names: readonly string[],
): Promise<Record<string, number>> {
  return page.locator(selector).evaluateAll((nodes, requestedNames) => {
    const values: Record<string, number> = {};
    for (const name of requestedNames as string[]) {
      const node = nodes.find((candidate) =>
        candidate.getAttribute('data-rm-projects-section') === name ||
        candidate.getAttribute('data-rm-project-detail-section') === name,
      );
      values[name] = Math.round(node?.getBoundingClientRect().height ?? 0);
    }
    return values;
  }, names);
}

test.describe('P0 redesigned projects listing and dynamic details', () => {
  test('renders exactly three active IBlock projects with real SEF links', async ({ page, request }) => {
    await page.goto('/projects/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);

    await expect(page.locator('.rm-projects-hero h1')).toHaveText('Реализованные проекты');
    const cards = page.locator('.rm-project-card');
    await expect(cards).toHaveCount(3);

    for (const [index, project] of expectedProjects.entries()) {
      const card = cards.nth(index);
      await expect(card).toHaveAttribute('data-project-code', project.code);
      await expect(card.locator('.rm-project-card__link')).toHaveAttribute('href', project.href);
      await expect(card.locator('.rm-project-card__meta')).toContainText('2025');
      await expect(card.locator('.rm-project-card__meta')).toContainText('По индивидуальному проекту');
      await expect(card.locator('.rm-project-card__summary > p')).toHaveText(
        'Декоративные корзины для кондиционеров',
      );
      const response = await request.get(project.href, { maxRedirects: 5 });
      expect(response.status(), `Broken project route: ${project.href}`).toBeLessThan(400);
    }
  });

  test('matches the Paper card order and keeps every product image aligned', async ({ page }) => {
    await page.goto('/projects/', { waitUntil: 'networkidle' });

    const projectGeometry = await page.locator('.rm-project-card').evaluateAll((cards) =>
      cards.map((card) => {
        const body = card.querySelector('.rm-project-card__body')!.getBoundingClientRect();
        const image = card.querySelector('.rm-project-card__image')!.getBoundingClientRect();
        return { bodyX: Math.round(body.x), imageX: Math.round(image.x) };
      }),
    );
    expect(projectGeometry.every(({ bodyX, imageX }) => bodyX < imageX)).toBe(true);

    const productGeometry = await page
      .locator('.rm-projects-product-card')
      .evaluateAll((cards) =>
        cards.map((card) => {
          const header = card
            .querySelector('.rm-projects-product-card__header')!
            .getBoundingClientRect();
          const mediaElement = card.querySelector(
            '.rm-projects-product-card__media',
          ) as HTMLElement;
          const media = mediaElement.getBoundingClientRect();
          const image = card.querySelector('img')!.getBoundingClientRect();
          const descriptionElement = card.querySelector(
            '.rm-projects-product-card__description',
          ) as HTMLElement;
          const description = descriptionElement.getBoundingClientRect();
          return {
            headerTop: Math.round(header.top),
            headerBottom: Math.round(header.bottom),
            imageTop: Math.round(media.top),
            imageBottom: Math.round(media.bottom),
            imageInsetTop: Math.round(image.top - media.top),
            imageInsetBottom: Math.round(media.bottom - image.bottom),
            objectFit: getComputedStyle(card.querySelector('img')!).objectFit,
            mediaOverflow: getComputedStyle(mediaElement).overflow,
            descriptionTop: Math.round(description.top),
            descriptionFits:
              descriptionElement.scrollHeight <= descriptionElement.clientHeight,
          };
        }),
      );
    expect(new Set(productGeometry.map(({ headerTop }) => headerTop)).size).toBe(1);
    expect(new Set(productGeometry.map(({ imageTop }) => imageTop)).size).toBe(1);
    expect(
      productGeometry.every(
        ({ headerBottom, imageTop, imageBottom, descriptionTop }) =>
          headerBottom === imageTop && imageBottom === descriptionTop,
      ),
    ).toBe(true);
    expect(productGeometry.every(({ descriptionFits }) => descriptionFits)).toBe(true);
    expect(productGeometry.every(({ mediaOverflow }) => mediaOverflow === 'hidden')).toBe(true);
    expect(Math.abs(productGeometry[0].imageInsetTop)).toBeLessThanOrEqual(1);
    expect(Math.abs(productGeometry[0].imageInsetBottom)).toBeLessThanOrEqual(1);
    await expect(page.locator('.rm-projects-product-card img').first()).toHaveCSS(
      'object-fit',
      'fill',
    );

    await expect(page.locator('.rm-projects-operation-grid img')).toHaveCount(3);
  });

  test('uses the shared restrained image hover on product cards', async ({ page }) => {
    await page.goto('/projects/', { waitUntil: 'networkidle' });

    const card = page.locator('.rm-projects-product-card').first();
    const image = card.locator('img');
    await card.hover();
    await page.waitForTimeout(220);

    await expect(card).toHaveCSS('transform', 'none');
    await expect(card).toHaveCSS('box-shadow', /rgba\(17, 30, 53, 0\.16\).*18px 40px/);
    await expect(image).toHaveCSS('transform', 'matrix(1.015, 0, 0, 1.015, 0, 0)');

    const operationLink = page.locator('.rm-projects-operations__footer a').first();
    await operationLink.hover();
    await expect(operationLink).toHaveCSS('color', 'rgb(255, 255, 255)');
    await expect(operationLink).toHaveCSS('background-color', 'rgb(23, 91, 239)');
  });

  test('loads localized project assets and keeps product category URLs dynamic', async ({ page }) => {
    await page.goto('/projects/', { waitUntil: 'networkidle' });

    for (const [index, project] of expectedProjects.entries()) {
      const image = page.locator('.rm-project-card__image img').nth(index);
      await expect(image).toHaveAttribute(
        'src',
        new RegExp(`/images/ralmaster/projects/${project.image.replace('.', '\\.')}$`),
      );
      await expect
        .poll(() =>
          image.evaluate((node) => {
            const element = node as HTMLImageElement;
            return {
              complete: element.complete,
              largeEnough: element.naturalWidth >= 600 && element.naturalHeight >= 400,
              local: element.currentSrc.includes('/images/ralmaster/projects/'),
            };
          }),
        )
        .toEqual({ complete: true, largeEnough: true, local: true });
      await expect(image).toHaveAttribute(
        'srcset',
        new RegExp(`/images/ralmaster/projects/${project.image.replace('.webp', '-768\\.webp')} 768w`),
      );
    }

    const productHrefs = await page.locator('.rm-projects-product-card').evaluateAll((nodes) =>
      nodes.map((node) => (node as HTMLAnchorElement).getAttribute('href')),
    );
    expect(productHrefs).toEqual([
      '/product/facadecassettes/',
      '/product/baskets/',
      '/product/pannier/',
    ]);
  });

  test('renders Symbol from the universal dynamic template without generic demo sections', async ({ page }) => {
    await page.goto('/projects/zhk-simvol/', { waitUntil: 'networkidle' });
    await expectHealthyDocument(page);

    const detail = page.locator('[data-rm-project-detail]');
    await expect(detail).toHaveClass(/rm-project-detail--symbol/);
    await expect(detail).toHaveAttribute('data-project-code', 'zhk-simvol');
    await expect(detail.locator('h1')).toContainText('ЖК «Символ»');
    await expect(detail.locator('.rm-project-detail__hero-media img')).toHaveAttribute(
      'src',
      /\/images\/ralmaster\/projects\/project-symbol\.webp$/,
    );
    await expect(detail.locator('[data-rm-project-detail-section="work"]')).toHaveCount(0);
    await expect(detail.locator('[data-rm-project-detail-section="result"]')).toHaveCount(0);
    await expect(detail.locator('.rm-project-detail__passport-empty')).toBeVisible();
  });

  test('renders a generic detail with dynamic project data and honest optional fallbacks', async ({ page }) => {
    await page.goto('/projects/zhk-belyy-grad/', { waitUntil: 'networkidle' });
    await expectHealthyDocument(page);

    const detail = page.locator('[data-rm-project-detail]');
    await expect(detail).toHaveClass(/rm-project-detail--generic/);
    await expect(detail).toHaveAttribute('data-project-code', 'zhk-belyy-grad');
    await expect(detail.locator('h1')).toContainText('Белый град');
    await expect(detail.locator('.rm-project-detail__intro')).toContainText('2025');
    await expect(detail.locator('.rm-project-detail__passport dl > div')).toHaveCount(0);
    await expect(detail.locator('.rm-project-detail__passport-empty')).toBeVisible();
    await expect(detail.locator('.rm-project-detail__notice')).toContainText(
      'Дополнительные характеристики заказа',
    );
    await expect(detail.locator('[data-rm-project-detail-section="work"] img')).toHaveAttribute(
      'src',
      /\/images\/ralmaster\/projects\/generic-project-work\.webp$/,
    );
    await expect(detail.locator('[data-rm-project-detail-section="result"] img')).toHaveCount(2);
  });

  test('renders the brackets project linked from the home drawings card', async ({ page }) => {
    await page.goto('/projects/krepezhnye-kronshteyny/', { waitUntil: 'networkidle' });
    await expectHealthyDocument(page);

    const detail = page.locator('[data-rm-project-detail]');
    await expect(detail).toHaveAttribute('data-project-code', 'krepezhnye-kronshteyny');
    await expect(detail.locator('h1')).toContainText('Крепёжные кронштейны');
    await expect(detail.locator('h1')).toContainText('по чертежам заказчика');
    await expect(detail.locator('.rm-project-detail__hero-media img')).toHaveAttribute(
      'src',
      /\/images\/ralmaster\/projects\/generic-project-hero\.webp$/,
    );
    await expect(detail.locator('.rm-project-detail__passport dl > div')).toHaveCount(6);
    await expect(detail.locator('.rm-project-detail__steps > li')).toHaveCount(3);
    await expect(detail.locator('.rm-project-detail__result-grid img')).toHaveCount(2);
    await expect(detail.locator('.rm-projects-kicker')).toHaveCount(0);
    await expect(detail.locator('.rm-project-detail__result-heading h2')).toHaveText('Результат');
    await expect(detail.locator('.rm-project-detail__result-links > span')).toHaveText(
      'Подробнее о возможностях:',
    );
    await expect(detail.locator('.rm-project-detail__result-links a')).toHaveText([
      'Металлообработка',
      'Порошковая окраска',
    ]);
    await expect(detail.locator('.rm-project-detail__result-links .rm-ui-arrow')).toHaveCount(2);

    const resultLink = detail.locator('.rm-project-detail__result-links a').first();
    const resultTransformBefore = await resultLink.evaluate((node) => getComputedStyle(node).transform);
    await resultLink.hover();
    await page.waitForTimeout(220);
    const resultTransformAfter = await resultLink.evaluate((node) => getComputedStyle(node).transform);
    expect(resultTransformAfter).not.toBe(resultTransformBefore);

    const cta = detail.locator('[data-rm-project-detail-section="cta"]');
    await expect(cta.locator('.rm-project-detail__cta-actions > p')).toBeVisible();
    await expect(cta.locator('.rm-project-detail__cta-primary')).toHaveText(/Рассчитать похожий заказ/);
    await expect(cta.locator('.rm-project-detail__cta-primary > .rm-ui-arrow')).toBeVisible();
    await expect(cta.locator('.rm-project-detail__cta-secondary')).toHaveText(/Все выполненные заказы/);
    const ctaFlow = await cta.locator('.rm-project-detail__cta-card').evaluate((card) => {
      const heading = card.querySelector('h2')!.getBoundingClientRect();
      const copy = card.querySelector('.rm-project-detail__cta-actions > p')!.getBoundingClientRect();
      const button = card.querySelector('.rm-project-detail__cta-primary')!.getBoundingClientRect();
      return {
        cardHeight: Math.round(card.getBoundingClientRect().height),
        copyAboveButton: copy.bottom < button.top,
        headingLeftOfActions: heading.right < copy.left,
      };
    });
    expect(ctaFlow).toEqual({ cardHeight: 248, copyAboveButton: true, headingLeftOfActions: true });
    const primary = cta.locator('.rm-project-detail__cta-primary');
    await primary.hover();
    await expect(primary).toHaveCSS('background-color', 'rgb(15, 79, 209)');
    await expect(primary).toHaveCSS('color', 'rgb(255, 255, 255)');
  });

  test('matches the desktop Paper section heights for the listing', async ({ page }) => {
    await page.goto('/projects/', { waitUntil: 'networkidle' });
    expect(
      await sectionHeights(page, '[data-rm-projects-section]', [
        'hero',
        'projects',
        'products',
        'operations',
        'estimate',
        'production-photo',
      ]),
    ).toEqual({
      hero: 226,
      projects: 1128,
      products: 696,
      operations: 616,
      estimate: 850,
      'production-photo': 543,
    });
  });

  test('matches the desktop Paper section heights for a generic detail', async ({ page }) => {
    await page.goto('/projects/zhk-belyy-grad/', { waitUntil: 'networkidle' });
    expect(
      await sectionHeights(page, '[data-rm-project-detail-section]', [
        'hero',
        'project',
        'work',
        'result',
        'cta',
        'production-photo',
      ]),
    ).toEqual({
      hero: 1206,
      project: 682,
      work: 688,
      result: 766,
      cta: 412,
      'production-photo': 543,
    });
  });

  test('matches the desktop Paper section heights for the Symbol detail', async ({ page }) => {
    await page.goto('/projects/zhk-simvol/', { waitUntil: 'networkidle' });
    expect(
      await sectionHeights(page, '[data-rm-project-detail-section]', [
        'hero',
        'project',
        'cta',
        'production-photo',
      ]),
    ).toEqual({ hero: 1186, project: 507, cta: 412, 'production-photo': 543 });
  });

  test('validates the compact estimate form without sending a POST', async ({ page }) => {
    const postRequests: string[] = [];
    page.on('request', (request) => {
      if (request.method() === 'POST') postRequests.push(request.url());
    });

    await page.goto('/projects/', { waitUntil: 'domcontentloaded' });
    const formRoot = page.locator(
      '[data-rm-estimate-form][data-rm-estimate-context="projects"]',
    );
    await expect(formRoot).toBeVisible();
    await expect(formRoot.getByRole('radio')).toHaveText([
      'Telegram',
      'MAX',
      'Телефон',
      'Почта',
    ]);
    await expect(formRoot.locator('[data-SID="CONTEXT"]')).toHaveValue(
      'projects; contact=telegram',
    );

    await formRoot.locator('[data-SID="CONTACT"] input').fill('qa-projects@example.test');
    await formRoot.locator('[data-SID="TASK"] textarea').fill('');

    const consent = formRoot.locator('.licence_block input[type="checkbox"]');
    if ((await consent.count()) > 0 && !(await consent.isChecked())) {
      await consent.evaluate((node) => {
        const input = node as HTMLInputElement;
        input.checked = true;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      });
    }
    await expect(consent).toHaveCSS('background-color', 'rgb(23, 91, 239)');

    await formRoot.locator('input[type="submit"]').click();
    await expect(formRoot.locator('[data-SID="TASK"] .rm-estimate-error')).toHaveText(
      'Опишите задачу или прикрепите файл.',
    );
    expect(postRequests).toEqual([]);
  });

  test('keeps a clear gap between the project description and file uploader', async ({ page }) => {
    await page.goto('/projects/', { waitUntil: 'domcontentloaded' });

    const geometry = await page
      .locator('[data-rm-estimate-form][data-rm-estimate-context="projects"]')
      .evaluate((form) => {
        const formBox = form.getBoundingClientRect();
        const textarea = form.querySelector('[data-SID="TASK"] textarea')!.getBoundingClientRect();
        const uploader = form.querySelector('[data-SID="FILE"] .uploader')!.getBoundingClientRect();
        const submit = form.querySelector('input[type="submit"]')!.getBoundingClientRect();
        return {
          spacing: uploader.top - textarea.bottom,
          submitBottom: submit.bottom,
          formBottom: formBox.bottom,
        };
      });
    expect(geometry.spacing).toBeGreaterThanOrEqual(16);
    expect(geometry.submitBottom).toBeLessThanOrEqual(geometry.formBottom);
  });

  test('desktop listing and details have no overflow or browser errors', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);
    for (const route of ['/projects/', '/projects/zhk-simvol/', '/projects/zhk-belyy-grad/']) {
      await page.goto(route, { waitUntil: 'domcontentloaded' });
      await expectHealthyDocument(page);
      await expectNoHorizontalOverflow(page);
    }
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });

  test('keeps listing cards and operation media inside an intermediate desktop viewport', async ({
    page,
  }) => {
    await page.setViewportSize({ width: 1288, height: 1000 });
    await page.goto('/projects/', { waitUntil: 'networkidle' });
    await expectNoHorizontalOverflow(page);

    for (const selector of ['.rm-project-card', '.rm-projects-operation-grid']) {
      const boxes = await page.locator(selector).evaluateAll((nodes) =>
        nodes.map((node) => {
          const rect = node.getBoundingClientRect();
          return { left: rect.left, right: rect.right };
        }),
      );
      expect(boxes.length).toBeGreaterThan(0);
      expect(boxes.every(({ left, right }) => left >= 0 && right <= 1288)).toBe(true);
    }
  });

  test('has no critical axe violations', async ({ page }, testInfo) => {
    await page.goto('/projects/', { waitUntil: 'networkidle' });
    const results = await new AxeBuilder({ page })
      .include('.rm-projects-page')
      .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
      .analyze();
    const critical = results.violations.filter((violation) => violation.impact === 'critical');
    await testInfo.attach('axe-results', {
      body: JSON.stringify(results, null, 2),
      contentType: 'application/json',
    });
    expect(critical).toEqual([]);
  });

  for (const mobileRoute of [
    { label: 'listing', href: '/projects/' },
    { label: 'Symbol detail', href: '/projects/zhk-simvol/' },
    { label: 'generic detail', href: '/projects/zhk-belyy-grad/' },
    { label: 'brackets detail', href: '/projects/krepezhnye-kronshteyny/' },
  ] as const) {
    test(`P0 @mobile ${mobileRoute.label} has no horizontal overflow or browser errors`, async ({ page }, testInfo) => {
      const diagnostics = collectBrowserDiagnostics(page);
      await page.goto(mobileRoute.href, { waitUntil: 'domcontentloaded' });
      await expectHealthyDocument(page);
      await expectNoHorizontalOverflow(page);
      await attachDiagnostics(testInfo, diagnostics);
      expect(diagnostics).toEqual([]);
    });
  }

  test('@mobile listing keeps breadcrumbs clear of the header and product images uncropped', async ({ page }) => {
    await page.goto('/projects/', { waitUntil: 'networkidle' });
    const header = page.locator('#mobileheader');
    const breadcrumbs = page.locator('.rm-projects-breadcrumbs');
    const image = page.locator('.rm-projects-product-card img').first();
    await expect(image).toHaveCSS('object-fit', 'contain');
    const geometry = await page.evaluate(() => {
      const headerBox = document.querySelector('#mobileheader')!.getBoundingClientRect();
      const breadcrumbBox = document.querySelector('.rm-projects-breadcrumbs')!.getBoundingClientRect();
      return { gap: Math.round(breadcrumbBox.top - headerBox.bottom) };
    });
    expect(geometry.gap).toBeGreaterThanOrEqual(20);
    await expect(header).toBeVisible();
    await expect(breadcrumbs).toContainText('Главная');
  });
});
