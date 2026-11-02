# Инженерные итерации редизайна

Последнее обновление: 2026-10-29.

Допустимые статусы: `TODO`, `IN PROGRESS`, `PASSED`, `BLOCKED`. Следующая продуктовая итерация не начинается, пока в текущей остаётся новая критическая регрессия. Read-only аудит и подготовка инфраструктуры могут выполняться параллельно, но не заменяют прохождение gate.

## Итерация 0 — Контроль исходного состояния и локальная среда

- **Цель:** обеспечить безопасную точку возврата и воспроизводимый dev/production-like запуск.
- **Область изменений:** Git, ignore rules, Dockerfile, Compose, env template, healthcheck, инструкции.
- **Связанные макеты Paper:** все фреймы косвенно; визуальная реализация не выполняется.
- **Затронутые файлы и компоненты:** `.gitignore`, `.dockerignore`, `.env.example`, `Dockerfile`, `compose*.yml`, `docker/**`, `README.md`.
- **Функциональные автотесты:** Compose validation, healthcheck, smoke `/`, затем полный Playwright baseline.
- **Визуальные проверки:** отсутствуют до запуска приложения.
- **Критерии приёмки:** `docker compose up --build` и production-like profile запускаются; БД импортирована; `/healthz.php` и `/` доступны; baseline проходит или исходные дефекты документированы.
- **Фактический результат:** первичная инфраструктурная точка `c345081...` сохранена, а фактический baseline HEAD полного редизайна зафиксирован как `a43ff3e...`; оба Compose-профиля валидны и фактически запущены; dev работает на 8084, изолированный production-like — на 8085; оба `db`/`web` healthy; dev и production-like схемы содержат по 422 таблицы; health/home/assets отвечают 200; production-like browser diagnostics 4/4 и smoke 32/32 проходят.
- **Обнаруженные дефекты:** исторически Docker Desktop падал на stale OTel socket, WSL возвращал `E_ACCESS_DENIED`, DB backup не был найден в project root; первый production-like запуск конфликтовал с dev project и не мог создать writable mountpoints внутри read-only bind.
- **Исправленные дефекты:** Docker Linux runtime восстановлен; чувствительный backup найден вне Git и импортирован; dev/prod Compose projects разделены; runtime mountpoints сохранены placeholders; безопасный потоковый DB clone не пишет SQL на хост и не перезаписывает непустой destination; секреты не внесены в Git.
- **Оставшиеся ограничения:** state-changing проверки выделены отдельно; backup и `.env` остаются вне Git; production-like image остаётся локальным acceptance artifact с read-only bind, а не deployment image.
- **Статус:** `PASSED`.

## Итерация 1 — Функциональный аудит и baseline

- **Цель:** зафиксировать текущие маршруты, сценарии и безопасный автоматический регрессионный контур до UI-изменений.
- **Область изменений:** только QA-инфраструктура и документация; код сайта не меняется.
- **Связанные макеты Paper:** все page frames используются для будущей трассировки.
- **Затронутые файлы и компоненты:** `package*.json`, `playwright.config.ts`, `tests/**`, `docs/redesign/01-*`, `02-*`, `TEST_RESULTS.md`.
- **Функциональные автотесты:** primary/secondary routes, navigation, catalog/services detail, search, invalid forms, mobile menu/CTA, 404, accessibility.
- **Визуальные проверки:** baseline screenshots текущего production на desktop/mobile.
- **Критерии приёмки:** полный безопасный live baseline зафиксирован; тот же safe-набор повторён на локальной БД; критических axe, browser errors, 5xx и fatal нет; исходные дефекты документированы.
- **Фактический результат:** аудит маршрутов завершён; Playwright/axe инфраструктура создана; expanded live baseline — 54/54 за 2.1 min; local smoke — 32/32 за 3.0 min; полный safe local baseline — 54/54 за 5.2 min. Во всех полных прогонах 0 failed/skipped/flaky; local browser diagnostics чисты, critical axe = 0, после импорта 5xx/fatal не обнаружены.
- **Обнаруженные дефекты:** исходные content/legacy/absolute-link риски и serious accessibility baseline; `/form/` без ID ожидаемо возвращает not-found без fatal.
- **Исправленные дефекты:** устранены ошибки селекторов и ошибочное наследование mobile browser-настроек в тестах.
- **Оставшиеся ограничения:** state-changing forms/cart/auth не входят в safe baseline и должны быть выполнены отдельно только локально; known serious accessibility issues сохранены для исправления редизайном.
- **Статус:** `PASSED`.

## Итерация 2 — Полный Paper-контекст и дизайн-система

- **Цель:** получить подтверждённый визуальный источник истины и набор reusable tokens/components.
- **Область изменений:** read-only анализ Paper и документация; Paper не изменяется.
- **Связанные макеты Paper:** 12 обнаруженных фреймов.
- **Затронутые файлы и компоненты:** `docs/redesign/03-paper-analysis.md`, `04-page-mapping.md`, будущий каталог ассетов.
- **Функциональные автотесты:** не применимо; только проверка целостности экспортов/ссылок.
- **Визуальные проверки:** screenshots всех frames, computed styles, размеры, ассеты, интерактивные состояния.
- **Критерии приёмки:** подтверждено имя/версия файла; все фреймы и оригинальные ассеты доступны; отсутствующие responsive/state решения согласованы.
- **Фактический результат:** подтверждён `Мария's Team` → `Ралмастер` → `Дизайн`; для всех 12 фреймов прочитаны trees, node info, computed styles, JSX и screenshots; 12 полноразмерных WebP-эталонов сохранены в `docs/redesign/references/paper/`; извлечена дизайн-система и спецификация shell/forms.
- **Обнаруженные дефекты:** старые документы ссылались на неверные имена файлов; mobile/tablet и interaction states отсутствуют.
- **Исправленные дефекты:** не применимо, макет не изменялся.
- **Оставшиеся ограничения:** адаптивные и hover/focus/error/loading состояния являются производными решениями; права/назначение ассетов проверяются перед публикацией.
- **Статус:** `PASSED`.

## Итерация 3 — Foundations и общий shell

- **Цель:** внедрить tokens, typography, primitive components, header, footer, breadcrumbs и mobile navigation.
- **Область изменений:** presentation layer в `local/`, без правок Bitrix core/vendor.
- **Связанные макеты Paper:** header/footer всех desktop frames, modal primitives.
- **Затронутые файлы и компоненты:** `.top.menu.php`, `local/templates/aspro-allcorp2/css/custom.css`, `page_blocks/header_1.php`, `header_mobile_1.php`, `footer_1.php`, локальные fonts/logos, `scripts/capture-redesign-shell.mjs`, `docker/clear-local-bitrix-cache.ps1`, точечные Playwright-селекторы.
- **Функциональные автотесты:** desktop/mobile navigation, keyboard traversal, focus, links, sticky/fixed behavior.
- **Визуальные проверки:** 1440 desktop и responsive 390/768/1024; сравнение header/footer/forms с Paper.
- **Критерии приёмки:** shell един для всех страниц; меню и focus доступны; нет регрессий маршрутов; tokens совпадают с извлечённой системой.
- **Фактический результат:** подключены локальные variable fonts Onest/Inter Tight/Manrope и CSS tokens; реализованы Paper header 106 px, footer 430 px, desktop/mobile navigation, формы, кнопки и focus states. Сохранены Bitrix menu/phone/search/cart/mobile menu и modal hooks. Desktop shell не имеет horizontal overflow; header/footer совпадают с базовой геометрией Paper. Targeted navigation/projects/modal/mobile tests проходят; PHP syntax и реальный локальный рендер проверены.
- **Обнаруженные дефекты:** legacy minified CSS не содержал `.banner_btns`; CTA выглядели полноширинными полосами. Subscription include ломал высоту footer; legal row и mobile footer выходили за корневой блок. Search/cart теснили desktop navigation. Legacy slider мог оставлять видимый CTA вне viewport теста.
- **Исправленные дефекты:** CTA восстановлены в последнем override-слое; subscription исключён только из нового footer; desktop utilities убраны из Paper-header при сохранении маршрутов и mobile controls; footer приведён к 430 px и корректному mobile flow; тест выбирает реально видимый CTA без ослабления assertions; добавлена безопасная процедура очистки локального Bitrix cache.
- **Оставшиеся ограничения:** Paper не содержит mobile shell; desktop search/cart доступны по существующим маршрутам, а controls остаются в mobile header. В Paper показаны Telegram/MAX, но URL отсутствуют в Bitrix-конфигурации, поэтому элементы пока не являются ссылками. Контраст прозрачного header окончательно принимается вместе с новым тёмным hero в итерации главной.
- **Статус:** `PASSED`.

## Итерация 4 — Главная страница

- **Цель:** реализовать законченный редизайн `/`.
- **Область изменений:** hero, продуктовые/сервисные секции, проекты, клиенты, производство, процесс, FAQ, CTA/form.
- **Связанные макеты Paper:** `Главная страница`, `РАЛМАСТЕР — Обсудить заказ`.
- **Затронутые файлы и компоненты:** `indexblocks_index2.php`, `local/templates/aspro-allcorp2/page_blocks/ralmaster_home.php`, `header.php`, `page_blocks/header_1.php`, `css/custom.css`, `js/custom.js`, локальные Paper assets в `images/ralmaster/home/`, `scripts/capture-redesign-home.mjs`, `tests/e2e/redesign/home.spec.ts`, затронутый mobile baseline и npm-команда visual capture.
- **Функциональные автотесты:** header links, cards/CTA, accordion, modal open/close/validation, mobile fallback.
- **Визуальные проверки:** full-page desktop comparison; responsive section flow; crop/focal point изображений.
- **Критерии приёмки:** все секции соответствуют Paper, динамические данные и CTA работают, нет layout shift/overflow.
- **Фактический результат:** реализована новая динамическая главная из 10 корневых секций `hero`, `work-model`, `products`, `projects`, `partners`, `production`, `process`, `faq`, `order`, `photo`. Каталог читается из разделов IBLOCK 41, услуги — из разделов IBLOCK 40, проекты — из IBLOCK 36, партнёры — из IBLOCK 20; SEF URL сохранены. Inline form и CTA переведены на отдельную Web Form SID `ralmaster_estimate_s1` с контекстом `home` и fallback на legacy form только при отсутствии нового SID. После переключения формы desktop home suite повторно прошёл 6/6, mobile/capture diagnostics чисты. На viewport 1440 повторно получена целевая высота 8645 px: hero `0–579`, work model `579–739`, products `739–2643`, projects `2643–3639`, partners `3639–4201`, production `4201–5283`, process `5283–6165`, FAQ `6165–7012`, order `7012–7672`, photo `7672–8215`, footer `8215–8645`. Итоговый axe scan не содержит нарушений; desktop/mobile не имеют horizontal overflow, browser console/page errors отсутствуют.
- **Обнаруженные дефекты:** legacy-композиция добавляла лишние 13 блоков и пустой mobile hero; header добавлял 106 px к Paper-высоте; внутренние Aspro containers удваивали отступы; отсутствовала производная responsive-компоновка; FAQ не имел нужного accessible one-open поведения; legacy form wrappers и consent ломали геометрию; HTML entities выводились в названиях проектов; ранний mobile resize вызывал race из-за неинициализированного `arAllcorp2Options`; абсолютные `bavrasem.beget.tech` URL попадали в данные.
- **Исправленные дефекты:** активный index заменён тонким dispatcher; home header переведён в overlay; вложенные контейнеры и legacy fixed controls изолированы только на новой главной; реализованы responsive flow, FAQ keyboard/ARIA behavior и безопасный ранний stub Aspro options; форма визуально приведена к Paper; entities декодируются при выводе; legacy host нормализуется; исходный дефект полноширинных CTA устранён. Финальный axe результат для главной — 0 нарушений.
- **Оставшиеся ограничения:** Paper не содержит mobile/tablet-фреймов, поэтому responsive является производным. Реальная успешная отправка, почтовое уведомление и внешние CRM-побочные эффекты намеренно не проверялись; этот state-changing gate остаётся общим ограничением проекта, а не блокером безопасного read-only UI gate главной.
- **Статус:** `PASSED`.

## Итерация 5 — Каталог и продукция

- **Цель:** реализовать `/product/` и общие listing/detail patterns без потери существующей функции.
- **Область изменений:** категории, карточки, фильтры, sort/pagination, detail navigation, CTA.
- **Связанные макеты Paper:** `Продукция`, общие блоки форм.
- **Затронутые файлы и компоненты:** `product/index.php`, `product/index_inc.php`, `.section.php`, `bitrix:news` template `catalog`, новый `sections_products_paper.php`, component template `product-sections-paper`, локальные assets `images/ralmaster/products/`, `tests/e2e/redesign/products.spec.ts`.
- **Функциональные автотесты:** listing → section → detail, filters/sort/pagination, back links, order/question form, optional cart.
- **Визуальные проверки:** listing desktop/mobile, карточки, длинные названия, пустое состояние.
- **Критерии приёмки:** SEF URLs и динамические данные сохранены; основные сценарии проходят; визуальное соответствие принято.
- **Фактический результат:** `/product/` перестроен по Paper без замены динамики: сохранены `bitrix:news`, IBlock 41, SEF section/detail routes и меню. Семь активных разделов читаются из БД и выводятся в целевом порядке с реальными URL. Inline form использует SID `ralmaster_estimate_s1`, контекст `product` и безопасную client/server validation. Desktop suite прошёл 8/8, mobile — 1/1, critical axe = 0, browser errors и horizontal overflow отсутствуют. Фактическая desktop geometry — 1440×3240; форма 692 px полностью помещается в секцию 804 px.
- **Обнаруженные дефекты:** legacy root-композиция не соответствовала Paper; первоначально нижние form controls могли обрезаться фиксированной секцией.
- **Исправленные дефекты:** добавлен отдельный root-only page block, динамический порядок категорий и точная form geometry без clipping; дочерние section/detail routes сохранены.
- **Оставшиеся ограничения:** Paper не содержит mobile/tablet-фреймов; успешный state-changing submit не выполнялся.
- **Статус:** `PASSED`.

## Итерация 6 — Металлообработка и порошковая окраска

- **Цель:** реализовать два сервисных направления и связанные detail flows.
- **Область изменений:** `/services/`, service detail pages, `/painting/`, примеры работ, формы.
- **Связанные макеты Paper:** `Металлообработка`, `Порошковая окраска`.
- **Затронутые файлы и компоненты:** `services/index.php`, `services/.section.php`, root-only `bitrix:news/services` page block и CSS; `painting/index.php`, detail dispatcher, reusable `ralmaster_powder.php/.css`; локальные assets metalworking/powder; `services.spec.ts`, `painting.spec.ts`.
- **Функциональные автотесты:** service listing/detail, CTA, galleries, forms, linked projects/products.
- **Визуальные проверки:** обе full pages, dark/light sections, image treatment, responsive tables/cards.
- **Критерии приёмки:** существующие URLs и контентные связи сохранены, повторяющийся legacy-контент устранён, regressions отсутствуют.
- **Фактический результат:** `/services/` реализован по Paper с динамическими разделами IBlock 40 и сохранёнными detail routes; desktop 5/5 и mobile 1/1 проходят, geometry 1440×5741. `/painting/` и `/painting/poroshkovaya-pokraska-metalla/` используют единый reusable block и оба отвечают 200; desktop 7/7 и mobile 1/1 проходят, geometry 1440×6396. Обе inline forms используют SID `ralmaster_estimate_s1` с контекстами `metalworking`/`powder`. Для powder form фактически измерены 748×738, внутренний `scrollHeight` равен `clientHeight`, все поля, consent и submit имеют `clipped=false`. Critical axe, browser errors и horizontal overflow отсутствуют.
- **Обнаруженные дефекты:** legacy painting содержал повторяющийся контент; первая Paper-версия powder form обрезала нижние controls.
- **Исправленные дефекты:** повторяющийся legacy layout заменён reusable page block; локализованы оригинальные assets; form gaps/heights и wrappers скорректированы до полного unclipped рендера.
- **Оставшиеся ограничения:** service detail pages сохраняют legacy presentation вне целевого root-фрейма; responsive layouts являются производными; успешный state-changing submit не выполнялся.
- **Статус:** `PASSED`.

## Итерация 7 — Производство и компания

