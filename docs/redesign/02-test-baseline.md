# Функциональный baseline

Статус live baseline: **PASSED**. Статус полной baseline-итерации: **IN PROGRESS**, поскольку локальный/state-changing контур заблокирован.

## Инфраструктура

Добавлены Playwright и axe-core:

- desktop Chromium, viewport `1440×1100`;
- mobile Chromium, viewport `390×844` с профилем iPhone 13;
- HTML-, JSON- и list-отчёты;
- trace, screenshot и video при сбое;
- timezone `Europe/Moscow`, locale `ru-RU`.

Команды:

```powershell
npm run test:e2e:install
npm run test:e2e
npm run test:e2e:baseline
npm run test:e2e:report
```

`test:e2e:baseline` запускает безопасные проверки против `BASELINE_URL`, по умолчанию `https://ral-master.ru`. Локальная проверка использует `BASE_URL`, по умолчанию `http://127.0.0.1:8080`.

## Покрытие

- smoke основных страниц и отсутствие PHP fatal/Bitrix error;
- неизвестный маршрут и not-found-состояние;
- переходы основного меню;
- каталог, услуги и detail pages;
- поиск и контакты;
- required-валидация формы без реальной отправки;
- mobile header/menu и CTA;
- secondary/legacy/account/cart routes;
- axe-проверка критических accessibility-нарушений на ключевых страницах;
- полноэкранные baseline screenshots основных маршрутов.

## Результат expanded live baseline

Начальный прогон выявил четыре проблемы тестовой инфраструктуры/селекторов: два desktop-селектора и две ошибочно унаследованные mobile browser-настройки. После исправления инфраструктуры desktop-набор прошёл, а обе mobile-проверки были отдельно перепроверены успешно. Затем набор был расширен secondary routes, accessibility, project/modal P0 и browser diagnostics.

Финальный expanded live baseline: **54/54 passed**, 0 failed/skipped/flaky за **2.1 min**.

В итоговый набор входят:

- два P0-сценария: переход из `/projects/` в detail route и закрытие request modal клавишей Escape без отправки;
- browser diagnostics на `/`, `/product/`, `/services/metal-bending/`, `/contacts/`: `console.error=0`, `pageerror=0`, ignored errors `=0`;
- direct `/form/` без form ID как ожидаемый 404/not-found без PHP fatal;
- secondary/legacy routes с обязательным финальным HTTP status `<400`, кроме явно ожидаемого `/form/` без ID.

## Ограничения baseline

- Локальный прогон реального Bitrix-сайта заблокирован отсутствием санитизированного дампа БД.
- Docker runtime не запускается из-за ошибки Docker Desktop/WSL.
- На production не выполняются валидные отправки форм, регистрация, изменение кабинета и оформление заказа.
- Корзина и региональность зависят от runtime-настроек темы и требуют локальной БД.
- Визуальный baseline существующего сайта не является целевым эталоном; целевой эталон — Paper.

## Критерий завершения

Baseline считается завершённым только после:

1. успешного полного безопасного прогона по production — выполнено, 54/54;
2. запуска локального Docker-окружения с БД;
3. повторения тестов локально, включая state-changing сценарии в изоляции;
4. фиксации обнаруженных дефектов и артефактов в `TEST_RESULTS.md`.
