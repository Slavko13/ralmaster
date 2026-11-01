import { expect, test, type Locator } from '@playwright/test';

type FormCase = {
  name: string;
  route: string;
  root: string;
};

const inlineForms: FormCase[] = [
  { name: 'home', route: '/', root: '.rm-home-order__form' },
  { name: 'product', route: '/product/', root: '.rm-product-estimate__form' },
  { name: 'painting', route: '/painting/', root: '.rm-powder-estimate__form' },
  { name: 'metalworking', route: '/services/', root: '.rm-metal-estimate__form' },
  { name: 'projects', route: '/projects/', root: '.rm-projects-estimate__form' },
];

async function formSignature(root: Locator) {
  return root.evaluate((node) => {
    const pick = (selector: string) => node.querySelector<HTMLElement>(selector);
    const styles = (selector: string) => {
      const element = pick(selector);
      if (!element) return null;
      const style = getComputedStyle(element);
      const rect = element.getBoundingClientRect();
      return {
        fontFamily: style.fontFamily,
        fontSize: style.fontSize,
        fontWeight: style.fontWeight,
        lineHeight: style.lineHeight,
        color: style.color,
        backgroundColor: style.backgroundColor,
        borderColor: style.borderColor,
        borderStyle: style.borderStyle,
        borderRadius: style.borderRadius,
        padding: style.padding,
        width: Math.round(rect.width),
        height: Math.round(rect.height),
      };
    };

    return {
      label: styles('[data-SID="NAME"] .form-group > label'),
      input: styles('[data-SID="NAME"] input'),
      contact: styles('[data-SID="CONTACT"] input'),
      textarea: styles('[data-SID="TASK"] textarea, [data-SID="MESSAGE"] textarea'),
      file: styles('[data-SID="FILE"] .uploader'),
      fileAction: styles('[data-SID="FILE"] .uploader .action'),
      fileName: styles('[data-SID="FILE"] .uploader .filename'),
      fileContent: (() => {
        const uploader = pick('[data-SID="FILE"] .uploader');
        const filename = pick('[data-SID="FILE"] .uploader .filename');
        return {
          uploaderBefore: uploader ? getComputedStyle(uploader, '::before').content : null,
          uploaderAfter: uploader ? getComputedStyle(uploader, '::after').content : null,
          filenameBefore: filename ? getComputedStyle(filename, '::before').content : null,
          filenameAfter: filename ? getComputedStyle(filename, '::after').content : null,
        };
      })(),
      consent: styles('.licence_block label'),
      submit: styles('input[type="submit"]'),
    };
  });
}

function expectSharedContract(signature: Awaited<ReturnType<typeof formSignature>>) {
  const field = {
    fontFamily: 'Onest, Arial, sans-serif',
    fontSize: '16px',
    fontWeight: '400',
    lineHeight: '24px',
    color: 'rgb(17, 30, 53)',
    backgroundColor: 'rgb(255, 255, 255)',
    borderColor: 'rgb(204, 214, 226)',
    borderStyle: 'solid',
    borderRadius: '4px',
    padding: '15px 16px',
  };

  expect(signature.label).toMatchObject({
    fontFamily: 'Onest, Arial, sans-serif',
    fontSize: '14px',
    fontWeight: '500',
    lineHeight: '20px',
    color: 'rgb(17, 30, 53)',
  });
  expect(signature.input).toMatchObject({ ...field, height: 56 });
  expect(signature.contact).toMatchObject({ ...field, height: 56 });
  expect(signature.textarea).toMatchObject({ ...field, height: 112 });
  expect(signature.file).toMatchObject({
    fontFamily: 'Onest, Arial, sans-serif',
    fontSize: '0px',
    lineHeight: '0px',
    backgroundColor: 'rgb(255, 255, 255)',
    borderColor: 'rgb(170, 184, 202)',
    borderStyle: 'dashed',
    borderRadius: '4px',
    padding: '8px',
    height: 56,
  });
  expect(signature.fileAction).toMatchObject({
    fontFamily: 'Onest, Arial, sans-serif',
    fontSize: '12px',
    lineHeight: '18px',
    color: 'rgb(52, 75, 106)',
    backgroundColor: 'rgb(255, 255, 255)',
    borderColor: 'rgb(216, 224, 232)',
    borderStyle: 'solid',
    borderRadius: '3px',
    padding: '10px 16px',
    height: 40,
  });
  expect(signature.fileContent).toEqual({
    uploaderBefore: 'none',
    uploaderAfter: 'none',
    filenameBefore: '""',
    filenameAfter: '"Прикрепить файлы"',
  });
  expect(signature.consent).toMatchObject({
    fontFamily: 'Onest, Arial, sans-serif',
    fontSize: '13px',
    fontWeight: '500',
    lineHeight: '20px',
    color: 'rgb(95, 111, 132)',
    height: 20,
  });
  expect(signature.submit).toMatchObject({
    fontFamily: 'Onest, Arial, sans-serif',
    fontSize: '16px',
    fontWeight: '500',
    lineHeight: '24px',
    color: 'rgb(255, 255, 255)',
    backgroundColor: 'rgb(23, 91, 239)',
    borderColor: 'rgb(23, 91, 239)',
    borderStyle: 'solid',
    borderRadius: '4px',
    padding: '0px 56px 0px 22px',
    height: 60,
  });
}

async function expectBlueConsent(root: Locator) {
  const consent = root.locator('.licence_block input[type="checkbox"]');
  await expect(consent).toHaveCSS('width', '20px');
  await expect(consent).toHaveCSS('height', '20px');
  if (!(await consent.isChecked())) await consent.check();
  await expect(consent).toHaveCSS('background-color', 'rgb(23, 91, 239)');
  await consent.uncheck();
}

test('all inline estimate forms share one visual contract', async ({ page }) => {

  for (const form of inlineForms) {
    await page.goto(form.route, { waitUntil: 'domcontentloaded' });
    const root = page.locator(`${form.root}[data-rm-estimate-form]`);
    await expect(root).toBeVisible();
    await expect(root.getByRole('radio')).toHaveText([
      'Telegram',
      'MAX',
      'Телефон',
      'Почта',
    ]);
    expectSharedContract(await formSignature(root));
    await expectBlueConsent(root);
  }
});

test('the shared estimate dialog uses the same visual contract', async ({ page }) => {
  await page.goto('/', { waitUntil: 'domcontentloaded' });
  await page.locator('[data-rm-discuss-open="home"]').first().click();
  const root = page.locator(
    'dialog[data-rm-discuss-variant="home"] [data-rm-estimate-form]',
  );
  await expect(root).toBeVisible();
  expectSharedContract(await formSignature(root));
  await expectBlueConsent(root);
});

test('P0 @mobile unified forms remain usable without horizontal overflow', async ({
  page,
}) => {
  for (const form of inlineForms) {
    await page.goto(form.route, { waitUntil: 'domcontentloaded' });
    const root = page.locator(`${form.root}[data-rm-estimate-form]`);
    await expect(root).toBeVisible();
    await expect(root.locator('[data-SID="FILE"] .uploader')).toHaveCSS('height', '56px');
    await expect(root.locator('[data-SID="FILE"] .uploader .action')).toBeHidden();
    await expect(root.locator('input[type="submit"]')).toBeVisible();
    await expectBlueConsent(root);

    const overflow = await page.evaluate(() => ({
      clientWidth: document.documentElement.clientWidth,
      scrollWidth: document.documentElement.scrollWidth,
    }));
    expect(overflow.scrollWidth).toBeLessThanOrEqual(overflow.clientWidth + 1);
  }
});