- **Цель:** реализовать производственную страницу и привести производные company pages к общей системе.
- **Область изменений:** `/company/`, таблица возможностей, этапы, клиенты, FAQ, CTA.
- **Связанные макеты Paper:** `О производстве`.
- **Затронутые файлы и компоненты:** `company/index.php`, `company/.section.php`, `page_blocks/ralmaster_production.php/.css`, локальные production assets, `tests/e2e/redesign/production.spec.ts`.
- **Функциональные автотесты:** company navigation, accordions, CTA/forms, partners/reviews/staff/vacancy smoke.
- **Визуальные проверки:** full-page desktop, responsive data table, long content, imagery.
- **Критерии приёмки:** production content корректно отображается, производные страницы не ломаются.
- **Фактический результат:** root `/company/` реализован как отдельная Paper-страница, а `/company/partners/` сохранён и отвечает 200 без root-блока. Партнёры и их URL читаются динамически из IBlock 20. CTA использует SID `ralmaster_estimate_s1` и контекст `production`. Desktop 8/8 и mobile 1/1 проходят; 768/1024 без overflow; critical axe и browser errors = 0. Фактическая geometry точно соответствует 1440×7203, включая footer 430 px.
- **Обнаруженные дефекты:** IBlock 17 не содержит вопросы целевого Paper FAQ; ограничения оборудования из макета нельзя выдавать как подтверждённые технические характеристики.
- **Исправленные дефекты:** FAQ реализован как явно документированный presentation fallback с доступным accordion; числовые ограничения видимо квалифицированы как примерные и требующие проверки чертежа; дочерний company route не перехвачен.
- **Оставшиеся ограничения:** FAQ не управляется IBlock 17 из-за несовпадения данных; responsive layout производный; успешный state-changing submit не выполнялся.
- **Статус:** `PASSED`.

## Итерация 8 — Проекты

- **Цель:** реализовать listing и единый detail template проектов.
- **Область изменений:** `/projects/`, `/projects/{element-code}/`, галереи, паспорт заказа, связанные операции.
- **Связанные макеты Paper:** `Реализованные проекты`, `Шаблон подробного проекта`, `ЖК «Символ»`.
- **Затронутые файлы и компоненты:** project listing/detail component templates, shared galleries/cards/forms и восемь локальных Paper-ассетов.
- **Функциональные автотесты:** listing → detail, next/back navigation, gallery, related entities, CTA.
- **Визуальные проверки:** list/detail full-page desktop и responsive; сравнение реального «ЖК Символ».
- **Критерии приёмки:** все проекты используют общий шаблон, данные не захардкожены, ссылки и галерея работают.
- **Фактический результат:** реализованы listing `/projects/` (1440×4405), общий detail (1440×4833) и отдельная композиция `/projects/zhk-simvol/` (1440×3184) на существующем IBlock 36/SEF. Ссылки, переходы и пустые состояния остаются динамическими; восемь используемых Paper-ассетов локализованы. Все три представления вошли в итоговый redesign suite 61/61 и route smoke 32/32.
- **Обнаруженные дефекты:** в локальных данных IBlock 36 отсутствуют или пусты `TASK`, `ORDERER`, `DATA`, `PHOTOS` и поля паспорта заказа; presentation fallback нельзя выдавать за фактические данные проекта.
- **Исправленные дефекты:** вместо фиктивного заполнения добавлены честные empty/demo states; общий detail и Symbol route не ломают SEF и динамический listing.
- **Оставшиеся ограничения:** паспорт/галерея будут полностью CMS-driven после появления утверждённых свойств и контента в IBlock 36; mobile/tablet являются производной реализацией, поскольку Paper содержит только desktop.
- **Статус:** `PASSED`.

## Итерация 9 — Контакты и формы

- **Цель:** реализовать контакты, общие формы и доступные modal flows.
- **Область изменений:** `/contacts/`, карта, реквизиты, feedback, order/discussion forms, mobile fallback.
- **Связанные макеты Paper:** `Контакты`, `РАЛМАСТЕР — Обсудить заказ`.
- **Затронутые файлы и компоненты:** contacts/includes, map config, form component templates, `/ajax/form.php` integration.
- **Функциональные автотесты:** tel/mail links, map fallback, modal lifecycle, required/format/file/error/success states, Escape/focus trap, `/form/` mobile.
- **Визуальные проверки:** contacts full page, modal at 748 px, responsive form states.
- **Критерии приёмки:** реальные контакты и реквизиты выводятся из источника данных, ключ карты отсутствует в tracked source, формы работают локально с mail sink.
- **Фактический результат:** `/contacts/` реализован в геометрии 1440×4120 с CMS phone/email, include-адресами и существующей Yandex-картой. Reusable dialog «Обсудить заказ» реализован как нативный доступный dialog 748×850 с Escape, клавиатурным открытием, безопасной client validation без POST и повторной декорацией после Bitrix AJAX replacement. Контакты и dialog входят в итоговый redesign suite 61/61; dedicated SID verified в dev и production-like.
- **Обнаруженные дефекты:** hardcoded map key остаётся в tracked include; карта отличается от статического Paper-изображения масштабом и двумя маркерами; реквизиты в макете не представлены утверждённым CMS-источником/PDF; первоначальный MutationObserver мог зациклить DOM, success header скрывался, server error layout обрезался.
- **Исправленные дефекты:** observer сделан idempotent и persistent для AJAX replacement без бесконечного цикла; success/error/loading layouts, disabled submit и timeout recovery исправлены; exact Paper dialog geometry и typography подтверждены тестами; реквизиты явно оформлены как presentation fallback.
- **Оставшиеся ограничения:** `SEC-001` (ключ карты) открыт; карта не является pixel-копией Paper; нет утверждённой карточки компании/PDF; успешный submit/mail/CRM намеренно не запускался без mail sink. Поэтому безопасный UI gate пройден, но полный state-changing/security gate остаётся открытым.
- **Статус:** `IN PROGRESS`.

## Итерация 10 — Формы запроса расчёта

- **Цель:** реализовать формы запроса ручного расчёта гибки и порошковой окраски без выдумывания отсутствующих в Paper формул.
- **Область изменений:** route-specific CTA, единое поле контакта, описание задачи, безопасная загрузка файлов, client/server validation, согласие, сохранение результата и уведомление.
- **Связанные макеты Paper:** `Расчёт гибки металла`, `Расчёт порошковой окраски`.
- **Затронутые файлы и компоненты:** отдельные Web Forms или строго scoped form variants, локальные component templates, безопасная идемпотентная DB-миграция, server-side validators и tests; точный список фиксируется перед изменением схемы.
- **Функциональные автотесты:** CTA/title/copy, contact phone-or-email, правило `описание || файл`, allowlist/size upload, consent, keyboard, сохранение результата, безопасный mail sink и отсутствие внешних CRM-побочных эффектов.
- **Визуальные проверки:** modal desktop и responsive mobile; error/success/loading states.
- **Критерии приёмки:** обе формы точно соответствуют Paper, работают на client/server, безопасно сохраняют файл и данные, отправляют корректное локальное уведомление и не вызывают production-интеграции в тестах.
- **Фактический результат:** подтверждено, что оба Paper-фрейма являются формами запроса, а не калькуляторами. Создана additive Web Form SID `ralmaster_estimate_s1`, не изменяющая legacy ID 5: optional `NAME`, required phone-or-email `CONTACT`, optional `TASK`, optional `FILE`, hidden `CONTEXT`. Идемпотентная миграция применена и `--verify` пройден в dev и изолированной production-like БД после локального snapshot, исключённого из Git. Server handler и общий client script проверяют contact, правило `TASK || FILE`, 20 MB и allowlist PDF/PNG/JPEG/WebP/DOC/DOCX/XLS/XLSX/DWG/DXF. Реализованы и проверены reusable dialog 748×850, bending 748×850 и coating 748×898; exact высоты, поля 56/116/56/60, typography 36/44, upload/consent/loading и keyboard lifecycle закреплены Playwright assertions. Inline variants используются на home/product/metalworking/powder/production.
- **Обнаруженные дефекты:** Web Form ID 5 не соответствует Paper; исходный CTA гибки был недоступен с клавиатуры и отсутствовал на detail route; после AJAX могли теряться namespaced IDs/декорация; общий JS не давал доступных имён скрытым Bitrix labels; финальный visual capture выявил overlap в mobile powder dialog из-за двухстрочных title/task help/file help/consent внутри фиксированных rows; mail template и CRM hooks требуют отдельной безопасной проверки.
- **Исправленные дефекты:** legacy form не изменялась; введены отдельная scoped schema, одинаковая client/server validation, context propagation и dry-run/apply/verify; bending получил route-scoped fallback CTA и keyboard semantics; AJAX lifecycle стал idempotent; всем контролам добавляются ARIA-labels; mobile dialog переведён с fixed rows на natural-flow heights. Добавлен тест попарного вертикального неперекрытия `title → intro → fields → consent → submit`; dev mobile 1/1 PASS за 11.7 s, production-like dialogs 3/3 PASS за 22.1 s без POST. Финальный redesign suite — 61/61.
- **Оставшиеся ограничения:** успешный submit, сохранение результата, mail template и CRM hooks не запускались без локального mail sink; поэтому UI/safe-functional gate `PASSED`, а state-changing gate остаётся `NOT RUN`.
- **Статус:** `IN PROGRESS`.

## Итерация 11 — Производные страницы и финальная регрессия

- **Цель:** привести страницы без отдельных макетов к системе и доказать готовность всего сайта.
- **Область изменений:** search, info, auth/cabinet, optional cart/region, legacy routes, 404; performance/accessibility hardening.
- **Связанные макеты Paper:** общая система всех фреймов, прямых макетов для этих страниц нет.
- **Затронутые файлы и компоненты:** только подтверждённые local templates/assets; финальный список фиксируется по diff.
- **Функциональные автотесты:** полный desktop/mobile набор, state-changing local flows, route crawl, console/network errors, accessibility.
- **Визуальные проверки:** полный регрессионный набор viewport, Paper comparison всех целевых frames, derived-page review.
- **Критерии приёмки:** dev/prod-like build и запуск подтверждены, все P0/P1 сценарии проходят, критических/high дефектов нет, отчёты заполнены.
- **Фактический результат:** завершён безопасный итоговый acceptance-контур: post-`UI-003` полный redesign suite на dev 8084, desktop+mobile — **61/61 PASS** за 6.4 min, exit 0; обновлённый baseline suite — **22/22 PASS** за 2.7 min; dev route smoke — **32/32 PASS** за 2.8 min; production-like smoke после force-recreate — **32/32 PASS** за 2.6 min; production-like dialogs desktop+mobile после mobile overlap fix — **3/3 PASS** за 22.1 s без POST. Dedicated form `ralmaster_estimate_s1` verified в dev и production-like. Финальный capture regenerated для 20 viewport/page runs: diagnostics `[]`, horizontal overflow отсутствует; `powder-mobile-390-dialog-bottom.png` подтверждает отсутствие overlap. Все целевые desktop geometry закреплены: 8645/3240/5741/6396/7203/4405/4833/3184/4120 px и модальные 850/850/898 px. PHP/JS syntax checks пройдены для затронутых файлов.
- **Обнаруженные дефекты:** первый полный redesign run дал 60/61 из-за недоступных имён скрытых Bitrix labels на Services; первый обновлённый baseline дал 19/22 из-за трёх устаревших ожиданий legacy jqModal и `/form/`; параллельные Playwright-процессы конфликтовали из-за общего `test-results`.
- **Исправленные дефекты:** общий form script назначает ARIA-label каждому control; baseline tests проверяют нативный dialog, client validation и Escape без POST; финальные прогоны выполняются последовательно. Повторные suites зелёные: 61/61, 22/22 и 32/32.
- **Оставшиеся ограничения:** Paper не содержит responsive/state frames; `SEC-001`, CMS-content gaps и state-changing mail/CRM gate открыты. Реальные POST не выполнялись. Первая sandbox-попытка production-like smoke была инфраструктурно невалидна из-за недоступного browser executable; корректный повтор с системным Chromium завершился 32/32 и exit 0.
- **Статус:** `PASSED` (safe read-only/invalid-input acceptance); state-changing gate `NOT RUN`.

## Итерация 12 — Hotfix desktop navigation (2026-10-29)

- **Цель:** восстановить корректный wide-menu продукции и непрерывный переход курсора в submenu металлообработки.
- **Область изменений:** desktop header navigation; mobile header, Paper и production не затрагиваются.
- **Связанные макеты Paper:** общий desktop shell/header.
- **Затронутые файлы и компоненты:** `page_blocks/header_1.php`, `css/custom.css`, `tests/e2e/redesign/header-menu.spec.ts`.
- **Функциональные автотесты:** wide-menu geometry/content, `focus-within`, 12-step pointer path и реальный переход на `/services/metal-bending/`; regression главной.
- **Визуальные проверки:** `test-results/menu-audit-final/products.png`, `test-results/menu-audit-final/services.png`.
- **Критерии приёмки:** меню продукции не схлопывается; dropdown не закрывается между top-link и дочерним пунктом; overlay-header остаётся прозрачным; console/page errors отсутствуют.
- **Фактический результат:** восстановлен обязательный `.menu-row` context; `td/wrap` растянуты на высоту header; direct dropdown поставлен на `top: 100%` без 18 px gap; добавлены `focus-within` и scoped transparent background. Product panel — 1008×178.84, 7 пунктов/7 изображений; 25-шаговый browser audit сохраняет services dropdown видимым; клик по «Гибка металла» открывает HTTP 200.
- **Обнаруженные дефекты:** wide-menu схлопывался из-за отсутствующего `.menu-row`; hover терялся в 18 px gap; первый вариант восстановления контекста дал белый фон поверх overlay-header.
- **Исправленные дефекты:** все три дефекта закрыты; `header-menu.spec.ts` — **2/2 PASS**, `home.spec.ts` desktop — **6/6 PASS**, browser console/page errors — `[]`.
- **Оставшиеся ограничения:** отсутствуют в scope hotfix; общие state-changing/mail/CRM и `SEC-001` остаются вне него.
- **Статус:** `PASSED`.

## Итерация 13 — Hotfix legacy sticky header (2026-10-29)

- **Цель:** исключить появление старой Aspro-шапки при прокрутке страниц с новым desktop shell.
- **Область изменений:** server-side выбор desktop fixed header; мобильная шапка, Paper и production не изменяются.
- **Связанные макеты Paper:** общий desktop shell/header.
- **Затронутые файлы и компоненты:** `header.php`, `page_blocks/header_1.php`, `tests/e2e/redesign/header-menu.spec.ts`.
- **Функциональные автотесты:** отсутствие `#headerfixed` до/после scroll на `/` и `/painting/poroshkovaya-pokraska-metalla/`; регрессия dropdown и перехода на «Гибка металла»; mobile header smoke.
- **Визуальные проверки:** `test-results/sticky-audit-painting-after-fix-scroll900.png`.
- **Критерии приёмки:** legacy fixed header не рендерится при активном `.rm-header`; новая шапка ведёт себя как обычный non-sticky shell; mobile header остаётся рабочим; console/page errors отсутствуют.
- **Фактический результат:** `header_1.php` выставляет явный `RM_DESIGN_HEADER_ACTIVE`; `header.php` не создаёт legacy `#headerfixed` только при этом флаге. На обеих desktop-страницах узел отсутствует до и после scroll 600/900 px; console/page errors — `[]`.
- **Обнаруженные дефекты:** Aspro независимо рендерил `header_fixed` и после порога около 106 px добавлял ему `.fixed`, поэтому поверх редизайна появлялось старое меню.
- **Исправленные дефекты:** legacy desktop fixed header исключён на сервере; vendor JS/CSS и отдельная mobile fixed-ветка не изменялись. PHP lint — 2/2; desktop header suite — **4/4 PASS**; mobile baseline — **2/2 PASS**.
- **Оставшиеся ограничения:** новая desktop-шапка намеренно не sticky и уходит из viewport при прокрутке; sticky-вариант нового дизайна потребует отдельного решения.
- **Статус:** `PASSED`.

## Итерация 14 — Подготовка staging deployment на VPS (2026-10-29)

