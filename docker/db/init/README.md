# Local database seed

Place only an approved sanitized Bitrix database dump in this directory before
the first `docker compose up --build` run. Supported MySQL image inputs include
`.sql` and `.sql.gz` files. Dump files are intentionally ignored by Git.

MySQL entrypoint seeds run only when the database volume is first initialized.
For an existing volume, keep the source backup outside the repository and
stream it into the running container:

```powershell
cmd /c type "<absolute-path-to-sensitive-backup.sql>" | docker compose exec -T db sh -lc 'exec mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"'
```

The placeholder must be replaced locally. Do not record a real password or
backup filename in reusable command examples or shell history. After import,
verify the exit code, database/container logs, expected table counts,
`/healthz.php`, and `/`.

The current local volume was populated manually from a sensitive Beget backup
kept outside Git. The import completed without SQL errors; no backup file was
copied into this directory or committed.
