# Визуальный контроль

Последнее обновление: 2026-10-29. Статус: **ВСЕ 12 PAPER-ФРЕЙМОВ РЕАЛИЗОВАНЫ; SAFE DESKTOP/MOBILE UI GATE PASSED; STATE-CHANGING FORM NOT RUN**.

## Источник истины

Целевой интерфейс находится в Paper Desktop: `Мария's Team` → `Ралмастер` →
`Дизайн` (`01M5F74BVZEJ54GZ0MW8A0JM06`, `p-2-0`). Для всех 12
desktop/modal-фреймов прочитаны trees, JSX, computed styles, размеры,
screenshots и доступные оригинальные images. Content hash при фиксации
эталонов: `811c9dc5`.

Полноразмерные целевые WebP-эталоны находятся в
`docs/redesign/references/paper/`. Они должны сравниваться с локальной
реализацией отдельно от исторических screenshots старого сайта.

## Что уже проверено

- Реальный рендер главной, производства, контактов, проектов и формы «Обсудить заказ» в Paper.
- Desktop canvas 1440 px, контейнеры 1312/1328 px, header 106 px, footer 430 px.
- Основные typography, colors, gaps, radii, button/input dimensions.
- Наличие 50 уникальных Paper file-assets и доступ к original URL/metadata.
- Полноразмерный export каждого из 12 целевых фреймов.
- Секционные screenshots header light/transparent, footer, buttons, inline
  form и modal через Paper MCP.
- Playwright снимает full-page baseline screenshots текущего production.
- Локальный desktop header: 1440×106, logo 152×24, footer: 1440×430;
  horizontal overflow отсутствует.
- Локальный derived mobile shell: header 390×72, footer находится в обычном
  потоке и полностью содержит контент; horizontal overflow отсутствует.
- Footer приведён к Paper-композиции: 1328 px content, navigation, production
  и office addresses, contacts, messenger controls и legal row.
- Главная реализована как 10 корневых секций в точном Paper-порядке; desktop
  full-page capture имеет размер 1440×8645.
- Измеренные секционные границы: hero `0–579`, work model `579–739`, products
  `739–2643`, projects `2643–3639`, partners `3639–4201`, production
  `4201–5283`, process `5283–6165`, FAQ `6165–7012`, order `7012–7672`,
  photo `7672–8215`, footer `8215–8645`.
- `desktop-1440-full.png` сопоставлен с `01-home.webp`; сохранены отдельные
  hero/order crops. Проверены реальные локальные Paper assets, порядок и
  подписи product/service/partner cards.
- Производный mobile capture 390 px не имеет horizontal overflow; изображения
  lazy-loaded до съёмки, console/page errors отсутствуют.
- `/product/` сопоставлен с Paper-фреймом «Продукция»: фактический desktop
  canvas 1440×3240; form section 804 px и form root 692 px, controls не
  обрезаются; 390 px без overflow.
- `/services/` сопоставлен с «Металлообработка»: фактический desktop canvas
  1440×5741, порядок/высоты секций и footer проверены; 390 px без
  horizontal overflow, tablet входит в финальный combined run.
- `/painting/` и detail alias сопоставлены с «Порошковая окраска»: desktop
  1440×6396. После корректировки form wrappers окно 748×738 полностью содержит
  TASK, upload, consent и submit (`clipped=false`).
- `/company/` сопоставлен с «О производстве»: desktop 1440×7203, включая
  секции hero/how/capabilities/order/repeat/shipping/partners/FAQ/CTA/photo и
  footer 430 px. Самостоятельный capture повторно подтвердил секционные
  границы; screenshot визуально сопоставлен с `06-production.webp`.
- `/projects/` сопоставлен с «Реализованные проекты»: desktop 1440×4405;
  универсальный detail — 1440×4833, ЖК «Символ» — 1440×3184. Listing/detail
  используют IBlock 36/SEF, а отсутствующие CMS passport/gallery данные
  показываются как честные empty/demo states.
- `/contacts/` сопоставлен с «Контакты»: desktop 1440×4120. Телефон, email и
  адреса читаются из существующих Bitrix/include источников. Фактическая
  Yandex-карта отличается от статического Paper-изображения масштабом и двумя
  маркерами; это документированное ограничение, а не скрытая pixel-match заявка.
- Три modal-фрейма проверены по точной geometry: discuss-order 748×850,
  metal-bending 748×850, powder-coating 748×898. Heading 36/44, inputs 56,
  textarea 116, upload 56 и submit 60 закреплены computed-style assertions.
- Home inline form проверена в Paper-геометрии 748×548: NAME/CONTACT 56,
  TASK 112, uploader 90, consent и submit 60; дубли labels, native file control
  и clipped submit устранены.
- Итоговый post-`UI-003` redesign regression на dev 8084, desktop+mobile,
  прошёл 61/61 за 6.4 min, exit 0; critical axe, browser errors и horizontal
  overflow отсутствуют в проверенном scope.
- Финальный visual capture после всех исправлений пересоздан для 20
  viewport/page runs: diagnostics `[]`, horizontal overflow отсутствует.
- Capture выявил overlap в mobile powder dialog при переносе title/task help/
  file help/consent на две строки внутри fixed rows. Mobile dialog переведён на
  natural-flow heights; попарный layout-test прошёл 1/1, а
  `powder-mobile-390-dialog-bottom.png` визуально подтверждает отсутствие
  overlap между fields, consent и submit.

