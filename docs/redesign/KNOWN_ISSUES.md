# Известные проблемы и ограничения

Последнее обновление: 2026-10-27.

| ID | Приоритет | Область | Проблема | Статус / действие |
| --- | --- | --- | --- | --- |
| ENV-001 | BLOCKER | Docker | Docker Desktop падает при удалении `userAnalyticsOtlpHttp.sock` | Восстановить Docker/WSL; factory reset не выполнялся |
| ENV-002 | BLOCKER | WSL | `wsl.exe --status` возвращает `Wsl/E_ACCESS_DENIED` | Требуется исправление Windows/WSL доступа |
| ENV-003 | MEDIUM | Production-like image | Исходники подключаются read-only bind mount, а не запекаются в автономный image | Допустимо для локальной приёмки; не считать deployment artifact |
| ENV-004 | BLOCKER | Native fallback | PHP/MySQL/web server и альтернативный container runtime на машине отсутствуют | Нужен рабочий Docker host либо установка native runtime вместе с санитизированной БД |
| DATA-001 | BLOCKER | Bitrix DB | Нет санитизированного дампа БД/backup | Предоставить dump до первого создания Docker volume |
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
- Локальные формы, корзина, auth/cabinet и региональность не могут быть полноценно проверены без БД.
- Paper audit неполон до восстановления квоты.
- Production-инфраструктура не изменялась и не должна изменяться в рамках локальной подготовки.
