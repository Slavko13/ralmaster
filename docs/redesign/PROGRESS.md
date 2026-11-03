# Прогресс редизайна

Последнее обновление: 2026-10-31.

## Текущее состояние

Полный UI-редизайн по 12 Paper-фреймам реализован и прошёл безопасную
desktop/mobile приёмку: redesign `61/61`, baseline `22/22`, dev smoke `32/32`.
После коррекции header завершены product/service/partners, семь
корпоративных/информационных групп и системные страницы. Search/map/cart/login/
404 переведены на общую систему с сохранением Bitrix-компонентов; три legacy-
дубля дают постоянные канонические редиректы. Последняя связанная desktop/mobile
регрессия — `35/35`, route smoke — `32/32`, полный axe scope — `[]`.
Отдельная Web Form SID `ralmaster_estimate_s1` установлена и проверена в dev и
изолированной production-like БД; legacy Web Form ID 5 не изменялась. Проекты,
контакты и три modal-варианта завершены в UI/safe-functional scope. Реальная
отправка, mail и CRM намеренно не запускались; `SEC-001` остаётся открытым.

| Направление | Статус | Результат |
| --- | --- | --- |
| Git safety | PASSED | Ветка `feature/full-paper-redesign`, последний опубликованный HEAD перед итерацией 18 — `94ef393`; `origin` указывает на `Slavko13/ralmaster` |
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
| Корпоративные / info pages | PASSED | 7 list groups + dynamic detail/section; targeted 15/15, visual 1440/390, full axe representative scope `[]` |
| System pages | PASSED (safe UI) | Search/map/cart/login/404 используют общий shell; targeted 8/8, related 35/35, axe `[]`; три legacy-дубля отвечают 301 |
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
- Создан общий `ralmaster-content` list/detail pattern для IBlock
  14/15/16/17/34/35/37. Отзывы, сотрудники, вакансии, новости, статьи, FAQ и
  акции больше не используют старый body; root/section/detail routes сохранены.
- Составлен фактический `ROUTE_UI_AUDIT.md`: cabinet routes подтверждены как
  отключённые редиректы на `/`, а search/map/login/cart/404 и legacy-дубликаты
  выделены в следующую системную итерацию.
- Targeted content suite прошёл 15/15 за 2.3 min. Desktop/mobile captures news,
  FAQ и article detail не содержат browser errors или overflow; полный axe
  репрезентативного scope вернул `[]`.
- Связанная regression content/derived/header прошла 27/27 за 3.5 min; полный
  локальный route smoke после изменений — 32/32 за 2.7 min.
- Реализован общий системный shell для search/map/cart/login/404 без замены
  динамических Bitrix-компонентов и без state-changing auth/cart действий.
- `/products/`, `/news/`, `/landings/` теперь дают 301 на `/product/`,
  `/info/news/`, `/info/articles/`; старые IBlock-данные не удалялись.
- System pages suite прошёл 8/8; связанная desktop/mobile regression — 35/35
  за 3.8 min; полный route smoke — 32/32 за 2.1 min. Axe representative scope
  пуст, captures 1440/390 не имеют horizontal overflow.
- По запросу владельца Lighthouse-оптимизация приостановлена, приоритет
  переключён на визуальные дефекты powder/metalworking.
- Powder page выведена из legacy maxwidth-wrapper; при 1440 px сохранены
  Paper-сетки 1312 px, а на промежуточной ширине 1288 px секции, серый фон и
  inline-форма полностью помещаются в viewport с боковыми отступами.
- На `/services/` восстановлены видимые labels формы; все CTA расчёта теперь
  открывают modal `Рассчитать металлообработку` без anchor-scroll.
- Телефонные и email-пиктограммы удалены из нового интерфейса, CSS-стрелка CTA
  заменена на корректную диагональную, control «наверх» приведён к синему
  `#175bef`.
- Проверки hotfix: powder desktop **8/8**, services desktop **7/7**, mobile
  powder/services **2/2**; PHP lint PASS, browser overflow на 1288 px отсутствует.
- Исправлена доступность общей request-form на коротких desktop viewport:
  dialog ограничивается `100dvh`, прокручивается внутри и сохраняет доступ к
  consent/submit при 1280×720 и 1824×794.
- CTA-стрелки home/hero/metalworking/powder/footer и inline submit приведены к
  единому знаку `↗` без SVG/background и деформируемых CSS-primitives.
- Проверки viewport/стрелок: desktop targeted regression **30/30**, mobile
  dialog regression **2/2**, PHP lint **2/2**, визуальные captures подтверждают
  доступный submit и одинаковую диагональную стрелку.
- На `/product/` верхняя CTA больше не прокручивает страницу к inline-форме:
  она открывает reusable dialog `product`, а полная Paper-форма ниже сохранена.
