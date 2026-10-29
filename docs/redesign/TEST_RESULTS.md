# Результаты тестирования

Последнее обновление: 2026-10-29.

## Live functional baseline

| Поле | Значение |
| --- | --- |
| Команда | `npm run test:e2e:baseline` |
| Target | `https://ral-master.ru` |
| Браузер | Playwright Chromium |
| Проекты | desktop `1440×1100`, mobile `390×844` |
| Результат expanded прогона | **54 passed / 0 failed / 0 skipped / 0 flaky** |
| Время | **2.1 min** |
| Статус | `PASSED` для безопасного live read-only/invalid-input baseline |

Проверены основные и secondary routes, desktop/mobile navigation, каталог/услуги и detail pages, поиск, контакты, пустая форма, mobile CTA, 404 и accessibility gate. В итоговые 54 входят два P0-теста: переход из `/projects/` в detail route и закрытие request modal через Escape без отправки. Реальные отправки данных и другие изменяющие состояние операции на production не выполнялись.

### Browser diagnostics

| Маршрут | `console.error` | `pageerror` | Ignored |
| --- | ---: | ---: | ---: |
| `/` | 0 | 0 | 0 |
| `/product/` | 0 | 0 | 0 |
| `/services/metal-bending/` | 0 | 0 | 0 |
| `/contacts/` | 0 | 0 | 0 |

### Status policy

- Для всех обычных primary, secondary и legacy routes требуется финальный HTTP status `<400`.
- Direct `/form/` без form ID является исключением: текущий сайт ожидаемо показывает 404/not-found. Тест подтверждает корректную not-found страницу и отсутствие PHP fatal, а не status `<400`.

## Accessibility baseline

Тестовый gate требует отсутствия `critical` axe-нарушений. Critical violations: **0**. При этом зафиксированы serious-проблемы существующего сайта, которые должны быть устранены редизайном:

| Маршрут | Rule | Количество узлов |
| --- | --- | ---: |
| `/` | `aria-hidden-focus` | 1 |
| `/` | `color-contrast` | 15 |
| `/` | `link-name` | 6 |
| `/` | `target-size` | 2 |
| `/services/metal-bending/` | `color-contrast` | 6 |
| `/services/metal-bending/` | `link-name` | 1 |
| `/contacts/` | `color-contrast` | 10 |
| `/contacts/` | `link-name` | 1 |

Это baseline-дефекты действующего сайта, а не новые регрессии. В каждой UI-итерации набор должен запускаться повторно; итоговый gate следует ужесточить до отсутствия подтверждённых critical и serious нарушений в новом UI.

## Артефакты

- HTML report: `playwright-report/index.html`.
- JSON results: `test-results/results.json`.
- Trace/video/failure screenshots создаются при сбоях.

Эти runtime-артефакты исключены из Git. Для просмотра HTML-отчёта используется `npm run test:e2e:report`.

## Local baseline

Статус safe baseline: `PASSED`.

| Проверка | Результат | Время |
| --- | --- | ---: |
| Development build/start | `db` и `web` healthy | — |
| `/healthz.php` | HTTP 200 | — |
| `/` | HTTP 200 | — |
| Smoke | **32/32 passed** | **3.0 min** |
| Полный safe baseline | **54/54 passed**, 0 failed/skipped/flaky | **5.2 min** |

Команды для текущего локального порта:

```powershell
docker compose up --build
$env:BASE_URL='http://127.0.0.1:8084'; npm run test:e2e:smoke
$env:BASE_URL='http://127.0.0.1:8084'; npm run test:e2e
```

После ручного импорта backup не обнаружены HTTP 5xx, PHP fatal или Bitrix
fatal. Browser diagnostics на `/`, `/product/`,
`/services/metal-bending/`, `/contacts/`: `console.error=0`, `pageerror=0`,
ignored=0. Critical axe violations: 0; известные serious-проблемы из таблицы
выше остаются baseline для редизайна.

Локальная схема содержит 422 таблицы; контрольные записи: `b_user` — 1,
`b_iblock` — 43, `b_iblock_element` — 334, `b_form` — 11, `b_lang` — 1.

## Production-like baseline

Статус: `PASSED` для read-only acceptance-проверок.

| Проверка | Результат |
| --- | --- |
| Compose project | отдельный `ralmaster-redesign-production` |
| DB clone | 422 таблицы в отдельном volume; dev volume не изменён |
| `db` / `web` | `healthy` |
| `http://127.0.0.1:8085/healthz.php` | HTTP 200 |
| `http://127.0.0.1:8085/` | HTTP 200 |
| Основной CSS / logo | HTTP 200 |
| Browser diagnostics | **4/4 passed** |
| Первичный smoke | **32/32 passed**, **2.2 min** |
| Smoke после финального force-recreate | **32/32 passed**, **2.6 min**, exit 0 |

