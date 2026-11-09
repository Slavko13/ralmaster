# Security Audit — РАЛМАСТЕР

Дата: 2026-11-08  
Ветка/база аудита: `feature/full-paper-redesign`, исходный HEAD `b3736c9`  
Стандарт: OWASP ASVS 4.0.3 Level 2 как чек-лист требований, не сертификат.

## 1. Резюме

Проведены ручной review PHP/JS/Apache/PHP/Compose, проверка Git history (62
коммита), dependency audit, локальные HTTP/Playwright-тесты, инвентаризация
Bitrix и пассивная проверка публичного DNS/HTTPS.

В исходном состоянии подтверждены open redirect, CSRF на изменяющих state
обработчиках, CAPTCHA oracle, опасные DOM/URL-синки, недостаточная защита
upload, публикация служебных артефактов, hardcoded legacy token и отсутствие
лимитов на сериализованный AJAX payload. Исправления внесены в исходники и
прошли локальную регрессию.

Релиз пока нельзя считать security-ready: production HTTPS не отвечал во время
проверки, Bitrix `main`/`security` устарели, а восстановленная БД показывает
отключённые/не настроенные MFA, event log, proactive filter, CAPTCHA регистрации
и восстановления. Эти пункты требуют владельца Bitrix/Beget и проверки уже на
актуальном production/staging.

## 2. Scope и ограничения

Проверено:

- публичные PHP entry points и custom/Aspro template code;
- auth/redirect, city/region, forms, uploads, CAPTCHA, basket, main-page AJAX;
- Apache/PHP и production-like Compose;
- npm dependencies и наличие других manifests;
- tracked files, ignored sensitive artifacts и Git history;
- локальный runtime `http://127.0.0.1:8084` и production-like `:8085`;
- пассивные DNS/HEAD проверки `ral-master.ru`.

Не выполнялось:

- активное сканирование, brute force, fuzzing, DDoS/load или эксплуатация
  production;
- изменение production/Beget/Bitrix admin;
- отправка реальной почты/CRM-заявок;
- полная верификация всех 286 требований ASVS;
- проверка панели Beget, DKIM private config, backups и WAF evidence.

## 3. Инвентаризация

| Компонент | Найдено | Актуальность/вывод |
| --- | --- | --- |
| PHP | 8.2.28 | локальный контейнер |
| MySQL | 8.0.40 | локальный контейнер |
| Bitrix `main` | 25.100.500 (2025-06-07) | отстаёт от опубликованной 26.1000.0 |
| Bitrix `security` | 24.0.300 (2025-06-16) | отстаёт от опубликованной 26.300.0 |
| Aspro Allcorp2 | 1.2.32 (2026-03-11) | latest не подтверждён публично; проверить в vendor portal |
| npm | Playwright/Lighthouse/axe и fonts | `npm audit`: 0 известных уязвимостей после fix |
| Composer и прочие manifests | отсутствуют | отдельного package audit нет |

Источник актуальных версий Bitrix: официальный список версий
`https://dev.1c-bitrix.ru/docs/versions.php`.

## 4. Подтверждённые и исправленные findings

### SEC-001 — Open redirect и недоверенные URL (High, CWE-601)

**Доказательство:** `ajax/auth.php`, `login/index.php`, regional city endpoints
принимали URL/host из запроса или конфигурации без строгого same-origin
allowlist.

**Исправлено:** добавлены `ralmaster_safe_local_path()` и
`ralmaster_safe_configured_host()`, повторное URL-decoding, запрет `//`,
backslash, control chars, schemes/IP hosts и чрезмерной длины. JS получает URL
через `json_encode`, подписи вставляются через `.text()`.

### SEC-002 — CSRF и недостаточная авторизация (High, CWE-352/862)

**Доказательство:** option save, basket mutations, CAPTCHA refresh и catalog
AJAX принимали GET/POST без обязательного Bitrix sessid; main-page options
использовал `NOT_CHECK_PERMISSIONS`.

**Исправлено:** state-changing операции переведены на POST +
`check_bitrix_sessid()`, административный save требует администратора,
`NOT_CHECK_PERMISSIONS` удалён. Клиент отправляет `sessid`.

### SEC-003 — CAPTCHA answer oracle и abuse (Medium, CWE-204/307)

**Доказательство:** endpoint позволял проверять ответ и обновлять CAPTCHA без
CSRF/rate limit.

**Исправлено:** oracle возвращает 410/false; refresh — только POST+sessid,
проверяет SID, `Cache-Control: no-store`, лимит 20 операций/5 минут на сессию.

### SEC-004 — XSS/DOM injection в региональном UI (High, CWE-79)

**Доказательство:** регион/URL формировались строковой конкатенацией и HTML.

**Исправлено:** JSON encoding, allowlist URL/host и DOM `.text()` вместо HTML
конкатенации для недоверенных значений.

### SEC-005 — Недостаточная валидация вложений (High, CWE-434)

