# Local database seed

Place a sanitized Bitrix database dump in this directory before the first
`docker compose up --build` run. Supported MySQL image inputs include `.sql`
and `.sql.gz` files. Dump files are intentionally ignored by Git.

The current workspace does not contain the production database. Starting the
stack without a dump verifies PHP, Apache, and MySQL connectivity, but the
Bitrix application itself cannot render its real pages until the schema and
content are imported.
