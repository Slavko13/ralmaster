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
        const filenameAfter = filename ? getComputedStyle(filename, '::after') : null;
        return {
          uploaderBefore: uploader ? getComputedStyle(uploader, '::before').content : null,
          uploaderAfter: uploader ? getComputedStyle(uploader, '::after').content : null,
          filenameBefore: filename ? getComputedStyle(filename, '::before').content : null,
          filenameAfter: filenameAfter?.content ?? null,
          filenameAfterPosition: filenameAfter?.position ?? null,
        };
      })(),
      consent: styles('.licence_block label'),
      submit: styles('input[type="submit"]'),
      submitArrow: (() => {
        const block = pick('.form-footer .inner-table-block:has(input[type="submit"])');
        const arrow = block ? getComputedStyle(block, '::after') : null;
        return arrow
          ? {
              content: arrow.content,
              width: arrow.width,
              height: arrow.height,
              maskImage: arrow.maskImage,
              webkitMaskImage: arrow.webkitMaskImage,
              top: arrow.top,
              transform: arrow.transform,
            }
          : null;
      })(),
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
    filenameAfterPosition: 'static',
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
  expect(signature.submitArrow).toMatchObject({
    content: '""',
    width: '26px',
    height: '26px',
  });
  expect(
    signature.submitArrow?.maskImage !== 'none' ||
      signature.submitArrow?.webkitMaskImage !== 'none',
  ).toBe(true);
  expect(signature.submitArrow?.transform).not.toBe('none');
}

async function expectBlueConsent(root: Locator) {
  const consent = root.locator('.licence_block input[type="checkbox"]');
  await expect(consent).toHaveCSS('width', '20px');
  await expect(consent).toHaveCSS('height', '20px');
  if (!(await consent.isChecked())) await consent.check();
  await expect(consent).toHaveCSS('background-color', 'rgb(23, 91, 239)');
  await consent.uncheck();
}