- **Цель:** подготовить воспроизводимое и повторяемое развёртывание `test.ral-master.ru` рядом с существующим сайтом, не конфликтуя с его портом и Caddy route.
- **Область изменений:** новый VPS bootstrap/deploy script и инструкция; удалённый сервер не изменяется.
- **Связанные макеты Paper:** все реализованные страницы косвенно; Paper не изменяется.
- **Затронутые файлы и компоненты:** `.gitattributes`, `deploy/ralmaster-vps-deploy.sh`, `deploy/README.md`.
- **Функциональные автотесты:** `bash -n`, `--help`, Git EOL attribute, `git diff --check`.
- **Визуальные проверки:** не применимо к подготовке deployment.
- **Критерии приёмки:** скрипт безопасно обновляет Git, восстанавливает Bitrix/upload и пустую БД, поднимает изолированный Compose, валидирует Caddy с rollback и не удаляет volumes/не перезаписывает непустую БД.
- **Фактический результат:** создан root-only staging workflow для `/root/ralmaster`, Compose project `ralmaster-test`, loopback `8081`, домена `test.ral-master.ru`, SQL/file backup и host Caddy. Скрипт останавливается при конфликте порта, изменённом checkout, непустой destination DB, отсутствующем Bitrix file backup или неизвестной containerized-Caddy сети.
- **Обнаруженные дефекты:** Git намеренно исключает Bitrix core и `upload`; одного SQL backup недостаточно. Режим запуска Caddy на удалённом VPS пока не подтверждён из Docker/systemd output.
- **Исправленные дефекты:** добавлено обязательное восстановление file backup, root-only secrets, безопасный DB gate, managed Caddy block с backup/validate/rollback и LF enforcement для shell scripts.
- **Оставшиеся ограничения:** фактический remote deploy, DNS/HTTPS и реальный Caddy mode ещё не проверены; перед запуском нужны Git URL/ref, DB backup, file backup и read-only вывод VPS preflight.
- **Статус:** `PASSED` для подготовки скрипта; remote execution `NOT RUN`.

## Итерация 15 — Коррекция header: плоская навигация (2026-10-31)

- **Цель:** привести desktop/mobile header к актуальному Paper и полностью убрать dropdown, submenu и mega-menu.
- **Область изменений:** общая навигация всех публичных страниц; sticky-header остаётся отключённым.
- **Связанные макеты Paper:** общий header файла `Мария's Team / Ралмастер / Дизайн`; шесть прямых ссылок без дочерних панелей.
- **Затронутые файлы и компоненты:** `include/header/menu.php`, новый `bitrix:menu/ralmaster_flat`, `header_mobile_1.php`, `header_mobile_menu_1.php`, `css/custom.css`, header/mobile Playwright tests.
- **Функциональные автотесты:** desktop direct links, отсутствие dropdown DOM, отсутствие legacy fixed header после scroll, mobile open/close ARIA и отсутствие nested navigation.
- **Визуальные проверки:** `test-results/redesign-shell/desktop-1440-top.png`, `mobile-390-top.png`; desktop header 1440×106, horizontal overflow отсутствует.
- **Критерии приёмки:** ровно шесть прямых ссылок, ни одного submenu/mega-menu, все href сохранены, mobile burger доступен с клавиатуры и сообщает состояние.
- **Фактический результат:** создан изолированный плоский Bitrix menu template с `MAX_LEVEL=1`/`USE_EXT=N`; desktop и mobile используют один источник `.top.menu.php`; burger преобразован в button с `aria-controls`, динамическими `aria-expanded` и label. PHP lint 4/4; targeted desktop 4/4, mobile 3/3 и дополнительная home/navigation regression 14/14 PASS.
- **Обнаруженные дефекты:** прежняя hotfix-реализация намеренно поддерживала dropdown, что теперь противоречит актуальному требованию; старый mobile trigger был без доступного имени.
- **Исправленные дефекты:** весь дочерний DOM исключён на сервере, а не только скрыт CSS; mobile control получил корректную семантику и состояние.
- **Оставшиеся ограничения:** mobile navigation является производным решением, поскольку отдельного mobile-фрейма в Paper нет.
- **Статус:** `PASSED`.

## Итерация 16 — Производные страницы: продукт, услуга и партнёры (2026-10-31)

- **Цель:** удалить оставшиеся legacy Aspro-body на `/product/kapelnik/`, `/services/laser-cutting/` и `/company/partners/`, сохранив Bitrix-данные, маршруты и формы.
- **Область изменений:** общий section-detail pattern для продукции и услуг; динамическая страница партнёров; устранение старого sidebar/empty-state/fixed contacts; интеграция существующих request-dialog flows.
- **Связанные макеты Paper:** дизайн-система фреймов `Продукция`, `Металлообработка`, `Шаблон подробного проекта` и общие формы файла `Мария's Team / Ралмастер / Дизайн`; отдельных фреймов для этих трёх URL в Paper нет.
- **Затронутые файлы и компоненты:** `product/index.php`, `services/index.php`, `company/partners/index.php`; section templates `catalog`/`services`; новые `section_product_paper.php`, `section_service_paper.php`, `ralmaster_detail.php/.css`; page block и `news.list/partners-paper`; `tests/e2e/redesign/derived-pages.spec.ts`.
- **Функциональные автотесты:** HTTP/SEF routes, динамические h1/images/7 партнёров, отсутствие legacy wrappers, dialog open/close без POST, сохранение точной bending form 748×850, browser diagnostics, axe и responsive overflow.
- **Визуальные проверки:** before/after captures трёх маршрутов; финальные `test-results/derived-after-kapelnik.png`, `derived-after-partners-final.png`, `derived-after-laser-mobile.png`; desktop 1440 и mobile 390 проверены секциями.
- **Критерии приёмки:** ни одного старого sidebar/alert/дублирующего CTA, CMS-данные не заменены статикой, локальные изображения загружаются, CTA доступен, desktop/mobile без overflow, axe violations отсутствуют.
- **Фактический результат:** создан единый динамический detail-шаблон для section routes продукции и услуг. Название, описание и section picture читаются из IBlock; локальный asset используется только как fallback. `/company/partners/` выводит 7 актуальных IBlock-записей с логотипами, описаниями и ссылками. HTML entities в названиях корректно декодируются. Старый fixed contacts widget скрыт только на новых страницах. Route-specific bending dialog перенесён в общий service-detail без потери контекста и точной Paper-геометрии. PHP lint 10/10; targeted suite 7/7; связанная regression 29/29; полный axe scope для всех трёх страниц — `[]`.
- **Обнаруженные дефекты:** legacy empty/sidebar composition, дублирующий bending CTA/dialog, видимый старый fixed contacts widget, literal `&quot;` в именах партнёров и два serious contrast-нарушения в 13 px eyebrow labels.
- **Исправленные дефекты:** старые wrappers обходятся server-side; bending flow конфигурируется внутри reusable detail; fixed widget скрыт scoped CSS; entities декодируются перед escaping; цвета eyebrow/contact kicker доведены до WCAG, повторный axe — 0 нарушений на всех трёх URL.
- **Оставшиеся ограничения:** отдельных desktop/mobile Paper-фреймов этих страниц нет, поэтому композиция наследует подтверждённую дизайн-систему; успешный POST/mail/CRM по-прежнему не выполнялся.
- **Статус:** `PASSED`.

## Итерация 17 — UI consistency: корпоративные и информационные разделы (2026-10-31)

- **Цель:** удалить legacy Aspro-body из публичных разделов отзывов, сотрудников, вакансий, новостей, статей, FAQ и акций, сохранив динамический Bitrix-контент и SEF-маршруты.
- **Область изменений:** семь list routes, доступные section/detail routes, фильтр новостей по году, review/question form hooks, общая responsive-композиция и аудит оставшихся служебных маршрутов.
- **Связанные макеты Paper:** прямых фреймов этих страниц нет; использована общая система `О производстве`, `Контакты`, `Шаблон подробного проекта` файла `Мария's Team / Ралмастер / Дизайн`.
- **Затронутые файлы и компоненты:** entry points `company/{reviews,staff,vacancy}` и `info/{news,articles,faq,sales}`; общий `ralmaster_content_list.php`, `ralmaster_content_detail.php`, `ralmaster_content.css`; новые `news.list/news.detail/ralmaster-content`; изолированный `bitrix:news/ralmaster-blog`; thin page blocks существующих news templates; capture script; `content-pages.spec.ts`; `ROUTE_UI_AUDIT.md`.
- **Функциональные автотесты:** HTTP/list/detail/section routes, точные CMS item counts 4/6/5/4/5/5/9, dynamic h1/content, year filter, FAQ keyboard behavior, сохранение jqModal hooks без POST, browser diagnostics, horizontal overflow и axe.
- **Визуальные проверки:** full-page desktop 1440 и mobile 390 для news, FAQ и article detail; screenshots в `test-results/redesign-pages/`; после первого capture исправлен невидимый текст CTA в aside detail.
- **Критерии приёмки:** старые intro/sidebar/ask wrappers не рендерятся, IBlock и SEF остаются источником данных, list/section/detail доступны, form hooks сохранены, desktop/mobile без overflow, diagnostics и axe чисты.
- **Фактический результат:** общий Paper-derived content system подключён к IBlock 14/15/16/17/34/35/37. Все семь списков и пять репрезентативных detail routes отвечают 200; article root/section/detail используют новый изолированный news template без изменения vendor/core. Targeted desktop+mobile suite — **15/15 PASS** за 2.3 min; связанная regression content/derived/header — **27/27 PASS** за 3.5 min; route smoke — **32/32 PASS** за 2.7 min. Полный axe scope на news/FAQ/article detail — `[]`; capture diagnostics — `[]`, scrollWidth равен clientWidth на 1440 и 390. PHP lint всех изменённых и новых PHP-файлов — PASS.
- **Обнаруженные дефекты:** семь legacy-body групп, демонстрационный текст FAQ, ошибочный h1 «Новости» на акциях, невидимый CTA detail-aside из-за специфичности старого правила ссылок; отдельно подтверждены legacy search/cart/login и дубли `/products/`, `/news/`, `/landings/`.
- **Исправленные дефекты:** legacy parents обходятся server-side только для новых view names; FAQ получает CMS-driven accessible accordion; акции имеют корректный title; detail CTA получает scoped contrast rule; статья использует новый UTF-8-safe component template; карта маршрутов зафиксирована в `ROUTE_UI_AUDIT.md`.
- **Оставшиеся ограничения:** search/map, auth, cart, 404 и три legacy-дубликата остаются следующей итерацией; успешные state-changing form/cart/auth действия не выполнялись; отдельных Paper-фреймов и mobile states для этих страниц нет.
- **Статус:** `PASSED`; оставшаяся системная группа — `TODO` в следующей итерации.

## Итерация 18 — Системные страницы и legacy-дубликаты (2026-10-31)

- **Цель:** устранить последний публичный legacy-body на поиске, карте сайта, корзине, авторизации и 404, а устаревшие дубли привести к каноническим маршрутам без удаления данных.
- **Область изменений:** `/search/`, `/search/map.php`, `/cart/`, `/login/`, неизвестные URL; постоянные перенаправления `/products/`, `/news/`, `/landings/`; общая responsive-система служебных страниц.
- **Связанные макеты Paper:** отдельных artboards для служебных страниц нет; использованы header/footer, типографика, цвета, контейнеры, кнопки и композиционные правила файла `Мария's Team / Ралмастер / Дизайн`.
- **Затронутые файлы и компоненты:** общий `ralmaster_system_start.php`, `ralmaster_system_end.php`, `ralmaster_system.css`; локальные шаблоны `search.page/search`, `main.map/ralmaster-system`, `system.auth.authorize/.default`; entry points search/map/cart/login/404 и три legacy entry point; capture script; `tests/e2e/redesign/system-pages.spec.ts`; route audit и отчёты.
- **Функциональные автотесты:** живой поисковый query flow, динамическая карта меню, существующий basket component, auth fields/recovery, реальный HTTP 404, точные 301 Location, desktop/mobile overflow, browser diagnostics и axe; state-changing cart/auth actions не выполнялись.
- **Визуальные проверки:** full-page captures 1440/390 для search/map/cart/login/404; повторные кадры после исправления маркеров карты, контраста CTA 404 и оформления social auth.
- **Критерии приёмки:** динамические компоненты Bitrix сохранены; system pages согласованы с общей дизайн-системой; 404 возвращает 404; дубли дают 301; нет новой critical/high регрессии, overflow, browser errors или axe violations в проверенном scope.
- **Фактический результат:** создан единый системный shell. Search сохраняет `bitrix:search.page`, карта строится из `bitrix:main.map`, корзина использует исходный `aspro:basket.allcorp2`, auth сохраняет поля, captcha, remember/recovery/register/social providers. Неизвестный URL возвращает HTTP 404 и новый body. `/products/` → `/product/`, `/news/` → `/info/news/`, `/landings/` → `/info/articles/` отвечают 301. PHP lint всех 14 PHP-файлов — PASS; targeted suite — **8/8 PASS**; связанная regression — **35/35 PASS** за 3.8 min; route smoke — **32/32 PASS** за 2.1 min. Full axe scope representative system pages — `[]`; desktop/mobile без overflow.
- **Обнаруженные дефекты:** прерванный сетью стартовый partial требовал проверки до подключения; локальный auth template не наследовал core localization; 404 CTA получил синий текст на синем фоне; карта наследовала legacy list markers; ожидаемый 404 фиксировался Chromium как console resource 404.
- **Исправленные дефекты:** каркас проверен до использования; добавлена локальная русская localization; CTA получил scoped white label; старые markers отключены; тесты отличают ожидаемый HTTP 404 основного документа от неожиданных browser errors. Social providers сгруппированы без удаления функций.
- **Оставшиеся ограничения:** успешные cart/order/auth операции не выполнялись; empty-cart `/cart/order/` штатно возвращает в `/cart/`; disabled cabinet routes по-прежнему ведут на `/`; Paper не содержит отдельных mobile/system artboards; Lighthouse и полный финальный regression остаются следующей итерацией.
- **Статус:** `PASSED` (safe read-only/UI scope); state-changing auth/cart gate `NOT RUN`.

## Итерация 19 — UI hotfix: powder и metalworking (2026-11-01)

- **Цель:** устранить сообщённые визуальные дефекты порошковой окраски и металлообработки, не продолжая Lighthouse-оптимизацию.
- **Область изменений:** full-width composition powder page; intermediate desktop responsive; inline metalworking form; calculation CTA/modal; phone/email icons; CTA и scroll-to-top arrows.
- **Связанные макеты Paper:** `Порошковая окраска` 1440×6396, секции `Подготовка поверхности перед окраской` 1440×777, `Требования к покрытию` с `#EFF1F4`, `Рассчитать порошковую окраску` 1440×850; общая форма расчёта металлообработки.
- **Затронутые файлы и компоненты:** `ralmaster_powder.css`; `sections_metalworking_paper.php/.css`; `css/custom.css`; `painting.spec.ts`; `services.spec.ts`; журналы итераций.
- **Функциональные автотесты:** embedded form validation без POST; видимые labels; CTA `Рассчитать резку/гибку/сварку` открывают native dialog с context `metalworking` и не меняют scroll; desktop/mobile overflow.
- **Визуальные проверки:** 1440 и 1288 px для powder; 1288 и 1506 px для metalworking; локальные captures `ui-fix-powder-preparation.png`, `ui-fix-powder-form-final.png`, `ui-fix-services-card.png`, `ui-fix-services-form.png`, `ui-fix-services-dialog.png`.
- **Критерии приёмки:** справа не обрезаются текст, секции или форма; серый фон совпадает с Paper; labels читаемы; телефон/конверт отсутствуют; стрелки не повреждены; CTA открывают popup; контроль «наверх» синий; новая критическая regression отсутствует.
- **Фактический результат:** legacy `.maxwidth-theme/.content-md` нейтрализованы только для powder page, breakpoint промежуточного desktop поднят до 1439 px, сохранены Paper-геометрия 1440 и адаптивные боковые отступы 1288. Label override учитывает legacy `!important`. Все calculation CTA переведены с `href="#rm-metal-estimate"` на button triggers общего dialog. Контактные SVG скрыты глобально по точным классам `.svg-inline-phone/.svg-inline-email`; scroll-to-top использует `#175bef`; CTA arrow рисуется стабильными border/gradient primitives.
- **Обнаруженные дефекты:** powder root наследовал 1232/1080 px legacy wrapper и скрывал overflow фиксированных 1312 px grids; media rule срабатывал только ниже 1200 px и отдельно растягивал estimate container до 100%; theme CSS скрывал labels через `display:none !important`; расчётные CTA были anchor links; прежний polygon arrow деформировался; phone/email SVG-классы не совпадали со старым `.svg-phone` selector.
- **Исправленные дефекты:** все перечисленные дефекты закрыты. PHP lint PASS; `painting.spec.ts` desktop **8/8 PASS**; `services.spec.ts` desktop **7/7 PASS**; mobile powder/services **2/2 PASS**. На 1288 px `scrollWidth === clientWidth`, estimate form полностью внутри viewport; computed background требований — `rgb(239, 241, 244)`.
- **Оставшиеся ограничения:** полный общий regression и Lighthouse намеренно не запускались/не продолжались по текущему указанию владельца; успешный state-changing submit/mail/CRM не выполнялся.
- **Статус:** `PASSED`.

