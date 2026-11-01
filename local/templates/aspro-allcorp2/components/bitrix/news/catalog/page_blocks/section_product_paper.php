<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$rmProductImages = array(
	'kapelnik' => 'products/drip-edges.webp',
	'korziny' => 'products/air-conditioner-baskets.webp',
	'koroba' => 'products/metal-boxes.webp',
	'kronshtejny' => 'products/brackets.webp',
	'otlivy' => 'products/flashings.webp',
	'fasadnye-kassety' => 'products/facade-cassettes.webp',
	'ekrany' => 'products/screens.webp',
);
$rmProductCode = (string)($arSection['CODE'] ?? '');
$rmProductImage = '';
if (!empty($arSection['DETAIL_PICTURE']) || !empty($arSection['PICTURE'])) {
	$rmProductImage = (string)CFile::GetPath($arSection['DETAIL_PICTURE'] ?: $arSection['PICTURE']);
}
if ($rmProductImage === '') {
	$rmProductImage = SITE_TEMPLATE_PATH.'/images/ralmaster/'.($rmProductImages[$rmProductCode] ?? 'products/drip-edges.webp');
}

$rmProductCatalogHtml = '';
if ($itemsCnt) {
	$display = 'table';
	$sort = 'SORT';
	$order = 'ASC';
	$isAjax = 'N';
	$arAvailableSort = array(
		'SORT' => array('SORT' => 'SORT'),
	);

	ob_start();
	include __DIR__.'/catalog_table_2.php';
	$rmProductCatalogHtml = trim((string)ob_get_clean());
}

$rmDetailPage = array(
	'context' => 'product-detail',
	'attribute' => 'data-rm-product-detail',
	'root_label' => 'Продукция',
	'root_url' => '/product/',
	'eyebrow' => 'Изделия из металла',
	'title' => (string)($arSection['NAME'] ?: 'Изделие из металла'),
	'lead' => 'Изготавливаем изделие по размерам, чертежу или техническому заданию. Согласуем материал, обработку, покрытие и требования к партии.',
	'description_html' => (string)($arSection['DESCRIPTION'] ?: $arSection['UF_TOP_SEO']),
	'image' => $rmProductImage,
	'image_alt' => (string)($arSection['NAME'] ?: 'Изделие производства РАЛМАСТЕР'),
	'cta_label' => 'Рассчитать изготовление',
	'about_title' => 'Изготовление под задачу проекта',
	'about_text' => 'Работаем с проектными, строительными и производственными компаниями. Подготовим единичное изделие, тестовую партию или серийный выпуск с повторяемой геометрией.',
	'features' => array(
		array('number' => '01', 'title' => 'По вашим размерам', 'text' => 'Проверяем исходные данные и производим детали по чертежу, эскизу или образцу.'),
		array('number' => '02', 'title' => 'Полный цикл', 'text' => 'Резка, гибка, сварка, подготовка поверхности и порошковая окраска на одной площадке.'),
		array('number' => '03', 'title' => 'Контроль партии', 'text' => 'Сохраняем согласованные параметры изделия и контролируем качество перед отгрузкой.'),
	),
	'process_title' => 'Как проходит заказ',
	'process' => array(
		array('title' => 'Получаем задачу', 'text' => 'Чертёж, размеры, количество и требования к покрытию.'),
		array('title' => 'Проверяем технологичность', 'text' => 'Уточняем материал, допуски, крепления и способ изготовления.'),
		array('title' => 'Рассчитываем', 'text' => 'Фиксируем стоимость, срок производства и условия поставки.'),
		array('title' => 'Изготавливаем', 'text' => 'Производим, проверяем, упаковываем и готовим партию к отгрузке.'),
	),
	'contact_title' => 'Отправьте чертёж на расчёт',
	'contact_text' => 'Приложите документацию и укажите количество. Если чертежа пока нет, опишите задачу — менеджер поможет собрать исходные данные.',
	'catalog_html' => $rmProductCatalogHtml,
	'catalog_title' => 'Готовые изделия',
	'catalog_text' => 'Выберите готовое изделие, откройте подробные характеристики или добавьте позицию в корзину.',
);

require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_detail.php';

