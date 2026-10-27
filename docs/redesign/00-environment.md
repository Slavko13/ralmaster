# Локальное окружение и воспроизводимый запуск

Дата проверки: 2026-10-27.

## Исходное приложение

- Корень проекта и Git-репозитория: `J:\ralmaster\ralmaser\public_html`.
- В workspace `J:\ralmaster` есть несколько каталогов хостинга. `ral-master.ru` и `ralmaster.ru` содержат только стартовые placeholder-страницы, `bavrasem.beget.tech` — установочный/служебный снимок. Рабочий сайт находится именно в каталоге с опечаткой `ralmaser\public_html`.
- Стек: PHP/1С-Битрикс, шаблон Aspro Allcorp2.
- Версия ядра Битрикс: `25.100.500`.
- Версия модуля Aspro Allcorp2: `1.2.32`.
- Зафиксированная на production версия PHP по журналам: `8.2.28`.
- Production использует MySQL 8.0.x и nginx; локальная конфигурация использует Apache с `mod_rewrite`.
- Проект содержит короткие PHP-теги, поэтому в контейнере включён `short_open_tag`.
- Исходное состояние сохранено локальным коммитом `c345081f850d966dcd6bdffa6559b870dce2b977` (`chore: capture pre-redesign baseline`) в ветке `feature/full-paper-redesign`.
- Удалённый Git remote не настроен; ничего не отправлялось во внешние репозитории.

## Подготовленная Docker-инфраструктура

Созданы:

- `Dockerfile` на базе `php:8.2.28-apache-bookworm` с dev- и production-целями;
- `compose.yml` для разработки;
- `compose.production.yml` для production-подобной проверки;
- `.env.example` без рабочих секретов;
- конфигурация Apache и PHP;
- Docker-only переопределения Bitrix DB-конфигурации через переменные окружения;
- `/healthz.php`, проверяющий необходимые PHP-расширения и подключение к БД;
- каталог `docker/db/init/` для санитизированного дампа.

Подключены только необходимые сервисы:

- `web`: PHP 8.2 + Apache;
- `db`: MySQL `8.0.40`.

Для разработки исходники подключаются bind mount. Отдельный hot reload для PHP не требуется: изменения PHP, CSS и JavaScript видны контейнеру сразу. Production-подобный профиль включает production-настройки opcache и монтирует код read-only, оставляя доступными только runtime-каталоги Bitrix.

Production-подобный профиль предназначен для локальной приёмки. Он не является автономным immutable deployment image: исходники поступают через read-only bind mount. Production deployment в рамках этой задачи не изменяется.

## Команды

Подготовка:

```powershell
Copy-Item .env.example .env
```

Локальный `.env` исключён из Git и закрепляет `APP_PORT=8084` и
`APP_PROD_PORT=8085`; значения локальных паролей не документируются.

Основной запуск:

```powershell
docker compose up --build
```

Production-подобный запуск:

```powershell
docker compose -f compose.production.yml up --build
```

Остановка:

```powershell
docker compose down
docker compose -f compose.production.yml down
```

Диагностика:

```powershell
docker compose ps --all
docker compose logs --no-color --tail 100 web
docker compose logs --no-color --tail 100 db
curl.exe http://127.0.0.1:8084/healthz.php
curl.exe -I http://127.0.0.1:8084/
```

Безопасный ручной импорт в уже созданный volume:

```powershell
cmd /c type "<absolute-path-to-sensitive-backup.sql>" | docker compose exec -T db sh -lc 'exec mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"'
```

В команде намеренно используются placeholder и переменные окружения
контейнера. Реальный пароль и имя backup не включаются в reusable-команды или
shell history. Backup должен оставаться вне Git; после импорта проверяются код
завершения, логи, количество таблиц, `/healthz.php` и `/`.

Удаление именованного volume БД выполняется только явно командой `docker compose down --volumes`. Это удаляет локальные Docker-данные и не должно применяться к production.

## Результаты проверки

- `docker compose config --quiet` для обоих Compose-файлов проходит успешно.
- Docker Desktop `4.97` восстановлен; engine `29.9.2` доступен.
- Docker CLI и Docker Compose доступны.
- Локальные PHP и Composer вне Docker отсутствуют.
- Нативные MySQL, Apache/nginx, XAMPP/Laragon и альтернативные container runtimes (Podman/nerdctl/Rancher Desktop) также не обнаружены.
- Node.js `v24.18.0` и npm `11.16.0` доступны.
- Development image успешно собран; контейнеры `db` и `web` достигают состояния healthy.
- Локальные `/healthz.php` и `/` отвечают HTTP 200 на `http://127.0.0.1:8084`.
- После импорта не обнаружены HTTP 5xx или PHP/Bitrix fatal errors.
- Production-like Compose-конфигурация валидна, но её фактический запуск на порту 8085 ещё не выполнен.

### Локальная база данных

Чувствительный Beget backup обнаружен вне Git по пути
`J:\ralmaster\_live.bavrasem_bx1.3364151.sql`. Размер — 5.53 MiB; контрольная
сумма SHA-256 зафиксирована как `3C4B...16E7`. Backup не копировался в
репозиторий и не должен публиковаться или коммититься.

Backup вручную импортирован в локальную схему `bitrix` уже созданного volume.
SQL-ошибок при импорте не было. Контрольные количества после импорта:

| Объект | Количество |
| --- | ---: |
| Таблицы | 422 |
| `b_user` | 1 |
| `b_iblock` | 43 |
| `b_iblock_element` | 334 |
| `b_form` | 11 |
| `b_lang` | 1 |

### Подтверждённые проверки

- Local smoke: **32/32 passed**, 3.0 min.
- Полный safe local baseline: **54/54 passed**, 0 failed/skipped/flaky, 5.2 min.
- Browser diagnostics: `console.error=0`, `pageerror=0`, ignored=0.
- Critical axe violations: 0; известные serious baseline-дефекты сохранены в `TEST_RESULTS.md` и `KNOWN_ISSUES.md`.

### Альтернативный локальный запуск

Аудит альтернатив выполнен: готового native PHP/MySQL/web runtime на машине
нет. Он больше не требуется для основного процесса, поскольку Docker runtime
восстановлен и проверен. Подмена приложения статической копией или прокси
действующего сайта не используется.

## Исторические блокеры и текущие ограничения

### Docker Desktop — RESOLVED

Docker Desktop завершается до запуска engine с ошибкой удаления stale OTel socket:

`C:\Users\firep\AppData\Local\Docker\run\userAnalyticsOtlpHttp.sock`

Файл существовал как недоступная reparse point. Безопасная попытка
переименования, точечный `Remove-Item` и удаление reparse metadata через
`fsutil` ранее не удавались; factory reset и сброс Docker data не выполнялись.
Сейчас Docker Desktop 4.97 и engine 29.9.2 работают, поэтому блокер закрыт.

### Данные Bitrix — RESOLVED для локального safe baseline

На момент первичного аудита в project root не было дампа; были найдены только
установочные SQL-файлы модулей. Позже чувствительный Beget backup был найден
вне Git и вручную импортирован в локальную схему без SQL-ошибок. Локальная
копия достаточна для safe baseline, но backup остаётся чувствительным и не
является публикуемым fixture.

## Оставшиеся действия

1. Фактически запустить production-like профиль на порту 8085 и повторить health/smoke.
2. Выполнить state-changing forms/cart/auth scenarios только на локальной БД с контролем побочных эффектов.
3. Не публиковать и не коммитить исходный backup или локальный `.env`.
4. Завершить Paper Gate после восстановления MCP-квоты; до этого UI не начинать.