## Итерация 20 — Короткий viewport формы и единые CTA-стрелки (2026-11-01)

- **Цель:** сделать request-dialog полностью достижимым на экранах небольшой высоты и унифицировать повреждённые CTA-стрелки по подтверждённому виду `↗`.
- **Область изменений:** reusable native dialog на desktop/mobile; home/banner/form submit; metalworking/powder CTA; footer messenger controls.
- **Связанные макеты Paper:** общие формы запроса расчёта и подтверждённый визуальный вариант стрелки на CTA порошковой окраски.
- **Затронутые файлы и компоненты:** `ralmaster_discuss_order.css`; `custom.css`; `sections_metalworking_paper.css`; `ralmaster_powder.php/.css`; `footer_1.php`; `derived-pages.spec.ts`; `home.spec.ts`; `services.spec.ts`.
- **Функциональные автотесты:** открытие product dialog при 1280×720; геометрия внутри viewport; внутренний scroll до submit; Escape; computed `content/background-image` всех CTA-стрелок; связанные form/modal flows без POST.
- **Визуальные проверки:** `test-results/short-dialog-fixed.png`, `short-dialog-submit-fixed.png`, `metal-arrow-fixed.png`, `powder-arrow-fixed.png`; верх и низ формы доступны, кнопки используют одинаковый знак `↗`.
- **Критерии приёмки:** dialog не выходит за верх/низ viewport; все поля, consent и submit достижимы; на мобильном нет horizontal overflow; стрелки не образуют сломанный угол и совпадают по направлению/виду; browser regression отсутствует.
- **Фактический результат:** при desktop height ≤899 px dialog получает `height/max-height` от `100dvh`, собственный vertical scroll и natural-height card; mobile также использует dynamic viewport units. Старые SVG data URI и border/gradient arrow primitives в новом интерфейсе заменены единым текстовым `↗` с фиксированной типографикой. Desktop targeted suite — **30/30 PASS** за 2.7 min; mobile dialog suite — **2/2 PASS**; PHP lint — **2/2 PASS**.
- **Обнаруженные дефекты:** fixed-height dialog 898 px при viewport 794 px имел `top:-52`, `bottom:846` и `overflow-y:hidden`; предыдущая CSS-стрелка могла отображаться как незамкнутый/сломанный угол.
- **Исправленные дефекты:** dialog ограничен текущей высотой viewport и прокручивается внутри; submit подтверждён отдельным нижним capture; CTA-стрелки home/metalworking/powder/footer и submit унифицированы.
- **Оставшиеся ограничения:** успешный state-changing submit/mail/CRM не выполнялся; Lighthouse остаётся приостановлен по текущему указанию владельца.
- **Статус:** `PASSED`.

## Итерация 21 — Product CTA, партнёрский маршрут и hover карточек (2026-11-01)

- **Цель:** исправить поведение верхней product CTA, точность home product-heading, маршрут партнёрских логотипов и добавить сдержанную hover-обратную связь карточкам.
- **Область изменений:** `/product/`; product dialog/inline form; home product/service/powder cards; home и production partner grids; `/company/partners/` как промежуточная локальная страница.
- **Связанные макеты Paper:** `Продукция` 1440×3240 и product-группа главной страницы; точные параметры CTA 336×60 и heading/link group 1312×112 прочитаны через Paper MCP без изменения макета.
- **Затронутые файлы и компоненты:** `product/index.php`, `product/index_inc.php`; `product-sections-paper/style.css`; `ralmaster_home.php`, `ralmaster_production.php`, `custom.css`; `products.spec.ts`, `home.spec.ts`, `production.spec.ts`, `derived-pages.spec.ts`.
- **Функциональные автотесты:** product CTA открывает dialog с context `product` без изменения scroll и POST; inline-форма сохраняется; desktop/mobile dialog bounds; локальные partner href; внешние ссылки только на partners page; hover computed transform/shadow; arrow content/font size.
- **Визуальные проверки:** `test-results/product-hero-popup-cta.png`, `product-dialog-popup.png`, `home-product-heading-paper.png`, `home-product-cards-hover.png`; отдельно сверены Paper screenshot и computed styles заголовка/ссылки/CTA.
- **Критерии приёмки:** hero CTA открывает popup; обе синие product CTA используют одинаковую `↗`; heading desktop не переносится; «Вся продукция» располагается отдельной строкой; логотипы не уводят сразу наружу; hover заметен, но не меняет сетку; mobile без overflow.
- **Фактический результат:** anchor `#product-estimate` заменён на native dialog trigger; подключён product-вариант reusable формы 898 px, а inline Paper form оставлена на месте. Hero и submit arrows приведены к Arial 22 px. На desktop ≥1200 px product heading использует одну строку, link row сохраняет 22 px и gap 12 px. Product/home cards получают подъём 2 px и мягкую тень. Все home/production partner logos ведут на `/company/partners/`, где карточки сохраняют внешние ссылки с `target=_blank` и `noopener`. Desktop related suite — **29/29 PASS** после исправления устаревшего strict selector; product desktop — **8/8 PASS**; mobile related — **4/4 PASS** и повторный product mobile — **1/1 PASS**; PHP lint — **4/4 PASS**.
- **Обнаруженные дефекты:** CTA была anchor-scroll; две product формы после добавления dialog потребовали scope старого теста; desktop title переносился вопреки Paper; home/production partner cards вели прямо на внешние домены; карточки почти не сообщали hover state.
- **Исправленные дефекты:** CTA/dialog lifecycle и scroll закреплены тестом; inline form selector изолирован; desktop typography/layout совпадает с Paper; партнёрский переход стал внутренним; hover не вызывает layout shift и отключает motion при соответствующей системной настройке.
- **Оставшиеся ограничения:** успешный state-changing submit/mail/CRM не выполнялся; Lighthouse остаётся приостановлен по указанию владельца.
- **Статус:** `PASSED`.

## Итерация 22 — Повторная коррекция services/projects по Paper (2026-11-01)

- **Цель:** закрыть сообщённые визуальные расхождения секции операций и формы `/services/`, а также listing/product/operations-блоков `/projects/`.
- **Область изменений:** `/services/` на 1440 и промежуточном desktop; `/projects/` listing, product cards, нижняя секция полного цикла и responsive breakpoint до 1439 px.
- **Связанные макеты Paper:** `Металлообработка` 1440×5741, узлы `Несколько операций в одном заказе` 1440×862 и `Рассчитать металлообработку` 1440×850; `Реализованные проекты` 1440×4405, узлы project cards 1440×1128, `Продукция` 1440×696 и operations 1440×616.
- **Затронутые файлы и компоненты:** `sections_metalworking_paper.css`; `projects-paper/template.php` и `result_modifier.php`; `project-products-paper/template.php`; `list_projects_paper.php`; `projects/style.css`; `services.spec.ts`; `projects.spec.ts`; журналы прогресса.
- **Функциональные автотесты:** динамические SEF-ссылки и локальные изображения; наличие year/type/description; form validation без POST; browser diagnostics; accessibility; desktop/mobile overflow.
- **Визуальные проверки:** Paper MCP screenshots/JSX для пяти точных секций; локальные captures формы 1288 px, services operations 1440 px, project cards/products/operations 1440 px; computed geometry полей и карточек.
- **Критерии приёмки:** поля формы не пересекаются; секция services повторяет вертикальную композицию Paper; project cards всегда имеют текст слева и изображение справа; год/подпись видимы; product images начинаются на одной вертикали; operations содержит три фото; новая критическая regression отсутствует.
- **Фактический результат:** все перечисленные композиции восстановлены. В services fixed-height grid старой формы заменён локальным natural-height layout с 20 px gap. В projects карточки используют 632/680 px, метаданные `2025 / По индивидуальному проекту`, 40 px arrow controls и единый порядок; product cards используют 316/164 px; operations использует сетку 512/368/384 px и локальные изображения. Breakpoint проекта поднят до 1439 px для безопасного промежуточного desktop. PHP lint **4/4 PASS**; desktop suite **18/18 PASS**; дополнительный intermediate gate **2/2 PASS**; mobile suite **4/4 PASS**.
- **Обнаруженные дефекты:** labels добавлялись поверх старых фиксированных grid rows 56/56/112/92; project template чередовал порядок image/body; IBlock fallback скрывал утверждённые год и подпись; header высота product card зависела от количества строк; нижний блок был текстовым вместо визуального.
- **Исправленные дефекты:** fixed rows и перекрытия устранены scoped override; порядок карточек унифицирован; Paper metadata/content добавлены как локализованный fallback для трёх утверждённых проектов; product image/body разделены фиксированными высотами; нижний блок повторяет Paper.
- **Оставшиеся ограничения:** успешный state-changing submit/mail/CRM не выполнялся; проектные metadata остаются presentation fallback до заполнения соответствующих полей IBlock; Lighthouse остаётся приостановлен по указанию владельца.
- **Статус:** `PASSED`.

## Итерация 23 — Мобильная таблица и точность examples/preparation (2026-11-01)

- **Цель:** исправить наложения в мобильной таблице `/company/` и закрыть
  визуальные расхождения examples `/services/` и нижней границы preparation на
  странице порошковой окраски.
- **Область изменений:** production capabilities на 390/768/1024 px; examples
  section на 1440 и responsive; powder preparation list/grid на desktop и
  промежуточном desktop.
- **Связанные макеты Paper:** `О производстве`, секция «Возможности
  производства» 1440×1094; `Металлообработка`, «Примеры выполненных работ»
  1440×888; `Порошковая окраска`, «Подготовка поверхности перед окраской»
  1440×777.
- **Затронутые файлы и компоненты:** `ralmaster_production.php/.css`;
  `sections_metalworking_paper.php/.css`; `ralmaster_powder.css`;
  `production.spec.ts`, `services.spec.ts`, `painting.spec.ts`; журналы
  прогресса.
- **Функциональные автотесты:** пять capabilities rows; последовательная
  геометрия всех mobile cells; локальные images; три per-card demo notes;
  section heights; last preparation row внутри steps border; desktop/mobile
  overflow, browser diagnostics и axe в существующем scope.
- **Визуальные проверки:** Paper MCP screenshots/JSX/computed styles трёх
  точных секций; локальные Chrome captures 390×900 и 1440×1000 после правок.
- **Критерии приёмки:** mobile capabilities читаются без overlap; examples
  совпадает с Paper по изображению, отступам, типографике, размерам и action;
  последняя строка preparation не выходит ниже 481 px и не пересекает border;
  новая критическая regression отсутствует.
- **Фактический результат:** mobile table rows переведены в natural-height card
  grid с labels и scoped parameter surface. Examples использует точную
  `custom-metalwork.webp`, 72/32 px rhythm, 656/390 px card geometry, per-card
  note и 40×40 arrow control. Унаследованный theme margin у preparation `li`
  обнулён, поэтому нижняя линия вновь совпадает с image bottom с допуском 1 px.
  PHP lint **2/2 PASS**; desktop suite **23/23 PASS**; mobile targeted **3/3
  PASS**.
- **Обнаруженные дефекты:** desktop `height: 126px` выигрывал по specificity у
  mobile reset; examples сохранял operations image и глобальную подпись вместо
  структуры Paper; общий Aspro selector добавлял каждому `ol li` 12 px снизу,
  превращая четыре 120 px grid tracks в 132 px и выталкивая последнюю строку.
- **Исправленные дефекты:** mobile row height сброшена с корректной
  специфичностью, cells изолированы и не накладываются; examples markup/styles
  синхронизированы с Paper; preparation item margins локально обнулены и
  закреплены geometry assertion.
- **Оставшиеся ограничения:** отдельного mobile-фрейма company в Paper нет,
  поэтому карточная версия является производным responsive-решением;
  state-changing submit/mail/CRM не выполнялся; Lighthouse остаётся
  приостановлен по указанию владельца.
- **Статус:** `PASSED`.

## Итерация 24 — Единая hover-модель карточек (2026-11-01)

- **Цель:** сохранить точную композицию accepted-products из Paper и
  унифицировать hover этой группы и product cards на `/projects/`.
- **Область изменений:** три карточки «Какие изделия принимаем на окраску»;
  три карточки «Продукция» в listing реализованных проектов; reduced-motion.
- **Связанные макеты Paper:** `Порошковая окраска`, accepted-products 1440×878;
  `Реализованные проекты`, product section 1440×696.
- **Затронутые файлы и компоненты:** `ralmaster_powder.css`;
  `projects/style.css`; `painting.spec.ts`; `projects.spec.ts`; журналы
  прогресса.
- **Функциональные автотесты:** точные размеры accepted section/grid/card/image/
  body; computed hover transform/shadow; отсутствие image-only transform;
  desktop/mobile overflow, browser diagnostics и axe в существующем scope.
- **Визуальные проверки:** Paper MCP screenshot/JSX/computed styles accepted
  section; локальные Chrome captures hovered accepted-card и project product-card
  при 1440 px.
- **Критерии приёмки:** статическое состояние accepted cards совпадает с Paper;
  обе группы используют один эффект без layout shift; старый image zoom projects
  отсутствует; reduced-motion отключает перемещение.
- **Фактический результат:** точная Paper-геометрия accepted section подтверждена
  и закреплена тестом. Обе группы используют `translateY(-2px)` и мягкую тень с
  transition 180 ms; product image больше не масштабируется отдельно. Desktop
  suite **22/22 PASS**, mobile targeted **4/4 PASS**.
- **Обнаруженные дефекты:** accepted cards не имели hover feedback; product cards
  проектов использовали отличный от сайта `scale(1.015)` только на изображении.
- **Исправленные дефекты:** добавлена общая card-level animation и
  `prefers-reduced-motion`; разнородный zoom удалён; geometry Paper не изменена.
- **Оставшиеся ограничения:** hover не применяется на touch-only устройствах;
  это ожидаемо, а мобильная статическая композиция проверена отдельно.
- **Статус:** `PASSED`.

## Итерация 25 — Карточки окраски и возврат каталога с корзиной (2026-11-01)

- **Цель:** сделать карточки «Какие изделия принимаем на окраску» визуально
  завершёнными по прямому запросу владельца и вернуть утраченную после
  редизайна возможность выбирать готовые изделия и добавлять их в корзину.
- **Область изменений:** accepted-products на `/painting/` и общем powder
  detail route; динамическая категория `/product/pannier/`; существующая
  Bitrix-корзина `/cart/`; desktop/mobile представление каталога.
- **Связанные макеты Paper:** `Порошковая окраска`, accepted-products; общая
  структура карточек из фрейма `Продукция`. Порядок title/image/description
  является явной коррекцией владельца поверх ранее подтверждённого макета.
- **Затронутые файлы и компоненты:** `ralmaster_powder.php/.css`;
  `section_product_paper.php`; общий `ralmaster_detail.php/.css`;
  `painting.spec.ts`; `derived-pages.spec.ts`; журналы прогресса.
- **Функциональные автотесты:** три динамических товара из IBlock; постоянно
  видимая desktop/mobile CTA корзины; add-to-cart и переключение на «В
  корзине»; фактическое наличие выбранного товара на `/cart/`; точный порядок
  heading/image/description; responsive overflow и browser diagnostics в
  существующем suite.
- **Визуальные проверки:** локальные captures
  `test-results/powder-accepted-title-image-description.png`,
  `pannier-catalog-restored-desktop.png`,
  `pannier-catalog-restored-mobile.png` и
  `pannier-card-desktop-final.png`; проверены 1440 и 390 px.
- **Критерии приёмки:** у каждой powder-card заголовок расположен над
  изображением, описание — под ним; redesign category не прерывает штатный
  Bitrix listing; товары, статус, количество и корзина остаются динамическими;
  desktop CTA не требует hover; на мобильном нет horizontal overflow.
- **Фактический результат:** три powder-card используют ряды 74/254/73 px при
  сохранённой общей высоте 401 px. Ранний возврат custom section template
  больше не отбрасывает catalog result: штатный `catalog_table_2` встроен в
  новую дизайн-систему. На `/product/pannier/` доступны КДК 3/2/1, счётчик
  `− 1 +`, кнопка «В корзину» и переход в существующую `/cart/`. Desktop и
  mobile end-to-end flow подтверждён реальным товаром. PHP lint **3/3 PASS**;
  полный связанный suite **20/20 PASS**; финальный targeted повтор **3/3 PASS**.