**Доказательство:** estimate form проверяла в основном расширение/размер;
`upload/` не имел достаточного независимого запрета выполнения.

**Исправлено:** allowlist расширений, имя/путь/размер/число файлов, source upload,
MIME/signature, image dimensions, OLE magic, DOCX/XLSX ZIP structure, DWG/DXF;
SVG исключён. В `upload/.htaccess` запрещены handlers, executable/double
extensions, ExecCGI и indexes; Apache добавляет второй уровень запрета.

**Остаток:** для production рекомендуется хранение вне webroot и AV/CDR.

### SEC-006 — Публичные служебные/backup-файлы (High, CWE-200/538)

**Доказательство:** tracked `.htaccess_back`; в рабочей копии есть ignored
`.env`, SQL dump, logs и archives.

**Исправлено:** `.htaccess_back` удалён из tracked tree и игнорируется;
Apache/.htaccess запрещают dotfiles, env, SQL, logs, backups, Docker/deploy/test
артефакты. E2E проверяет восемь типичных путей.

**Остаток:** ignored-файлы остаются риском рабочей станции и должны быть
перенесены в шифрованное backup-хранилище/удалены по retention policy.

### SEC-007 — Hardcoded legacy Instagram token (High, CWE-798)

**Доказательство:** fallback token был в `include/mainpage/comp_instagramm.php`.

**Исправлено в коде:** fallback удалён; значение теперь только из Bitrix option.

**Обязательное внешнее действие:** отозвать старый token, проверить provider
audit log, создать новый с минимальными правами. Удаление из HEAD не отменяет
компрометацию и наличие значения в Git history.

### SEC-008 — HTTP/PHP disclosure и отсутствующие baseline headers (Medium)

**Исправлено:** `nosniff`, `frame-ancestors`, X-Frame-Options,
Referrer-Policy, Permissions-Policy, no-store для auth/cart/form/admin;
production-like Apache скрывает версию/подпись и TRACE; PHP скрывает errors и
версию; `X-Powered-By` и Bitrix `X-Powered-CMS` удаляются на уровне web server.

**Остаток:** полный CSP не включён из-за legacy inline JS; нужен поэтапный
`Content-Security-Policy-Report-Only` с nonce/hash и telemetry. HSTS следует
включать на Beget только после восстановления и проверки HTTPS на домене.

### SEC-009 — Сериализованный AJAX payload и resource exhaustion (High)

**Доказательство:** form/catalog endpoints принимали сериализованные параметры
без строгого размера/структуры/rate limit.

**Исправлено:** strict base64, лимиты длины, array-only payload,
`allowed_classes=false` в Aspro serializer, глубина/число элементов/длина строк,
allowlist filter/template, catalog session rate 30/min.

### SEC-010 — Container/runtime hardening (Medium)

**Исправлено в локальном production-like профиле:** обязательные DB passwords,
loopback publish, `no-new-privileges`, pids/CPU/RAM limits, grace periods,
read-only code, ограниченные writable mounts, request timeouts/limits и
production PHP flags.

**Важно:** эти меры не разворачиваются на Beget автоматически и не заменяют
настройки хостинга.

### SEC-011 — Уязвимые transitive npm dev dependencies (Medium)

**Доказательство:** до обновления `npm audit` находил 4 moderate findings.

**Исправлено:** безопасное transitive update lockfile; итоговый `npm audit` —
0 известных уязвимостей.

## 5. Подтверждённые незакрытые/внешние findings

### SEC-101 — Устаревшие Bitrix core/security (High)

Установлены `main 25.100.500`, `security 24.0.300`; официальный каталог на дату
аудита публикует `main 26.1000.0`, `security 26.300.0`. Автообновление не
выполнялось: требуется действующая лицензия, backup, staging и проверка
совместимости Aspro/custom code.

### SEC-102 — Защитные настройки Bitrix отключены/не подтверждены (High)

Локальная восстановленная БД показывает:

- регистрация включена (`new_user_registration=Y`), CAPTCHA регистрации и
  восстановления выключена;
- login success/fail/password change event logging выключен;
- MFA mandatory, proactive filter, antivirus action, DB sessions, redirect
  protection и activity control не настроены (`unset`).

Это подтверждение для локального snapshot, не доказательство текущего
production. Владелец должен проверить и включить настройки в актуальной панели.

### SEC-103 — Production HTTPS недоступен (High / release blocker)

На 2026-11-08 DNS `ral-master.ru` возвращал `45.130.41.192`, но пассивный
`HEAD https://ral-master.ru/` завершился timeout через 10 секунд. TLS/headers
production поэтому не подтверждены.

После восстановления TLS проверить redirect HTTP→HTTPS, цепочку сертификата,
срок действия и включить `Strict-Transport-Security` сначала с безопасным
коротким `max-age`; `includeSubDomains`/preload — только после инвентаризации
всех поддоменов.

### SEC-104 — Edge DDoS/WAF/logging/backups Beget не подтверждены (High)