- Стрелки двух синих product CTA унифицированы (`↗`, Arial 22 px); desktop
  группа «Изготовление металлических изделий / Вся продукция» возвращена к
  однострочной композиции Paper.
- Логотипы «С нами сотрудничают» на главной и `/company/` теперь сначала ведут
  на `/company/partners/`; внешние ссылки остаются только в карточках партнёров.
- Для product/service/powder-карточек добавлен restrained hover: подъём 2 px и
  тень `0 8px 22px rgba(17,30,53,.08)` с reduced-motion fallback.
- Проверки итерации: desktop related regression **29/29** после уточнения
  legacy test selector, product desktop **8/8**, mobile related **4/4** и
  отдельный product popup **1/1**; PHP lint **4/4**.

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

## Коррекция services/projects по повторной сверке Paper — 2026-11-01

- Paper MCP повторно подтвердил файл `Ралмастер`, страницу `Дизайн` и точные
  секции `Металлообработка`/`Реализованные проекты`; макет не изменялся.
- На `/services/` секция нескольких операций возвращена к вертикальной
  композиции Paper, а embedded-форма получила natural-height строки: labels,
  inputs и textarea не пересекаются на промежуточной ширине 1288 px.
- На `/projects/` все три проекта используют единый порядок «текст слева —
  изображение справа»; восстановлены заголовки Paper, описание, год `2025` и
  подпись `По индивидуальному проекту`.
- Product cards на `/projects/` перестроены в точный порядок Paper
  «изображение 316 px — контент 164 px», поэтому однострочный заголовок больше
  не смещает картинку.
- Нижний блок проектов восстановлен с тремя производственными фотографиями,
  заголовками поверх изображений и двумя связанными CTA.
- Проверки: PHP lint **4/4 PASS**; desktop services/projects **18/18 PASS**,
  затем intermediate breakpoint **2/2 PASS**; mobile services/projects **4/4 PASS**; geometry assertions
  проверяют отсутствие overlap и совпадение вертикали трёх product images.

## Коррекция company/services/powder по повторной сверке Paper — 2026-11-01

- Мобильная таблица `/company/` переработана в пять самостоятельных карточек:
  фиксированная desktop-высота строк больше не наследуется, labels и значения
  идут последовательно, а параметры получают отдельную светло-синюю поверхность.
- Секция «Примеры выполненных работ» на `/services/` приведена к точному узлу
  Paper 1440×888: padding 72 px, heading gap 32 px, cards 421×656, body 390 px,
  первая фотография заменена на утверждённую, demo-подпись возвращена в каждую
  карточку, action-arrow оформлена контролом 40×40.
- В секции подготовки поверхности устранён скрытый legacy `margin-bottom: 12px`
  у элементов `ol li`: четыре шага теперь укладываются в 481 px, нижняя граница
  совпадает с изображением и больше не пересекает текст «Сушка».
- Проверки: PHP lint **2/2 PASS**; desktop company/services/painting **23/23
  PASS**; mobile targeted **3/3 PASS**; intermediate 768/1024/1288 px вошли в
  desktop suite; horizontal overflow и browser diagnostics отсутствуют.

## Единый hover карточек окраски и продукции проектов — 2026-11-01

- Блок «Какие изделия принимаем на окраску» повторно сверен с Paper и закреплён
  geometry-тестом: секция 1440×878, grid 1312×401, карточки 421×401,
  изображения 421×254 и content 421×147.
- Три карточки окраски получили общий hover сайта: подъём 2 px и тень
  `0 8px 22px rgba(17,30,53,.08)` без изменения размеров и положения соседей.
- В product-блоке `/projects/` удалено отличавшееся увеличение только картинки;
  теперь hover/focus применяется ко всей карточке тем же эффектом. Добавлен
  `prefers-reduced-motion` fallback для обеих групп.
- Проверки: desktop painting/projects **22/22 PASS**; mobile targeted **4/4
  PASS**; axe, intermediate breakpoint, browser diagnostics и overflow входят
  в пройденный набор.

## Карточки окраски и возврат товарной корзины — 2026-11-01

- В accepted-products порошковой окраски заголовок каждой карточки перенесён
  над изображением, а описание оставлено отдельным нижним блоком; общая
  геометрия карточки 421×401 px и общий hover сохранены.
- Подтверждена причина потери старой корзины: custom product section делал
  ранний `return` и не выводил динамический результат `catalog_table_2`.
- На `/product/pannier/` в новый интерфейс возвращены три реальные позиции
  IBlock, статусы наличия, ссылки на detail, счётчик количества и штатный
  add-to-cart. `/cart/` остаётся существующей динамической Bitrix-корзиной.
- Legacy desktop `display:none`/`opacity:0` устранён локально: `− 1 +` и синяя
  кнопка «В корзину» видны постоянно, а не только при наведении; mobile
  использует компактную полноширинную кнопку.
