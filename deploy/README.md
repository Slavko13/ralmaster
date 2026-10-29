# Развёртывание тестового РАЛМАСТЕР на VPS

Скрипт `ralmaster-vps-deploy.sh` предназначен для отдельного staging-сайта
`test.ral-master.ru` на том же VPS, где Caddy уже обслуживает другой сайт.
Он не использует отдельный публичный порт приложения. Для системного Caddy
Apache публикуется только на `127.0.0.1:8081`. Если Caddy уже работает в
Docker, скрипт подключает web-контейнер к существующей proxy-сети под уникальным
alias и маршрутизирует домен напрямую на этот alias.

## Что требуется до запуска

1. В Beget создать `test.ral-master.ru` и выбрать «Направить домен на VPS».
   A-запись должна указывать на `176.98.184.23`.
2. Подготовить доступ VPS к приватному Git-репозиторию. Предпочтителен SSH
   deploy key; токен нельзя помещать в URL или сам скрипт.
3. Положить в `/root/ralmaster/` два чувствительных backup-файла:
   - базу: `.sql`, `.sql.gz`, `.dump` или `.dump.gz`;
   - файловый архив Beget: `.tar`, `.tar.gz`, `.tgz` или `.zip`.

Файловый архив обязателен для первой установки. Git намеренно не содержит
`bitrix/` и `upload/`; локальный `upload` занимает около 111 МБ. Скрипт берёт
из архива только Bitrix core, `upload` и отсутствующий локальный include, не
перезаписывая актуальный редизайн из Git.

## Перед первым запуском

Сначала на VPS посмотрите фактические Docker-порты и способ запуска Caddy:

```bash
docker ps --format 'table {{.Names}}\t{{.Image}}\t{{.Ports}}'
sudo ss -lntp
sudo systemctl status caddy --no-pager
```

Автоматическая настройка proxy поддерживает Caddy как системный сервис и Caddy
в Docker. Для контейнерного Caddy скрипт находит его Compose-сеть и
смонтированный `/etc/caddy/Caddyfile`, создаёт отдельный Compose override для
РАЛМАСТЕР и проверяет конфигурацию внутри работающего proxy-контейнера. Если
запущено несколько Caddy или контейнер подключён к нескольким сетям, передайте
точный выбор через `--caddy-container` и `--caddy-network`.

## Установка скрипта

Скопируйте его на VPS отдельно от Git checkout и проверьте перед запуском:

```bash
mkdir -p /root/ralmaster
chmod 700 /root/ralmaster
cp ralmaster-vps-deploy.sh /root/ralmaster/deploy.sh
chmod 700 /root/ralmaster/deploy.sh
less /root/ralmaster/deploy.sh
```

## Первый запуск

Замените URL и ветку на фактические значения:

```bash
/root/ralmaster/deploy.sh \
  --repo 'git@github.com:OWNER/REPOSITORY.git' \
  --ref 'feature/full-paper-redesign' \
  --domain 'test.ral-master.ru' \
  --port 8081 \
  --project 'ralmaster-test' \
  --db-backup '/root/ralmaster/database.sql.gz' \
  --files-backup '/root/ralmaster/files.tar.gz'
```

Без точных путей скрипт попробует найти по одному подходящему DB- и
file-архиву непосредственно в `/root/ralmaster`. При нескольких кандидатах он
остановится и попросит передать пути явно.

## Что делает скрипт

1. Проверяет root, Docker/Compose, host Caddy и свободный loopback-порт.
2. Устанавливает только недостающие базовые утилиты через `apt-get`.
3. Клонирует Git в `/root/ralmaster/app`; повторный запуск делает только
   `fetch` и fast-forward merge, если tracked checkout чистый.
4. Восстанавливает отсутствующие `bitrix/` и `upload/` из файлового архива.
5. Создаёт `/root/ralmaster/.env.production` с правами `600` и случайными
   паролями, не помещая секреты в Git.
6. Поднимает отдельную MySQL 8.0 и импортирует SQL только если в схеме 0 таблиц.
   Поверх непустой базы импорт никогда не выполняется.
7. Собирает `compose.production.yml`, проверяет health endpoint и применяет
   идемпотентную миграцию формы `ralmaster_estimate_s1`.
8. Добавляет в Caddy отдельный маркированный блок, валидирует конфигурацию,
   делает backup и только затем reload. При ошибке Caddyfile восстанавливается.
   Для Docker Caddy существующий сайт и его контейнер не пересоздаются.
9. Проверяет локальный маршрут и публичный HTTPS. Если DNS ещё не обновился,
   контейнеры остаются рабочими, а скрипт выводит предупреждение.

## Обновление из Git

Повторите ту же команду. SQL не импортируется, файловый архив не
восстанавливается, если `bitrix/` и `upload/` уже присутствуют. Для осознанного
повторного восстановления файлов добавьте `--restore-files`.

## Диагностика

```bash
docker compose \
  --env-file /root/ralmaster/.env.production \
  -p ralmaster-test \
  -f /root/ralmaster/app/compose.production.yml \
  -f /root/ralmaster/compose.caddy-proxy.yml \
  ps --all

docker compose \
  --env-file /root/ralmaster/.env.production \
  -p ralmaster-test \
  -f /root/ralmaster/app/compose.production.yml \
  -f /root/ralmaster/compose.caddy-proxy.yml \
  logs --tail 100 web db

docker logs --tail 100 itsoft-test-caddy-1
curl -I https://test.ral-master.ru/
```

Секреты и backup-файлы должны оставаться в `/root/ralmaster` с правами root и
не должны добавляться в Git. Скрипт не изменяет production-инфраструктуру
`ral-master.ru` и не удаляет Docker volumes.