- **Обнаруженные дефекты:** custom section template выполнял `return` до
  стандартного `$itemsCnt` rendering; старый `.footer-button` имел
  `display:none`/`opacity:0` вне hover; clearfix pseudo-elements ломали grid
  счётчика; accepted-card показывала объединённый нижний content-block и
  воспринималась как обрезанная.
- **Исправленные дефекты:** live Bitrix component подключён без статических
  копий товара; старые hover-only и clearfix rules локально нейтрализованы;
  quantity control выровнен и сохраняет исходный JS; powder-card получила
  явные heading и description rows.
- **Оставшиеся ограничения:** данные, цены и наличие зависят от наполнения
  IBlock; успешная отправка форм/mail/CRM не выполнялась; Lighthouse остаётся
  приостановлен по указанию владельца.
- **Статус:** `PASSED`.

## Итерация 26 — Полный legacy-flow товарных категорий и popup CTA покраски (2026-11-01)

- **Цель:** вернуть товарным категориям структуру и функции старого сайта без
  возврата legacy-визуала, а все CTA расчёта на странице порошковой окраски
  перевести с anchor-scroll на общий popup.
- **Область изменений:** `/product/pannier/`, `/product/bracket/` и остальные
  разделы `/product/*/`; left navigation, smart filter, сортировка, виды
  каталога, карточки, количество и корзина; accepted/examples/orders CTA на
  `/painting/`.
- **Связанные макеты Paper:** дизайн-система текущих product/powder страниц;
  функциональная структура проверена по действующим legacy-страницам
  `ral-master.ru/product/pannier/` и `ral-master.ru/product/bracket/`.
- **Затронутые файлы и компоненты:** `product/index.php`; общий section template
  `catalog/section.php`; новый `ralmaster_product_catalog.css`; удалённый
  `section_product_paper.php`; `ralmaster_powder.php/.css`;
  `derived-pages.spec.ts`, `painting.spec.ts`; журналы прогресса.
- **Функциональные автотесты:** семь ссылок категорий и active state; smart
  filter; sort/view controls; фактические 3/1 товара; add-to-cart POST и товар
  в `/cart/`; popup CTA без изменения `scrollY`; desktop/mobile overflow и
  browser diagnostics.
- **Визуальные проверки:** production reference captures pannier/bracket;
  локальные desktop/mobile captures 1440×1100 и 390×844; hover/focus state;
  заполнение accepted images через `object-fit: cover`.
- **Критерии приёмки:** верхний detail-hero и кнопка расчёта отсутствуют у
  категории; левое меню возвращено; характеристики не показываются при
  наведении; изображение остаётся большим и центрированным; `− 1 +` и корзина
  работают; CTA покраски открывают dialog, а не прокручивают к inline-форме.
- **Фактический результат:** custom early-return удалён, категории снова идут
  через штатные `include_filter.php`, `include_sort.php` и
  `catalog_table_2.php`. Старое меню, фильтр, сортировка и три режима вида
  сохранены и оформлены в новой системе. Product image занимает 300 px frame,
  остаётся видимым на hover, `.props_wrapper` всегда скрыт, quantity и basket
  controls используют исходный Bitrix JS. На mobile категории доступны в
  горизонтальном scroll, CTA и счётчик остаются видимыми. Все пять внутренних
  powder CTA открывают общий dialog; accepted images заполняют 254 px блоки.
- **Обнаруженные дефекты:** прежний custom template отбрасывал меню, фильтр и
  сортировку; theme hover скрывал `<a>` с изображением ради таблицы
  характеристик, поэтому после скрытия характеристик карточка становилась
  пустой; mobile sort select выходил за viewport; CTA оставались ссылками на
  `#powder-estimate`; `contain` создавал белые поля в двух accepted cards.
- **Исправленные дефекты:** восстановлен полный динамический catalog flow;
  image anchor принудительно остаётся видимым на hover/focus; mobile ordering
  layout переведён в grid; CTA заменены на dialog triggers; изображения
  используют `cover`. PHP lint **3/3 PASS**; финальный связанный Playwright
  regression desktop/mobile **22/22 PASS** за 2.4 min; дополнительный targeted
  hover/image gate **1/1 PASS**.
- **Оставшиеся ограничения:** цены в текущем IBlock пустые, поэтому корзина
  корректно показывает итог `0`; успешная отправка форм/mail/CRM не
  выполнялась; Lighthouse остаётся приостановлен по указанию владельца.
- **Статус:** `PASSED`.

## Итерация 27 — Каталог без legacy-акцентов и сквозная оптимизация загрузки (2026-11-01)

- **Цель:** исправить визуальные дефекты фильтра и controls товарной категории,
  убрать лишние тени и сократить фактическое время первоначальной загрузки и
  переходов на локальном стенде и после следующего staging deployment.
- **Область изменений:** smart filter, range slider, view controls и buy block
  товарных категорий; development Docker I/O/OPcache; responsive-изображения
  главной, продукции, услуг, покраски, производства, проектов, контактов и
  общих detail pages.
- **Связанные макеты Paper:** текущая дизайн-система `Ралмастер` → `Дизайн`;
  визуальные corrections владельца для `/product/pannier/` и общие
  изображения соответствующих страниц.
- **Затронутые файлы и компоненты:** `compose.yml`;
  `docker/php/development.ini`; `ralmaster_product_catalog.css`; home/powder/
  production/detail/contact page blocks; product/project/service component
  templates; 33 новых `*-768.webp`; `derived-pages.spec.ts`,
  `products.spec.ts`, `projects.spec.ts`; README/environment/progress docs.
- **Функциональные автотесты:** точные blue computed styles slider/reset;
  одинаковая геометрия filter buttons; отсутствие shadow у card/button block;
  mobile centering; responsive `srcset` и достаточное intrinsic resolution;
  live basket flow; полный redesign desktop/mobile regression.
- **Визуальные проверки:** локальные Chrome captures
  `artifacts/ui-audit/catalog-desktop.png`,
  `catalog-mobile-controls.png`, `catalog-card-flat-final.png`; desktop 1440 и
  mobile 390 px.
- **Критерии приёмки:** filter controls единообразно синие и выровнены; красный
  legacy accent отсутствует; buy controls не имеют glow/shadow; все ключевые
  routes возвращают 200 без broken resources; Docker сохраняет hot reload;
  измеримые TTFB/image-transfer/Lighthouse улучшаются без регрессий.
- **Фактический результат:** reset/slider/handles используют `#175bef`, две
  filter CTA занимают одинаковую ширину, mobile view controls центрированы,
  legacy shadow контейнера `.button_block` нейтрализована. Dev Bitrix caches и
  logs перенесены с Windows bind mount в named volumes; OPcache настроен на
  2-second timestamp revalidation. Прогретый curl TTFB снизился с прежнего
  диапазона примерно 2.66–4.21 s до 0.84–2.38 s; сопоставимый Playwright wall
  range — с 3.35–4.96 s до 1.51–2.69 s. Image transfer: home 1.78→0.58 MB,
  services 1.04→0.45 MB, powder 1.52→0.30 MB. Lighthouse desktop Performance
  **94/97/96/99/97**, mobile **81/93/86/93/92** для home/products/services/
  powder/projects; Best Practices **100**. PHP lint **12/12 PASS**, Compose
  config PASS, containers healthy, asset response gate **0 failures**. Полный
  regression дал **111/113 PASS**; два устаревших assertions на обязательную
  загрузку original-size image заменены проверкой responsive source и
  достаточного resolution, после чего targeted repeat **2/2 PASS**.
- **Обнаруженные дефекты:** Aspro `!important` сохранял красный Reset и slider;
  legacy margin смещал mobile controls на 2 px; тень находилась на
  `.button_block`, а не на самой CTA; Bitrix cache I/O шёл через Windows bind
  mount; страницы передавали original WebP 1.4–2.1K px даже в карточках
  310–438 px; первый service image был LCP, но оставался lazy.
- **Исправленные дефекты:** усилены scoped design overrides; сброшены legacy
  margins/shadows; кеши изолированы в volumes; добавлены responsive WebP/
  `srcset`/`sizes`; первый service LCP переведён в eager high priority (mobile
  Performance 79→86, LCP 4.84→3.71 s).
- **Оставшиеся ограничения:** staging получит новые WebP и template changes
  только после deploy ветки; объединённый legacy Aspro CSS около 130 KB остаётся
  render-blocking и ограничивает mobile home LCP — безопасное code splitting
  требует отдельной итерации. Local/staging `noindex` объясняет Lighthouse SEO
  66; state-changing mail/CRM submit не выполнялся.
- **Статус:** `PASSED`.

## Итерация 28 — Единая плитка каталога и состояния фильтра (2026-11-01)

- **Цель:** убрать остаточные legacy-состояния товарных категорий и сделать
  страницу предсказуемой при любом количестве товаров.
- **Область изменений:** sort bar, серверный выбор представления, smart filter,
  однотоварная карточка, list price и basket controls.
- **Связанные макеты Paper:** текущая дизайн-система `Ралмастер` → `Дизайн` и
  visual corrections владельца для `/product/bracket/`.
- **Затронутые файлы и компоненты:** `news/catalog/include_sort.php`;
  `catalog_table.php`, `catalog_table_2.php`; соответствующие два
  `news.list` templates; `ralmaster_product_catalog.css`;
  `derived-pages.spec.ts`; журналы итераций и прогресса.
- **Функциональные автотесты:** категории pannier/bracket и фактические товары;
  отсутствие view controls и сохранение `table` даже с `?display=list`;
  отсутствие list price `1`; синий checked checkbox и popup result; компактная
  однострочная геометрия Apply/Reset; сохранение quantity и basket flow.
- **Визуальные проверки:** desktop 1440 px captures однотоварного каталога до
  и после выбора статуса; ручная проверка карточки, filter CTA и footer flow.
- **Критерии приёмки:** каталог всегда плиточный; лишняя цена `1` отсутствует;
  кнопки фильтра находятся в одной строке; Reset остаётся белым с синим
  текстом; checkbox и popup не используют красный accent; карточка не имеет
  искусственного нижнего min-height.
- **Фактический результат:** переключатели table/list/price удалены из HTML,
  `$display` зафиксирован как `table` независимо от query/cookie. List price
  отключён параметром компонента в обоих table templates, поэтому quantity
  `− 1 +` является единственным числом управления. Text/card padding сокращён,
  filter CTA имеют высоту 40 px и стоят в одной строке. Checkbox, popup result,
  slider и Reset используют `#175bef`.
- **Обнаруженные дефекты:** IBlock price со значением `1` выводился отдельной
  строкой; фактически активен `catalog_table_2`, поэтому исправление только
  резервного `catalog_table` не меняло страницу; legacy `min-height: 60px
  !important` растягивал CTA; theme color продолжал красить checked checkbox и
  popup результата.
- **Исправленные дефекты:** синхронно обновлены оба table templates; добавлен
  scoped override legacy min-height; удалена серверная логика смены вида;
  PHP lint **5/5 PASS**, targeted Playwright desktop/mobile **3/3 PASS**.
- **Оставшиеся ограничения:** цены каталога намеренно не показываются в
  плитке — стоимость формируется по запросу; сами данные IBlock не изменялись.
- **Статус:** `PASSED`.

## Итерация 29 — Нижняя граница последнего ряда каталога (2026-11-01)

- **Цель:** убрать пустую полосу под последним рядом товарных карточек при
  любом количестве строк.
- **Область изменений:** grid wrappers плиточного каталога и regression gate
  геометрии последнего ряда.
- **Связанные макеты Paper:** визуальная сетка товарных карточек проекта
  `Ралмастер` и correction владельца для `/product/bracket/`.
- **Затронутые файлы и компоненты:** `ralmaster_product_catalog.css`,
  `derived-pages.spec.ts`, журналы прогресса и итераций.
- **Функциональные автотесты:** категории с одним и тремя товарами; computed
  `margin-bottom: 0`; совпадение нижней границы grid и последнего ряда с
  допуском 1 px.
- **Визуальные проверки:** desktop 1440 px, категории bracket и pannier.
- **Критерии приёмки:** между строками остаётся grid gap 24 px, но после
  последней строки дополнительный отступ отсутствует.
- **Фактический результат:** legacy margin `0 0 32px` у Bootstrap wrappers
  сброшен scoped-правилом; межстрочный интервал продолжает задаваться CSS Grid,
  нижняя разница для 1 и 3 карточек равна **0 px**.
- **Обнаруженные дефекты:** отступ находился не на `.items` и не на карточке,
  а на каждом дочернем `col-*` wrapper, поэтому увеличивал последнюю grid row.
- **Исправленные дефекты:** margin wrappers сброшен; targeted Playwright
  **1/1 PASS**, геометрический gate bracket/pannier **2/2 PASS**.
- **Оставшиеся ограничения:** отсутствуют в проверенном scope.
- **Статус:** `PASSED`.

## Итерация 30 — Иконки повторных партий, центрирование CTA и отступ загрузчика (2026-11-01)

- **Цель:** устранить три визуальных дефекта, отмеченных владельцем на
  `/company/` и `/projects/`.
- **Область изменений:** SVG-иконки блока «Повторные партии»; широкоформатное
  положение CTA «Обсудить заказ или регулярные поставки»; вертикальный ритм
  формы расчёта проекта.
- **Связанные макеты Paper:** текущие production/projects экраны проекта
  `Ралмастер` и приложенные владельцем контрольные снимки.
- **Затронутые файлы и компоненты:** `ralmaster_production.php/.css`;
  `components/bitrix/news/projects/style.css`; `production.spec.ts`,
  `projects.spec.ts`; журналы прогресса и итераций.
- **Функциональные автотесты:** геометрия и overflow трёх SVG; центр CTA
  относительно секции на viewport 1882 px; зазор textarea/uploader не менее
  16 px; полный production/projects desktop regression.
- **Визуальные проверки:** локальные captures `repeat-icons-fixed.png`,
  `production-contact-centered.png`, `projects-form-gap.png` в
  `artifacts/ui-audit/`.
- **Критерии приёмки:** repeat icon не обрезан; карандаш не содержит лишнего
  диагонального сегмента; CTA центрирован на широком экране; загрузчик не
  соприкасается с полем описания.
- **Фактический результат:** первые две иконки заменены чистыми 24×24 SVG,
  scoped CSS устраняет более позднее legacy-правило `svg:not(:root)`;
  фиксированная CTA-карточка получает автоматические боковые отступы; перед
  FILE row добавлено 16 px.
- **Обнаруженные дефекты:** исходный repeat path был визуально неполным;
  pencil path содержал лишний сегмент; CTA начинался с фиксированного padding
  секции даже при viewport шире 1440 px; фактический зазор формы был **0 px**.
- **Исправленные дефекты:** все три дефекта закрыты. PHP lint **1/1 PASS**;
  targeted checks **3/3 PASS**; полная связанная desktop regression
  production/projects **24/24 PASS**.
- **Оставшиеся ограничения:** успешная отправка формы/mail/CRM не выполнялась;
  данные и файлы пользователя не изменялись.
- **Статус:** `PASSED`.

## Итерация 31 — Структура заголовков, карточек и legacy-hover (2026-11-01)

- **Цель:** закрыть пять визуальных замечаний владельца на страницах партнёров,
  проектов, производства и товарного каталога.
- **Область изменений:** heading партнёров; порядок содержимого project product
  cards; нижняя граница шагов заказа; hover заголовка smart filter; исходные
  SVG блока повторных партий.
- **Связанные макеты Paper:** production/projects/partners экраны текущего
  проекта `Ралмастер` и пять приложенных владельцем контрольных снимков.
- **Затронутые файлы и компоненты:** `partners-paper/style.css`;
  `project-products-paper/template.php`; `news/projects/style.css`;
  `ralmaster_production.php/.css`; `ralmaster_product_catalog.css`;
  `derived-pages.spec.ts`, `production.spec.ts`, `projects.spec.ts` и журналы.
- **Функциональные автотесты:** одинаковая левая координата kicker/title;
  последовательность title → image → description и отсутствие внутреннего
  overflow; совпадение нижней границы списка с последней строкой; синий
  computed hover; исходные SVG paths и размер 28 px.
- **Визуальные проверки:** пять captures `iteration-31-*` и финальный
  `iteration-31-project-products-final.png` в `artifacts/ui-audit/`.
