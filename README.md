# RALMASTER local development

The application is a PHP 8.2 / 1C-Bitrix monolith using the Aspro Allcorp2
template. Docker configuration is local-only and does not alter production
infrastructure.

## Prerequisites

1. Docker Desktop with the Linux engine running.
2. A local `.env` copied from `.env.example` with local-only passwords.
3. A local Bitrix database seed. Keep any hosting backup outside Git and treat
   it as sensitive data.

The verified workstation currently uses Docker Desktop 4.97 with engine
29.9.2. The development image builds successfully and both `db` and `web`
reach healthy state. A sensitive hosting backup has already been imported into
the local `bitrix` schema without SQL errors; it was not copied into the
repository.

## Development mode

```powershell
Copy-Item .env.example .env
docker compose up --build
```

The ignored local `.env` currently pins separate
`DEV_COMPOSE_PROJECT_NAME` / `PROD_COMPOSE_PROJECT_NAME` values together with
`APP_PORT=8084` and `APP_PROD_PORT=8085`. The development site is therefore exposed at
`http://127.0.0.1:8084`. PHP files, CSS, and JavaScript are bind-mounted, so
changes are reflected without a separate hot-reload process.

## Production-like mode

```powershell
# First run only: clone the already-imported dev schema into an isolated volume.
.\docker\clone-db-to-production.ps1

# First and subsequent application starts.
docker compose -p ralmaster-redesign-production -f compose.production.yml up --build -d --wait
```

This mode enables production opcache settings and mounts application code
read-only except for the Bitrix caches, logs, and uploads required at runtime.
It is intended for local acceptance checks, not deployment. The resulting
runtime image is not an immutable deployment artifact because the application
source is supplied by a read-only bind mount. It is exposed at
`http://127.0.0.1:8085` with the current `.env`.

Always keep the explicit `-p ralmaster-redesign-production` argument in
production-like commands. It guarantees that containers, networks, images,
and volumes remain separate from development even if a shell defines the
special `COMPOSE_PROJECT_NAME` variable. The clone script streams data inside
Docker, writes no SQL dump to the host, refuses to overwrite a non-empty
destination, temporarily joins the destination DB to the development network,
disconnects it afterwards, and verifies the cloned table count.

The profile was verified on 2026-10-28: both services reached `healthy`, the
isolated schema contained 422 tables, `/healthz.php`, `/`, the main template
CSS and the logo returned HTTP 200, browser diagnostics passed 4/4, and the
production-like smoke suite passed 32/32; the final repeat after force-recreate
also passed 32/32 in 2.6 min with exit code 0.

## Import into an existing database volume

MySQL entrypoint files run only when the data directory is first initialized.
For an already-created volume, keep the sensitive SQL file outside the
repository and stream it manually. The command below deliberately uses a
placeholder path and the container's existing environment variables; do not
put a real password or backup filename into reusable commands or shell history.

```powershell
cmd /c type "<absolute-path-to-sensitive-backup.sql>" | docker compose exec -T db sh -lc 'exec mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"'
```

After import, verify the command exit code, inspect the container logs, check
table counts, and request both the health endpoint and `/`. Never commit the
backup, copy it into tracked paths, or expose its credentials/content.

## Stop and reset

```powershell
docker compose down
docker compose -p ralmaster-redesign-production -f compose.production.yml down
```

To recreate a local database from a new dump, explicitly remove the relevant
named volume after stopping the stack:

```powershell
docker compose down --volumes
```

This deletes only local Docker data and must not be run against production.
To discard only the disposable production-like clone, use the fully scoped
command below and rerun `docker/clone-db-to-production.ps1` afterwards:

```powershell
docker compose -p ralmaster-redesign-production -f compose.production.yml down --volumes
```

## Diagnostics

```powershell
docker compose ps --all
docker compose logs --no-color --tail 100 web
docker compose logs --no-color --tail 100 db
curl.exe http://127.0.0.1:8084/healthz.php
curl.exe -I http://127.0.0.1:8084/
docker compose -p ralmaster-redesign-production -f compose.production.yml ps --all
docker compose -p ralmaster-redesign-production -f compose.production.yml logs --no-color --tail 100 web
docker compose -p ralmaster-redesign-production -f compose.production.yml logs --no-color --tail 100 db
curl.exe http://127.0.0.1:8085/healthz.php
curl.exe -I http://127.0.0.1:8085/
```

The health endpoint proves that PHP extensions and the database connection are
available. A successful health response does not prove that the Bitrix schema
has been imported; always verify `/` and run the Playwright baseline as well.

## Browser baseline

Install Chromium once, then run either the local or read-only live baseline:

```powershell
npm install
npm run test:e2e:install
$env:BASE_URL='http://127.0.0.1:8084'; npm run test:e2e:smoke
$env:BASE_URL='http://127.0.0.1:8084'; npm run test:e2e
npm run test:e2e:baseline
```

`npm run test:e2e` targets `BASE_URL` and defaults to the development Compose
address `http://127.0.0.1:8084`. Override it explicitly for production-like
checks on `http://127.0.0.1:8085` or another isolated environment.
`npm run test:e2e:baseline` targets `BASELINE_URL` (default
`https://ral-master.ru`) and must remain read-only or use invalid input only.
The final verified development results are 61/61 redesign tests, 22/22 updated
baseline tests, and 32/32 route smoke tests. Successful state-changing
form/mail/CRM scenarios remain intentionally not run. Engineering status and
remaining blockers are tracked in `docs/redesign/`.
