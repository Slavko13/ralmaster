# Известные проблемы и ограничения

Последнее обновление: 2026-10-31.

| ID | Приоритет | Область | Проблема | Статус / действие |
| --- | --- | --- | --- | --- |
| ENV-001 | RESOLVED | Docker | Docker Desktop исторически падал при удалении `userAnalyticsOtlpHttp.sock` | Закрыто: Docker Desktop 4.97/engine 29.9.2 работает; factory reset не выполнялся |
| ENV-002 | RESOLVED | WSL | Первичный `wsl.exe --status` возвращал `Wsl/E_ACCESS_DENIED` | Закрыто для Docker workflow: Linux engine доступен и dev runtime проверен |
| ENV-003 | MEDIUM | Production-like image | Исходники подключаются read-only bind mount, а не запекаются в автономный image | Допустимо для локальной приёмки; не считать deployment artifact |
| ENV-004 | RESOLVED | Native fallback | PHP/MySQL/web server и альтернативный container runtime на машине отсутствуют | Native fallback больше не требуется: Docker runtime восстановлен |
| ENV-005 | RESOLVED | Production-like run | Первичный запуск конфликтовал с dev project и падал на writable mountpoints внутри read-only bind | Имена проектов разделены; mountpoints сохранены; отдельная БД клонирована; 8085 healthy; после force-recreate системный Chromium smoke 32/32 за 2.6 min, exit 0 |
| ENV-007 | RESOLVED | Playwright sandbox | Первая sandbox-попытка production-like smoke не нашла browser executable | Это инфраструктурно невалидный запуск, не дефект сайта; корректный системный Chromium run прошёл 32/32 |
| ENV-006 | LOW | Apache | В production-like web-log есть предупреждение об отсутствующем глобальном `ServerName` | Не влияет на HTTP/health/tests; можно убрать отдельной конфигурационной правкой |
| DATA-001 | RESOLVED | Bitrix DB | На первичном аудите backup не был найден в project root | Закрыто: чувствительный Beget backup вне Git импортирован без SQL-ошибок; 422 таблицы |
| DATA-002 | HIGH | Backup security | Использованный hosting backup содержит чувствительные данные | Оставлять вне Git, не публиковать, не копировать в tracked paths |
| TEST-001 | MEDIUM | State-changing flows | Успешные forms/cart/auth не входят в safe acceptance | Выполнить отдельно только локально после изоляции mail/CRM и с контролем побочных эффектов; текущий статус `NOT RUN` |
| TEST-002 | RESOLVED | Playwright artifacts | Параллельные процессы писали в общий `test-results` и дали ложный 23/32 | Финальные suites запускаются последовательно; повторные 61/61, 22/22 и 32/32 прошли |
| PAPER-001 | RESOLVED | Paper MCP | В первичном аудите достигался недельный лимит | Повторная проверка 2026-10-28: все детальные read-only методы и export работают |
| PAPER-002 | RESOLVED | Paper identity | Старые документы ссылались на `Cheerful breeze`/`Polite hill` | Цель подтверждена: `Мария's Team` → `Ралмастер` → `Дизайн` |
| PAPER-003 | MEDIUM | Responsive | Нет mobile/tablet-фреймов | Зафиксировать производные responsive-решения |
| PAPER-004 | MEDIUM | States | Нет hover/focus/error/loading макетов | Спроектировать по дизайн-системе и WCAG |
| PAPER-005 | LOW | Long-page previews | Интерактивные screenshots Paper уменьшают высокие фреймы до 2000 px | Использовать полноразмерные exports и секционные сравнения |
| FUNC-001 | RESOLVED | Calculation frames | Аудит ошибочно ожидал калькуляторы и формулы | Закрыто: оба Paper-фрейма являются формами запроса ручного расчёта; числовых полей, формул, цены и блока результата нет |
| FORM-001 | RESOLVED | Paper forms / legacy Web Form ID 5 | Legacy form не содержит file и не соответствует Paper | Создан и verified отдельный additive SID `ralmaster_estimate_s1`; legacy form ID 5 не изменялась |
| FORM-002 | RESOLVED | Form validation foundation | Paper: optional имя, обязательный phone-or-email и правило `описание || файл` | Dedicated schema и общий client/server validation реализуют `CONTACT`, `TASK || FILE`, 20 MB и allowlist расширений |
| FORM-003 | HIGH | Notifications/integrations | Mail template и внешние CRM hooks могут дать неверный адресат или реальный побочный эффект | Перед успешным E2E изолировать почту/CRM, проверить macros и результат в локальной БД |
| FORM-004 | RESOLVED | Modal variants | Bending/powder/discuss-order modal UI и lifecycle требовали отдельной интеграции | Реализованы native dialog variants 748×850/850/898; targeted 3/3 и home+dialogs 10/10 PASS, полный redesign 61/61 |
| SEC-001 | HIGH | Maps | В tracked include обнаружен hardcoded map key | Не раскрывать; вынести в env и рассмотреть ротацию |
| CONTENT-001 | RESOLVED | Painting | Повторяющийся текст и hardcoded images | Заменено единым reusable Paper page block для обоих painting routes; originals хранятся локально |
| ROUTE-001 | MEDIUM | Legacy | `/products/`, `/news/`, `/landings/` могут быть устаревшими, но доступны | Не удалять без аналитики/redirect plan |
| ROUTE-002 | MEDIUM | Links | В коде есть абсолютные ссылки на `bavrasem.beget.tech` | Инвентаризировать и заменить безопасно |
| A11Y-001 | RESOLVED | Home | Baseline `aria-hidden-focus`: 1 узел | Новая главная исправлена; итоговый axe scan = `[]` |
| A11Y-002 | RESOLVED | Home | Baseline `color-contrast`: 15 узлов | Цвета/состояния новой главной исправлены; итоговый axe scan = `[]` |
| A11Y-003 | RESOLVED | Home | Baseline `link-name`: 6 узлов | Добавлены доступные имена и устранён redundant linked-image alt; итоговый axe scan = `[]` |
| A11Y-004 | RESOLVED | Home | Baseline `target-size`: 2 узла | Интерактивные зоны новой главной исправлены; итоговый axe scan = `[]` |
| A11Y-005 | HIGH | Service detail | `color-contrast`: 6, `link-name`: 1 | Исправить в service iteration |
| A11Y-006 | HIGH | Contacts | `color-contrast`: 10, `link-name`: 1 | Исправить в contacts iteration |
| ROUTE-003 | INFO | Forms | Direct `/form/` без form ID ожидаемо возвращает 404/not-found без fatal | Сохранить корректное поведение; передавать ID в реальном form flow |
| GIT-001 | LOW | Delivery | Git remote отсутствует | Локальные коммиты допустимы; push только после явного назначения remote |
| UI-001 | RESOLVED | Home CTA | Две CTA главной отображались полноширинными полосами из-за отсутствующих правил в legacy minified CSS | Исправлено в последнем `custom.css` override; реальный рендер и modal test проходят |
| UI-002 | RESOLVED | Footer | Subscription band и flex shrink ломали Paper-высоту 430 px, legal row и mobile footer | Subscription исключён из нового footer; desktop/mobile root geometry исправлена |
| HOME-001 | RESOLVED | Mobile home | Legacy hero на 390 px оставлял большое пустое поле | Новая Paper-композиция внедрена; capture 390 px без horizontal overflow и browser errors |
| HOME-002 | RESOLVED | Home inline form | Legacy SID CSS создавал дубли labels, native file input и обрезал submit | Форма переведена на NAME/CONTACT/TASK/FILE/CONTEXT и точную 748×548 geometry; AJAX redecorate сохраняется |
| JS-001 | RESOLVED | Mobile Aspro | Ранний resize мог обратиться к `arAllcorp2Options.THEME` до инициализации | Добавлен безопасный ранний stub перед `ShowHead`; mobile diagnostics и тесты проходят |
| JS-002 | RESOLVED | Dialog AJAX lifecycle | MutationObserver мог зациклить DOM, а AJAX replacement терял namespace/decorations | Observer сделан idempotent/persistent; success/error/loading states и повторная декорация проверены |
| UI-003 | RESOLVED | Mobile powder dialog | Двухстрочные title/task help/file help/consent перекрывались внутри fixed rows | Mobile CSS переведён на natural-flow heights; pairwise layout test 1/1 PASS, production-like dialogs 3/3 PASS, финальный capture без overlap |
| UI-004 | SUPERSEDED | Desktop navigation | Исторический hotfix поддерживал wide dropdown и hover trajectory | Актуальный Paper требует плоскую навигацию; dropdown полностью удалён в UI-006 |
| UI-005 | RESOLVED | Desktop sticky header | При прокрутке Aspro рендерил отдельный legacy `#headerfixed` поверх нового `.rm-header` | Новый header выставляет явный server-side флаг; legacy fixed header при нём не рендерится. Desktop 4/4, mobile 2/2, PHP lint 2/2 PASS; browser diagnostics `[]` |
| UI-006 | RESOLVED | Header navigation | Desktop/mobile содержали dropdown, submenu и mega-menu вопреки актуальному Paper | Добавлен общий flat Bitrix template; ровно 6 direct links, child DOM отсутствует, mobile ARIA исправлен; desktop 4/4 и mobile 3/3 PASS |
| A11Y-007 | RESOLVED | Dedicated forms | Скрытые Bitrix labels оставляли controls без доступного имени на Services | Общий form JS назначает ARIA-label каждому non-hidden control; targeted Services 5/5 и полный redesign 61/61 PASS |
| CONTENT-002 | MEDIUM | Social links | Paper показывает Telegram/MAX, но локальная Bitrix-конфигурация не содержит URL | Не выдумывать URL; визуальные controls помечены как ненастроенные до получения фактических ссылок |
| CONTENT-003 | MEDIUM | Production FAQ | IBlock 17 не содержит вопросы Paper | Accessible FAQ является явно документированным presentation fallback; синхронизировать с CMS после появления утверждённых данных |
| CONTENT-004 | RESOLVED | Production limits | Значения Paper могли выглядеть как подтверждённые пределы оборудования | UI явно помечает их примерными и требующими проверки чертежа |
| CONTENT-005 | MEDIUM | Project data | IBlock 36 не содержит утверждённые TASK/ORDERER/DATA/PHOTOS/passport values | Сохранены динамические SEF/data и честные empty/demo states; заполнить после появления CMS-свойств |
| CONTENT-006 | MEDIUM | Contacts data | Нет утверждённого CMS/PDF источника всех реквизитов; Yandex map отличается от статического Paper | Реквизиты явно presentation fallback; не заявлять pixel-match карты, получить карточку компании/PDF |

