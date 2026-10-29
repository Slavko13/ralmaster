# Сопоставление Paper и существующего сайта

## Карта реализации

| Paper-фрейм | Текущий маршрут/сценарий | Состояние | Основная задача |
| --- | --- | --- | --- |
| Главная страница | `/` | Есть, собирается через Bitrix/Aspro | Перестроить композицию, сохранив динамический контент и навигацию |
| Продукция | `/product/` | Есть | Новый каталог и категории; сохранить SEF, фильтры, detail links |
| Металлообработка | `/services/` и detail pages | Есть частично | Сопоставить контент БД с новой услугой/лендингом |
| Расчёт гибки металла | `/services/metal-bending/` + modal/form flow | Динамический route IBlock 40; Paper-фрейм является формой запроса, не калькулятором | Route-specific CTA, file upload, contact phone-or-email и server-side правило `описание || файл` |
| Порошковая окраска | `/painting/`, `/painting/poroshkovaya-pokraska-metalla/` | Есть | Заменить старый контентный шаблон, сохранить URL |
| О производстве | `/company/` | Есть, структура отличается | Собрать новую производственную страницу из данных/инфоблоков |
| Реализованные проекты | `/projects/` | Есть | Новый листинг с сохранением detail routes |
| Шаблон подробного проекта | `/projects/{element-code}/` | Есть | Новый универсальный detail template |
| ЖК «Символ» | `/projects/zhk-simvol/` | Элемент IBLOCK 36 и общий detail template | Сохранить динамический detail route и общий шаблон |
| Расчёт порошковой окраски | `/painting/poroshkovaya-pokraska-metalla/` + modal/form flow | Статический legacy route; Paper-фрейм является формой запроса, не калькулятором | Сохранить URL, добавить scoped request form без выдумывания формулы |
| Обсудить заказ | popup на desktop, `/form/` на mobile | Есть базовая форма | Новый modal/UI при сохранении Bitrix submission flow |
| Контакты | `/contacts/` | Есть | Новый layout, карта, реквизиты и feedback form |

## Общие компоненты

| Компонент | Источник сейчас | Целевое состояние |
| --- | --- | --- |
| Header/navigation | `local/templates/aspro-allcorp2/header.php`, меню Bitrix | Общий desktop/mobile header по Paper с сохранением динамических пунктов |
| Footer | `local/templates/aspro-allcorp2/footer.php` и include areas | Общий footer по Paper, данные из существующих include/настроек |
| Breadcrumbs | Bitrix component/template | Единый компонент для внутренних страниц |
| Product/service cards | Bitrix component templates | Визуальные варианты из дизайн-системы, данные без дублирования |
| Project cards/detail | IBLOCK 36 и component templates | Унифицированный listing/detail |
| Forms/modals | `/ajax/form.php`, Bitrix form components | Доступные состояния, сохранённая серверная валидация и mobile fallback |
| FAQ | инфоблок/компонент | Accordion по Paper с keyboard support |
| Contacts/map | include areas, map integration | Новый layout; ключи только из окружения |

## Маршруты вне Paper

Редизайн не должен ломать:

- поиск и карту сайта;
- корзину и оформление заказа, если функция включена;
- авторизацию, регистрацию и кабинет;
- вспомогательные страницы news/articles/faq/sales;
- partners/reviews/staff/vacancy;
- региональность;
- legacy `/products/`, `/news/`, `/landings/` до явного решения об их судьбе;
- корректный 404.

Для страниц без отдельного макета применяется общая дизайн-система Paper, но такие решения помечаются как производные, а не pixel-perfect воспроизведение.

## Неразрешённые вопросы

1. Подготовить отдельную идемпотентную миграцию Web Form для request-estimate flow: optional name, required phone-or-email, `описание || файл`, безопасный upload и контролируемые уведомления. Общую форму ID 5 автоматически не менять.
2. Определить адаптивные композиции, поскольку Paper содержит только desktop и modal-фреймы.
3. Проверить права и назначение каждого оригинального ассета перед публикацией; технический доступ и экспорт подтверждены.

Целевой Paper-файл подтверждён как `Ралмастер` в `Мария's Team`. Локальная БД
импортирована, а проект «ЖК Символ» доступен по существующему динамическому
маршруту.
