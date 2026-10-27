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
curl.exe http://127.0.0.1:8080/healthz.php
```

Удаление именованного volume БД выполняется только явно командой `docker compose down --volumes`. Это удаляет локальные Docker-данные и не должно применяться к production.

## Результаты проверки

- `docker compose config --quiet` для обоих Compose-файлов проходит успешно.
- Docker CLI `27.1.1` и Docker Compose `v2.29.1` установлены.
- Локальные PHP и Composer вне Docker отсутствуют.
- Node.js `v24.18.0` и npm `11.16.0` доступны.
- Фактическая сборка образов и запуск контейнеров пока не выполнены из-за неисправности Docker Desktop.

## Блокеры

### Docker Desktop

Docker Desktop завершается до запуска engine с ошибкой удаления stale OTel socket:

`C:\Users\firep\AppData\Local\Docker\run\userAnalyticsOtlpHttp.sock`

Файл существует как недоступная reparse point. Безопасная попытка переименования, точечный `Remove-Item` и удаление reparse metadata через `fsutil` не удались с сообщением «The file cannot be accessed by the system»; сам файл не был изменён. Дополнительно `wsl.exe --status` возвращает `Wsl/E_ACCESS_DENIED`. Factory reset и сброс Docker data не выполнялись.

### Данные Bitrix

В workspace отсутствует полный санитизированный дамп рабочей БД или Bitrix backup. Найдены только установочные SQL-файлы модулей, которых недостаточно для восстановления контента, инфоблоков, форм и настроек. Поэтому даже после восстановления Docker контейнеры можно будет поднять, но реальный сайт не будет воспроизводим до импорта дампа.

## Что требуется для снятия блокировки

1. Восстановить работу Docker Desktop/WSL, не затрагивая production-инфраструктуру.
2. Предоставить санитизированный дамп БД и поместить его в `docker/db/init/` до создания volume.
3. Создать локальный `.env` на основе `.env.example` с локальными паролями.
4. Выполнить `docker compose up --build`, проверить `/healthz.php`, `/` и затем полный Playwright baseline.
