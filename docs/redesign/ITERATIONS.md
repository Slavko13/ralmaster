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