- Проверки: PHP lint **3/3 PASS**; связанная regression painting/derived pages
  **20/20 PASS**; повторный desktop/mobile product flow и geometry gate
  **3/3 PASS**; horizontal overflow и browser errors отсутствуют.
- Visual QA: `test-results/powder-accepted-title-image-description.png`,
  `pannier-catalog-restored-desktop.png`,
  `pannier-catalog-restored-mobile.png`, `pannier-card-desktop-final.png`.

## Полный товарный flow и popup CTA покраски — 2026-11-01

- По production reference `/product/pannier/` и `/product/bracket/`
  восстановлены семь категорий слева, smart filter, сортировка и три режима
  представления, при этом визуальная оболочка остаётся в новой дизайн-системе.
- Верхний product detail-hero и CTA расчёта больше не подменяют category page;
  раздел выводит штатные динамические товары Bitrix и существующую корзину.
- Характеристики на hover отключены; изображение товара увеличено, центрировано
  и остаётся видимым при hover/focus. Счётчик `− 1 +` и «В корзину» доступны на
  desktop и mobile, add-to-cart подтверждён реальным POST и товаром в `/cart/`.
- На powder page accepted images заполняют весь блок без белых боковых полей;
  CTA габаритов, примеров и производственного заказа открывают общий popup без
  прокрутки к inline-форме.
- Добавлены regression assertions на legacy navigation/controls, скрытые
  характеристики, геометрию product image, popup/scroll behavior и реальный
  basket flow. PHP lint **3/3 PASS**; финальный связанный Playwright suite
  desktop/mobile **22/22 PASS** за 2.4 min; targeted hover/image gate **1/1
  PASS**. Horizontal overflow, browser errors и critical axe violations в
  проверенном scope отсутствуют.

## Каталог и производительность — 2026-11-01

- Smart filter полностью переведён с красного legacy accent на синий
  `#175bef`; CTA одинаковой ширины, range handles синие, mobile view controls
  центрированы. У карточек и buy block удалены остаточные тени.
- Development Bitrix cache/log I/O перенесён в named Docker volumes при
  сохранённом bind-mounted hot reload; OPcache revalidation — 2 секунды.
- Для ключевых страниц добавлены 33 responsive WebP 768 px и корректные
  `srcset`/`sizes`. Изображения главной сократились 1.78→0.58 MB, покраски
  1.52→0.30 MB, услуг 1.04→0.45 MB.
- Lighthouse local simulated: desktop Performance **94–99**, mobile **81–93**;
  service LCP correction подняла mobile score 79→86. Все пять desktop routes
  имеют Accessibility 96–100 и Best Practices 100.
- Docker `db`/`web` healthy; Compose config и PHP lint **12/12 PASS**; восемь
  ключевых routes имеют **0** failed/4xx/5xx resources. Полный Playwright run:
  **111/113 PASS**, два несовместимых с `srcset` fixed-dimension assertions
  адаптированы и повторно прошли **2/2**.
- Отчёты: `artifacts/lighthouse/iteration-27-after/`,
  `artifacts/lighthouse/iteration-27-metalworking-lcp/`; visual QA:
  `artifacts/ui-audit/`.
- До staging deployment внешний сервер продолжит отдавать прежние тяжёлые PNG;
  после pull/redeploy будут доступны текущие WebP и шаблоны. Следующий отдельный
  performance step — безопасное разделение общего legacy Aspro CSS.

## Единая плитка каталога — 2026-11-01

- Удалены переключатели table/list/price; сервер всегда выбирает плиточный
  template, включая запросы со старым `?display=list` и прежними cookie.
- Из карточек категорий убран ошибочный list price `1`; количество осталось
  только внутри штатного `− 1 +`. Удалён искусственный нижний `min-height`,
  сокращены внутренние интервалы однотоварной карточки.
- Apply/Reset снова компактны и стоят в одну строку. Reset — белый с синим
  текстом; checked checkbox и popup «Выбрано» переведены с красного на
  `#175bef`.
- PHP lint **5/5 PASS**; targeted Playwright desktop/mobile **3/3 PASS**;
  визуально проверены обычное и активное состояния `/product/bracket/`.

## Последний ряд каталога — 2026-11-01

- Удалён legacy `margin-bottom: 32px` у `col-*` wrappers карточек. Grid gap
  между рядами сохранён, под последним рядом дополнительной полосы больше нет.
- Геометрия подтверждена на категориях с 1 и 3 товарами: нижняя граница
  последней карточной строки совпадает с границей grid, разница **0 px**.
- Targeted Playwright regression: **1/1 PASS**.

## Точечная полировка production/projects — 2026-11-01

- В «Повторных партиях» заменены дефектные repeat/pencil paths; все иконки
  остаются в 24×24 viewBox и больше не обрезаются поздним legacy SVG-правилом.
