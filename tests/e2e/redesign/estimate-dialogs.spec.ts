import { expect, test, type Page } from '@playwright/test';

type DialogCase = {
  route: string;
  context: string;
  title: string;
  intro: string;
  taskLabel: string;
  placeholder: string;
  uploadLabel: string;
  taskHelp?: string;
  note: string;
  height: number;
};

const cases: DialogCase[] = [
  {
    route: '/services/metal-bending/',
    context: 'metal-bending',
    title: 'Расчёт гибки металла',
    intro:
      'Пришлите чертёж или опишите задачу. Укажите материал, толщину и количество деталей. Если нужны другие операции, добавьте их в описание.',
    taskLabel: 'Описание задачи',
    placeholder:
      'Например: согнуть 300 деталей из стали 3 мм по чертежу. После гибки нужна порошковая окраска.',
    uploadLabel: 'Прикрепить чертежи или техническое задание',
    note:
      'Проверим задачу и свяжемся с вами для уточнения деталей и согласования стоимости.',
    height: 850,
  },
  {
    route: '/painting/poroshkovaya-pokraska-metalla/',
    context: 'powder-coating',
    title: 'Расчёт порошковой окраски',
    intro:
      'Пришлите фотографии или чертежи. Проверим возможность окраски и подготовим расчёт по вашему запросу.',
    taskLabel: 'Что нужно окрасить',
    placeholder:
      'Например: 200 стальных профилей длиной 2 м, без старого покрытия. Нужен цвет RAL 7016, для использования на улице.',
    uploadLabel: 'Прикрепить фотографии, чертежи или ТЗ',
    taskHelp:
      'Укажите размеры, вес, количество и состояние изделий. Цвет и требования к покрытию добавьте, если они уже известны.',
    note: 'Свяжемся с вами для уточнения деталей и согласования условий заказа.',
    height: 898,
  },
];

async function openDialog(page: Page, dialogCase: DialogCase) {
  await page.goto(dialogCase.route, { waitUntil: 'domcontentloaded' });
  const trigger = page.locator(`[data-rm-discuss-open="${dialogCase.context}"]`).first();
  await expect(trigger).toBeVisible();
  await trigger.click();

  const dialog = page.locator(`dialog[data-rm-discuss-variant="${dialogCase.context}"]`);
  await expect(dialog).toBeVisible();
  return dialog;
}