Исходный код не доказывает параметры сети/хостинга. Нужны evidence из панели
или поддержки Beget: WAF/anti-DDoS, rate limiting, retention логов, backup
частота и успешный restore test.

### SEC-105 — Email authentication неполна (Medium)

Пассивная DNS-проверка: SPF присутствует (`redirect=beget.com`), MX — Beget,
`_dmarc.ral-master.ru` отсутствует. DKIM требует проверки selector в панели.
Рекомендуется начать DMARC с `p=none` + контролируемым report mailbox, затем
ужесточать после анализа.

### SEC-106 — Local sensitive artifacts (Medium)

Ignored `.env`, SQL dump, logs и archives не попали в tracked tree, но остаются
на рабочей станции. Необходимо переместить/зашифровать/удалить согласно сроку
хранения и проверить резервные копии IDE/cloud sync.

## 6. Secret и history scan

- проверены tracked tree и 62 Git commits на high-confidence AWS/GitHub/private
  key/password/token patterns и подозрительные имена;
- private keys и high-confidence AWS/GitHub credentials не обнаружены;
- обнаружен legacy Instagram token, удалён из HEAD; требуется revoke;
- `.env.example` допустим и не содержит секретов;
- `.htaccess_back` удалён;
- отсутствие совпадения regex не является доказательством отсутствия всех
  секретов; provider-side inventory остаётся обязательным.

## 7. ASVS L2 coverage

| Область | Результат | Комментарий |
| --- | --- | --- |
| V1 Architecture | частично | threat model создан; production evidence неполна |
| V2 Authentication | blocked | MFA/rate controls требуют Bitrix admin/Beget |
| V3 Session | частично | strict/HttpOnly/SameSite в profile; Bitrix DB session/TTL unset |
| V4 Access Control | частично passed | admin save закрыт; полный role matrix не тестировался |
| V5 Validation | passed для изменённых endpoints | redirect/region/form/catalog bounds и encoding |
| V6 Cryptography | blocked | TLS production недоступен; secrets rotation внешняя |
| V7 Logging | failed в snapshot | auth/security event logging выключен |
| V8 Data Protection | частично | no-store/ignored files; retention/PII process внешние |
| V9 Communications | blocked | production TLS timeout |
| V10 Malicious Code | частично | dependency/history scan; Bitrix/Aspro update требуется |
| V11 Business Logic | частично | basket/forms/CAPTCHA abuse; end-to-end owner workflow не тестировался |
| V12 Files | частично passed | upload defense-in-depth; AV/CDR/out-of-webroot отсутствуют |
| V13 API/Web | частично passed | CSRF/method/limits на custom endpoints |
| V14 Configuration | частично passed | local headers/runtime; Beget config неизвестна |

## 8. Проверки и результаты

| Проверка | Результат |
| --- | --- |
| PHP helper regression | PASSED |
| PHP lint изменённых security-файлов | PASSED |
| Playwright security suite | 14/14 PASSED |
| Existing smoke suite | 32/32 PASSED после всех code/config fixes |
| `npm audit` | 0 vulnerabilities |
| `git diff --check` | PASSED, только ожидаемые CRLF warnings |
| Dev runtime | `db`/`web` healthy на `127.0.0.1:8084` |
| Production-like runtime | `db`/`web` healthy на `127.0.0.1:8085`; headers verified |
| Production passive HTTPS | BLOCKED: connection timeout |

Команды:

```bash
npm run test:security
npm run audit:security:config
npm run test:e2e:smoke
npm audit
git diff --check
```

## 9. Изменённые security-компоненты

- helpers/init: `local/php_interface/include/ralmaster_security.php`, `init.php`;
- endpoints: `ajax/auth.php`, `login/index.php`, CAPTCHA/city/form/options;
- basket/catalog client-server calls and `general.js`;
- estimate upload validation;
- `.htaccess`, `upload/.htaccess`, Apache/PHP/Compose hardening;
- npm lockfile и security tests;
- удаление tracked `.htaccess_back` и legacy token fallback.

## 10. Release decision

**Статус: BLOCKED до внешних действий.** Кодовые high-risk findings исправлены
и локальная security suite проходит, но обязательны:

1. восстановить production HTTPS и снять TLS/header evidence;
2. обновить Bitrix/Aspro через staging;
3. включить MFA, event log, proactive protection, registration/recovery abuse
   controls;
4. revoke legacy token;
5. подтвердить Beget WAF/anti-DDoS/backups/logging;
6. настроить DMARC/DKIM и выполнить финальный smoke/security gate после deploy.

## 11. Ссылки

- OWASP ASVS: `https://owasp.org/projects/asvs`
- OWASP File Upload Cheat Sheet:
  `https://cheatsheetseries.owasp.org/cheatsheets/File_Upload_Cheat_Sheet.html`
- OWASP Denial of Service Cheat Sheet:
  `https://cheatsheetseries.owasp.org/cheatsheets/Denial_of_Service_Cheat_Sheet.html`
- Bitrix official versions: `https://dev.1c-bitrix.ru/docs/versions.php`