Команды:

```powershell
.\docker\clone-db-to-production.ps1
docker compose -p ralmaster-redesign-production -f compose.production.yml up --build -d --wait
$env:BASE_URL='http://127.0.0.1:8085'; npm run test:e2e:smoke
$env:BASE_URL='http://127.0.0.1:8085'; npx playwright test tests/e2e/baseline/browser-errors.spec.ts --project=desktop-chromium
```

Clone script не создаёт SQL-файл на хосте, не перезаписывает непустую
destination-БД и после проверки отключает production-like DB от dev-сети.
В web-логах не обнаружены PHP fatal/error. Зафиксировано только некритичное
предупреждение Apache о глобальном `ServerName`.

После создания dedicated request form та же идемпотентная migration была
применена только к изолированной production-like БД; `--verify` подтвердил SID
`ralmaster_estimate_s1`. Production-БД и production-инфраструктура не
затрагивались.

### Оставшиеся проверки

- State-changing forms/cart/auth scenarios только на локальной БД с контролем
  отправки почты и восстановлением состояния.

## Итерация 3 — foundations/shell

Статус: `PASSED`.

| Проверка | Результат |
| --- | --- |
| PHP syntax: desktop/mobile header и footer | **3/3 PASS** |
| Desktop navigation + route assertions | **PASS** |
| Projects/detail assertions | **PASS** |
| Modal open → Escape без отправки | **1/1 PASS** |
| Mobile navigation/CTA | **2/2 PASS** |
| Browser diagnostics на ключевых маршрутах | **4/4 PASS** |
| Shell capture 1440/390 | **PASS**, horizontal overflow = 0 |

Legacy FlexSlider содержит одновременно несколько CTA; тест модалки теперь
переключает реальные pagination controls и выбирает элемент, пересекающий
viewport. Проверка открытия, видимости и закрытия модалки сохранена, mock или
skip не добавлялись.

Команда визуального capture:

```powershell
$env:BASE_URL='http://127.0.0.1:8084'; npm run visual:shell
```

Артефакты создаются в `test-results/redesign-shell/` и исключены из Git.

## Итерация 4 — главная страница

Статус: `PASSED` для visual/responsive/accessibility и безопасного functional
scope. Отдельный общий state-changing gate формы остаётся непроверенным.

| Проверка | Результат |
| --- | --- |
| `tests/e2e/redesign/home.spec.ts` | **7/7 PASS** |
| Затронутый mobile baseline | **2/2 PASS** |
| Расширенный затронутый gate до корректировки старого mobile expectation | **23/24 PASS**; единственная ошибка была устаревшим ожиданием jqModal, тест приведён к фактическому `/form/?id=5` fallback и повторно прошёл 2/2 |
| Итоговый axe scan новой главной | **0 нарушений** (`[]`) |
| Browser diagnostics desktop/mobile | **PASS**, `console.error=0`, `pageerror=0` |
| Horizontal overflow 1440/390 | **0 px** |
| PHP syntax затронутых home/header файлов | **PASS** |
| `node --check` для `custom.js` | **PASS** |
| Full-page visual capture 1440 | **PASS**, 1440×8645 и целевые секционные границы |
| Повтор после переключения на SID `ralmaster_estimate_s1` | **6/6 desktop PASS**, capture diagnostics clean, geometry снова 1440×8645 |

Visual artifacts:

- `test-results/redesign-home/desktop-1440-full.png`
- `test-results/redesign-home/desktop-1440-hero.png`
- `test-results/redesign-home/desktop-1440-order.png`
- `test-results/redesign-home/mobile-390-full.png`
- `test-results/redesign-home/mobile-390-hero.png`
- `test-results/redesign-home/mobile-390-order.png`

Capture принудительно прокручивает lazy images перед full-page screenshot.
Desktop-секции измерены как: hero `0–579`, work model `579–739`, products
`739–2643`, projects `2643–3639`, partners `3639–4201`, production
`4201–5283`, process `5283–6165`, FAQ `6165–7012`, order `7012–7672`,
photo `7672–8215`, footer `8215–8645`.

Реальная успешная отправка не проверена. Dedicated SID содержит optional
`NAME`, required `CONTACT`, optional `TASK`/`FILE` и hidden `CONTEXT`, а
client/server validation выражает phone-or-email, `TASK || FILE`, 20 MB и
allowlist. Submit, mail и CRM hooks намеренно не выполнялись до mail sink.

## Итерация 5 — продукция

Статус: `PASSED`.

