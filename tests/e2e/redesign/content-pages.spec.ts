import AxeBuilder from '@axe-core/playwright';
import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const listRoutes = [
  { path: '/company/reviews/', kind: 'reviews', title: 'Отзывы', count: 4 },
  { path: '/company/staff/', kind: 'staff', title: 'Сотрудники', count: 6 },
  { path: '/company/vacancy/', kind: 'vacancy', title: 'Вакансии', count: 5 },
  { path: '/info/news/', kind: 'news', title: 'Новости', count: 4 },
  { path: '/info/articles/', kind: 'articles', title: 'Статьи', count: 5 },
  { path: '/info/faq/', kind: 'faq', title: 'Вопросы и ответы', count: 5 },
  { path: '/info/sales/', kind: 'sales', title: 'Акции', count: 9 },
] as const;

const detailRoutes = [
  {
    path: '/company/staff/shershnev-denis-vladimirovich/',
    kind: 'staff',
    title: /Шершнев/i,
  },
  { path: '/company/vacancy/slesar/', kind: 'vacancy', title: /Слесарь/i },
  {
    path: '/info/news/rasshirenie-otdela-dostavki/',
    kind: 'news',
    title: /Расширение отдела доставки/i,
  },
  {
    path: '/info/sales/blagotvoritelnaya-aktsiya/',
    kind: 'sales',
    title: /Благотворительная акция/i,
  },
  {
    path: '/info/articles/biznes-sovety/preimushchestva-izgotovleniya-metallokonstruktsiy-pod-klyuch/',
    kind: 'articles',
    title: /Преимущества изготовления металлоконструкций/i,
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

test.describe('P0 consistent company and information pages', () => {
  for (const route of listRoutes) {
    test(`${route.path} uses the shared Paper-derived content system`, async ({ page, request }) => {
      const response = await request.get(route.path, { maxRedirects: 5 });
      expect(response.status()).toBeLessThan(400);

      await page.goto(route.path, { waitUntil: 'domcontentloaded' });
      await expectHealthyDocument(page);
      const root = page.locator(`[data-rm-content-list="${route.kind}"]`);
      await expect(root).toBeVisible();
      await expect(root).toHaveAttribute('data-rm-content-count', String(route.count));
      await expect(root.locator('h1')).toHaveText(route.title);
      await expect(page.locator('.block_before_items, .ask_a_question_wrapper')).toHaveCount(0);
      await expect(page.locator('.ajax_basket > .fixed_wrapper')).toBeHidden();
    });
  }

  for (const route of detailRoutes) {
    test(`${route.path} keeps its dynamic detail route in the new system`, async ({ page }) => {
      await page.goto(route.path, { waitUntil: 'domcontentloaded' });
      await expectHealthyDocument(page);
      const root = page.locator(`[data-rm-content-detail="${route.kind}"]`);
      await expect(root).toBeVisible();
      await expect(root.locator('h1')).toHaveText(route.title);
      await expect(root.locator('.rm-content-back')).toHaveAttribute('href', /^\/(company|info)\//);
    });
  }

  test('FAQ is keyboard-readable and review action keeps the existing Bitrix form hook', async ({ page }) => {
    await page.goto('/info/faq/', { waitUntil: 'domcontentloaded' });
    const firstQuestion = page.locator('.rm-content-faq details').first();
    await firstQuestion.locator('summary').focus();
    await page.keyboard.press('Enter');
    await expect(firstQuestion).toHaveAttribute('open', '');
    await expect(firstQuestion.locator('.rm-content-faq__answer')).toBeVisible();

    await page.goto('/company/reviews/', { waitUntil: 'domcontentloaded' });
    const reviewButton = page.getByRole('button', { name: /Оставить отзыв/i });
    await expect(reviewButton).toHaveAttribute('data-event', 'jqm');
    await expect(reviewButton).toHaveAttribute('data-name', 'feedback');
  });

  test('representative pages have no overflow, browser errors, or axe violations', async ({
    page,
  }, testInfo) => {
    for (const route of [listRoutes[3], listRoutes[5], detailRoutes[4]]) {
      const diagnostics = collectBrowserDiagnostics(page);
      await page.goto(route.path, { waitUntil: 'domcontentloaded' });
      await page.waitForTimeout(500);
      await expectNoHorizontalOverflow(page);
      const axe = await new AxeBuilder({ page })
        .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
        .analyze();
      expect(axe.violations).toEqual([]);
      await attachDiagnostics(testInfo, diagnostics);
      expect(diagnostics).toEqual([]);
    }
  });

  test('P0 @mobile content listings and details remain usable without overflow', async ({
    page,
  }, testInfo) => {
    for (const route of [...listRoutes, detailRoutes[0], detailRoutes[4]]) {
      const diagnostics = collectBrowserDiagnostics(page);
      await page.goto(route.path, { waitUntil: 'domcontentloaded' });
      await expect(page.locator('.rm-content-page')).toBeVisible();
      await expectNoHorizontalOverflow(page);
      await page.waitForTimeout(250);
      await attachDiagnostics(testInfo, diagnostics);
      expect(diagnostics).toEqual([]);
    }
  });
});
