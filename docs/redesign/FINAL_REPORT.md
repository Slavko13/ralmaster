# Итоговый отчёт по редизайну

Статус: **UI REDESIGN COMPLETED — SAFE ACCEPTANCE PASSED; STATE-CHANGING MAIL/CRM GATE NOT RUN**.

Дата последнего обновления: 2026-10-28.

Все 12 целевых Paper-фреймов реализованы и прошли итоговую безопасную
desktop/mobile регрессию. Статус не включает успешную отправку заявок,
почтовые уведомления или внешние CRM hooks: эти state-changing проверки
намеренно не запускались без mail sink и изоляции интеграций.

## Актуальный источник дизайна

- Team: `Мария's Team`.
- File: `Ралмастер` (`01M5F74BVZEJ54GZ0MW8A0JM06`).
- Page: `Дизайн` (`p-2-0`).
- Paper MCP повторно подтверждает 12 фреймов, 2854 узла и content hash
  `811c9dc5`.
- Доступны tree, node info, JSX, computed styles, geometry, screenshots и
  оригинальные image fills.
- `Polite hill` и `Cheerful breeze` являются устаревшими названиями из ранних
  заметок и не используются как целевой источник.

## Реализовано

### Среда и архитектура

- Исходники находятся в `J:\ralmaster\ralmaser\public_html`.
- Рабочая ветка: `feature/full-paper-redesign`; исходная контрольная точка:
  `a43ff3ee71789da5ee8ddbf2a5bb64c8e0eb0ffb`.
- Development Docker Compose работает на `127.0.0.1:8084`.
- Изолированный production-like Compose проверен на `127.0.0.1:8085` и не
  использует production-БД.
- Локальный backup импортирован без публикации SQL; Bitrix runtime содержит 422
  таблицы.
- Выполнено сопоставление Paper → Bitrix templates/components/IBlocks; ядро
  `/bitrix/modules/` не изменяется.

### Paper и дизайн-система

- Изучены все 12 фреймов.
- Сохранены 12 полноразмерных Paper WebP-эталонов в
  `docs/redesign/references/paper/`.
- Извлечены локальные шрифты, цвета, типографическая шкала, контейнеры,
  отступы, формы и responsive-правила.
- Оригинальные ассеты главной локализованы; runtime-hotlink на Paper не
  используется.

### Общий shell

- Реализованы desktop/mobile header, navigation и footer по Paper.
- Сохранены динамические Bitrix menu/include данные и существующие маршруты.
- Устранена исходная рассинхронизация старых banner button styles через новый
  scoped UI, без хаотичного ручного редактирования minified bundle.

### Формы запроса

- Добавлена отдельная additive Web Form SID `ralmaster_estimate_s1`; общая
  legacy Web Form ID 5 не изменялась.
- Схема: optional `NAME`, required phone-or-email `CONTACT`, optional `TASK`,
  optional `FILE`, hidden `CONTEXT`.
- Client/server validation выражает `TASK || FILE`, лимит 20 MB и allowlist
  документов/изображений/CAD; контекст страницы передаётся скрытым полем.
- До применения сохранён полный локальный snapshot, исключённый из Git.
  Идемпотентные apply/verify прошли в dev и изолированной production-like БД.
- Реальный submit, почтовое уведомление и CRM hooks не выполнялись.

### Главная страница

- Реализованы 10 корневых Paper-секций: hero, work model, products, projects,
  partners, production, process, FAQ, order form и production photo.
- Динамические URL/данные читаются из IBlock 41, 40, 36 и 20; SEF и detail
  routes сохранены.
- Desktop geometry совпадает по секционным границам с Paper: `1440×8645`.
- Производный mobile layout на 390 px не имеет horizontal overflow.
- FAQ поддерживает keyboard interaction и одно открытое состояние.

### Продукция

- `/product/` реализован по Paper на существующем `bitrix:news`, IBlock 41 и
  SEF; семь активных разделов и URL остаются динамическими.
- Desktop geometry — 1440×3240; inline form использует dedicated SID и
  context `product`.

### Металлообработка и порошковая окраска

- `/services/` реализован с динамическими разделами IBlock 40 и сохранёнными
  detail routes; desktop geometry — 1440×5741.
- `/painting/` и `/painting/poroshkovaya-pokraska-metalla/` используют один
  reusable block и отвечают 200; desktop geometry — 1440×6396.
- Powder form 748×738 измерена без clipping всех controls.

### Производство

- `/company/` реализован по Paper; партнёры и URL читаются из IBlock 20.
- `/company/partners/` остаётся доступным legacy child route.
- Desktop geometry — 1440×7203. FAQ является явно документированным
  presentation fallback из-за несовпадения IBlock 17, а ограничения
  оборудования помечены как примерные и требующие подтверждения.

### Проекты

- `/projects/` реализован на существующем IBlock 36/SEF; desktop geometry —
  1440×4405.
- Универсальный detail соответствует 1440×4833, отдельная композиция
  `/projects/zhk-simvol/` — 1440×3184.
- Восемь Paper-ассетов локализованы. Отсутствующие в CMS TASK/ORDERER/DATA/
  PHOTOS/passport значения показаны честными empty/demo states, а не выданы за
  фактические данные заказа.

### Контакты