| Проверка | Результат |
| --- | --- |
| `tests/e2e/redesign/products.spec.ts`, desktop | **8/8 PASS** |
| Mobile 390 | **1/1 PASS** |
| Geometry | **1440×3240**, секция form 804 px, form root 692 px |
| Dynamic architecture | IBlock 41, семь active sections, SEF section/detail routes сохранены |
| Estimate form | SID `ralmaster_estimate_s1`, context `product`, safe validation без POST |
| Browser/a11y/overflow | critical axe = 0; console/page errors = 0; overflow = 0 |

## Итерация 6 — металлообработка и порошковая окраска

Статус: `PASSED`.

| Страница | Desktop | Mobile | Geometry | Дополнительный gate |
| --- | ---: | ---: | ---: | --- |
| `/services/` | **5/5 PASS** | **1/1 PASS** | **1440×5741** | IBlock 40/SEF сохранены; context `metalworking` |
| `/painting/` и detail alias | **7/7 PASS** | **1/1 PASS** | **1440×6396** | оба routes 200; context `powder` |

Powder inline form измерена как 748×738: TASK 140/textarea 112, uploader 56,
consent 40, submit 60; все контролы имеют `clipped=false`, внутренний
`scrollHeight=clientHeight=738`. Browser errors, critical axe и horizontal
overflow отсутствуют. Реальный POST не выполнялся.

## Итерация 7 — производство

Статус: `PASSED`.

| Проверка | Результат |
| --- | --- |
| `tests/e2e/redesign/production.spec.ts`, desktop | **8/8 PASS** |
| Mobile 390 | **1/1 PASS** |
| Responsive 768/1024 | **PASS**, horizontal overflow = 0 |
| Geometry | **1440×7203**, секционные границы и footer 430 px совпадают |
| Dynamic architecture | партнёры и URL из IBlock 20; `/company/partners/` = HTTP 200 |
| Estimate form | SID `ralmaster_estimate_s1`, context `production`, без POST |
| Browser/a11y | critical axe = 0; console/page errors = 0 |

FAQ использует явно задокументированный presentation fallback, поскольку
IBlock 17 не содержит вопросы Paper. Числовые производственные ограничения
видимо помечены как примерные/требующие подтверждения, а не как сертификаты.

## Form foundation

| Проверка | Результат |
| --- | --- |
| Dry run миграции | PASS, legacy source form только прочитана |
| Apply + verify, dev DB | PASS, SID `ralmaster_estimate_s1` |
| Apply + verify, production-like isolated DB | PASS, SID `ralmaster_estimate_s1` |
| Client JS syntax | `node --check` PASS |
| Rollback safety | полный pre-migration snapshot сохранён локально и исключён из Git; destructive rollback отсутствует |
| State-changing submit/mail/CRM | **NOT RUN** |

Playwright теперь по умолчанию использует development Compose URL
`http://127.0.0.1:8084`; `BASE_URL` всё ещё можно явно переопределить для
production-like 8085.

## Финальная безопасная регрессия редизайна

Статус: **PASSED** для UI, read-only, navigation, invalid-input и accessibility
scope. Успешный state-changing submit/mail/CRM: **NOT RUN**.

| Набор | Команда / scope | Итоговый результат | Время |
| --- | --- | ---: | ---: |
| Полный redesign post-`UI-003` | `npm run test:e2e:redesign -- --project=desktop-chromium --project=mobile-chromium --trace=off`, dev 8084 | **61/61 PASS**, exit 0 | **6.4 min** |
| Обновлённый baseline | `npx playwright test tests/e2e/baseline --project=desktop-chromium --project=mobile-chromium --trace=off` | **22/22 PASS** | **2.7 min** |
| Dev route smoke | `npm run test:e2e:smoke -- --trace=off` с `BASE_URL=http://127.0.0.1:8084` | **32/32 PASS** | **2.8 min** |
| Production-like smoke | та же команда с `BASE_URL=http://127.0.0.1:8085` после force-recreate | **32/32 PASS**, exit 0 | **2.6 min** |
| Production-like dialogs после mobile overlap fix | `estimate-dialogs.spec.ts`, desktop+mobile, `BASE_URL=http://127.0.0.1:8085` | **3/3 PASS**, без POST | **22.1 s** |
| Dev mobile powder overlap assertion | попарное вертикальное неперекрытие title/intro/fields/consent/submit | **1/1 PASS** | **11.7 s** |
| Финальный visual capture | 20 viewport/page runs | **PASS**, diagnostics `[]`, no horizontal overflow | — |
| Dialog variants | discuss/bending/coating, desktop+mobile assertions | **3/3 PASS** | — |
| Home + dialogs | объединённый targeted run | **10/10 PASS** | — |
| Services после ARIA-fix | targeted desktop/mobile | **5/5 PASS** | — |
| Dedicated form verify | dev + isolated production-like DB | **PASS** | — |