async function formAnchors(root: Locator) {
  return root.evaluate((node) => {
    const rootBox = node.getBoundingClientRect();
    const top = (selector: string) => {
      const element = node.querySelector<HTMLElement>(selector)!;
      return Math.round(element.getBoundingClientRect().top - rootBox.top);
    };
    return {
      rootHeight: Math.round(rootBox.height),
      contact: top('[data-SID="CONTACT"] input'),
      task: top('[data-SID="TASK"] textarea'),
      file: top('[data-SID="FILE"] .uploader'),
      consent: top('.licence_block'),
      submit: top('input[type="submit"]'),
    };
  });
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

test('inline validation uses reserved rows without moving controls', async ({ page }) => {
  for (const form of inlineForms) {
    await page.goto(form.route, { waitUntil: 'domcontentloaded' });
    const root = page.locator(`${form.root}[data-rm-estimate-form]`);
    await expect(root).toBeVisible();

    for (const sid of ['CONTACT', 'TASK']) {
      await expect(root.locator(`.row[data-SID="${sid}"]`)).toHaveCSS('padding-bottom', '26px');
    }
    await expect(root.locator('.row[data-SID="FILE"]')).toHaveCSS('padding-bottom', '0px');

    const before = await formAnchors(root);
    await root.locator('input[type="submit"]').click();
    await expect(root.locator('[data-SID="CONTACT"] .rm-estimate-error')).toBeVisible();
    await expect(root.locator('[data-SID="TASK"] .rm-estimate-error')).toBeVisible();
    expect(await formAnchors(root)).toEqual(before);

    await root.locator('[data-SID="CONTACT"] input').fill('3214');
    await root.locator('[data-SID="TASK"] textarea').fill('Тестовая задача');
    await root.locator('[data-SID="FILE"] input[type="file"]').setInputFiles({
      name: 'invalid.exe',
      mimeType: 'application/octet-stream',
      buffer: Buffer.from('invalid'),
    });
    const beforeFileError = await formAnchors(root);
    await root.locator('input[type="submit"]').click();
    await expect(root.locator('[data-SID="FILE"] .rm-estimate-error')).toBeVisible();
    expect(await formAnchors(root)).toEqual(beforeFileError);
  }
});

test('all desktop estimate columns have equal height and balanced spacing', async ({ page }) => {
  const layouts = [
    { route: '/', left: '.rm-home-order__intro', right: '.rm-home-order__form' },
    { route: '/product/', left: '.rm-product-estimate__aside', right: '.rm-product-estimate__form' },
    { route: '/painting/', left: '.rm-powder-estimate__aside', right: '.rm-powder-estimate__form' },
    { route: '/services/', left: '.rm-metal-estimate__intro', right: '.rm-metal-estimate__form' },
    { route: '/projects/', left: '.rm-projects-estimate__aside', right: '.rm-projects-estimate__form' },
  ];
  for (const item of layouts) {
    await page.goto(item.route, { waitUntil: 'domcontentloaded' });
    const geometry = await page.locator(item.right).evaluate((right, selectors) => {
      const left = document.querySelector<HTMLElement>(selectors.left)!;
      const rightBox = right.getBoundingClientRect();
      const leftBox = left.getBoundingClientRect();
      const name = right.querySelector<HTMLElement>('[data-SID="NAME"] label')!.getBoundingClientRect();
      const nameInput = right.querySelector<HTMLElement>('[data-SID="NAME"] input')!.getBoundingClientRect();
      const contact = right.querySelector<HTMLElement>('[data-SID="CONTACT"] input')!.getBoundingClientRect();
      const submit = right.querySelector<HTMLElement>('input[type="submit"]')!.getBoundingClientRect();
      const task = right.querySelector<HTMLElement>('[data-SID="TASK"] textarea')!.getBoundingClientRect();
      const uploader = right.querySelector<HTMLElement>('[data-SID="FILE"] .uploader')!.getBoundingClientRect();
      const consent = right.querySelector<HTMLElement>('.licence_block')!.getBoundingClientRect();
      const rowHeight = (sid: string) => Math.round(right.querySelector<HTMLElement>(`.row[data-SID="${sid}"]`)!.getBoundingClientRect().height);
      return {
        route: selectors.route,
        leftHeight: Math.round(leftBox.height),
        rightHeight: Math.round(rightBox.height),
        top: Math.round(name.top - rightBox.top),
        bottom: Math.round(rightBox.bottom - submit.bottom),
        nameToContact: Math.round(contact.top - nameInput.bottom),
        contactToTask: Math.round(task.top - contact.bottom),
        taskToFile: Math.round(uploader.top - task.bottom),
        fileToConsent: Math.round(consent.top - uploader.bottom),
        consentToSubmit: Math.round(submit.top - consent.bottom),
        consentHeight: Math.round(consent.height),
        rows: {
          name: rowHeight('NAME'),
          contact: rowHeight('CONTACT'),
          task: rowHeight('TASK'),
          file: rowHeight('FILE'),
        },
      };
    }, item);
    expect(geometry).toMatchObject({
      route: item.route,
      leftHeight: 738,
      rightHeight: 738,
      top: 32,
      bottom: 51,
      fileToConsent: 58,
      consentToSubmit: 16,
      consentHeight: 20,
      rows: { name: 84, contact: 155, task: 166, file: 82 },
    });
    expect(geometry.nameToContact).toBeGreaterThanOrEqual(88);
    expect(geometry.nameToContact).toBeLessThanOrEqual(89);
    expect(geometry.contactToTask).toBeGreaterThanOrEqual(70);
    expect(geometry.contactToTask).toBeLessThanOrEqual(71);
    expect(geometry.taskToFile).toBeGreaterThanOrEqual(29);
    expect(geometry.taskToFile).toBeLessThanOrEqual(34);
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

    const beforeValidation = await formAnchors(root);
    await root.locator('input[type="submit"]').click();
    await expect(root.locator('[data-SID="CONTACT"] .rm-estimate-error')).toBeVisible();
    await expect(root.locator('[data-SID="TASK"] .rm-estimate-error')).toBeVisible();
    expect(await formAnchors(root)).toEqual(beforeValidation);

    const overflow = await page.evaluate(() => ({
      clientWidth: document.documentElement.clientWidth,
      scrollWidth: document.documentElement.scrollWidth,
    }));
    expect(overflow.scrollWidth).toBeLessThanOrEqual(overflow.clientWidth + 1);
  }
});

