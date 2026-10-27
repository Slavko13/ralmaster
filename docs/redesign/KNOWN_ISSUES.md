# Известные проблемы и ограничения

Последнее обновление: 2026-10-27.

| ID | Приоритет | Область | Проблема | Статус / действие |
| --- | --- | --- | --- | --- |
| ENV-001 | RESOLVED | Docker | Docker Desktop исторически падал при удалении `userAnalyticsOtlpHttp.sock` | Закрыто: Docker Desktop 4.97/engine 29.9.2 работает; factory reset не выполнялся |
| ENV-002 | RESOLVED | WSL | Первичный `wsl.exe --status` возвращал `Wsl/E_ACCESS_DENIED` | Закрыто для Docker workflow: Linux engine доступен и dev runtime проверен |
| ENV-003 | MEDIUM | Production-like image | Исходники подключаются read-only bind mount, а не запекаются в автономный image | Допустимо для локальной приёмки; не считать deployment artifact |
| ENV-004 | RESOLVED | Native fallback | PHP/MySQL/web server и альтернативный container runtime на машине отсутствуют | Native fallback больше не требуется: Docker runtime восстановлен |
| ENV-005 | MEDIUM | Production-like run | Compose-конфигурация валидна, но фактический запуск на 8085 не выполнен | Запустить и повторить health/smoke до UI-итераций |
| DATA-001 | RESOLVED | Bitrix DB | На первичном аудите backup не был найден в project root | Закрыто: чувствительный Beget backup вне Git импортирован без SQL-ошибок; 422 таблицы |
| DATA-002 | HIGH | Backup security | Использованный hosting backup содержит чувствительные данные | Оставлять вне Git, не публиковать, не копировать в tracked paths |
| TEST-001 | MEDIUM | State-changing flows | Forms/cart/auth не входят в safe baseline | Выполнить отдельно только локально с контролем побочных эффектов |
| PAPER-001 | BLOCKER | Paper MCP | Достигнут недельный лимит, восстановление через три дня от аудита | Продолжить read-only аудит после reset |
| PAPER-002 | HIGH | Paper identity | Открыт `Cheerful breeze`, а ожидался `Polite hill` | Подтвердить целевой файл/версию |
| PAPER-003 | MEDIUM | Responsive | Нет mobile/tablet-фреймов | Зафиксировать производные responsive-решения |
| PAPER-004 | MEDIUM | States | Нет hover/focus/error/loading макетов | Спроектировать по дизайн-системе и WCAG |
| FUNC-001 | HIGH | Calculators | В репозитории не найдена логика калькуляторов | Нужна бизнес-спецификация формул и payload |
| SEC-001 | HIGH | Maps | В tracked include обнаружен hardcoded map key | Не раскрывать; вынести в env и рассмотреть ротацию |
| CONTENT-001 | MEDIUM | Painting | Повторяющийся текст и hardcoded images | Исправить в соответствующей продуктовой итерации |
| ROUTE-001 | MEDIUM | Legacy | `/products/`, `/news/`, `/landings/` могут быть устаревшими, но доступны | Не удалять без аналитики/redirect plan |
| ROUTE-002 | MEDIUM | Links | В коде есть абсолютные ссылки на `bavrasem.beget.tech` | Инвентаризировать и заменить безопасно |
| A11Y-001 | HIGH | Home | `aria-hidden-focus`: 1 узел | Исправить в shell/home iteration |
| A11Y-002 | HIGH | Home | `color-contrast`: 15 узлов | Исправить с проверкой design tokens |
| A11Y-003 | HIGH | Home | `link-name`: 6 узлов | Добавить доступные имена |
| A11Y-004 | HIGH | Home | `target-size`: 2 узла | Увеличить интерактивные зоны |
| A11Y-005 | HIGH | Service detail | `color-contrast`: 6, `link-name`: 1 | Исправить в service iteration |
| A11Y-006 | HIGH | Contacts | `color-contrast`: 10, `link-name`: 1 | Исправить в contacts iteration |
| ROUTE-003 | INFO | Forms | Direct `/form/` без form ID ожидаемо возвращает 404/not-found без fatal | Сохранить корректное поведение; передавать ID в реальном form flow |
| GIT-001 | LOW | Delivery | Git remote отсутствует | Локальные коммиты допустимы; push только после явного назначения remote |

## Исходные дефекты и новые регрессии

Accessibility, legacy links и content issues выше обнаружены до редизайна и являются baseline. Они не должны маскировать новые дефекты: любая новая critical/high регрессия блокирует переход между итерациями. При устранении baseline-проблемы её ID сохраняется в истории итерации как исправленный дефект.

## Ограничения проверок

- Live production baseline намеренно не изменяет данные.
- Expanded live baseline полностью проходит: 54/54; browser diagnostics на четырёх ключевых маршрутах чисты.
- Local smoke полностью проходит: 32/32; safe baseline — 54/54, browser diagnostics clean, critical axe = 0.
- Локальная БД доступна, но формы, корзина и auth/cabinet с изменением состояния ещё не проверены.
- Production-like профиль фактически ещё не запущен.
- Paper audit неполон до восстановления квоты.
- Production-инфраструктура не изменялась и не должна изменяться в рамках локальной подготовки.