## Исходные дефекты и новые регрессии

Accessibility, legacy links и content issues выше обнаружены до редизайна и являются baseline. Они не должны маскировать новые дефекты: любая новая critical/high регрессия блокирует переход между итерациями. При устранении baseline-проблемы её ID сохраняется в истории итерации как исправленный дефект.

## Ограничения проверок

- Live production baseline намеренно не изменяет данные.
- Expanded live baseline полностью проходит: 54/54; browser diagnostics на четырёх ключевых маршрутах чисты.
- Финальный post-`UI-003` local redesign suite на dev 8084 полностью проходит:
  61/61 за 6.4 min, exit 0; обновлённый baseline — 22/22; dev route smoke —
  32/32. Browser diagnostics clean, critical axe = 0 в проверенном scope.
- Финальный visual capture: 20 viewport/page runs, diagnostics `[]`, без
  horizontal overflow; mobile powder overlap исправлен и подтверждён отдельным
  bottom-crop.
- Dedicated form schema verified в dev и изолированной production-like БД, но успешный submit, mail, CRM, cart и auth/cabinet с изменением состояния ещё не проверены.
- Production-like профиль изолирован от dev и фактически запущен на 8085;
  schema verify PASS, smoke после force-recreate — 32/32 за 2.6 min, exit 0.
- Paper Gate пройден; остаются только продуктовые ограничения по responsive и
  interaction states, которых нет в исходном desktop-макете.
- Production-инфраструктура не изменялась и не должна изменяться в рамках локальной подготовки.