- CTA «Обсудить заказ или регулярные поставки» центрирован на экранах шире
  1440 px без изменения эталонной геометрии 1440 px.
- На `/projects/` между полем «Описание задачи» и загрузчиком добавлен
  фактический зазор 16 px вместо прежних 0 px.
- PHP lint **1/1 PASS**; targeted Playwright **3/3 PASS**; полный
  production/projects desktop regression **24/24 PASS**. Проверки включают
  точную геометрию Paper, axe, overflow и browser diagnostics.
- Visual QA: `artifacts/ui-audit/repeat-icons-fixed.png`,
  `production-contact-centered.png`, `projects-form-gap.png`.

## Заголовки, карточки, divider и filter hover — 2026-11-01

- Heading «Компании, с которыми мы работаем» перенесён из второй grid-колонки
  под kicker и теперь начинается с общей левой границы секции.
- В product-блоке `/projects/` карточки собраны в порядке заголовок → изображение
  → описание. На desktop/intermediate/mobile текст полностью помещается; все
  изображения и описания сохраняют общую вертикаль.
- На `/company/` убран унаследованный `margin-bottom: 12px` у order steps:
  третья строка теперь заканчивается ровно на нижнем divider, без пересечения.
- Hover заголовка «Статус» в smart filter закреплён синим `#175bef` вместо
  legacy-красного. Исходные repeat/pencil/calendar SVG возвращены и увеличены
  до 28×28 в контейнере 32 px.
- PHP lint **2/2 PASS**; targeted **5/5 PASS**; desktop **35/35 PASS**; mobile
  **7/7 PASS**; финальный intermediate gate **2/2 PASS**.
- Visual QA: `artifacts/ui-audit/iteration-31-*.png`.

## Изображения product-карточек Projects без полей — 2026-11-01

- На `/projects/` три фотографии product-карточек переведены с `contain` на
  центрированный `cover`: image row 421×280 px заполняется полностью.
- Геометрия заголовков, описаний и высота секции сохранены.
- Targeted Playwright **3/3 PASS**; полный Projects desktop regression
  **14/14 PASS**; mobile regression **3/3 PASS**.
- Visual QA: `artifacts/ui-audit/iteration-32-project-products-cover.png`.

## Popup обсуждения заказа и mobile upload — 2026-11-01

- Paper MCP подтверждён: файл `Ралмастер`, страница `Дизайн`, artboard
  «РАЛМАСТЕР — Обсудить заказ» (`3U0-0`) доступен для чтения структуры,
  стилей и размеров; макет не изменялся.
- В popup добавлен выбор Telegram / MAX / почта / телефон с доступным
  radiogroup, отдельными placeholder и проверкой формата. Выбранный канал
  сохраняется вместе с контекстом заявки.
- FILE остаётся необязательным. Исправлены полная focus-рамка имени,
  пересечение mobile uploader на `/painting/` и порядок FILE → согласие → CTA
  на `/services/`.
- Устранено зависание на «Отправляем…»: popup использует контролируемый
  асинхронный POST с timeout/error/success states, а validator привязан только
  к текущему экземпляру Bitrix-формы.
- Реальный POST без файла создал локальную запись Bitrix (последний
  проверенный result ID 29). PHP lint
  **2/2 PASS**; связанный Playwright desktop **20/20 PASS**, mobile **3/3
  PASS**; Docker `db`/`web` healthy, главная отвечает HTTP 200.
- Отдельное ограничение: локальный mail event создаётся со
  `SUCCESS_EXEC=F`, потому что SMTP/sendmail в Docker не настроен. Сама заявка
  сохранена; для реальной почтовой доставки нужны серверные SMTP-реквизиты.

## Формы, партнёры и production layout — 2026-11-01

- На главной inline-ошибки больше не прячутся за следующими полями: при
  валидации grid rows и Bitrix wrappers автоматически увеличивают высоту.
- Hero партнёров собран в общей стилистике внутренних страниц: заголовок,
  затем описание; kicker удалён, breadcrumb использует `/`. На этой странице
  пункт «О производстве» больше не остаётся активным.
- В popup порядок каналов теперь Telegram / MAX / телефон / почта. Telegram и
  MAX используют фирменные SVG из footer; двойная focus-рамка заменена одной
  синей рамкой.
- Hover карточек усилен до `scale(1.1)` с тенью. У ссылок «Подробнее…» текст и
  стрелка поднимаются вместе одним элементом.
- Paper MCP повторно подтвердил размеры production table: 1344 px и поля по
  48 px на artboard 1440 px. Таблица и соседние блоки переведены на адаптивную
  ширину и не выходят за основную сетку на intermediate/mobile viewport.
- Финальная проверка: PHP lint **2/2 PASS**; Playwright desktop **37/37 PASS**,
  mobile **5/5 PASS**; Docker `db`/`web` healthy; три ключевых URL отвечают
  HTTP **200**. Paper не изменялся.

