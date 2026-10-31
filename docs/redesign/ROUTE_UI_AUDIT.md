# Аудит визуального состояния маршрутов

Последнее обновление: 2026-10-31.

Проверка выполнена по Bitrix SEF-конфигурации, файловым entry point, меню,
локальной БД и реальному рендеру на `http://127.0.0.1:8084`. Наличие классов
Aspro само по себе не считалось дефектом: статус отражает фактический body.
Paper-источник: `Мария's Team` → `Ралмастер` → `Дизайн`; прямых фреймов для
перечисленных ниже служебных и информационных страниц нет, поэтому для них
применяется подтверждённая общая дизайн-система.

| Route | Template / source | UI status | Required changes | Validation |
| --- | --- | --- | --- | --- |
| `/` | custom home page block | NEW / PASSED | — | redesign suite, visual capture, axe |
| `/product/` | `bitrix:news/catalog`, Paper root block | NEW / PASSED | — | product suite, exact geometry |
| `/product/*/` | shared `section_product_paper` | NEW / PASSED | — | representative dynamic route + dialog |
| `/services/` | `bitrix:news/services`, Paper root block | NEW / PASSED | — | services suite, exact geometry |
| `/services/*/` | shared `section_service_paper` | NEW / PASSED | — | representative dynamic routes + dialogs |
| `/painting/`, `/painting/poroshkovaya-pokraska-metalla/` | shared powder page block | NEW / PASSED | — | desktop/mobile, form geometry |
| `/company/` | Paper production page block | NEW / PASSED | — | production suite, exact geometry |
| `/company/partners/` | dynamic `partners-paper` | NEW / PASSED | — | 7 CMS elements, logos, axe |
| `/projects/`, `/projects/*/` | project list/detail/Symbol templates | NEW / PASSED | — | listing + two detail variants |
| `/contacts/` | Paper contact page block | NEW / PASSED | — | safe UI suite; POST intentionally not sent |
| `/company/reviews/` | shared `ralmaster-content` / IBlock 14 | NEW / PASSED | — | 4 dynamic items, legacy intro removed |
| `/company/staff/`, `/company/staff/*/` | shared list/detail / IBlock 15 | NEW / PASSED | — | 6 items + representative detail |
| `/company/vacancy/`, `/company/vacancy/*/` | shared list/detail / IBlock 16 | NEW / PASSED | — | 5 items + representative detail |
| `/info/news/`, `/info/news/*/` | shared list/detail / IBlock 34 | NEW / PASSED | — | 4 items, year filter, representative detail |
| `/info/articles/`, `/info/articles/*/` | `ralmaster-blog` list/section/detail / IBlock 37 | NEW / PASSED | — | root, section and representative detail |
| `/info/faq/` | shared accessible FAQ / IBlock 17 | NEW / PASSED | — | 5 dynamic questions, keyboard `<details>` |
| `/info/sales/`, `/info/sales/*/` | shared list/detail / IBlock 35 | NEW / PASSED | — | 9 items + representative detail |
| `/search/` | `bitrix:search.page/search` | LEGACY / TODO | Новый shell, поля, results/empty state | HTTP 200; legacy body confirmed |
| `/search/map.php` | `bitrix:main.map/.default` | LEGACY / TODO | Новый shell и типографика карты сайта | HTTP 200; legacy body confirmed |
| `/cart/` | `aspro:basket.allcorp2/.default` | LEGACY / TODO | Restyle basket states without changing data/API | HTTP 200; legacy body confirmed |
| `/cart/order/` | `aspro:basket.allcorp2/order` | REDIRECT / PARTIAL | Validate intended empty-cart redirect; restyle reachable order state | Redirects to `/cart/` locally |
| `/login/` | Bitrix auth via `NEED_AUTH` | LEGACY / TODO | New auth shell, labels, errors and focus states | HTTP 200; legacy auth body confirmed |
| `/cabinet/`, `/cabinet/registration/`, `/cabinet/forgot-password/` | cabinet feature gate | DISABLED / PASSED | Do not create unreachable fake UI | All redirect to `/`; not an independent public body |
| `/products/` | legacy `bitrix:catalog/.default` | LEGACY / TODO | Confirm canonical redirect to `/product/` | HTTP 200; duplicate legacy catalog |
| `/news/` | legacy `bitrix:news/.default` | LEGACY / TODO | Confirm canonical redirect to `/info/news/` | HTTP 200; duplicate legacy news |
| `/landings/` | `bitrix:news/landings`, IBlock 43 | LEGACY / TODO | Confirm business purpose; redesign or redirect | HTTP 200; legacy body confirmed |
| unknown route | Bitrix 404 | FUNCTIONAL / TODO UI | Align 404 composition with new system | Real unknown URL returns HTTP 404 |

## Итог текущего прохода

- Старый body устранён на семи информационных/корпоративных разделах и их
  доступных detail/section routes через общие Bitrix-шаблоны.
- Динамика IBlock, SEF URL, пагинация, фильтр новостей по году и существующие
  hooks форм отзывов/вопросов сохранены.
- Cabinet-маршруты не являются отдельными публичными страницами при текущей
  конфигурации и корректно перенаправляются на главную.
- Следующая группа исправлений: search/map, auth, cart, 404 и решение по трём
  legacy-дубликатам `/products/`, `/news/`, `/landings/`.
