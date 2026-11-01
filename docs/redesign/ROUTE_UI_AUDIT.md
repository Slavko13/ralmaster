# Аудит визуального состояния маршрутов

Последнее обновление: 2026-11-01.

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
| `/product/*/` | штатный catalog section flow + `ralmaster_product_catalog.css` | NEW / PASSED | — | 7-category nav, filter, sort/views, products and basket desktop/mobile |
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
| `/search/` | `bitrix:search.page/search` + local system template | NEW / PASSED | — | Query/empty states, axe, desktop/mobile PASS |
| `/search/map.php` | `bitrix:main.map/ralmaster-system` | NEW / PASSED | — | Live menu tree, axe, desktop/mobile PASS |
| `/cart/` | `aspro:basket.allcorp2/.default` inside system shell | NEW / PASSED | — | Empty state and real product add-to-cart flow, desktop/mobile PASS |
| `/cart/order/` | `aspro:basket.allcorp2/order` | REDIRECT / PASSED EMPTY STATE | Validate filled-cart state only after safe fixture | Empty cart correctly redirects to `/cart/` |
| `/login/` | local `system.auth.authorize/.default` | NEW / PASSED SAFE STATE | Successful auth remains separate | Fields/recovery/register/social providers preserved; no POST |
| `/cabinet/`, `/cabinet/registration/`, `/cabinet/forgot-password/` | cabinet feature gate | DISABLED / PASSED | Do not create unreachable fake UI | All redirect to `/`; not an independent public body |
| `/products/` | legacy IBlock 2 entry point | 301 / PASSED | — | Redirects to `/product/`; old data retained |
| `/news/` | legacy IBlock 1 entry point | 301 / PASSED | — | Redirects to `/info/news/`; old data retained |
| `/landings/` | legacy IBlock 43 entry point | 301 / PASSED | — | Redirects to `/info/articles/`; old data retained |
| unknown route | shared system 404 | NEW / PASSED | — | Real HTTP 404, accessible actions, desktop/mobile PASS |

## Итог текущего прохода

- Старый body устранён на информационных/корпоративных и системных публичных
  маршрутах через общие локальные Bitrix-шаблоны.
- Динамика IBlock, SEF URL, пагинация, фильтр новостей по году и существующие
  hooks форм отзывов/вопросов сохранены.
- Cabinet-маршруты не являются отдельными публичными страницами при текущей
  конфигурации и корректно перенаправляются на главную.
- Search/map/auth/cart/404 прошли targeted 8/8 и связанную regression 35/35;
  route smoke после 301-редиректов прошёл 32/32.
- Открыты только намеренно не выполненные state-changing cart/auth/order
  сценарии и общие контентные/security-ограничения проекта.