## Контакты, production CTA и фирменный hover — 2026-11-01

- Telegram/MAX уменьшены до 14 px, чтобы залитые логотипы визуально
  соответствовали 16 px outline-иконкам телефона и почты.
- На главной `ralmaster@inbox.ru` теперь находится под номером телефона и
  выровнен с ним по левой границе.
- Powder accepted card продолжает увеличиваться на 10%, но масштабируется от
  верхней границы и больше не поднимается к заголовку секции.
- «Отправить чертёж на проверку» на `/company/` заменена с anchor jump на
  общий production popup; положение страницы при открытии не меняется.
- Связанные кнопки изготовления и порошковой окраски больше не становятся
  чёрными: services links остаются синими, projects buttons используют синий
  фон и белый текст.
- Проверки: PHP lint **1/1 PASS**; targeted Playwright **7/7 PASS**; полный
  связанный desktop suite **54/54 PASS**; mobile **8/8 PASS**.

## Маршруты главной и новый success popup — 2026-11-01

- Hero-карточка фасадных металлокассет теперь открывает выбранную категорию
  `/product/facadecassettes/`, а все три карточки металлообработки главной
  ведут на общий раздел `/services/`.
- У этапов 01/02/03 в блоке «Несколько операций в одном заказе» установлены
  одинаковые стрелки 26 px, включая ранее пустой третий этап.
- Нижние формы главной и `/services/` получили тот же доступный selector
  Telegram / MAX / телефон / почта, что и request popup; выбранный канал
  сохраняется в CONTEXT.
- Legacy success-блок больше не заменяет форму. После успешного POST поверх
  страницы появляется отдельный popup «Ваша заявка принята» с именем клиента
  и кнопкой «ОК»; исходная форма остаётся видимой и работоспособной.
- Проверки: PHP lint **3/3 PASS**, Node syntax **1/1 PASS**; targeted suite с
  двумя реальными POST **6/6 PASS**; desktop regression **18/18 PASS**;
  mobile **3/3 PASS**.

## FAQ и очистка успешно отправленных форм — 2026-11-01

- На `/company/` жёсткие высоты FAQ заменены минимальными: закрытый блок
  сохраняет композицию Paper, а при раскрытии ответа секция растёт по
  содержимому. Последний пункт больше не выходит за тёмный фон и рамку.
- После успешного POST текущая форма полностью очищается, снимаются ошибки и
  согласие, selector способа связи возвращается к Telegram.
- Для формы, открытой в request popup, исходный popup теперь закрывается до
  появления уведомления «Ваша заявка принята». После «ОК» он не открывается
  повторно. Inline-формы остаются видимыми и готовы к новой заявке.
- Проверки: Node syntax **1/1 PASS**; targeted Playwright **4/4 PASS**;
  полная связанная desktop regression **24/24 PASS**; Docker build успешен,
  `db`/`web` healthy, `/` и `/company/` отвечают HTTP **200**.

## Партнёры и Telegram/MAX submit — 2026-11-01

- Удалён подзаголовок «Нам доверяют» над заголовком «Компании, с которыми мы
  работаем»; заголовок и сетка карточек сохранили исходное выравнивание.
- Устранено расхождение клиентской и серверной валидации: Telegram и MAX
  принимают username/номер от трёх символов, телефон и email проверяются по
  собственным правилам.
- Точный сценарий со значением `3214`, без имени и вложения, теперь закрывает
  request popup, очищает форму и показывает «Ваша заявка принята».
- Проверки: PHP lint **1/1 PASS**; связанные Playwright suites **13/13 PASS**;
  Docker build успешен, оба контейнера healthy, `/painting/` и страница
  партнёров отвечают HTTP **200**.

## Единый вид inline-форм — 2026-11-01

- В `/product/`, `/painting/` и `/projects/` добавлен общий selector Telegram /
  MAX / телефон / почта; выбранный канал сохраняется в CONTEXT формы.
- Checkbox согласия во всех estimate forms теперь использует фирменный синий
  цвет и одинаковую белую отметку вместо красной темы шаблона.
- Проектная форма переведена на grid с интервалами 16 px и увеличена до
  полноценной панели 738 px; submit и загрузчик больше не обрезаются. Product
  и projects секции имеют достаточные 850 px, на мобильных высота остаётся
  автоматической.
- Проверки: PHP lint **3/3 PASS**; targeted Playwright **10/10 PASS**; полная
  desktop-регрессия трёх страниц **32/32 PASS**; mobile **5/5 PASS**; Docker
  build успешен, `db`/`web` healthy.

## Единый визуальный контракт всех форм — 2026-11-01

- Главная, `/product/`, `/painting/`, `/services/`, `/projects/` и общий
  request popup теперь используют один набор размеров, шрифтов, цветов и
  отступов для labels, inputs, textarea, uploader, consent и submit.