- `/contacts/` реализован в geometry 1440×4120; телефон, email и адреса читаются
  из существующих Bitrix/include источников.
- Существующая Yandex-карта сохранена. Её масштаб и два маркера визуально
  отличаются от статического Paper-фона; hardcoded key остаётся `SEC-001`.
- Реквизиты являются явно обозначенным presentation fallback: утверждённая
  CMS-карточка компании/PDF в исходных данных отсутствует.

### Диалоги и формы

- Реализован общий discuss-order dialog 748×850, bending 748×850 и
  powder-coating 748×898.
- Exact Paper typography/controls закреплены тестами: title 36/44, поля 56,
  textarea 116, uploader 56, submit 60.
- Исправлены повторная декорация после Bitrix AJAX replacement, success/error/
  loading layout, double-submit protection, timeout recovery и keyboard CTA.
- Финальный visual capture выявил mobile powder overlap при двухстрочных
  title/help/consent в fixed rows. Mobile CSS переведён на natural-flow heights,
  а тест теперь попарно проверяет неперекрытие title/intro/fields/consent/submit.
- Home inline form приведена к 748×548 без дублирующих labels, native file
  input и clipped submit.

## Проверки

- Исторический safe baseline: `54/54 PASS`.
- Исторический smoke: `32/32 PASS`.
- Home redesign: исторический suite `7/7 PASS`; после переключения на
  dedicated SID повторный desktop suite `6/6 PASS`, geometry снова 1440×8645.
- Product redesign: `8/8 desktop + 1/1 mobile PASS`.
- Metalworking: `5/5 desktop + 1/1 mobile PASS`.
- Powder coating: `7/7 desktop + 1/1 mobile PASS`.
- Production: `8/8 desktop + 1/1 mobile PASS`; 768/1024 overflow gate PASS.
- Projects listing/detail/Symbol, contacts и dialogs включены в полный redesign
  suite и безопасный route smoke.
- Mobile regression главной после обновления CTA fallback: `2/2 PASS`.
- Финальный post-`UI-003` redesign на dev 8084, desktop+mobile:
  **61/61 PASS** за 6.4 min, exit 0.
- Обновлённый baseline desktop+mobile: **22/22 PASS** за 2.7 min.
- Dev route smoke: **32/32 PASS** за 2.8 min.
- Production-like smoke после force-recreate: **32/32 PASS** за 2.6 min,
  exit 0. Первая sandbox-попытка была инфраструктурно невалидна из-за
  недоступного browser executable; корректный системный Chromium run зелёный.
- Dev mobile powder overlap-test: **1/1 PASS** за 11.7 s.
- Production-like dialogs после исправления: **3/3 PASS** за 22.1 s на
  desktop+mobile, без POST.
- Targeted dialogs: **3/3 PASS**; home+dialogs: **10/10 PASS**; Services после
  ARIA-fix: **5/5 PASS**.
- Финальный axe gate: critical violations = 0 в проверенном scope.
- Desktop/mobile captures: без `console.error`, `pageerror`, broken images и
  horizontal overflow.
- Финальный capture пересоздан: 20 viewport/page runs, diagnostics `[]`, без
  horizontal overflow; `powder-mobile-390-dialog-bottom.png` подтверждает
  отсутствие overlap.
- PHP lint и JavaScript syntax check изменённых файлов: PASS.

Подробные команды, длительности и артефакты находятся в `TEST_RESULTS.md` и
`VISUAL_QA.md`.

## Открытые gates и ограничения

- State-changing form tests с локальным mail sink и отключёнными CRM hooks:
  успешное сохранение результата, mail template и external integrations —
  **NOT RUN**. Ни одной реальной заявки не отправлялось.
- `SEC-001`: hardcoded map key остаётся в tracked include; требуется перенос в
  env и решение о ротации.
- Paper не содержит mobile/tablet и interaction-state frames; responsive и
  error/loading states являются производными решениями.
- Project passport/gallery и часть contact requisites не имеют утверждённого
  CMS-источника; UI показывает документированные empty/presentation states.
- Production-like после последней force-recreate: form schema verify и smoke
  32/32 прошли на 8085.

## Известные ограничения

Paper-фреймы «Расчёт гибки металла» и «Расчёт порошковой окраски» являются
формами ручного запроса, а не калькуляторами: формулы и автоматический результат
не требуются. Несоответствие legacy ID 5 закрыто отдельной additive form SID и
одинаковой client/server validation, но сами route-specific modal presentations
реализованы и прошли safe UI/validation gate. Это не доказывает server-side
success/mail/CRM lifecycle.

До валидной state-changing проверки необходимо изолировать почту и внешние CRM
hooks. Реальные заявки в production не отправляются.

Playwright default `BASE_URL` синхронизирован с development Compose и равен
`http://127.0.0.1:8084`; production-like проверки явно используют 8085.

## Итоговая граница приёмки

UI scope завершён: 12 Paper-фреймов реализованы, desktop geometry и derived
mobile layouts проверены, безопасные functional/accessibility/browser suites
зелёные. Проект нельзя называть полностью проверенным по бизнес-доставке заявки,
пока отдельно не пройден state-changing mail/CRM gate и не закрыт `SEC-001`.
Production-инфраструктура и Paper-файл не изменялись; commit/push не выполнялись.
