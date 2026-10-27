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

Статус: `BLOCKED`.

Причины:

- Docker Desktop не запускает engine из-за stale OTel socket;
- WSL возвращает `E_ACCESS_DENIED`;
- санитизированный дамп Bitrix DB отсутствует.

После снятия блокировки требуется:

1. `docker compose up --build`;
2. healthcheck и smoke `/`;
3. `BASE_URL=http://127.0.0.1:8080 npm run test:e2e`;
4. state-changing forms/cart/auth tests на изолированных данных;
5. production-like build и повторная регрессия.

## Интерпретация

Прохождение live baseline доказывает работоспособность покрытых production-сценариев на момент запуска. Оно не доказывает воспроизводимость локального окружения, корректность скрытых DB-dependent сценариев или визуальное соответствие Paper.
