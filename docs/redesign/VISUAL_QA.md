# Визуальный контроль

Последнее обновление: 2026-10-27. Статус: **BLOCKED / baseline only**.

## Источник истины

Целевой интерфейс находится в Paper Desktop. Прочитаны 12 desktop/modal-фреймов, computed styles, размеры и screenshots. Активный файл называется `Cheerful breeze`; исходно ожидался `Polite hill`, поэтому финальная визуальная приёмка требует подтверждения файла.

## Что уже проверено

- Реальный рендер главной, производства, контактов, проектов и формы «Обсудить заказ» в Paper.
- Desktop canvas 1440 px, контейнеры 1312/1328 px, header 106 px, footer 430 px.
- Основные typography, colors, gaps, radii, button/input dimensions.
- Наличие 50 уникальных изображений и 110 их использований.
- Playwright снимает full-page baseline screenshots текущего production.

## Что ещё не проверено

- Полный экспорт оригинальных Paper-ассетов.
- Pixel/overlay comparison реализации с каждым целевым frame.
- Responsive 390/768/1024, поскольку прямых mobile/tablet макетов нет.
- Hover, focus, active, error, success, disabled и loading states.
- Crop/focal points всех изображений.
- Windows/browser font rendering после фактического подключения fonts.
- Visual regression production-like build.

## Матрица будущих проверок

| Объект | Desktop | Tablet | Mobile | Interaction | Статус |
| --- | --- | --- | --- | --- | --- |
| Общий shell | 1440 | 1024/768 | 390 | menu/focus/sticky | TODO |
| Главная | 1440 full page | 1024/768 | 390 | CTA/FAQ/modal | TODO |
| Продукция | 1440 full page | 1024/768 | 390 | cards/filter/form | TODO |
| Металлообработка | 1440 full page | 1024/768 | 390 | gallery/CTA | TODO |
| Порошковая окраска | 1440 full page | 1024/768 | 390 | gallery/CTA | TODO |
| Производство | 1440 full page | 1024/768 | 390 | table/FAQ | TODO |
| Проекты | 1440 list/detail | 1024/768 | 390 | gallery/navigation | TODO |
| Контакты | 1440 full page | 1024/768 | 390 | map/form | TODO |
| Modals/calculators | 748 target | responsive | 390 | validation/result | TODO |

## Процедура итерации

1. Снять screenshot реализации на фиксированном viewport.
2. Сопоставить структуру, bounding boxes, spacing, typography, colors, images и content flow с Paper.
3. Отдельно проверить responsive и interaction states как производные решения.
4. Зафиксировать отклонения и их причину.
5. Исправить, пересобрать и повторить functional + visual checks.
6. Не переводить итерацию в `PASSED` при новой критической визуальной регрессии: перекрытом контенте, горизонтальном overflow, недоступном CTA или сломанной навигации.

## Блокеры

- Paper MCP weekly limit достигнут; восстановление ожидается через три дня от даты аудита.
- Реализация UI не начата до прохождения local runtime gate.
- Нет утверждённых responsive-макетов и interaction states.