- **Критерии приёмки:** heading партнёров начинается слева; заголовок каждой
  карточки проекта находится над изображением, описание — под ним; нижний
  divider не пересекает последний шаг; status hover не красный; исходные
  repeat/pencil/calendar icons сохранены и получили больше места.
- **Фактический результат:** heading grid удалён; карточки переведены на три
  явных ряда 96/280/104 px (на intermediate 96/260/104 px); глобальный
  `ol li margin-bottom: 12px` нейтрализован; legacy theme hover перекрыт синим
  scoped-правилом; исходные paths восстановлены в SVG 28×28 и контейнере 32 px.
- **Обнаруженные дефекты:** heading был разорван на две колонки; description
  находился в одном нижнем body с title; три order rows выходили на 23 px за
  list border; theme CSS возвращал `#de002b`; после первой перестановки длинное
  описание на intermediate viewport требовало ещё 20 px высоты.
- **Исправленные дефекты:** все замечания закрыты. PHP lint **2/2 PASS**;
  targeted Playwright **5/5 PASS**; desktop regression **35/35 PASS**; mobile
  regression **7/7 PASS**; дополнительный intermediate gate **2/2 PASS**.
- **Оставшиеся ограничения:** state-changing отправка форм/mail/CRM не
  выполнялась; данные IBlock не изменялись.
- **Статус:** `PASSED`.

## Итерация 32 — Полное заполнение изображений в карточках Projects (2026-11-01)

- **Цель:** убрать белые поля внутри image row трёх product-карточек на
  `/projects/`.
- **Область изменений:** изображения `.rm-projects-product-card` на desktop,
  intermediate и mobile viewport.
- **Связанные макеты Paper:** product-блок страницы Projects и приложенный
  владельцем контрольный снимок.
- **Затронутые файлы и компоненты:** `news/projects/style.css`,
  `projects.spec.ts`, журналы прогресса и итераций.
- **Функциональные автотесты:** computed `object-fit: cover`; общая вертикаль
  изображений; точные section heights; hover; overflow, browser diagnostics и
  axe; mobile listing/details.
- **Визуальные проверки:** desktop 1440 px capture
  `artifacts/ui-audit/iteration-32-project-products-cover.png`.
- **Критерии приёмки:** фотография занимает весь ряд 421×280 px без боковых
  или верхних полей; размеры карточки и соседние тексты не меняются.
- **Фактический результат:** `contain` заменён на центрированный `cover`; все
  три 1448×1086 источника заполняют row 421×280 px.
- **Обнаруженные дефекты:** `<img>` занимал весь grid row, но `contain`
  вписывал исходник 4:3 в более широкий контейнер и оставлял видимые поля.
- **Исправленные дефекты:** поля устранены. Targeted Playwright **3/3 PASS**;
  полный Projects desktop regression **14/14 PASS**; mobile **3/3 PASS**.
- **Оставшиеся ограничения:** исходные файлы изображений и данные IBlock не
  изменялись.
- **Статус:** `PASSED`.

## Итерация 33 — Канал связи и надёжная отправка формы заказа (2026-11-01)

- **Цель:** устранить зависание popup-формы на «Отправляем…», сделать файл
  необязательным и привести загрузчик в порядок на мобильных экранах.
- **Область изменений:** общий popup «Обсудить заказ»; выбор способа связи;
  Bitrix form instance; mobile-формы `/painting/` и `/services/`.
- **Связанные макеты Paper:** `Ралмастер` → `Дизайн` → «РАЛМАСТЕР — Обсудить
  заказ» (`3U0-0`, 748×850) и предоставленный владельцем пример выбора канала.
- **Затронутые файлы и компоненты:** `ralmaster_discuss_order.php/.css`;
  `ralmaster-estimate.js`; form template `contacts/template.php`;
  `ralmaster_powder.css`; `sections_metalworking_paper.css`; тесты popup,
  painting и services; журналы итераций и прогресса.
- **Функциональные автотесты:** четыре канала Telegram/MAX/почта/телефон;
  method-specific validation; отсутствие `required` у FILE; реальный POST без
  файла; успешный результат popup; отсутствие POST при ошибках; независимая
  валидация нескольких одноимённых Bitrix-форм.
- **Визуальные проверки:** desktop popup 748 px; mobile popup 390 px; полная
  синяя focus-рамка имени; отсутствие пересечения filename/action; порядок
  FILE → согласие → submit на `/services/`.
- **Критерии приёмки:** файл не обязателен; канал сохраняется в CONTEXT;
  отправка завершается success state; поля и uploader не пересекаются; dialog
  остаётся внутри viewport.
- **Фактический результат:** добавлен доступный radiogroup каналов и
  контекстные placeholder/валидация; popup отправляет FormData асинхронно,
  обрабатывает timeout/error и показывает подтверждение; тестовая заявка без
  файла сохранена в Bitrix (последний проверенный result ID 29). Local Docker
  `db`/`web` healthy, HTTP 200.
- **Обнаруженные дефекты:** старый Bitrix iframe-AJAX сохранял результат, но
  не обновлял DOM popup; глобальный jQuery validator захватывал все формы с
  одинаковым name; mobile services footer перекрывал FILE на 111 px; inline
  powder uploader сталкивал label с кнопкой выбора.
- **Исправленные дефекты:** submit изолирован на текущей форме; success/error
  rendering не зависит от iframe; uploader action скрыт на узком экране;
  mobile flow переведён на auto-height. PHP lint **2/2 PASS**; связанный
  Playwright desktop **20/20 PASS**, mobile **3/3 PASS**.
- **Оставшиеся ограничения:** в локальном Docker отсутствует почтовый
  транспорт: Bitrix сохраняет заявку, но `FORM_FILLING_ralmaster_estimate_s1`
  имеет `SUCCESS_EXEC=F`. Для доставки уведомлений на staging/production нужны
  SMTP-реквизиты либо настроенный sendmail; секреты в репозиторий не добавлялись.
- **Статус:** `PASSED` для UI, валидации и сохранения заявки; SMTP отмечен как
  внешнее инфраструктурное ограничение.

## Итерация 34 — Формы, партнёры и геометрия production (2026-11-01)

- **Цель:** закрыть замечания владельца по ошибкам формы главной, оформлению
  страницы партнёров, активному меню, микровзаимодействиям и ширине таблицы
  производственных возможностей.
- **Область изменений:** inline validation главной; hero и breadcrumb
  `/company/partners/`; активное состояние header; каналы связи popup;
  focus-state полей; hover карточек и текстовых ссылок; responsive-геометрия
  `/company/`.
- **Связанные макеты Paper:** `Ралмастер` → `Дизайн`; production artboard
  «О производстве» (`3AF-0`), секция возможностей (`3BN-0`) и таблица
  (`3BQ-0`, 1344 px с полями по 48 px на холсте 1440 px).
- **Затронутые файлы и компоненты:** flat menu; `partners-paper` template/CSS;
  `ralmaster-estimate.js`; `ralmaster_discuss_order.css`; `custom.css`;
  production, powder, detail, projects, product sections и metalworking CSS;
  Playwright suites home/header/estimate/derived/production/painting/products/
  projects; журналы итераций и прогресса.
- **Функциональные автотесты:** ошибки формы находятся между своим полем и
  следующим контролом; `/company/partners/` не наследует active production;
  breadcrumb использует `/`; порядок Telegram / MAX / телефон / почта и
  корректные SVG; единственная focus-рамка; карточки получают scale 1.1;
  текст и стрелка поднимаются вместе; таблица остаётся внутри viewport на
  390/768/1024/1366/1440 px.
- **Визуальные проверки:** Paper MCP повторно подтверждён без изменения
  макетов; partners и company проверены в браузере на intermediate viewport;
  таблица имеет одинаковые боковые поля с основной сеткой, popup использует
  фирменные иконки Telegram/MAX.
- **Критерии приёмки:** validation text не перекрывается контролами; hero
  партнёров имеет структуру заголовок → описание без kicker; меню не содержит
  ложного active; поля имеют одну синюю focus-рамку; hover заметный, но не
  ломает сетку; production не создаёт горизонтальный overflow.
- **Фактический результат:** высота строк формы становится auto при ошибках;
  partners hero приведён к стилю внутренних страниц; ложный active снят на
  уровне Bitrix menu template; Telegram/MAX используют точные SVG из footer,
  телефон расположен перед почтой; глобальный `focus-visible` outline
  нейтрализован внутри popup; карточки увеличиваются на 10%, а link label и
  arrow двигаются единым элементом. Production wrappers и таблица получили
  адаптивную ширину, повторяющую Paper margins.
- **Обнаруженные дефекты:** фиксированная высота form rows прятала ошибки;
  дочерняя partners route наследовала selection `/company/`; hero сохранял
  лишний kicker и стрелку breadcrumb; одновременно применялись border и
  глобальный outline; fixed production columns выходили из content frame на
  intermediate viewport.
- **Исправленные дефекты:** все замечания текущего scope закрыты. PHP lint
  **2/2 PASS**; связанная desktop regression **37/37 PASS**; mobile regression
  **5/5 PASS**; Docker `db`/`web` healthy; `/`, `/company/partners/` и
  `/company/` отвечают HTTP **200**.
- **Оставшиеся ограничения:** SMTP/sendmail локального окружения по-прежнему
  не настроен; state-changing отправка в реальный внешний mail/CRM в этой
  итерации не выполнялась. Paper-макет не изменялся.
- **Статус:** `PASSED`.

## Итерация 35 — Контакты, popup CTA и hover-состояния (2026-11-01)

- **Цель:** устранить пять визуальных и функциональных замечаний владельца:
  дисбаланс иконок каналов, положение email, смещение карточки при hover,
  прокрутку production CTA и чёрные hover-состояния связанных услуг.
- **Область изменений:** popup способов связи; контактная часть формы главной;
  accepted-product cards на `/painting/`; CTA проверки чертежа на `/company/`;
  связанные ссылки на `/services/` и `/projects/`.
- **Связанные макеты Paper:** текущая дизайн-система `Ралмастер` → `Дизайн` и
  приложенные владельцем контрольные снимки.
- **Затронутые файлы и компоненты:** `ralmaster_discuss_order.css`;
  `custom.css`; `ralmaster_powder.css`; `ralmaster_production.php/.css`;
  metalworking и projects CSS; Playwright suites estimate/home/painting/
  production/services/projects; журналы итераций и прогресса.
- **Функциональные автотесты:** Telegram/MAX имеют 14 px, телефон — 16 px;
  email находится под телефоном; верхняя граница powder card не смещается при
  scale 1.1; production CTA открывает общий popup без изменения scroll; hover
  связанных ссылок остаётся синим/белым в фирменной палитре.
- **Визуальные проверки:** сохранена композиция title → image → description;
  scale закреплён относительно верхней границы карточки; контактная пара
  выровнена одной колонкой; белая production CTA не меняет геометрию.
- **Критерии приёмки:** логотипы Telegram/MAX визуально не доминируют над
  линейными иконками; email не стоит в одной строке с телефоном; карточка не
  поднимается к заголовку секции; кнопка не выполняет anchor jump; hover не
  становится чёрным.
- **Фактический результат:** Telegram/MAX уменьшены с 16 до 14 px; контакты
  главной переведены в колонку; `transform-origin` powder card установлен в
  `center top`; anchor `#rm-production-contact` заменён кнопкой
  `data-rm-discuss-open="production"`; service links закреплены синими, а
  project operation buttons получают синий фон и белый текст.
- **Обнаруженные дефекты:** одинаковый CSS box делал залитые логотипы
  визуально крупнее outline-иконок; desktop contacts оставались flex-row;
  scale относительно центра поднимал карточку; production CTA сохраняла
  старую anchor-навигацию; глобальный legacy hover перекрашивал links.
- **Исправленные дефекты:** все пять замечаний закрыты. PHP lint **1/1 PASS**;
  targeted regression **7/7 PASS**; связанная desktop regression **54/54
  PASS**; mobile regression **8/8 PASS**.
- **Оставшиеся ограничения:** SMTP/sendmail локального окружения не настроен;
  реальная внешняя почтовая доставка в этой итерации не проверялась.
- **Статус:** `PASSED`.

## Итерация 36 — Маршруты карточек, каналы inline-форм и подтверждение заявки (2026-11-01)

- **Цель:** исправить маршруты главной, дополнить визуальные указатели этапов
  и заменить разрушительное success-состояние форм на отдельное подтверждение.
- **Область изменений:** hero-карточка фасадных кассет; три карточки
  металлообработки главной; этапы 01–03 `/services/`; нижние формы главной и
  металлообработки; общий POST/success flow всех estimate forms.
- **Связанные макеты Paper:** текущая дизайн-система `Ралмастер` → `Дизайн` и
  четыре приложенных владельцем контрольных снимка.
- **Затронутые файлы и компоненты:** `ralmaster_home.php`; metalworking
  template/CSS; `ralmaster-estimate.js/.css`; `ralmaster_discuss_order.php/.css`;
  `custom.css`; Playwright suites home/services/estimate-dialogs; журналы
  итераций и прогресса.
- **Функциональные автотесты:** facade feature ведёт прямо на
  `/product/facadecassettes/`; три service cards ведут на `/services/`; этапы
  01/02/03 имеют стрелки 26 px; обе inline-формы показывают четыре канала и
  записывают канал в CONTEXT; реальный POST показывает имя в success dialog,
  не заменяет форму и закрывается кнопкой «ОК».
- **Визуальные проверки:** success popup имеет затемнённый backdrop, синюю
  отметку, заголовок и полноширинную синюю кнопку; исходная форма остаётся на
  прежнем месте; добавление channel row не создаёт desktop/mobile overflow.
- **Критерии приёмки:** карточки открывают требуемые разделы; у третьего этапа
  нет отсутствующей стрелки; нижние формы повторяют selector popup; после
  успешной отправки отсутствует legacy-блок «Обновить страницу» и DOM формы
  не заменяется.
- **Фактический результат:** hero ведёт в выбранную категорию кассет; URL всех
  home metalworking cards нормализован до `/services/`; все три стрелки
  одинаковы. Общий capture-submit валидирует и отправляет FormData, показывает
  безопасный native dialog «Ваша заявка принята» с именем, восстанавливает CTA
  и сохраняет исходную форму. Telegram/MAX/телефон/почта подключены к нижним
  home/metalworking forms.
- **Обнаруженные дефекты:** hero ссылался на корень каталога; динамический URL
  service cards переопределял fallback; третий этап не имел `<i>`; success
  AJAX заменял форму legacy-разметкой; communication selector был включён
  только в request popup.
- **Исправленные дефекты:** все замечания закрыты. PHP lint **3/3 PASS**; Node
  syntax check **1/1 PASS**; targeted regression с двумя реальными POST **6/6
  PASS**; связанная desktop regression **18/18 PASS**; mobile **3/3 PASS**.
- **Оставшиеся ограничения:** локальная запись заявки подтверждена, но
  SMTP/sendmail всё ещё не настроен, поэтому внешняя почтовая доставка зависит
  от серверной инфраструктуры.
- **Статус:** `PASSED`.

## Итерация 37 — Раскрытие FAQ и завершение success-flow форм (2026-11-01)

- **Цель:** устранить выход раскрытого ответа FAQ за границы секции и
  завершить ожидаемый сценарий успешной отправки для inline- и popup-форм.
- **Область изменений:** FAQ страницы `/company/`; общий асинхронный submit
  estimate forms; success dialog; production/home/popup автотесты.
- **Связанные макеты Paper:** текущая дизайн-система `Ралмастер` → `Дизайн`;
  макеты не изменялись, исправлена только адаптация динамической высоты.
- **Затронутые файлы и компоненты:** `ralmaster_production.css`;
  `ralmaster-estimate.js`; Playwright suites production, home и
  estimate-dialogs; журналы итераций и прогресса.
- **Функциональные автотесты:** раскрытый FAQ полностью находится внутри
  списка и секции; после успешного POST очищаются имя, контакт, задача, файл и
  согласие; канал связи возвращается к Telegram; popup исходной формы
  закрывается до показа success dialog; inline-форма остаётся на странице.
- **Визуальные проверки:** FAQ сохраняет минимальную высоту Paper в закрытом
  состоянии и растёт по содержимому в открытом; шестой пункт не выходит за
  тёмный фон и нижнюю рамку.
- **Критерии приёмки:** раскрытие любого ответа не создаёт наложений; после
  успешной отправки форма готова к новой заявке; одновременно не отображаются
  два модальных окна; уведомление «Ваша заявка принята» получает фокус.
