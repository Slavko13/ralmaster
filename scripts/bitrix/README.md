# RALMASTER Bitrix migrations

## Estimate request form

`migrate-ralmaster-estimate-form.php` prepares a dedicated Bitrix Web Form with
SID `ralmaster_estimate_s1`. It does not reuse or change the existing Aspro
service form. The form contains:

- optional `NAME`;
- required `CONTACT` accepting a phone number or email;
- optional `TASK`;
- optional `FILE`;
- hidden `CONTEXT` for the originating page.

The server handler requires `TASK` or `FILE`, validates the contact, limits an
upload to 20 MB, and permits only PDF, PNG/JPEG/WebP, DOC/DOCX, XLS/XLSX, DWG,
and DXF. Aspro's existing consent handler continues to enforce the consent
checkbox when the form is submitted through `aspro:form.allcorp2`.

### Commands

Run commands inside the development web container so the script uses the
isolated Docker database:

```powershell
docker compose exec -T web php scripts/bitrix/migrate-ralmaster-estimate-form.php
docker compose exec -T web php scripts/bitrix/migrate-ralmaster-estimate-form.php --verify
docker compose exec -T web php scripts/bitrix/migrate-ralmaster-estimate-form.php --apply
```

The default mode is a read-only dry run. `--verify` is also read-only and fails
unless the installed form matches the expected schema. Only `--apply` writes.
Re-running `--apply` is safe: a matching target is a no-op; a target with a
different schema causes an error and is never modified.

The source form is resolved by SID
`aspro_allcorp2_order_services_s1`; no environment-specific numeric form ID is
used. Source fields, results, statuses, and mail templates are never changed or
deleted. A new default administrative mail template targets
`#DEFAULT_EMAIL_FROM#`; it does not send a copy to the visitor.

### Before applying

1. Confirm that Docker development is using the isolated local database.
2. Take a database snapshot according to the project's Docker backup procedure.
3. Run the default dry run and review the five planned fields.
4. Use `--apply`, then run `--verify`.
5. Test submission only with local mail/CRM side effects disabled or captured.

### Rollback

There is deliberately no automatic destructive rollback. If the new form must
be removed, first confirm in Bitrix admin that it has zero results, retain the
database snapshot, and delete only the form whose SID is
`ralmaster_estimate_s1`. Never delete or repurpose the existing Aspro forms.