- Uploader везде имеет высоту 56 px, белый фон, dashed border, одинаковую
  скрепку и подпись; на mobile кнопка выбора скрывается единообразно без
  горизонтального overflow.
- Checkbox согласия имеет 20×20 px и фирменный синий checked-state. Поля
  используют одинарное синее focus-состояние без двойной рамки.
- Устранены поздние page overrides, из-за которых на product/powder/projects
  появлялся второй служебный блок и форма переполняла панель на 96 px.
- Добавлен отдельный вычислительный regression contract. Финальная связанная
  desktop-регрессия **61/61 PASS**, mobile **10/10 PASS**. Docker build
  успешен, оба контейнера healthy, пять ключевых маршрутов отвечают HTTP 200.

## Подпись загрузчика, CTA и заголовок категории — 2026-11-01

- На главной и `/services/` восстановлена подпись «Прикрепить файлы»: общий
  стиль сбрасывает у неё старые absolute coordinates, поэтому текст остаётся
  внутри uploader на desktop и mobile.
- Стрелка submit-кнопок всех расчётных форм унифицирована: 26 px, вертикальное
  центрирование через `top: 50%` и `translateY(-50%)`.
- В заголовках категорий продукции хлебные крошки теперь разделяются `/`, а
  лишний kicker «Каталог продукции» удалён.
- «Рассчитать окраску» и «Услуги металлообработки» поднимают текст и стрелку
  вместе на 4 px. Hit area не перемещается, popup открывается без прокрутки.
- Проверки: PHP lint **3/3 PASS**; Playwright desktop **23/23 PASS**, mobile
  **5/5 PASS**; Docker build успешен, `db`/`web` healthy; `/`, `/services/`,
  `/painting/`, `/product/` и `/product/kapelnik/` отвечают HTTP **200**.

## Обновление production OPcache на VPS — 2026-11-01

- Подтверждено, что staging при Git HEAD `f2364a5` отдавал старый HTML даже с
  cache-busting и `Cache-Control: no-cache`; проблема находилась не в Caddy или
  браузерном кэше.
- Причина: production использует `opcache.validate_timestamps=0`, а Compose не
  пересоздавал web при изменении только bind-mounted исходников.
- VPS deploy теперь выполняет `up -d --build --wait --force-recreate web`.
  Перезапускается только Apache/PHP; MySQL и постоянные volumes не затрагиваются.
- Проверки: Git Bash syntax **PASS**; Compose поддерживает flag; локальный web
  успешно recreated и healthy, DB осталась healthy; HTTP smoke **200**.

## Валидация request popup без перекрытия кнопки — 2026-11-01

- Клиентские ошибки CONTACT и TASK теперь переводят popup в отдельное
  auto-height состояние: фиксированные Bitrix wrappers и grid tracks больше не
  удерживают старую высоту после появления текста ошибки.
- Внутренняя область формы прокручивается, поэтому submit остаётся доступным и
  не попадает под нижнее примечание; обычное состояние popup не изменилось.
- Добавлена точная регрессия на `/product/` при 768×943 px, соответствующая
  проблемному пограничному desktop-размеру.
- Проверки: Playwright desktop **4/4 PASS**, mobile **1/1 PASS**; Docker build
  успешен, `db`/`web` healthy; `/product/` отвечает HTTP **200**.

## Корзина и оформление заказа — 2026-11-01

- `/cart/` переведён на фирменные синий, navy, Inter Tight и Onest без красных
  ссылок и hover-состояний; print-кнопка получила белые текст и иконку.
- Хлебные крошки корзины и checkout используют `/`; `/cart/order/` подключён к
  общему system shell с иерархией `Главная / Корзина / Оформление заказа`.
- Товарная строка, счётчик, итог и действия корзины переработаны; сохранены
  исходные Bitrix handlers количества, удаления, печати и перехода к заказу.
- Checkout оформлен в две карточки: единая форма слева и sticky-состав заказа
  справа. На mobile они последовательно складываются в одну колонку без
  горизонтального overflow.
- Добавлен отдельный `cart-order.spec.ts` с реальным добавлением товара и
  контрольными снимками. PHP lint **2/2 PASS**, целевая проверка **3/3 PASS**,
  связанная desktop/mobile-регрессия **24/24 PASS**; Docker build успешен,
  оба контейнера healthy.

## Стабильные ошибки форм и единый scroll popup — 2026-11-01

- У request popup удалена внутренняя прокрутка формы: теперь при ограниченной
  высоте окна прокручивается весь popup вместе с заголовком, полями, кнопкой и
  нижним пояснением.
- Во всех шести контекстах под ошибки CONTACT, TASK и FILE заранее выделено
  26 px. Текст ошибки занимает этот слот абсолютно и больше не сдвигает
  uploader, согласие или submit.