- **Фактический результат:** жёсткие desktop-высоты FAQ заменены минимальными;
  общий success handler сбрасывает текущую форму, восстанавливает исходный
  способ связи и закрывает родительский request dialog перед открытием
  подтверждения. Inline-формы не скрываются.
- **Обнаруженные дефекты:** `height: 810px/578px` не учитывал высоту раскрытого
  `<details>`; формы сохраняли введённые данные; popup формы оставался открыт
  под success dialog.
- **Исправленные дефекты:** все три замечания закрыты. Node syntax **1/1
  PASS**; целевые сценарии **4/4 PASS**; полная desktop-регрессия трёх
  затронутых suite **24/24 PASS**; production responsive gate 768/1024 входит
  в regression. Docker build успешен, `db`/`web` healthy, `/` и `/company/`
  отвечают HTTP **200**.
- **Оставшиеся ограничения:** SMTP/sendmail локального окружения по-прежнему
  не настроен; тест подтверждает сохранение заявки и UI success-flow, но не
  внешнюю доставку почтового уведомления.
- **Статус:** `PASSED`.

## Итерация 38 — Заголовок партнёров и валидация каналов связи (2026-11-01)

- **Цель:** удалить лишний подзаголовок списка партнёров и восстановить
  успешную отправку popup-формы для Telegram/MAX.
- **Область изменений:** секция партнёров `/company/partners/`; серверная
  валидация общей estimate form; сценарий успешной отправки request popup.
- **Связанные макеты Paper:** текущая дизайн-система `Ралмастер` → `Дизайн` и
  два приложенных владельцем контрольных снимка; Paper не изменялся.
- **Затронутые файлы и компоненты:** шаблон `partners-paper`; PHP validator
  estimate form; Playwright suites derived-pages и estimate-dialogs; журналы
  итераций и прогресса.
- **Функциональные автотесты:** запрос Telegram со значением `3214`, без имени
  и файла, проходит серверную валидацию, закрывает request dialog, очищает
  поля и открывает success dialog; список партнёров не содержит kicker «Нам
  доверяют».
- **Визуальные проверки:** заголовок «Компании, с которыми мы работаем» остаётся
  выровнен по левой границе контейнера; карточки и сетка не меняются.
- **Критерии приёмки:** Telegram/MAX принимают username или номер длиной от
  трёх символов; телефон и email сохраняют специализированную проверку;
  успешный POST не оставляет исходный popup открытым.
- **Фактический результат:** сервер извлекает канал из поля CONTEXT и применяет
  согласованную с интерфейсом проверку. Удалён только требуемый kicker над
  списком компаний. Точный пользовательский сценарий воспроизведён и закрыт.
- **Обнаруженные дефекты:** клиент разрешал короткий Telegram/MAX identifier,
  а сервер принимал только email или телефон; HTTP 200 содержал ошибку формы,
  поэтому success dialog не открывался.
- **Исправленные дефекты:** все замечания закрыты. PHP lint **1/1 PASS**;
  связанная desktop-регрессия **13/13 PASS**; Docker build успешен,
  `db`/`web` healthy, `/painting/` и `/company/partners/` отвечают HTTP **200**.
- **Оставшиеся ограничения:** SMTP/sendmail локального окружения не настроен;
  тест подтверждает запись заявки и UI success-flow, но не внешнюю доставку
  почтового уведомления.
- **Статус:** `PASSED`.

## Итерация 39 — Единые каналы связи и checkbox расчётных форм (2026-11-01)

- **Цель:** привести inline-формы продукции, порошковой окраски и проектов к
  общему виду request popup, убрать красный checkbox и исправить тесную
  компоновку формы проектов.
- **Область изменений:** `/product/`, `/painting/`, `/projects/`; общий CSS
  estimate forms; desktop/mobile представление и валидация.
- **Связанные макеты Paper:** текущая дизайн-система `Ралмастер` → `Дизайн` и
  четыре приложенных владельцем контрольных снимка; макеты не изменялись.
- **Затронутые файлы и компоненты:** product/powder/projects PHP templates и
  CSS; общий `ralmaster-estimate.css`; Playwright suites products, painting и
  projects; журналы итераций и прогресса.
- **Функциональные автотесты:** каждая inline-форма отображает Telegram, MAX,
  телефон и почту, сохраняет `contact=telegram` в CONTEXT, блокирует пустую
  задачу без POST и сохраняет доступную группу radio buttons.
- **Визуальные проверки:** checked checkbox имеет фирменный синий `#175bef`;
  selector каналов помещается внутри формы; у проекта сохранён зазор не менее
  16 px перед загрузчиком, submit полностью находится внутри белой панели;
  горизонтального overflow нет на 1440, intermediate desktop и 390 px.
- **Критерии приёмки:** ни одна из трёх форм не использует красный checkbox;
  четыре канала видимы; поля, загрузчик, согласие и CTA не перекрываются и не
  обрезаются на desktop/mobile.
- **Фактический результат:** communication selector и checkbox перенесены в
  общий слой estimate form. Product и projects секции расширены до 850 px под
  фактический состав полей; project form переведена на grid с равными
  интервалами и высотой панели 738 px. Мобильные формы остаются auto-height.
- **Обнаруженные дефекты:** три inline-формы не имели флага подключения
  selector; checkbox наследовал красную тему; фиксированная project panel
  548 px обрезала нижние элементы и не задавала интервалы между полями.
- **Исправленные дефекты:** все замечания закрыты. PHP lint **3/3 PASS**;
  целевые сценарии **10/10 PASS**; полная desktop-регрессия **32/32 PASS**;
  mobile **5/5 PASS**; Docker build успешен, `db`/`web` healthy.
- **Оставшиеся ограничения:** внешняя доставка уведомлений по-прежнему зависит
  от SMTP/sendmail серверного окружения и не входит в визуальную итерацию.
- **Статус:** `PASSED`.

## Итерация 40 — Единый визуальный контракт всех расчётных форм (2026-11-01)

- **Цель:** окончательно устранить различия полей, шрифтов, загрузчика,
  согласия и CTA между встроенными формами и общим popup.
- **Область изменений:** формы главной, `/product/`, `/painting/`,
  `/services/`, `/projects/` и reusable discuss-order dialog; desktop/mobile.
- **Связанные макеты Paper:** текущая дизайн-система `Ралмастер` → `Дизайн`
  и три приложенных владельцем контрольных снимка; Paper не изменялся.
- **Затронутые файлы и компоненты:** общий
  `ralmaster-estimate.css`; размеры home order panel в `custom.css`;
  Playwright suites forms-consistency, contacts и estimate-dialogs; журналы
  итераций и прогресса.
- **Функциональные автотесты:** пять inline-форм и popup сравниваются по
  вычисленным стилям label/input/textarea/uploader/consent/submit; проверяются
  четыре канала связи, синий checked checkbox и отсутствие mobile overflow.
- **Визуальные проверки:** Onest 14/20 для label, 16/24 для полей; input 56
  px, textarea 112 px, uploader 56 px, submit 60 px; белый uploader с dashed
  border, единая скрепка и кнопка выбора файла; одинарное синее focus-состояние.
- **Критерии приёмки:** ни одна страница не переопределяет общий uploader или
  consent; скрытый служебный Bitrix-блок не занимает место; все элементы
  помещаются в панели 738 px и остаются доступны на 390 px.
- **Фактический результат:** введён единый компонентный CSS-контракт для всех
  шести контекстов. Локальные поздние стили больше не переводят согласие в
  вертикальный вид и не показывают второй служебный submit-block. Устранены
  переполнения product/powder/projects и выровнена высота home form panel.
- **Обнаруженные дефекты:** четыре варианта uploader, submit 56/60 px,
  consent line-height 20/24, dialog fields 14/22, textarea 116/112, красный
  checkbox и поздние page overrides, добавлявшие 96 px к форме.
- **Исправленные дефекты:** все различия закрыты. Полная связанная desktop-
  регрессия **61/61 PASS**, mobile **10/10 PASS**; отдельный contract suite
  покрывает пять inline-форм и reusable dialog. Docker build успешен, `db` и
  `web` healthy; пять проверенных маршрутов отвечают HTTP **200**.
- **Оставшиеся ограничения:** SMTP/sendmail локального окружения не настроен;
  внешняя почтовая доставка не относится к визуальной унификации форм.
- **Статус:** `PASSED`.

## Итерация 41 — Подпись загрузчика, CTA и заголовок категории (2026-11-01)

- **Цель:** вернуть видимую подпись загрузки на главной и `/services/`,
  выровнять стрелки submit-кнопок, привести заголовок категории продукции к
  принятому виду хлебных крошек и добавить согласованный hover двум ссылкам
  блока заказов порошковой окраски.
- **Область изменений:** общие estimate forms; `/painting/`, `/product/` и
  производные категории `/product/*/`; desktop/mobile.
- **Связанные макеты Paper:** текущая дизайн-система `Ралмастер` → `Дизайн` и
  два приложенных владельцем контрольных снимка; Paper не изменялся.
- **Затронутые файлы и компоненты:** общий `ralmaster-estimate.css`; category
  header и catalog CSS; powder orders PHP/CSS; reusable discuss-order popup;
  Playwright suites forms-consistency, painting и derived-pages; журналы
  итераций и прогресса.
- **Функциональные автотесты:** пять inline-форм и popup проверяют видимую
  подпись uploader и единый submit-arrow; категории используют два `/` и не
  содержат kicker «Каталог продукции»; обе powder order CTA поднимают текст и
  стрелку, сохраняя стабильную геометрию клика и позицию страницы при popup.
- **Визуальные проверки:** uploader показывает «Прикрепить файлы» внутри блока
  56 px; стрелка submit имеет 26 px и вертикально центрирована; хлебные крошки
  категории отображаются через `/`; CTA-анимация поднимает содержимое на 4 px.
- **Критерии приёмки:** подпись не обрезается старыми absolute-стилями;
  стрелка одинакова на `/painting/` и `/product/`; лишний синий kicker удалён;
  hover не вызывает прокрутку при открытии popup; mobile overflow отсутствует.
- **Фактический результат:** общий uploader явно сбрасывает у псевдоэлемента
  legacy positioning; submit-arrow получает более специфичный общий контракт;
  category header очищен; анимация применена к двум внутренним span, поэтому
  текст и стрелка двигаются вместе без изменения hit area. Popup дополнительно
  сохраняет текущую прокрутку при фокусировке.
- **Обнаруженные дефекты:** `top: 49px` из старых home styles обрезал подпись;
  page overrides смещали и уменьшали стрелку; category template использовал
  стрелки и лишний kicker; transform всей CTA менял геометрию клика.
- **Исправленные дефекты:** все замечания закрыты. PHP lint **3/3 PASS**;
  связанная desktop-регрессия **23/23 PASS**, mobile **5/5 PASS**; Docker build
  успешен, `db`/`web` healthy; пять проверенных маршрутов отвечают HTTP **200**.
- **Оставшиеся ограничения:** внешняя доставка уведомлений зависит от
  SMTP/sendmail серверного окружения и не входит в эту визуальную итерацию.
- **Статус:** `PASSED`.

## Итерация 42 — Гарантированное обновление PHP OPcache на VPS (2026-11-01)

- **Цель:** устранить ситуацию, когда Git checkout содержит новый коммит, но
  публичный staging продолжает отдавать старые PHP-шаблоны.
- **Область изменений:** VPS deploy lifecycle; production OPcache; документация
  повторного развёртывания. База данных и production-инфраструктура не меняются.
- **Связанные макеты Paper:** не применимо; макеты и UI-файлы не изменялись.
- **Затронутые файлы и компоненты:** `deploy/ralmaster-vps-deploy.sh` и
  `deploy/README.md`; журналы итераций и прогресса.
- **Функциональные автотесты:** Bash syntax check; проверка поддержки Compose
  `--force-recreate`; фактическое пересоздание только локального web-контейнера
  с сохранением healthy DB; HTTP smoke после пересоздания.
- **Визуальные проверки:** публичный HTML до исправления проверен напрямую с
  cache-busting и `Cache-Control: no-cache`: он содержал старые стрелки,
  старый kicker и старую разметку powder CTA при новом Git HEAD.
- **Критерии приёмки:** каждый повторный deploy пересоздаёт web-контейнер даже
  при неизменившихся image/Compose config; MySQL и volumes не пересоздаются;
  Apache/PHP стартует с пустым process OPcache.
- **Фактический результат:** команда запуска web дополнена
  `--force-recreate`. Это соответствует production-настройке
  `opcache.validate_timestamps=0` и гарантирует загрузку шаблонов нового
  коммита. В README зафиксирована причина и отсутствие влияния на БД.
- **Обнаруженные дефекты:** Compose видел тот же image/config и оставлял web
  контейнер возрастом 9 часов; bind-mounted checkout обновлялся, но PHP workers
  продолжали использовать bytecode старых шаблонов из OPcache.
- **Исправленные дефекты:** deploy lifecycle исправлен. Git Bash syntax
  **PASS**; Compose flag подтверждён; локальный `web` фактически recreated и
  healthy, `db` остался running/healthy; `/product/kapelnik/` отвечает HTTP
  **200**.
- **Оставшиеся ограничения:** внешняя копия deploy-скрипта в
  `/root/ralmaster/` хранится вне Git checkout; её нужно один раз заменить
  исправленной версией из `/root/ralmaster/app/deploy/`.
- **Статус:** `PASSED`.

## Итерация 43 — Устойчивая валидация request popup (2026-11-01)

- **Цель:** исключить наложение ошибок клиентской валидации на футер формы и
  сохранить доступность submit-кнопки при незаполненных обязательных полях.
- **Область изменений:** reusable request popup всех контекстов; desktop,
  пограничная ширина 768 px и mobile.
- **Связанные макеты Paper:** текущий popup «Обсудить заказ» в проекте
  `Ралмастер`; макет не изменялся. Дополнительно использован контрольный снимок
  владельца с фактическим ошибочным состоянием.
- **Затронутые файлы и компоненты:**
  `ralmaster_discuss_order.css`; Playwright suite `estimate-dialogs`; журналы
  итераций и прогресса.
- **Функциональные автотесты:** пустая отправка создаёт две inline-ошибки без
  POST; форма переключается в прокручиваемый error-state; submit остаётся
  достижимым; валидный Telegram submit продолжает закрывать исходный popup и
  открывать success dialog.
- **Визуальные проверки:** при 768×943 px submit целиком находится внутри
  viewport формы после прокрутки, нижнее примечание начинается не раньше
  нижней границы формы; нормальное состояние сохраняет размеры Paper.
- **Критерии приёмки:** ошибки CONTACT/TASK растягивают собственные строки;
  футер не перекрывает поля; кнопка не обрезается и не попадает под примечание;
  мобильная компоновка остаётся последовательной.
- **Фактический результат:** фиксированные высоты Bitrix wrappers и grid rows
  отключаются только при наличии `.rm-estimate-error`. Контент получает
  естественную высоту, а внутренний контейнер — вертикальную прокрутку без
  изменения обычной геометрии popup.
- **Обнаруженные дефекты:** клиентские ошибки не добавляли серверный класс
  `.form.contacts.error`, поэтому существующая fallback-ветка CSS не
  срабатывала; строка TASK выходила за фиксированный track и перекрывала footer.
- **Исправленные дефекты:** все замечания закрыты. Playwright desktop **4/4
  PASS**, mobile **1/1 PASS**; Docker build успешен, `db`/`web` healthy,
  `/product/` отвечает HTTP **200**.
- **Оставшиеся ограничения:** внешняя доставка уведомлений зависит от
  SMTP/sendmail серверного окружения и не относится к этой layout-итерации.
- **Статус:** `PASSED`.

## Итерация 44 — Корзина и оформление заказа в системе РАЛМАСТЕР (2026-11-01)

- **Цель:** привести рабочую Bitrix-корзину и страницу оформления заказа к
  общей визуальной системе редизайна, не меняя существующую механику товаров,
  количества и создания заказа.
- **Область изменений:** `/cart/` и `/cart/order/`; desktop и mobile;
  состояния hover/focus, пустая корзина и заполненная корзина.
- **Связанные макеты Paper:** текущая дизайн-система `Ралмастер` → `Дизайн` и
  приложенные владельцем контрольные снимки старой корзины; Paper не изменялся.
- **Затронутые файлы и компоненты:** системный shell оформления заказа,
  breadcrumbs системных страниц, `ralmaster_system.css`, новый Playwright suite
  `cart-order`; журналы итераций и прогресса.