Первый полный redesign run завершился 60/61: Services axe gate обнаружил
контролы без доступного имени, потому что Bitrix labels визуально скрыты. Общий
`ralmaster-estimate.js` теперь назначает `aria-label` каждому non-hidden
`input`, `textarea` и `select`; targeted 5/5 и повторный полный run 61/61 зелёные.

Первый обновлённый baseline run завершился 19/22: три теста всё ещё ожидали
legacy jqModal и `/form/`. Проверки актуализированы под нативный dialog без
ослабления gate: открытие, отсутствие POST, client errors и закрытие Escape.
Targeted run 7/7 и повторный полный baseline 22/22 прошли.

Полные Playwright-прогоны выполнялись последовательно. Ранний ложный результат
23/32 был вызван одновременной записью двух процессов в общий `test-results` и
не считается дефектом приложения.

Первая sandbox-попытка production-like smoke после force-recreate была
инфраструктурно невалидна: в sandbox отсутствовал доступный browser executable.
Повтор с системным Chromium использовал тот же `BASE_URL=http://127.0.0.1:8085`
и завершился **32/32 PASS** за 2.6 min, exit 0.
Финальный visual capture обнаружил overlap в mobile powder dialog: двухстрочные
title, task help, file help и consent попадали в фиксированные rows. Mobile CSS
переведён на natural-flow heights; новый тест проверяет попарное вертикальное
неперекрытие `title → intro → fields → consent → submit` и прошёл 1/1 за 11.7 s.
После исправления production-like dialog gate прошёл **3/3** за 22.1 s на
desktop+mobile и не выполнял POST.

Capture пересоздан после исправления: 20 viewport/page runs, diagnostics `[]`,
horizontal overflow отсутствует. Артефакт
`test-results/redesign-pages/powder-mobile-390-dialog-bottom.png` визуально
подтверждает отсутствие overlap в нижней части powder dialog.

Проверенные modal размеры и computed styles: discuss-order **748×850**,
metal-bending **748×850**, powder-coating **748×898**; heading **36/44**, поля
**56**, textarea **116**, upload **56**, submit **60**. Реальный POST не
выполнялся.

## Hotfix desktop navigation — 2026-10-29

| Проверка | Результат |
| --- | --- |
| `tests/e2e/redesign/header-menu.spec.ts`, desktop Chromium | **2/2 PASS** |
| `tests/e2e/redesign/home.spec.ts`, desktop Chromium | **6/6 PASS** |
| Wide product dropdown | **PASS** — 1008×178.84, 7 items, 7 images |
| Services hover trajectory | **PASS** — dropdown visible на всех 25 шагах |
| «Гибка металла» | **PASS** — `/services/metal-bending/`, HTTP 200 |
| Keyboard / overlay / diagnostics | **PASS** — `focus-within`, transparent header, console/page errors `[]` |

В regression-тест добавлены проверки usable-width wide-menu, прямого pointer
перехода к дочерней ссылке и прозрачного background нового overlay-header.
Визуальные артефакты: `test-results/menu-audit-final/products.png` и
`test-results/menu-audit-final/services.png`.

## Hotfix legacy sticky header — 2026-10-29

- Desktop `header-menu.spec.ts`: **4/4 PASS**; два новых сценария проверяют `/`
  и `/painting/poroshkovaya-pokraska-metalla/` до и после scroll 900 px.
- Mobile `baseline/mobile.spec.ts`: **2/2 PASS**; `#mobileheader` и CTA/modal
  flow не регрессировали.
- PHP lint `header.php` и `page_blocks/header_1.php`: **2/2 PASS**.
- Ручной Chrome audit 1440×900: `#headerfixed`/`#headerfixed.fixed` — 0,
  console/page errors — `[]` на обеих страницах.

## Staging deployment script — 2026-10-29

- `bash -n deploy/ralmaster-vps-deploy.sh`: **PASS**.
- `deploy/ralmaster-vps-deploy.sh --help`: **PASS**.
- `git check-attr text eol`: **PASS**, shell script закреплён как LF.
- `git diff --check`: **PASS** (только существующие Windows LF/CRLF warnings).
- Remote Docker/Caddy execution: **NOT RUN**; сервер не изменялся.

## Интерпретация

Live baseline и финальные local safe suites подтверждают покрытые
read-only/invalid-input/UI сценарии и воспроизводимость локального runtime.
Production-like schema verify выполнен; повторный smoke после последней
force-recreate завершился 32/32 PASS за 2.6 min, exit 0.
Эти проверки не подтверждают успешный state-changing mail/CRM flow.