- Удалены старые error-state правила главной и popup, которые меняли высоты
  grid/wrappers только после submit и создавали визуальный скачок.
- Автотесты сравнивают координаты ключевых элементов до и после пустой
  отправки. Playwright desktop **7/7 PASS**, mobile **2/2 PASS**; Docker image
  пересобран, оба контейнера healthy.

## Баланс колонок и отступов расчётных форм — 2026-11-01

- Пять двухколоночных форм приведены к одной desktop-высоте 738 px: левая
  визуальная и правая формовая части теперь заканчиваются на одной линии.
- Верхняя пустота формы уменьшена с 48 до 32 px, а под submit оставлен
  безопасный отступ 51 px вместо прижатия кнопки к нижней границе.
- Лишние интервалы TASK→FILE и FILE→consent сокращены; постоянная подпись
  загрузчика теперь одновременно служит зарезервированным местом для ошибки
  файла и не создаёт второй пустой ряд.
- Клиентские ошибки CONTACT, TASK и неверного типа FILE не двигают элементы.
  На mobile фиксированная высота отключена и сохраняется естественный поток.
- Playwright desktop **8/8 PASS**, mobile **2/2 PASS**.

## Финальная геометрия корзины и состава заказа — 2026-11-01

- В hero корзины удалён kicker «Ваш заказ», а вводный текст перенесён под
  заголовок слева. Empty-state теперь одна чистая карточка без иконки.
- «Очистить» и «Итого» выровнены по правому краю, крестик товарной строки
  центрирован; хлебные крошки корзины, каталога и карточек используют `/`.
- AJAX-очистка возвращает только внутренний fragment корзины и больше не
  вставляет второй system shell/hero внутрь страницы.
- Checkout сохраняет checkbox и текст согласия в одной строке, а состав заказа
  показывает ровные миниатюры товаров 70 px.
- Aspro-шаблоны перенесены в отслеживаемые локальные переопределения темы с
  собственными lang-файлами, поэтому исправления гарантированно попадают на
  staging из Git и не зависят от файлового архива Bitrix.
- Проверки: PHP lint **3/3 PASS**; cart desktop **4/4 PASS**, mobile **1/1
  PASS**; breadcrumbs **1/1 PASS**; accessibility/overflow **1/1 PASS**;
  Docker build успешен, оба контейнера healthy, HTTP smoke **3/3 PASS**.

## Фирменная кнопка очистки корзины — 2026-11-01

- «Очистить» оформлена синей outline-кнопкой РАЛМАСТЕР вместо визуально
  отключённого серого действия; hover использует синий фон и белый текст.
- Крестик сброшен с legacy absolute positioning и центрирован в одном
  flex-контейнере с подписью.
- Playwright desktop **1/1 PASS**, mobile **1/1 PASS**; горизонтального
  overflow нет. Docker build успешен, оба контейнера healthy, `/cart/` — HTTP
  **200**.

## Иерархия hero оформления заказа — 2026-11-01

- На `/cart/order/` удалён kicker «Ваш заказ».
- Описание перенесено под «Оформление заказа», выровнено по левому краю
  заголовка и использует тот же одноколоночный hero, что корзина.
- PHP lint **1/1 PASS**, desktop checkout **1/1 PASS**, mobile **1/1 PASS**;
  горизонтального overflow нет. Docker build успешен, оба контейнера healthy;
  checkout без товаров возвращает штатный HTTP **302** в корзину.

## Финальный correction pass и фотоотчёт — 2026-11-02

- Header приведён к двум контекстам: прозрачный/тёмный поверх главного hero и
  белый на внутренних страницах. Высота 72 px, контент совпадает с основными
  направляющими; меню содержит пять прямых пунктов и компактное подменю услуг.
- Главный экран имеет высоту 579 px по актуальному artboard Paper. CTA используют общий принцип:
  текст слева, стрелка справа.
- Введён единый motion contract: 160/220/500/560 ms, restrained scale только
  изображений, reveal ниже первого экрана и `prefers-reduced-motion`.
- Email, телефон и живой Aspro-счётчик корзины сохранены в desktop header;
  реальное добавление товара из `/product/pannier/` подтверждено до `/cart/`
  и `/cart/order/`.
- Полная redesign-регрессия **141/141 PASS**, smoke **32/32 PASS**. Проверены
  390/768/1024/1440/wide desktop, axe, browser errors и overflow.
- Фотоотчёт «до/после» создан в
  `J:\ralmaster\redesign-report\2026-11-02`; повторная съёмка выполняется
  командой `npm run visual:correction-report`.
- Paper использовался только как источник; макет не изменялся. Настоящие
  формы не отправлялись.

## Счётчик корзины и проект крепёжных кронштейнов — 2026-11-03