- **Функциональные автотесты:** добавление реального товара из категории
  `/product/pannier/`; отображение товара и счётчика в корзине; переход к
  checkout; наличие формы и состава заказа; сохранение live Bitrix/Aspro
  компонентов; desktop/mobile overflow gate.
- **Визуальные проверки:** хлебные крошки используют `/`; красные ссылки и
  hover-состояния заменены фирменным синим; print-кнопка имеет тёмный фон и
  белые текст/иконку; поля checkout соответствуют общему контракту 56 px,
  submit — 60 px; desktop использует form/summary grid, mobile — одну колонку.
- **Критерии приёмки:** корзина остаётся функциональной; все CTA читаемы и не
  используют красную тему; форма и order summary не выходят за контейнер;
  mobile не имеет горизонтальной прокрутки; печатный режим не затронут.
- **Фактический результат:** корзина собрана в белую товарную панель с
  фирменным счётчиком, чистыми действиями и новым empty-state. Оформление
  подключено к system shell, форма и состав заказа оформлены отдельными
  карточками. Устранено влияние Bootstrap `.row::before/::after`, которое
  первоначально сдвигало форму в правую колонку CSS Grid.
- **Обнаруженные дефекты:** старые красные CTA/hover и Open Sans; чёрный текст
  print-кнопки; стрелочные breadcrumbs; checkout вне нового shell; Bootstrap
  clearfix pseudo-elements становились grid-items; мобильные legacy floats.
- **Исправленные дефекты:** все замечания закрыты. PHP lint **2/2 PASS**;
  отдельный cart/order suite **3/3 PASS**; связанная desktop/mobile-регрессия
  корзины, derived и system pages **24/24 PASS**; Docker build успешен,
  `db`/`web` healthy.
- **Оставшиеся ограничения:** цена исходных товаров в текущей БД равна нулю,
  поэтому итог `0` является данными каталога, а не ошибкой интерфейса; отправка
  реального заказа не выполнялась, чтобы не создавать тестовую заявку.
- **Статус:** `PASSED`.

## Итерация 45 — Стабильная геометрия ошибок и скролл всего popup (2026-11-01)

- **Цель:** перенести вертикальную прокрутку с внутренней формы на весь request
  popup и исключить скачки полей/submit при появлении клиентских ошибок.
- **Область изменений:** reusable request popup и пять inline estimate forms на
  главной, `/product/`, `/painting/`, `/services/`, `/projects/`; desktop,
  пограничная высота окна и mobile 390 px.
- **Связанные макеты Paper:** текущий popup «Обсудить заказ» и общий контракт
  форм проекта `Ралмастер`; Paper не изменялся. Дополнительно использован
  контрольный снимок владельца с внутренним scrollbar.
- **Затронутые файлы и компоненты:** общий `ralmaster-estimate.css`,
  `ralmaster_discuss_order.css`, удалённый legacy error-state главной в
  `custom.css`, Playwright suites forms-consistency и estimate-dialogs;
  журналы итераций и прогресса.
- **Функциональные автотесты:** пустая отправка создаёт ошибки CONTACT/TASK без
  POST; позиции contact/task/file/consent/submit сравниваются до и после
  валидации; popup и inline-формы проверяются на desktop и mobile.
- **Визуальные проверки:** у CONTACT/TASK/FILE всегда зарезервировано 26 px под
  текст ошибки; popup имеет один scrollbar по внешней границе; внутренний
  `.rm-discuss__form` сохраняет `overflow-y: visible`; горизонтального overflow
  на 390 px нет.
- **Критерии приёмки:** появление/удаление ошибок не меняет геометрию формы;
  submit не проваливается под нижнее примечание; на низком desktop viewport
  прокручивается весь popup; валидная отправка и success dialog не регрессируют.
- **Фактический результат:** ошибки переведены в absolute slot внутри заранее
  зарезервированного padding; удалены динамические `:has(.rm-estimate-error)`,
  которые после submit меняли высоты wrappers. Dialog использует естественную
  высоту содержимого и единственную внешнюю область прокрутки.
- **Обнаруженные дефекты:** прежняя итерация включала `overflow-y: auto` на
  `.rm-discuss__form` только после ошибки; главная отдельно меняла высоты
  секции и grid tracks через `:has`, поэтому кнопка и соседние блоки прыгали.
- **Исправленные дефекты:** все замечания закрыты. Targeted Playwright desktop
  **7/7 PASS**, mobile **2/2 PASS**; Docker image пересобран, `db` и `web`
  healthy.
- **Оставшиеся ограничения:** тексты клиентских ошибок рассчитаны на одну
  строку 18 px; слот 26 px включает обязательный верхний интервал 8 px.
- **Статус:** `PASSED`.

## Итерация 46 — Баланс колонок и вертикальных отступов форм (2026-11-01)

- **Цель:** устранить лишние вертикальные промежутки в расчётных формах,
  вернуть безопасный нижний отступ под submit и выровнять высоту визуальной и
  формовой колонок.
- **Область изменений:** пять inline estimate forms на главной, `/product/`,
  `/painting/`, `/services/`, `/projects/`; desktop и mobile; резервирование
  места под клиентские ошибки.
- **Связанные макеты Paper:** общий контракт двухколоночной формы проекта
  `Ралмастер` и контрольный снимок владельца с отмеченными отступами; Paper не
  изменялся.
- **Затронутые файлы и компоненты:** `ralmaster-estimate.css`,
  `ralmaster_discuss_order.css`, Playwright suite `forms-consistency`;
  журналы итераций и прогресса.
- **Функциональные автотесты:** пустая отправка и неверный тип файла не меняют
  координаты uploader/consent/submit; desktop проверяет точную геометрию всех
  пяти пар колонок; mobile проверяет отсутствие горизонтального overflow.
- **Визуальные проверки:** обе desktop-колонки имеют высоту 738 px; верхний
  отступ формы уменьшен с 48 до 32 px, нижний установлен в 51 px; интервал
  TASK→FILE уменьшен с 42 до 29–34 px, FILE→consent — с 84 до 58 px.
- **Критерии приёмки:** левая и правая части равны по высоте; submit не
  прижат к нижней границе; лишние пустоты между полями отсутствуют; сообщения
  ошибок не вызывают скачков; mobile сохраняет естественную высоту.
- **Фактический результат:** desktop-панели получили единый track 738 px и
  нормализованные строки NAME/CONTACT/TASK/FILE 84/155/166/82 px. Постоянная
  подпись загрузчика используется как резерв ошибки FILE без дополнительного
  пустого слота. На mobile фиксированная высота отключена.
- **Обнаруженные дефекты:** одновременно применялись постоянная подпись FILE и
  отдельный error padding; форма имела избыточный верхний padding, а product
  растягивал правую колонку выше визуальной.
- **Исправленные дефекты:** все замечания закрыты. Playwright desktop **8/8
  PASS**, mobile **2/2 PASS**.
- **Оставшиеся ограничения:** точные 738 px применяются только начиная с
  desktop breakpoint; на mobile высота определяется содержимым, что необходимо
  для доступности и переноса текста.
- **Статус:** `PASSED`.

## Итерация 47 — Финальная геометрия корзины и состава заказа (2026-11-01)

- **Цель:** привести пустое и заполненное состояния корзины к единой системе
  РАЛМАСТЕР, устранить дублирование страницы после AJAX-очистки и дополнить
  состав заказа аккуратными миниатюрами товаров.
- **Область изменений:** `/cart/`, `/cart/order/`, хлебные крошки `/product/`
  и производных карточек; desktop и mobile; live Bitrix basket handlers.
- **Связанные макеты Paper:** системные страницы проекта `Ралмастер` и
  приложенные владельцем контрольные снимки корзины; Paper не изменялся.
- **Затронутые файлы и компоненты:** системный shell корзины, локальные
  переопределения `aspro:basket.allcorp2` для cart/order, общий
  `ralmaster_system.css`, Playwright suites `cart-order` и `derived-pages`;
  журналы итераций и прогресса.
- **Функциональные автотесты:** пустая корзина; добавление реального товара;
  счётчик и удаление; AJAX «Очистить» без повторного hero; переход к checkout;
  миниатюра в составе заказа; checkbox согласия; desktop/mobile overflow;
  slash separators; axe gate системных страниц.
- **Визуальные проверки:** intro расположен под заголовком корзины слева;
  empty-state состоит из одной карточки без пиктограммы; «Очистить» и «Итого»
  выровнены справа; крестик удаления центрирован; миниатюры состава заказа
  имеют единый размер и `object-fit: contain`.
- **Критерии приёмки:** AJAX-ответ корзины содержит только внутренний fragment;
  на странице остаётся ровно один system shell и один hero; подписи Bitrix не
  теряются в локальном template override; checkout остаётся рабочим и
  доступным на mobile.
- **Фактический результат:** добавлен серверный режим `INNER_ONLY` для AJAX
  обновлений и отсечение внешнего shell при POST/XHR. Шаблоны корзины и заказа
  перенесены из игнорируемого `/bitrix/` в отслеживаемые локальные
  переопределения темы вместе с языковыми файлами. Состав заказа получил
  70-px изображения товаров.
- **Обнаруженные дефекты:** legacy JS вставлял полный HTML `/cart/` внутрь
  `.basket.default`, создавая второй hero; первичная локальная копия шаблона
  не имела собственных lang-файлов, поэтому подписи empty-state были пустыми.
- **Исправленные дефекты:** все замечания закрыты. PHP lint локальных шаблонов
  и cart entrypoint **3/3 PASS**; cart desktop **4/4 PASS**, mobile **1/1
  PASS**; breadcrumbs **1/1 PASS**; system accessibility/overflow gate **1/1
  PASS**. Docker build успешен, `db`/`web` healthy; `/cart/`, `/product/` и
  `/product/pannier/` отвечают HTTP **200**.
- **Оставшиеся ограничения:** цены исходных товаров в текущей базе равны нулю,
  поэтому сумма `0` остаётся корректным отображением данных каталога; реальный
  заказ не отправлялся, чтобы не создавать тестовую заявку.
- **Статус:** `PASSED`.

## Итерация 48 — Фирменная кнопка очистки корзины (2026-11-01)

- **Цель:** убрать вид отключённой кнопки «Очистить» и точно центрировать её
  крестик.
- **Область изменений:** action row заполненной корзины на desktop и mobile.
- **Связанные макеты Paper:** цветовая и интерактивная система РАЛМАСТЕР;
  Paper не изменялся. Использован контрольный снимок владельца.
- **Затронутые файлы и компоненты:** `ralmaster_system.css`, Playwright suite
  `cart-order`; журналы итераций и прогресса.
- **Функциональные автотесты:** наличие live clear action; проверка синей
  рамки/текста и белого фона; точные computed styles псевдоэлемента крестика;
  mobile overflow gate.
- **Визуальные проверки:** кнопка имеет фирменный синий outline, белый фон и
  заполненный синий hover; крестик 16×16 px выровнен в общем flex-контейнере.
- **Критерии приёмки:** подпись и крестик читаемы и центрированы; hover не
  использует красный цвет; кнопка не вызывает mobile overflow.
- **Фактический результат:** legacy absolute positioning псевдоэлемента
  сброшен; label и `×` собраны в центрированный flex с gap 10 px.
- **Обнаруженные дефекты:** кнопка выглядела disabled, а крестик наследовал
  смещение Aspro относительно текста.
- **Исправленные дефекты:** targeted desktop **1/1 PASS**, mobile **1/1 PASS**;
  контрольный снимок проверен визуально; Docker build успешен, `db`/`web`
  healthy, `/cart/` отвечает HTTP **200**.
- **Оставшиеся ограничения:** отсутствуют.
- **Статус:** `PASSED`.

## Итерация 49 — Иерархия hero оформления заказа (2026-11-01)

- **Цель:** убрать лишний kicker «Ваш заказ» и расположить описание сразу под
  заголовком оформления заказа.
- **Область изменений:** hero `/cart/order/` на desktop и mobile.
- **Связанные макеты Paper:** системная иерархия РАЛМАСТЕР; Paper не
  изменялся. Использован контрольный снимок владельца.
- **Затронутые файлы и компоненты:** `cart/order/index.php`, общий
  `ralmaster_system.css`, Playwright suite `cart-order`; журналы итераций и
  прогресса.
- **Функциональные автотесты:** отсутствие kicker; положение intro ниже h1 и
  совпадение левых координат; checkout contract; mobile overflow gate.
- **Визуальные проверки:** hero использует одну колонку шириной до 720 px;
  описание находится под h1 с интервалом 18 px.
- **Критерии приёмки:** «Ваш заказ» отсутствует; описание не находится в
  правой колонке; desktop/mobile не имеют горизонтального overflow.
- **Фактический результат:** оформление заказа использует ту же hero-иерархию,
  что и корзина: breadcrumbs, h1, затем вводный текст слева.
- **Обнаруженные дефекты:** checkout сохранял общий двухколоночный system hero
  и отдельный kicker, хотя корзина уже была переведена на новую иерархию.
- **Исправленные дефекты:** PHP lint **1/1 PASS**, desktop checkout **1/1
  PASS**, mobile **1/1 PASS**; контрольный снимок проверен визуально; Docker
  build успешен, `db`/`web` healthy. Прямой checkout без корзины корректно
  перенаправляет с HTTP **302**.
- **Оставшиеся ограничения:** отсутствуют.
- **Статус:** `PASSED`.

## Итерация 50 — Меню 72 px, motion-аудит и live-корзина (2026-11-02)

- **Цель:** выполнить согласованный correction pass: заменить и выровнять
  меню, сделать связку header + главный экран высотой 100svh, унифицировать
  CTA и motion, закрыть приложенный layout-аудит и подтвердить, что корзина
  остаётся активной относительно реального каталога.
- **Область изменений:** глобальный desktop/mobile header; главная; контакты;
  производство; услуги; окраска; продукция; проекты; общий request dialog;
  live-корзина и checkout; адаптив 390–1882 px.
- **Связанные макеты Paper:** актуальный проект `Ралмастер` команды Марии,
  варианты меню «белый фон / 72 px» и «компактное с почтой / 72 px», а также
  приложенные motion/layout-аудиты. Макет Paper не изменялся.
- **Затронутые файлы и компоненты:** `.top.menu.php`, шаблон плоского меню,
  `header_1.php`, общий `custom.css`/`custom.js`, контакты, производство,
  project/product/service templates, общий discuss-order dialog, Playwright
  suites и воспроизводимый `capture-correction-report.mjs`.
- **Функциональные автотесты:** пять прямых пунктов меню и подменю услуг;
  active-state; 72-px shell; live add-to-cart и счётчик; корзина/количество/
  удаление/очистка/checkout; единые формы; popup/Escape/scroll position;
  desktop/mobile route, overflow, browser errors и axe gates.
- **Визуальные проверки:** тёмный overlay-header на hero и белый header на
  светлых страницах; 100svh hero; общие направляющие; подпись CTA слева и
  стрелка справа; restrained image hover; карточная mobile-таблица; формы и
  системные страницы. Фотоотчёт: `J:\ralmaster\redesign-report\2026-11-02`.
- **Критерии приёмки:** без `transition: all` в новом motion-слое; поддержка
  `prefers-reduced-motion`; изображения увеличиваются внутри карточек без
  скачка сетки; меню доступно с клавиатуры; реальный товар из `/product/`
  появляется в header и `/cart/`; критические регрессии отсутствуют.
- **Фактический результат:** header переведён на согласованную структуру из
  пяти пунктов, email/phone/cart и контекстные light/overlay-состояния. Hero
  занимает экран, animation contract применён к reveal, ссылкам, фото и
  диалогам. Корзина использует штатную Bitrix-сессию и live catalog handlers.
- **Обнаруженные дефекты:** Playwright выполнял собственную actionability-
  прокрутку до открытия production popup; element-screenshot повторял
  технический off-canvas search при длинном мобильном кадре.
- **Исправленные дефекты:** тест отделён от служебной прокрутки драйвера, а
  отчёт снимает мобильную таблицу в отдельном чистом контексте. Полная
  redesign-регрессия **137/137 PASS**, smoke **32/32 PASS**; Docker `db`/`web`
  healthy; фотоотчёт содержит 7 снимков «до» и 12 снимков «после».
- **Оставшиеся ограничения:** реальные формы не отправлялись, чтобы не
  создавать тестовые обращения; нулевые цены отражают данные текущей БД.
- **Статус:** `PASSED`.