## Что ещё не проверено или ограничено исходными данными

- Прямой pixel diff для mobile/tablet невозможен: Paper содержит только
  desktop/modal-фреймы; responsive 390 является производным решением.
- Успешный server-side submit/success, mail и CRM не проверялись без mail sink;
  client errors, disabled/loading и timeout recovery реализованы и проверены.
- Контактная карта наследует рабочую Yandex-интеграцию и визуально отличается
  от статического Paper-фона; hardcoded key остаётся `SEC-001`.
- В CMS нет утверждённых полей паспорта/фотогалереи проектов и PDF-карточки
  компании; UI не подменяет их фиктивными production-данными.
- Production-like visual capture после последней force-recreate не заменяет
  dev full-page evidence и фиксируется отдельно при необходимости.

## Матрица будущих проверок

| Объект | Desktop | Tablet | Mobile | Interaction | Статус |
| --- | --- | --- | --- | --- | --- |
| Общий shell | 1440 | 1024/768 | 390 | menu/focus/modal | PASSED |
| Главная | 1440×8645 PASS | derived | 390 PASS | CTA/FAQ/form UI PASS; submit not run | PASSED |
| Продукция | 1440×3240 PASS | derived | 390 PASS | categories/form UI PASS; submit not run | PASSED |
| Металлообработка | 1440×5741 PASS | derived | 390 PASS | dynamic services/form/dialog PASS | PASSED |
| Порошковая окраска | 1440×6396 PASS | derived | 390 PASS, natural-flow no overlap | inline + 748×898 dialog PASS | PASSED |
| Производство | 1440×7203 PASS | 1024/768 PASS | 390 PASS | table/FAQ/CTA PASS | PASSED |
| Проекты | 1440×4405 / 4833 / 3184 PASS | derived | 390 PASS | navigation/empty states PASS | PASSED |
| Контакты | 1440×4120 PASS | derived | 390 PASS | map/dialog UI PASS | PASSED WITH LIMITATIONS |
| Формы запроса расчёта | 748×850 / 748×898 PASS | derived | 390 PASS | validation/upload/loading PASS; POST not run | PASSED (SAFE UI) |

## Hotfix desktop navigation — 2026-10-29

- До исправления wide-menu «Продукция» схлопывался в узкую колонку, а 18 px
  gap закрывал submenu при движении к «Гибке металла».
- После исправления панель продукции имеет размер 1008×178.84, содержит 7
  равномерно разложенных пунктов и 7 изображений; overlay-header прозрачен и
  все шесть top-links видимы.
- 25-шаговая траектория курсора сохраняет services dropdown открытым; keyboard
  `focus-within` работает; переход на detail возвращает HTTP 200.
- Console/page errors: `[]`. Артефакты:
  `test-results/menu-audit-final/products.png` и `services.png`.

## Hotfix legacy sticky header — 2026-10-29

- Chrome 1440×900, scrollY=600: на главной и powder detail старая fixed-шапка
  отсутствует; новый `.rm-header` штатно уходит за viewport.
- На главной единственный видимый fixed-элемент после scroll — штатная кнопка
  `#scrollToTop`; на powder detail видимых fixed/sticky элементов нет.
- Console/page errors: `[]`.
- Артефакт: `test-results/sticky-audit-painting-after-fix-scroll900.png`.

## Процедура итерации

## Коррекция header — 2026-10-31

- Источник: общий header фреймов Paper `Мария's Team / Ралмастер / Дизайн`.
- Desktop capture `test-results/redesign-shell/desktop-1440-top.png`: header 1440×106, шесть прямых белых ссылок поверх hero, logo/phone/alignment соответствуют Paper; dropdown-слоёв нет.
- Mobile capture `test-results/redesign-shell/mobile-390-top.png`: 390×72, контент не выходит за viewport; navigation drawer проверен функционально как плоский список.
- Вердикт checkpoint: spacing/typography/contrast/alignment/artboard fit — PASS; отдельного mobile Paper-фрейма нет, поэтому mobile остаётся согласованной производной реализацией.

1. Снять screenshot реализации на фиксированном viewport.
2. Сопоставить структуру, bounding boxes, spacing, typography, colors, images и content flow с Paper.
3. Отдельно проверить responsive и interaction states как производные решения.
4. Зафиксировать отклонения и их причину.
5. Исправить, пересобрать и повторить functional + visual checks.
6. Не переводить итерацию в `PASSED` при новой критической визуальной регрессии: перекрытом контенте, горизонтальном overflow, недоступном CTA или сломанной навигации.

## Ограничения

- Paper MCP доступен; технического Paper-блокера нет.
- Production-like runtime gate пройден; UI-итерации выполняются последовательно.
- Dedicated Web Form SID реализует file, единый `CONTACT` и `TASK || FILE`;
  inline и modal-варианты проверены. Реальный submit/mail/CRM остаётся
  непроверенным функциональным ограничением, а не скрытым визуальным PASS.
- Нет утверждённых responsive-макетов и interaction states; эти проверки
  будут оценивать согласованные производные решения, а не прямой pixel diff.
- Очень высокие страницы необходимо сравнивать секциями: интерактивный Paper
  preview уменьшает их до 2000 px, тогда как сохранённые exports имеют
  исходные размеры.
