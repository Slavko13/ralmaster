import { expect, test, type Locator, type Page } from '@playwright/test';

type PrimaryButtonCase = {
  label: string;
  route: string;
  selector: string;
};

const primaryButtons: PrimaryButtonCase[] = [
  {
    label: 'home hero',
    route: '/',
    selector: '.rm-home-hero .rm-button--primary',
  },
  {
    label: 'metalworking hero',
    route: '/services/',
    selector: '.rm-metal-hero .rm-metal-button',
  },
  {
    label: 'powder hero',
    route: '/painting/',
    selector: '.rm-powder-hero .rm-powder-button--primary',
  },
  {
    label: 'product intro',
    route: '/product/',
    selector: '.rm-products-intro .rm-products-button--primary',
  },
  {
    label: 'production contact',
    route: '/company/',
    selector: '.rm-production-contact__actions button',
  },
  {
    label: 'contacts summary',
    route: '/contacts/',
    selector: '.rm-contacts-button',
  },
  {
    label: 'service detail',
    route: '/services/laser-cutting/',
    selector: '.rm-detail-button',
  },
  {
    label: 'project detail',
    route: '/projects/krepezhnye-kronshteyny/',
    selector: '.rm-project-detail__cta-primary',
  },
  {
    label: 'system search',
    route: '/search/?q=%D0%BC%D0%B5%D1%82%D0%B0%D0%BB%D0%BB',
    selector: '.rm-system-button',
  },
];

async function activeTransform(page: Page, button: Locator): Promise<string> {
  const box = await button.boundingBox();
  expect(box).not.toBeNull();
  await page.mouse.move(box!.x + box!.width / 2, box!.y + box!.height / 2);
  await page.mouse.down();
  await page.waitForTimeout(180);
  const transform = await button.evaluate((node) => getComputedStyle(node).transform);
  await page.mouse.move(box!.x - 4, box!.y - 4);
  await page.mouse.up();
  return transform;
}

function expectPressedScale(transform: string): void {
  const match = transform.match(/^matrix\(([^,]+),\s*0,\s*0,\s*([^,]+),/);
  expect(match, `Expected a 2D scale matrix, received ${transform}`).not.toBeNull();
  expect(Number(match![1])).toBeCloseTo(0.99, 2);
  expect(Number(match![2])).toBeCloseTo(0.99, 2);
}

test.describe('RALMASTER interaction states', () => {
  for (const buttonCase of primaryButtons) {
    test(`${buttonCase.label} uses the shared primary hover, focus and press states`, async ({
      page,
    }) => {
      await page.goto(buttonCase.route, { waitUntil: 'domcontentloaded' });
      const button = page.locator(buttonCase.selector).first();
      await button.scrollIntoViewIfNeeded();
      await expect(button).toBeVisible();

      await expect(button).toHaveCSS('background-color', 'rgb(23, 91, 239)');
      await button.hover();
      await page.waitForTimeout(180);
      await expect(button).toHaveCSS('background-color', 'rgb(13, 75, 215)');
      await expect(button).toHaveCSS('color', 'rgb(255, 255, 255)');

      await button.focus();
      await expect(button).toHaveCSS('outline-style', 'solid');
      await expect(button).toHaveCSS('outline-color', 'rgb(23, 91, 239)');

      const transform = await activeTransform(page, button);
      expectPressedScale(transform);
    });
  }

  test('secondary buttons share the pale-blue hover and the same press feedback', async ({
    page,
  }) => {
    const cases = [
      { route: '/', selector: '.rm-home-hero .rm-button--secondary' },
      { route: '/painting/', selector: '.rm-powder-button--secondary' },
    ];

    for (const secondaryCase of cases) {
      await page.goto(secondaryCase.route, { waitUntil: 'domcontentloaded' });
      const button = page.locator(secondaryCase.selector).first();
      await button.scrollIntoViewIfNeeded();
      await expect(button).toBeVisible();
      await button.hover();
      await page.waitForTimeout(180);
      await expect(button).toHaveCSS('background-color', 'rgb(238, 244, 255)');
      await expect(button).toHaveCSS('color', 'rgb(23, 91, 239)');
      expectPressedScale(await activeTransform(page, button));
    }
  });

  test('button arrows move diagonally without moving their buttons', async ({ page }) => {
    const cases = [
      {
        route: '/painting/',
        button: '.rm-powder-hero .rm-powder-button--primary',
        arrow: '.rm-powder-button__arrow',
      },
      {
        route: '/product/',
        button: '.rm-products-intro .rm-products-button--primary',
        arrow: '.rm-products-button__arrow',
      },
      {
        route: '/projects/krepezhnye-kronshteyny/',
        button: '.rm-project-detail__cta-primary',
        arrow: '.rm-ui-arrow',
      },
    ];

    for (const arrowCase of cases) {
      await page.goto(arrowCase.route, { waitUntil: 'domcontentloaded' });
      const button = page.locator(arrowCase.button).first();
      const arrow = button.locator(arrowCase.arrow).first();
      await button.scrollIntoViewIfNeeded();
      await button.hover();
      await page.waitForTimeout(180);
      await expect(button).toHaveCSS('transform', 'none');
      await expect(arrow).toHaveCSS('transform', 'matrix(1, 0, 0, 1, 2, -2)');
    }

    await page.goto('/services/', { waitUntil: 'domcontentloaded' });
    const metalButton = page.locator('.rm-metal-hero .rm-metal-button').first();
    await metalButton.hover();
    await page.waitForTimeout(180);
    await expect(metalButton).toHaveCSS('transform', 'none');
    const pseudoTransform = await metalButton.evaluate(
      (node) => getComputedStyle(node, '::after').transform,
    );
    expect(pseudoTransform).toBe('matrix(1, 0, 0, 1, 2, -2)');
  });
});
