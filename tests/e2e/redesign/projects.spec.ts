import AxeBuilder from '@axe-core/playwright';
import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const expectedProjects = [
  {
    code: 'zhk-simvol',
    href: '/projects/zhk-simvol/',
    image: 'project-symbol.jpg',
    width: 1920,
    height: 1280,
  },
  {
    code: 'zhk-belyy-grad',
    href: '/projects/zhk-belyy-grad/',
    image: 'project-white-city.jpg',
    width: 1920,
    height: 1535,
  },
  {
    code: 'zhk-simvol-2',
    href: '/projects/zhk-simvol-2/',
    image: 'project-symbol-2.png',
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
      const response = await request.get(project.href, { maxRedirects: 5 });
      expect(response.status(), `Broken project route: ${project.href}`).toBeLessThan(400);
    }
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
              width: element.naturalWidth,
              height: element.naturalHeight,
            };
          }),
        )
        .toEqual({ complete: true, width: project.width, height: project.height });
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
      /\/images\/ralmaster\/projects\/project-symbol\.jpg$/,
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
      /\/images\/ralmaster\/projects\/generic-project-work\.png$/,
    );
    await expect(detail.locator('[data-rm-project-detail-section="result"] img')).toHaveCount(2);
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
      estimate: 660,
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
    await expect(formRoot.locator('[data-SID="CONTEXT"]')).toHaveValue('projects');

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

    await formRoot.locator('input[type="submit"]').click();
    await expect(formRoot.locator('[data-SID="TASK"] .rm-estimate-error')).toHaveText(
      'Опишите задачу или прикрепите файл.',
    );
    expect(postRequests).toEqual([]);
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
});
