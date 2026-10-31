# Прогресс редизайна

Последнее обновление: 2026-10-31.

## Текущее состояние

Полный UI-редизайн по 12 Paper-фреймам реализован и прошёл безопасную
desktop/mobile приёмку: redesign `61/61`, baseline `22/22`, dev smoke `32/32`.
После коррекции header завершена итерация производных страниц: три legacy body
заменены новой системой, targeted `7/7`, связанная regression `29/29`, axe `[]`.
Отдельная Web Form SID `ralmaster_estimate_s1` установлена и проверена в dev и
изолированной production-like БД; legacy Web Form ID 5 не изменялась. Проекты,
контакты и три modal-варианта завершены в UI/safe-functional scope. Реальная
отправка, mail и CRM намеренно не запускались; `SEC-001` остаётся открытым.

| Направление | Статус | Результат |
| --- | --- | --- |
| Git safety | PASSED | Ветка `feature/full-paper-redesign`, текущий базовый HEAD `08f9756`; `origin` указывает на `Slavko13/ralmaster` |
| Docker-конфигурация | PASSED (syntax) | Dev/prod-like Compose и healthcheck созданы, `config --quiet` проходит |
| Docker runtime | PASSED | Docker Desktop 4.97, engine 29.9.2; dev build успешен, `db`/`web` healthy |
| Данные Bitrix | PASSED (local) | Чувствительный backup вне Git импортирован без SQL-ошибок; 422 таблицы |
| Функциональный аудит | PASSED | Карта маршрутов, сценариев, endpoints и рисков составлена |
| Live Playwright baseline | PASSED | Expanded suite: 54/54, 0 failed/skipped/flaky, 2.1 min |
| Local smoke | PASSED | 32/32, 3.0 min; `/healthz.php` и `/` = HTTP 200 |
| Local Playwright baseline | PASSED (safe) | 54/54, 0 failed/skipped/flaky, 5.2 min; browser diagnostics clean |
| Production-like runtime | PASSED | Изолированный project/DB clone; `db`/`web` healthy на 8085; после force-recreate системный Chromium smoke 32/32 за 2.6 min |
| State-changing scenarios | TODO | Forms/cart/auth проверять только локально после изоляции mail/CRM; реальные POST не выполнялись |
| Paper read-only audit | PASSED | Детально прочитаны 12 frames: trees, node info, computed styles, JSX, screenshots и assets |
| Paper identity | PASSED | `Мария's Team` → `Ралмастер` → `Дизайн` (`01M5F74...`, `p-2-0`) |
| Paper references | PASSED | 12 полноразмерных WebP-эталонов сохранены в `docs/redesign/references/paper/` |
| Foundations/shell | PASSED | Fonts/tokens/primitives, header 106 px, footer 430 px и mobile navigation внедрены; targeted tests проходят |
| Главная страница | PASSED | 10 Paper-секций; desktop 1440×8645; после переключения на SID form desktop 6/6, mobile/capture diagnostics clean, axe = 0 |
| Продукция | PASSED | Dynamic IBlock 41/SEF; desktop 8/8 + mobile 1/1; exact 1440×3240; form context `product` |
| Металлообработка | PASSED | Dynamic IBlock 40; desktop 5/5 + mobile 1/1; exact 1440×5741; context `metalworking` |
| Порошковая окраска | PASSED | Оба маршрута 200; desktop 7/7 + mobile 1/1; exact 1440×6396; form 748×738 unclipped |
| Производство | PASSED | Dynamic partners IBlock 20; desktop 8/8 + mobile 1/1; exact 1440×7203; `/company/partners/` сохранён |
| Производные страницы | PASSED | `/product/kapelnik/`, `/services/laser-cutting/`, `/company/partners/`; reusable detail + dynamic partners, regression 29/29, axe `[]` |
| Проекты | PASSED | Listing 1440×4405, общий detail 1440×4833, ЖК «Символ» 1440×3184; IBlock 36/SEF и честные empty/demo states сохранены |
| Контакты | PASSED (safe UI) | 1440×4120; CMS phone/email/addresses, map и dialog 748×850; реальный submit не выполнялся, `SEC-001` открыт |
| Modal variants | PASSED (safe UI) | Discuss/bending 748×850, coating 748×898; mobile natural-flow без overlap; keyboard, validation, AJAX redecorate и loading проверены без POST |
| Estimate Web Form foundation | PASSED (schema/UI); NOT RUN (state-changing) | Dedicated SID установлен/verified в dev и production-like; server/client validation готова; реальный submit/mail/CRM не проверен |
| Финальный redesign regression | PASSED | Post-`UI-003`, dev 8084, desktop+mobile **61/61**, 6.4 min, exit 0 |
| Обновлённый baseline regression | PASSED | Desktop+mobile **22/22**, 2.7 min; три legacy ожидания актуализированы без ослабления проверки |

