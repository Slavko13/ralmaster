# Project assets from Paper

Оригинальные изображения фреймов `Реализованные проекты`, `Шаблон подробного
проекта` и `ЖК «Символ»` из read-only Paper-файла `Ралмастер`
(`01M5F74BVZEJ54GZ0MW8A0JM06`, страница `Дизайн`). Файлы сохранены локально;
runtime-зависимости от Paper CDN нет.

Base URL:
`https://app.paper.design/file-assets/01M5F74BVZEJ54GZ0MW8A0JM06/`

## Локализованные оригиналы

| Локальный файл | Paper asset | Формат | Размер, px | Bytes | Роль |
| --- | --- | --- | ---: | ---: | --- |
| `project-symbol.jpg` | `0PW6M870PD2KQVWJAGSF57BF4Q.jpg` | JPEG | 1920×1280 | 448759 | Карточка и hero ЖК «Символ» |
| `project-white-city.jpg` | `1M3YH2BT134VWQS9RA9PR6Q3H1.jpg` | JPEG | 1920×1535 | 565020 | Карточка ЖК «Белый град» |
| `project-symbol-2.png` | `1C8N39FJ3HT91V9BBEVXR3JYJB.png` | PNG | 1536×1024 | 3486039 | Карточка ЖК «Символ 2» |
| `product-air-conditioner-baskets.png` | `01M5SAVWDQP8C5X1BC2020PSQC.png` | PNG | 1448×1086 | 1534434 | Карточка продукции «Корзины для кондиционеров» |
| `generic-project-hero.png` | `6CXPZ0E02H69XK9XB6XZ4BJ6G3.png` | PNG | 1536×1024 | 2885441 | Hero демонстрационного универсального detail-шаблона |
| `generic-project-work.png` | `3RQMN1ZH29RVNP019J0XP923GK.png` | PNG | 1536×1024 | 2312676 | «Что сделали» в демонстрационном detail-шаблоне |
| `generic-project-result-batch.png` | `10JQ746K01NB7MHF8PNFT0JWGQ.png` | PNG | 1536×1024 | 2551798 | Результат: партия изделий |
| `generic-project-result-detail.png` | `72MFE8VSNQA2GKNPSMGQFEZA6S.png` | PNG | 1536×1024 | 2715570 | Результат: крупный план изделия |

Три изображения реальных проектов побайтово совпадают с их `DETAIL_PICTURE`
в локальном инфоблоке 36. Шаблон может использовать динамический Bitrix-файл;
локальные копии сохраняют точную Paper-привязку и служат только презентационным
fallback по коду проекта.

## Повторно используемые ассеты

Следующие file-assets уже были локализованы для других страниц и не
дублируются в этой папке:

| Paper asset | Существующий локальный файл | Роль в проектах |
| --- | --- | --- |
| `46PQ65QGMHN0VJWQ8MGE55K42Z.webp` | `../products/facade-cassettes.webp` | Карточка продукции |
| `7ERWY4BAQKB2RND3T41MS5Q03S.png` | `../products/metal-boxes.png` | Карточка продукции |
| `3VYG2PCXZ066S67F4DDSZWHEDT.webp` | `../home/hero-laser-cutting.webp` | Операция «Резка» |
| `514TQQ9X9AGDC1KQQ7SK6R4EYG.webp` | `../home/metal-bending.webp` | Операция «Гибка» и фон компактной формы |
| `3CJWQ5KJ7KHAAW93Z664GAQ8QD.webp` | `../home/powder-flat-panels.webp` | Операция «Окраска» |
| `01M5JATAE1ZG6PFKQMW1K08R11.png` | `../home/production-powder-line.png` | Нижняя производственная фотография |

`product-air-conditioner-baskets.png` имеет те же байты, что и уже
локализованные изображения корзин, но оставлен здесь с отдельной семантической
ролью и точным asset ID текущего Paper-фрейма.

Логотипы header/footer относятся к общему шаблону сайта и повторно не
экспортировались.

## Ограничение контента

Материал, количество, RAL, перечень работ и результат в универсальном Paper-
detail помечены самим макетом как демонстрационные и вымышленные. Их нельзя
выводить как реальные характеристики элементов инфоблока. При отсутствии
свойств `PHOTOS`, `TASK_PROJECT`, `ORDERER`, `DATA` и других опциональных полей
секция должна скрываться или использовать нейтральное представление доступных
динамических данных без выдуманных спецификаций.
