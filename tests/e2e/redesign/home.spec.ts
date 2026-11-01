import { expect, test, type ConsoleMessage, type Page, type TestInfo } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const expectedSections = [
  'hero',
  'work-model',
  'products',
  'projects',
  'partners',
  'production',
  'process',
  'faq',
  'order',
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
    diagnostics.push({
      kind: 'pageerror',
      message: error.stack ?? error.message,
    });
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

test.describe('P0 redesigned home', () => {
  test('matches the Paper information architecture and exact hero copy', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);

    const home = page.locator('[data-rm-home]');
    await expect(home).toBeVisible();

    const heading = home.locator('[data-rm-section="hero"] h1');
    await expect(heading).toBeVisible();
    const normalizedHeading = (await heading.innerText()).replace(/\s+/g, ' ').trim();
    expect(normalizedHeading).toBe(
      'Изготовление металлических изделий и металлообработка',
    );

    const sections = await home
      .locator(':scope > [data-rm-section]')
      .evaluateAll((nodes) => nodes.map((node) => node.getAttribute('data-rm-section')));
    expect(sections).toEqual(expectedSections);
  });

  test('keeps card data dynamic and all redesigned links local', async ({ page, request }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    const home = page.locator('[data-rm-home]');

    await expect(home.locator('[data-rm-card="project"]').first()).toBeVisible();
    await expect(home.locator('[data-rm-card="partner"]').first()).toBeVisible();
    await expect(home.locator('.rm-home-hero__feature')).toHaveAttribute(
      'href',
      '/product/facadecassettes/',
    );
    await expect(home.locator('.rm-home-service-card')).toHaveCount(3);
    expect(
      await home.locator('.rm-home-service-card').evaluateAll((cards) =>
        cards.every((card) => card.getAttribute('href') === '/services/'),
      ),
    ).toBe(true);

    const hrefs = await home.locator('a[href]').evaluateAll((links) =>
      links
        .map((link) => link.getAttribute('href'))
        .filter((href): href is string => Boolean(href)),
    );

    expect(hrefs.length).toBeGreaterThan(0);
    expect(hrefs.join('\n')).not.toMatch(/bavrasem\.beget\.tech/i);
    await expect(home.locator('.rm-home-partner')).toHaveCount(7);
    expect(
      await home.locator('.rm-home-partner').evaluateAll((nodes) =>
        nodes.every((node) => node.getAttribute('href') === '/company/partners/'),
      ),
    ).toBe(true);

    const routeHrefs = [
      ...new Set(
        hrefs.filter(
          (href) =>
            href.startsWith('/') &&
            !href.startsWith('//') &&
            !href.startsWith('/ajax/') &&
            !href.startsWith('/bitrix/'),
        ),
      ),
    ];

    const representativeRoutes = ['/product/', '/services/', '/projects/'].map((prefix) => {
      const href = routeHrefs.find((candidate) => candidate.startsWith(prefix));
      expect(href, `Missing redesigned home link for ${prefix}`).toBeTruthy();
      return href!;
    });

    for (const href of representativeRoutes) {
      const response = await request.get(href, { maxRedirects: 5 });
      expect(response.status(), `Broken internal home link: ${href}`).toBeLessThan(500);
    }

    const arrows = await home.locator('.rm-link-arrow').evaluateAll((nodes) =>
      nodes.map((node) => {
        const style = getComputedStyle(node, '::after');
        return { content: style.content, backgroundImage: style.backgroundImage };
      }),
    );
    expect(arrows.length).toBeGreaterThan(0);
    expect(arrows.every((arrow) => arrow.content === '"↗"')).toBe(true);
    expect(arrows.every((arrow) => arrow.backgroundImage === 'none')).toBe(true);

    const productHeading = home
      .locator('.rm-home-group-heading h3', { hasText: 'Изготовление металлических изделий' })
      .first();
    await expect(productHeading).toHaveCSS('white-space', 'nowrap');

    const productCard = home.locator('.rm-home-product-card').first();
    await productCard.hover();
    await page.waitForTimeout(220);
    expect(await productCard.evaluate((node) => getComputedStyle(node).transform)).not.toBe('none');
    expect(await productCard.evaluate((node) => getComputedStyle(node).boxShadow)).not.toBe('none');
  });

  test('loads every rendered image asset', async ({ page }) => {
    await page.goto('/', { waitUntil: 'networkidle' });
    const images = page.locator('[data-rm-home] img');
    const imageCount = await images.count();
    expect(imageCount).toBeGreaterThan(0);

    for (let index = 0; index < imageCount; index += 1) {
      const image = images.nth(index);
      await image.scrollIntoViewIfNeeded();
      await expect
        .poll(() =>
          image.evaluate((element) => {
            const node = element as HTMLImageElement;
            return node.complete && node.naturalWidth > 0 && node.naturalHeight > 0;
          }),
        )
        .toBe(true);
    }
  });

  test('FAQ is keyboard operable and exposes its controlled panel', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    const trigger = page
      .locator('[data-rm-section="faq"] button[aria-expanded][aria-controls]')
      .first();

    await expect(trigger).toBeVisible();
    const panelId = await trigger.getAttribute('aria-controls');
    expect(panelId).toBeTruthy();

    const panel = page.locator(`#${panelId}`);
    await expect(panel).toHaveCount(1);

    const initialState = await trigger.getAttribute('aria-expanded');
    expect(initialState === 'true' || initialState === 'false').toBe(true);

    await trigger.focus();
    await expect(trigger).toBeFocused();
    await page.keyboard.press('Enter');
    await expect(trigger).toHaveAttribute(
      'aria-expanded',
      initialState === 'true' ? 'false' : 'true',
    );

    await page.keyboard.press('Space');
    await expect(trigger).toHaveAttribute('aria-expanded', initialState!);
  });

  test('request modal opens and closes without submitting', async ({ page }) => {
    const postRequests: string[] = [];
    page.on('request', (request) => {
      if (request.method() === 'POST') postRequests.push(request.url());
    });

    await page.goto('/', { waitUntil: 'domcontentloaded' });
    const trigger = page
      .locator('[data-rm-section="hero"] [data-rm-discuss-open="home"]')
      .first();

    await expect(trigger).toBeVisible();
    await trigger.click();

    const dialog = page.locator('dialog[data-rm-discuss-variant="home"]');
    await expect(dialog).toBeVisible();
    await expect(dialog.locator('h2')).toHaveText('Обсудить заказ');
    await expect(dialog.locator('[data-SID="CONTEXT"]')).toHaveValue('home; contact=telegram');
    await page.keyboard.press('Escape');
    await expect(dialog).toBeHidden();
    expect(postRequests).toEqual([]);
  });

  test('inline validation messages stay between their own controls and the next field', async ({
    page,
  }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    const formRoot = page.locator('.rm-home-order__form[data-rm-estimate-form]');
    await expect(formRoot.getByRole('radio')).toHaveText([
      'Telegram',
      'MAX',
      'Телефон',
      'Почта',
    ]);
    await expect(formRoot.locator('[data-SID="CONTEXT"]')).toHaveValue(
      'home; contact=telegram',
    );
    await formRoot.locator('input[type="submit"]').click();

    const geometry = await formRoot.evaluate((root) => {
      const box = (selector: string) => {
        const rect = root.querySelector<HTMLElement>(selector)!.getBoundingClientRect();
        return { top: rect.top, bottom: rect.bottom };
      };
      return {
        contact: box('[data-SID="CONTACT"] input'),
        contactError: box('[data-SID="CONTACT"] .rm-estimate-error'),
        task: box('[data-SID="TASK"] textarea'),
        taskError: box('[data-SID="TASK"] .rm-estimate-error'),
        file: box('[data-SID="FILE"]'),
      };
    });

    expect(geometry.contactError.top).toBeGreaterThanOrEqual(geometry.contact.bottom);
    expect(geometry.contactError.bottom).toBeLessThanOrEqual(geometry.task.top);
    expect(geometry.taskError.top).toBeGreaterThanOrEqual(geometry.task.bottom);
    expect(geometry.taskError.bottom).toBeLessThanOrEqual(geometry.file.top);
  });

  test('keeps the inline form visible and confirms a successful request by name', async ({
    page,
  }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    const formRoot = page.locator('.rm-home-order__form[data-rm-estimate-form]');
    await formRoot.locator('[data-SID="NAME"] input').fill('Мария');
    await formRoot.getByRole('radio', { name: 'Почта' }).click();
    await formRoot.locator('[data-SID="CONTACT"] input').fill('maria@example.com');
    await formRoot.locator('[data-SID="TASK"] textarea').fill('Тест заявки с главной страницы.');
    await formRoot.locator('.licence_block input[type="checkbox"]').check();
    await formRoot.locator('input[type="submit"]').click();

    const success = page.locator('dialog[data-rm-estimate-success-dialog]');
    await expect(success).toBeVisible({ timeout: 15_000 });
    await expect(success.getByRole('heading')).toHaveText('Ваша заявка принята');
    await expect(success.getByText(/Спасибо, Мария!/)).toBeVisible();
    await expect(formRoot.locator('form')).toBeVisible();
    await expect(formRoot.locator('.form.contacts.success')).toHaveCount(0);
    await success.getByRole('button', { name: 'ОК' }).click();
    await expect(success).toBeHidden();
    await expect(formRoot.locator('form')).toBeVisible();
  });

  test('places the email below the phone in the order contact panel', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    const contacts = page.locator('.rm-home-order__contacts');
    const geometry = await contacts.locator('a').evaluateAll((links) =>
      links.map((link) => {
        const rect = link.getBoundingClientRect();
        return { top: rect.top, bottom: rect.bottom, left: rect.left };
      }),
    );

    expect(geometry).toHaveLength(2);
    expect(geometry[1].top).toBeGreaterThanOrEqual(geometry[0].bottom);
    expect(Math.abs(geometry[1].left - geometry[0].left)).toBeLessThanOrEqual(1);
  });

  test('desktop render has no overflow or browser errors', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);

    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await page.waitForTimeout(2_000);
    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });

  test('P0 @mobile mobile render has no overflow or browser errors', async ({ page }, testInfo) => {
    const diagnostics = collectBrowserDiagnostics(page);

    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expectHealthyDocument(page);
    await expect(page.locator('[data-rm-home]')).toBeVisible();
    await page.waitForTimeout(2_000);
    await expectNoHorizontalOverflow(page);
    await attachDiagnostics(testInfo, diagnostics);
    expect(diagnostics).toEqual([]);
  });
});