## Выполнено

- Проанализирован PHP/Bitrix/Aspro стек и production-параметры.
- Найдены основные и динамические маршруты, формы, AJAX и внешние зависимости.
- Создана безопасная локальная ветка и baseline commit.
- Добавлена минимальная Docker Compose архитектура с dev/prod-like режимами.
- Подготовлены `.env.example`, Docker-only DB overrides и healthcheck.
- Docker Desktop восстановлен; dev image собран, MySQL/web healthy.
- Production-like project изолирован от dev, отдельная БД склонирована без
  SQL-файла на хосте; `db`/`web` healthy на 8085.
- Чувствительный Beget backup оставлен вне Git и импортирован в локальную схему `bitrix` без SQL-ошибок.
- Локальные `/healthz.php` и `/` отвечают HTTP 200; после импорта нет 5xx/fatal.
- Создан Playwright/axe регрессионный контур для desktop и mobile.
- Финальный expanded live baseline прошёл: 54/54 за 2.1 min, без failed/skipped/flaky.
- Local smoke прошёл 32/32 за 3.0 min; полный safe local baseline — 54/54 за 5.2 min.
- Production-like browser diagnostics прошёл 4/4; после финального
  force-recreate корректный системный Chromium smoke — 32/32 за 2.6 min.
- Browser diagnostics на четырёх ключевых маршрутах не обнаружил `console.error` или `pageerror`; ignored errors также отсутствуют.
- Зафиксированы serious accessibility-проблемы существующего сайта при отсутствии critical нарушений.
- Повторно подтверждён актуальный файл Paper `Ралмастер` в `Мария's Team`.
- Для всех 12 Paper-фреймов прочитаны размеры, деревья, JSX, computed styles,
  screenshots и доступные оригинальные ассеты.
- Сохранены 12 полноразмерных Paper WebP-эталонов отдельно от legacy baseline.
- Создана карта Paper → маршруты и последовательный план итераций.
- Подключены локальные variable fonts Onest, Inter Tight и Manrope с
  воспроизводимым sync-скриптом и лицензиями.
- Реализованы Paper header/footer, обновлена навигация, сохранены Bitrix
  menu/phone/mobile/search/cart/form hooks и доступные focus states.
- Исправлен исходный дефект полноширинных CTA, вызванный отсутствием
  `.banner_btns` в legacy minified CSS.
- Проведён shell visual QA на 1440 и 390 px: horizontal overflow отсутствует;
  desktop header/footer имеют целевые 106/430 px.
- Реализована новая главная из 10 корневых Paper-секций с динамическими
  разделами продукции и услуг, проектами и партнёрами из Bitrix.
- Сохранены существующие SEF-ссылки и form flow; legacy host в runtime URL
  нормализуется, Paper assets сохранены локально без runtime-hotlink.
- Desktop capture имеет точную общую высоту 8645 px и целевые границы всех
  секций; на 390 px horizontal overflow отсутствует, console/page errors = 0.
