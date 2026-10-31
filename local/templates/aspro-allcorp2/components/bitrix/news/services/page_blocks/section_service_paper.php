<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$rmServiceImages = array(
	'laser-cutting' => 'home/hero-laser-cutting.webp',
	'metal-bending' => 'home/metal-bending.webp',
);
$rmServiceCode = (string)($arSection['CODE'] ?? '');
$rmServiceImage = '';
if (!empty($arSection['DETAIL_PICTURE']) || !empty($arSection['PICTURE'])) {
	$rmServiceImage = (string)CFile::GetPath($arSection['DETAIL_PICTURE'] ?: $arSection['PICTURE']);
}
if ($rmServiceImage === '') {
	$rmServiceImage = SITE_TEMPLATE_PATH.'/images/ralmaster/'.($rmServiceImages[$rmServiceCode] ?? 'home/hero-laser-cutting.webp');
}
$rmServiceIsBending = $rmServiceCode === 'metal-bending';
$rmServiceContext = $rmServiceIsBending ? 'metal-bending' : 'service-detail';
$rmServiceDialog = array();
if ($rmServiceIsBending) {
	$rmServiceDialog = array(
		'title' => 'Расчёт гибки металла',
		'intro' => 'Пришлите чертёж или опишите задачу. Укажите материал, толщину и количество деталей. Если нужны другие операции, добавьте их в описание.',
		'task_placeholder' => 'Например: согнуть 300 деталей из стали 3 мм по чертежу. После гибки нужна порошковая окраска.',
		'upload_label' => 'Прикрепить чертежи или техническое задание',
		'note' => 'Проверим задачу и свяжемся с вами для уточнения деталей и согласования стоимости.',
		'height' => 850,
	);
}

$rmDetailPage = array(
	'context' => $rmServiceContext,
	'attribute' => 'data-rm-service-detail',
	'root_label' => 'Металлообработка',
	'root_url' => '/services/',
	'eyebrow' => 'Производственная услуга',
	'title' => (string)($arSection['NAME'] ?: 'Металлообработка'),
	'lead' => 'Выполняем работы на собственном производстве. Проверяем исходные данные, подбираем технологию и рассчитываем срок под объём партии.',
	'description_html' => (string)($arSection['DESCRIPTION'] ?: $arSection['UF_TOP_SEO']),
	'image' => $rmServiceImage,
	'image_alt' => (string)($arSection['NAME'] ?: 'Производство РАЛМАСТЕР'),
	'cta_label' => $rmServiceIsBending ? 'Рассчитать гибку' : 'Рассчитать стоимость',
	'about_title' => 'Технология под требования изделия',
	'about_text' => 'Работаем с деталями для строительства, фасадных систем, инженерного оборудования и серийного производства. Помогаем проверить документацию до запуска.',
	'features' => array(
		array('number' => '01', 'title' => 'Точное оборудование', 'text' => 'Подбираем режим обработки с учётом материала, толщины и требований к кромке.'),
		array('number' => '02', 'title' => 'Связанные операции', 'text' => 'При необходимости добавляем гибку, сварку, зачистку и порошковую окраску.'),
		array('number' => '03', 'title' => 'Разовые и серийные партии', 'text' => 'Фиксируем параметры заказа для повторяемого результата при следующих поставках.'),
	),
	'process_title' => 'От заявки до готовой партии',
	'process' => array(
		array('title' => 'Исходные данные', 'text' => 'Получаем чертёж, материал, количество и требования к результату.'),
		array('title' => 'Технологическая проверка', 'text' => 'Проверяем геометрию и согласуем последовательность операций.'),
		array('title' => 'Расчёт и срок', 'text' => 'Готовим предложение с понятным составом работ и сроком.'),
		array('title' => 'Производство', 'text' => 'Выполняем заказ, контролируем качество и готовим к выдаче.'),
	),
	'contact_title' => 'Рассчитаем задачу по вашим данным',
	'contact_text' => 'Пришлите чертёж или техническое задание. Укажите материал и количество, если они уже известны.',
	'dialog' => $rmServiceDialog,
);

require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_detail.php';