for (const dialogCase of cases) {
  test(`${dialogCase.context} reproduces its Paper request dialog without POST`, async ({ page }) => {
    const postRequests: string[] = [];
    page.on('request', (request) => {
      if (request.method() === 'POST') postRequests.push(request.url());
    });

    const dialog = await openDialog(page, dialogCase);
    await expect(dialog.locator('h2')).toHaveText(dialogCase.title);
    await expect(dialog.locator('.rm-discuss__intro')).toHaveText(dialogCase.intro);
    await expect(dialog.locator('[data-SID="TASK"] label')).toContainText(dialogCase.taskLabel);
    await expect(dialog.locator('[data-SID="TASK"] textarea')).toHaveAttribute(
      'placeholder',
      dialogCase.placeholder,
    );
    await expect(dialog.locator('[data-SID="NAME"] input')).toHaveAttribute(
      'placeholder',
      'Как к вам обращаться',
    );
    await expect(dialog.locator('[data-SID="CONTACT"] input')).toHaveAttribute(
      'placeholder',
      'Укажите удобный способ связи',
    );
    await expect(dialog.locator('.uploader .filename')).toHaveAttribute(
      'data-rm-upload-label',
      dialogCase.uploadLabel,
    );
    await expect(dialog.locator('[data-SID="FILE"] .form-group')).toHaveAttribute(
      'data-rm-file-help',
      'Добавьте описание задачи или файл. Можно отправить и то и другое.',
    );
    if (dialogCase.taskHelp) {
      await expect(dialog.locator('[data-SID="TASK"] .form-group')).toHaveAttribute(
        'data-rm-task-help',
        dialogCase.taskHelp,
      );
    }
    await expect(dialog.locator('.licence_block label')).toContainText(
      'Даю согласие на обработку персональных данных на условиях политики конфиденциальности.',
    );
    await expect(dialog.locator('.rm-discuss__note')).toHaveText(dialogCase.note);
    await expect(dialog.locator('[data-SID="CONTEXT"]')).toHaveValue(dialogCase.context);

    const size = await dialog.evaluate((node) => {
      const rect = node.getBoundingClientRect();
      return { width: Math.round(rect.width), height: Math.round(rect.height) };
    });
    expect(size).toEqual({ width: 748, height: dialogCase.height });

    const paperControls = await dialog.evaluate((node) => {
      const rect = (selector: string) => {
        const element = node.querySelector(selector);
        return element ? Math.round(element.getBoundingClientRect().height) : 0;
      };
      const heading = getComputedStyle(node.querySelector('h2')!);
      const submit = getComputedStyle(node.querySelector('input[type="submit"]')!);
      return {
        headingFontSize: heading.fontSize,
        headingLineHeight: heading.lineHeight,
        nameHeight: rect('[data-SID="NAME"] input'),
        taskHeight: rect('[data-SID="TASK"] textarea'),
        uploadHeight: rect('.uploader'),
        submitHeight: rect('input[type="submit"]'),
        submitAlign: submit.textAlign,
      };
    });
    expect(paperControls).toEqual({
      headingFontSize: '36px',
      headingLineHeight: '44px',
      nameHeight: 56,
      taskHeight: 116,
      uploadHeight: 56,
      submitHeight: 60,
      submitAlign: 'left',
    });

    await dialog.locator('input[type="submit"]').click();
    await expect(dialog.locator('[data-SID="CONTACT"] .rm-estimate-error')).toBeVisible();
    await expect(dialog.locator('[data-SID="TASK"] .rm-estimate-error')).toBeVisible();
    expect(postRequests).toEqual([]);

    await page.keyboard.press('Escape');
    await expect(dialog).toBeHidden();
  });
}

test('P0 @mobile calculation dialogs stay inside a 390 px viewport', async ({ page }) => {
  for (const dialogCase of cases) {
    const dialog = await openDialog(page, dialogCase);
    const geometry = await dialog.evaluate((node) => {
      const rect = node.getBoundingClientRect();
      const box = (selector: string) => {
        const element = node.querySelector<HTMLElement>(selector);
        if (!element) return null;
        const elementRect = element.getBoundingClientRect();
        return {
          top: Math.round(elementRect.top),
          bottom: Math.round(elementRect.bottom),
        };
      };
      return {
        left: Math.round(rect.left),
        right: Math.round(rect.right),
        width: Math.round(rect.width),
        viewport: document.documentElement.clientWidth,
        boxes: {
          title: box('.rm-discuss__header h2'),
          intro: box('.rm-discuss__intro'),
          name: box('[data-SID="NAME"]'),
          contact: box('[data-SID="CONTACT"]'),
          task: box('[data-SID="TASK"]'),
          file: box('[data-SID="FILE"]'),
          consent: box('.licence_block'),
          submit: box('input[type="submit"]'),
        },
      };
    });
    expect(geometry.left).toBeGreaterThanOrEqual(0);
    expect(geometry.right).toBeLessThanOrEqual(geometry.viewport + 1);
    expect(geometry.width).toBeLessThanOrEqual(390);

    const orderedBoxes = [
      geometry.boxes.title,
      geometry.boxes.intro,
      geometry.boxes.name,
      geometry.boxes.contact,
      geometry.boxes.task,
      geometry.boxes.file,
      geometry.boxes.consent,
      geometry.boxes.submit,
    ];
    expect(orderedBoxes.every(Boolean)).toBe(true);
    for (let index = 1; index < orderedBoxes.length; index += 1) {
      expect(orderedBoxes[index]!.top).toBeGreaterThanOrEqual(orderedBoxes[index - 1]!.bottom);
    }
    await page.keyboard.press('Escape');
  }
});