- Header на `/cart/` теперь считает позиции из той же Aspro-сессии, которую
  используют каталог и корзина; стандартный Sale basket больше не даёт
  ложный ноль.
- Иконка корзины использует фирменный синий на светлом header и белый на
  главном overlay-header. Ненулевой badge синий с белым числом; нулевой badge
  скрыт.
- Карточка «Изготовление по чертежам» ведёт на отдельную страницу
  `/projects/krepezhnye-kronshteyny/`, собранную по artboard «Шаблон
  подробного проекта» Paper: hero, задача, паспорт заказа, три этапа,
  результат и CTA.
- Проверены реальные add-to-cart и переход в корзину, desktop/mobile route,
  отсутствие горизонтального overflow и локальные изображения. Paper
  использовался только для чтения.
- Полная redesign-регрессия **141/141 PASS**, smoke основных и legacy-
  маршрутов **32/32 PASS**; Docker `db`/`web` healthy. Фотоотчёт дополнен
  кадрами `17-cart-header-count.png` и `18-brackets-project-page.png`.

## Единая корзина и обновлённый hover порошковой окраски — 2026-11-03

- Иконка корзины на `/cart/` теперь имеет тот же базовый бело-синий вид, что
  на остальных светлых страницах; текущий маршрут не добавляет отдельный фон.
  Hover/focus остаётся единым, а синий badge с белым числом сохраняется.
- На карточках принимаемых к окраске удалено старое увеличение всего блока на
  10%. Карточка не сдвигает сетку: плавно увеличивается только изображение на
  1.5% и появляется лёгкая тень; reduced-motion отключает масштабирование.
- Реальное добавление товара, переход в корзину и обе powder routes проверены
  на desktop/mobile: **20/20 PASS**; smoke **32/32 PASS**. Docker `db`/`web`
  healthy. Фотоотчёт переснят, включая новый hover-кадр.

## PDF, подробный проект и финальная мобильная коррекция — 2026-11-03

- Неактивная подпись скачивания заменена на реальную локальную PDF-карточку
  компании. PDF сформирован воспроизводимым скриптом, содержит одну A4-
  страницу, реквизиты извлекаются как текст; ссылка возвращает `200` и
  `application/pdf`.
- Подробный проект крепёжных кронштейнов повторно сверен с Paper: убраны
  лишние uppercase-kickers, result получил заголовок, двухколоночную галерею
  и нижние ссылки возможностей в структуре макета.
- Mobile services теперь закрыты по умолчанию и раскрываются по нажатию с
  полным выделением строки. Корзина постоянно видна на `/cart/`, zero badge
  скрыт, live badge и счётчик внутри mobile menu используют фирменный синий.
- Mobile cart выровнен по общей 72-px сетке с burger и logo; скрытые legacy-
  слоты больше не смещают правую иконку. Точный browser gate проверяет центр
  и правый отступ.
- На главной выровнены стрелки карточек, закреплены квадратные маркеры и
  устранено наложение строк процесса. Powder-карточки на 390 px показывают
  изображения целиком без обрезки.
- Результат фильтра каталога остаётся внутри sidebar и исчезает после снятия
  checkbox. Product/projects images получили отдельный clipping media-row;
  первая кассета масштабируется через `object-fit: contain` без зазора между
  заголовком и фотографией и полностью помещается на desktop/mobile.
- Одностраничный PDF и 33 контрольных PNG сохранены в
  `J:\ralmaster\redesign-report\2026-11-03\after`. Связанная desktop/mobile
  регрессия **84/84 PASS**, корректирующий карточный gate **17/17 PASS**, PHP lint
  **3/3 PASS**.

## Следующий допустимый шаг

### Коррекция header — 2026-10-31

- Подтверждён актуальный источник: `Мария's Team` → `Ралмастер` → `Дизайн`.
- Dropdown/mega-menu удалены из desktop и mobile на уровне Bitrix template data (`MAX_LEVEL=1`, `USE_EXT=N`).
- Общая навигация содержит ровно шесть прямых ссылок Paper; mobile burger имеет корректные ARIA-состояния.
- PHP lint: 4/4 PASS; targeted desktop: 4/4 PASS; targeted mobile: 3/3 PASS; home/navigation regression: 14/14 PASS.
- Visual shell: desktop 1440×106, mobile 390×72, horizontal overflow отсутствует.

1. Развернуть подтверждённый commit на тестовом VPS и провести приёмку по
   фотоотчёту на реальном домене/IP.
2. Для state-changing gate сначала изолировать mail/CRM; отдельно закрыть
   `SEC-001` с hardcoded map key.

## Правило обновления

После каждой инженерной итерации здесь фиксируются новый статус, проверяемый результат, ссылки на тестовые/визуальные отчёты и оставшиеся блокеры. Подробные критерии и дефекты находятся в `ITERATIONS.md`.
