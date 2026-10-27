# Результаты тестирования

Последнее обновление: 2026-10-27.

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

### Оставшиеся проверки

- Actual production-like run на порту 8085 и повторный health/smoke.
- State-changing forms/cart/auth scenarios только на локальной БД с контролем
  отправки почты и восстановлением состояния.

## Интерпретация

Live и local safe baseline подтверждают покрытые read-only/invalid-input
сценарии и воспроизводимость development runtime на момент запуска. Они не
подтверждают state-changing flows, фактический production-like профиль или
визуальное соответствие Paper.
