# RALMASTER local development

The application is a PHP 8.2 / 1C-Bitrix monolith using the Aspro Allcorp2
template. Docker configuration is local-only and does not alter production
infrastructure.

## Prerequisites

1. Docker Desktop with the Linux engine running.
2. A sanitized dump of the `bavrasem_bx1` database placed in
   `docker/db/init/` before the first database volume is created.
3. A local `.env` copied from `.env.example` with local-only passwords.

## Development mode

```powershell
Copy-Item .env.example .env
docker compose up --build
```

The site is exposed at `http://127.0.0.1:8080`. PHP files, CSS, and JavaScript
are bind-mounted, so changes are reflected without a separate hot-reload
process.

## Production-like mode

```powershell
docker compose -f compose.production.yml up --build
```

This mode enables production opcache settings and mounts application code
read-only except for the Bitrix caches, logs, and uploads required at runtime.
It is intended for local acceptance checks, not deployment. The resulting
runtime image is not an immutable deployment artifact because the application
source is supplied by a read-only bind mount.

## Stop and reset

```powershell
docker compose down
docker compose -f compose.production.yml down
```

To recreate a local database from a new dump, explicitly remove the relevant
named volume after stopping the stack:

```powershell
docker compose down --volumes
```

This deletes only local Docker data and must not be run against production.

## Diagnostics

```powershell
docker compose ps --all
docker compose logs --no-color --tail 100 web
docker compose logs --no-color --tail 100 db
curl.exe http://127.0.0.1:8080/healthz.php
```

The health endpoint proves that PHP extensions and the database connection are
available. A successful health response does not prove that the Bitrix schema
has been imported; always verify `/` and run the Playwright baseline as well.

## Browser baseline

Install Chromium once, then run either the local or read-only live baseline:

```powershell
npm install
npm run test:e2e:install
npm run test:e2e
npm run test:e2e:baseline
```

`npm run test:e2e` targets `BASE_URL` (default `http://127.0.0.1:8080`).
`npm run test:e2e:baseline` targets `BASELINE_URL` (default
`https://ral-master.ru`) and must remain read-only or use invalid input only.
The engineering status and known blockers are tracked in `docs/redesign/`.