- Home tests прошли 7/7, затронутый mobile suite — 2/2; итоговый axe scan
  новой главной вернул пустой список нарушений.
- После установки отдельной Web Form главная переведена на SID
  `ralmaster_estimate_s1`; повторный desktop suite прошёл 6/6, capture снова
  подтвердил 1440×8645, mobile diagnostics и overflow чисты.
- `/product/` реализован на существующем `bitrix:news`/IBlock 41: 8/8 desktop,
  1/1 mobile, exact 1440×3240 и context `product`.
- `/services/` сохраняет динамические разделы IBlock 40: 5/5 desktop, 1/1
  mobile, exact 1440×5741 и context `metalworking`.
- `/painting/` и `/painting/poroshkovaya-pokraska-metalla/` используют общий
  page block и отвечают 200: 7/7 desktop, 1/1 mobile, exact 1440×6396; форма
  748×738 измерена без clipping.
- `/company/` реализован с динамическими партнёрами IBlock 20: 8/8 desktop,
  1/1 mobile, exact 1440×7203; `/company/partners/` остался доступен.
- Создана additive миграция SID `ralmaster_estimate_s1`, выполненная после
  полного локального snapshot. `--verify` пройден в dev и production-like;
  legacy form ID 5 и production infrastructure не изменялись.
- Playwright default `BASE_URL` приведён к адресу development Compose
  `http://127.0.0.1:8084`, чтобы тесты не попадали на посторонний порт 8080.
- Исправлены legacy mobile hero, лишняя header-высота, двойные containers,
  FAQ behavior, форма/consent layout, HTML entities и race Aspro options.
- Read-only аудит уточнил, что Paper-фреймы «Расчёт гибки металла» и
  «Расчёт порошковой окраски» являются формами запроса ручного расчёта, а не
  калькуляторами: формул, числовых входов и автоматического результата нет.
- Реализованы `/projects/`, общий project detail и ЖК «Символ» на IBlock 36/SEF;
  desktop geometry — 1440×4405, 1440×4833 и 1440×3184.
- Реализована `/contacts/` в geometry 1440×4120; карта и контакты используют
  существующие источники, а несуществующие CMS-реквизиты не выдаются за данные.
- Реализованы три нативных modal-варианта: discuss/bending 748×850 и coating
  748×898. Исправлены повторная AJAX-декорация, success/error/loading layout,
  keyboard CTA и infinite MutationObserver loop.
- Home inline form приведена к Paper 748×548 без дублирующих labels, native file
  control и clipped submit; повторная декорация после AJAX сохраняется.
- Общий form script назначает доступные имена всем скрытым Bitrix controls;
  повторный Services gate прошёл 5/5, а полный redesign regression — 61/61.
- Полный обновлённый baseline regression прошёл 22/22, dev route smoke — 32/32.
- Финальный capture выявил и позволил исправить mobile powder overlap:
  двухстрочные title/help/consent теперь используют natural-flow heights;
  новый попарный layout assertion прошёл 1/1 за 11.7 s.
- Production-like dialogs после исправления прошли 3/3 за 22.1 s без POST.
- Финальный visual capture пересоздан для 20 viewport/page runs: diagnostics
  пусты, horizontal overflow отсутствует; отдельный
  `powder-mobile-390-dialog-bottom.png` подтверждает неперекрывающийся низ формы.
- Legacy-body на `Капельник`, `Лазерная резка металла` и `Партнёры` заменены
  новой Paper-системой. Данные остаются CMS-driven; 7 партнёров читаются из
  IBlock 20, section name/description/picture — из соответствующих IBlock.
- Общий product/service detail сохраняет bending dialog 748×850 и безопасный
  no-POST flow; targeted suite 7/7, связанная regression 29/29, полный axe для
  трёх новых страниц не содержит нарушений.

