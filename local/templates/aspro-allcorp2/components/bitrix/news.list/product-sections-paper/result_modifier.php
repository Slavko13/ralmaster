<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Loader;

$rmProductSpecs = array(
	'facadecassettes' => array(
		'NAME' => 'Фасадные металлокассеты',
		'DESCRIPTION' => 'Металлические кассеты для облицовки фасадов.',
		'IMAGE' => 'facade-cassettes.webp',
	),
	'baskets' => array(
		'NAME' => 'Корзины для кондиционеров',
		'DESCRIPTION' => 'Декоративные корзины для наружных блоков кондиционеров.',
		'IMAGE' => 'air-conditioner-baskets.png',
	),
	'pannier' => array(
		'NAME' => 'Короба',
		'DESCRIPTION' => 'Металлические короба по размерам и техническому заданию.',
		'IMAGE' => 'metal-boxes.png',
	),
	'bracket' => array(
		'NAME' => 'Кронштейны',
		'DESCRIPTION' => 'Крепёжные изделия по чертежам заказчика.',
		'IMAGE' => 'brackets.webp',
	),
	'lowtide' => array(
		'NAME' => 'Отливы',
		'DESCRIPTION' => 'Металлические элементы для отвода воды.',
		'IMAGE' => 'flashings.png',
	),
	'kapelnik' => array(
		'NAME' => 'Капельники',
		'DESCRIPTION' => 'Доборные элементы для отвода капель воды.',
		'IMAGE' => 'drip-edges.png',
	),
	'shield' => array(
		'NAME' => 'Экраны',
		'DESCRIPTION' => 'Металлические экраны по размерам и требованиям проекта.',
		'IMAGE' => 'screens.png',
	),
);

$rmDynamicSections = array();
if (Loader::includeModule('iblock')) {
	$rmSectionResult = CIBlockSection::GetList(
		array('SORT' => 'ASC', 'NAME' => 'ASC'),
		array(
			'IBLOCK_ID' => (int)$arParams['IBLOCK_ID'],
			'ACTIVE' => 'Y',
			'GLOBAL_ACTIVE' => 'Y',
			'DEPTH_LEVEL' => 1,
		),
		false,
		array('ID', 'IBLOCK_ID', 'NAME', 'CODE', 'SECTION_PAGE_URL')
	);

	while ($rmSection = $rmSectionResult->GetNext(false, false)) {
		$rmDynamicSections[] = $rmSection;
	}
}

$rmSectionsByCode = array();
foreach ($rmDynamicSections as $rmSection) {
	$rmCode = (string)($rmSection['CODE'] ?? '');
	if (isset($rmProductSpecs[$rmCode])) {
		$rmSectionsByCode[$rmCode] = $rmSection;
	}
}

$arResult['PAPER_SECTIONS'] = array();
foreach ($rmProductSpecs as $rmCode => $rmSpec) {
	if (!isset($rmSectionsByCode[$rmCode])) {
		continue;
	}

	$rmSection = $rmSectionsByCode[$rmCode];
	$arResult['PAPER_SECTIONS'][] = array(
		'ID' => (int)$rmSection['ID'],
		'IBLOCK_ID' => (int)$rmSection['IBLOCK_ID'],
		'CODE' => $rmCode,
		'NAME' => $rmSpec['NAME'],
		'DESCRIPTION' => $rmSpec['DESCRIPTION'],
		'URL' => (string)($rmSection['SECTION_PAGE_URL'] ?: '/product/'.$rmCode.'/'),
		'IMAGE' => SITE_TEMPLATE_PATH.'/images/ralmaster/products/'.$rmSpec['IMAGE'],
	);
}
