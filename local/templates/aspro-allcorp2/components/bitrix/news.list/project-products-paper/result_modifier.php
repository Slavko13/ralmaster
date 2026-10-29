<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Loader;

$rmProjectProductSpecs = array(
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
);

$arResult['RM_PROJECT_PRODUCTS'] = array();
if (Loader::includeModule('iblock') && (int)$arParams['IBLOCK_ID'] > 0) {
	$rmProductSections = CIBlockSection::GetList(
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

	$rmSectionsByCode = array();
	while ($rmProductSection = $rmProductSections->GetNext(false, false)) {
		$rmSectionsByCode[(string)$rmProductSection['CODE']] = $rmProductSection;
	}

	foreach ($rmProjectProductSpecs as $rmProductCode => $rmProductSpec) {
		if (empty($rmSectionsByCode[$rmProductCode])) {
			continue;
		}
		$rmProductSection = $rmSectionsByCode[$rmProductCode];
		$arResult['RM_PROJECT_PRODUCTS'][] = array(
			'ID' => (int)$rmProductSection['ID'],
			'IBLOCK_ID' => (int)$rmProductSection['IBLOCK_ID'],
			'CODE' => $rmProductCode,
			'NAME' => $rmProductSpec['NAME'],
			'DESCRIPTION' => $rmProductSpec['DESCRIPTION'],
			'IMAGE' => SITE_TEMPLATE_PATH.'/images/ralmaster/products/'.$rmProductSpec['IMAGE'],
			'URL' => (string)($rmProductSection['SECTION_PAGE_URL'] ?: '/product/'.$rmProductCode.'/'),
		);
	}
}