## Hotfix desktop navigation — 2026-10-29

- Исправлено схлопывание wide-menu «Продукция»: возвращён `.menu-row` context;
  панель 1008×178.84 содержит 7 разделов и 7 изображений.
- Удалён 18 px hover-gap между top-link и dropdown «Металлообработка»;
  `td/wrap` занимают высоту header, dropdown начинается на `top: 100%`.
- Добавлены keyboard `focus-within` и scoped transparent background, поэтому
  overlay-header сохраняет все шесть белых ссылок видимыми.
- `header-menu.spec.ts`: **2/2 PASS**; desktop `home.spec.ts`: **6/6 PASS**.
  Browser audit: 25-шаговый pointer path сохраняет dropdown, клик на «Гибка
  металла» открывает `/services/metal-bending/` с HTTP 200, diagnostics `[]`.
- Артефакты: `test-results/menu-audit-final/products.png`, `services.png`.

## Hotfix legacy sticky header — 2026-10-29

| Проверка | Результат |
| --- | --- |
| `tests/e2e/redesign/header-menu.spec.ts`, desktop Chromium | **4/4 PASS** |
| `tests/e2e/baseline/mobile.spec.ts`, mobile Chromium | **2/2 PASS** |
| `/` и powder detail после scroll | **PASS** — `#headerfixed` отсутствует |
| PHP lint | **PASS** — 2 изменённых шаблона |
| Browser diagnostics | **PASS** — console/page errors `[]` |

Legacy `header_fixed` теперь не рендерится только при активном новом
`rm-header`; другие варианты Aspro и отдельный `#mobileheader` сохранены.
Скриншот: `test-results/sticky-audit-painting-after-fix-scroll900.png`.

## Подготовка staging deployment — 2026-10-29

- Добавлены `deploy/ralmaster-vps-deploy.sh` и подробная инструкция.
- Целевая схема: `test.ral-master.ru` → host Caddy → `127.0.0.1:8081` →
  отдельный Compose project `ralmaster-test`.
- SQL импортируется только в пустую схему; Docker volumes не удаляются;
  Caddyfile сохраняется и откатывается при validation/reload error.
- Учтено, что `bitrix/` и `upload/` отсутствуют в Git: первый deploy требует
  отдельный файловый архив Beget наряду с DB backup.
- Локальная проверка: Bash syntax/help PASS, `*.sh eol=lf`, diff check clean.
- Удалённый VPS не изменялся; фактический deploy/DNS/HTTPS — `NOT RUN`.

## Следующий допустимый шаг

### Коррекция header — 2026-10-31

- Подтверждён актуальный источник: `Мария's Team` → `Ралмастер` → `Дизайн`.
- Dropdown/mega-menu удалены из desktop и mobile на уровне Bitrix template data (`MAX_LEVEL=1`, `USE_EXT=N`).
- Общая навигация содержит ровно шесть прямых ссылок Paper; mobile burger имеет корректные ARIA-состояния.
- PHP lint: 4/4 PASS; targeted desktop: 4/4 PASS; targeted mobile: 3/3 PASS; home/navigation regression: 14/14 PASS.
- Visual shell: desktop 1440×106, mobile 390×72, horizontal overflow отсутствует.

1. Выполнить аудит остальных публичных маршрутов на старые Aspro-body и
   составить точный список оставшихся производных страниц без Paper-фреймов.
2. После route audit запустить полную functional/visual regression и Lighthouse
   на desktop/mobile; результаты фиксировать без искусственного завышения.
3. Для state-changing gate сначала изолировать mail/CRM; отдельно закрыть
   `SEC-001` с hardcoded map key.

## Правило обновления

После каждой инженерной итерации здесь фиксируются новый статус, проверяемый результат, ссылки на тестовые/визуальные отчёты и оставшиеся блокеры. Подробные критерии и дефекты находятся в `ITERATIONS.md`.
