<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Loader;

$rmProjectText = static function ($rmValue) {
	if (is_array($rmValue) && array_key_exists('TEXT', $rmValue)) {
		$rmValue = $rmValue['TEXT'];
	}
	if (is_array($rmValue)) {
		$rmValue = implode(' ', array_filter($rmValue, static function ($rmPart) {
			return is_scalar($rmPart);
		}));
	}
	return trim(preg_replace('/\s+/u', ' ', strip_tags((string)$rmValue)));
};

$rmProjectPropertyText = static function ($rmCode) use (&$arResult, $rmProjectText) {
	if (empty($arResult['PROPERTIES'][$rmCode])) {
		return '';
	}
	$rmProperty = $arResult['PROPERTIES'][$rmCode];
	$rmValue = array_key_exists('~VALUE', $rmProperty) ? $rmProperty['~VALUE'] : ($rmProperty['VALUE'] ?? '');
	return $rmProjectText($rmValue);
};

$rmProjectFilePath = static function ($rmFile) {
	if (is_array($rmFile) && !empty($rmFile['SRC'])) {
		return (string)$rmFile['SRC'];
	}
	if (is_numeric($rmFile) && (int)$rmFile > 0) {
		return (string)CFile::GetPath((int)$rmFile);
	}
	return '';
};

$rmProjectCode = mb_strtolower(trim((string)($arResult['CODE'] ?? '')));
$rmProjectRawName = (string)($arResult['~NAME'] ?? $arResult['NAME'] ?? '');
$rmProjectName = trim(preg_replace('/\s+/u', ' ', html_entity_decode($rmProjectRawName, ENT_QUOTES | ENT_HTML5, LANG_CHARSET)));
$rmProjectAssetRoot = SITE_TEMPLATE_PATH.'/images/ralmaster/projects/';

$rmProjectPresentationMap = array(
	'zhk-simvol' => array(
		'IMAGE' => 'project-symbol.webp',
		'TITLE_LINES' => array('Декоративные корзины', 'для кондиционеров в ЖК «Символ»'),
		'SUMMARY' => 'Для жилого комплекса выполнен заказ на декоративные корзины для кондиционеров по индивидуальному проекту.',
		'TASK_PARAGRAPHS' => array(
			'Для ЖК «Символ» выполнен заказ на декоративные корзины для кондиционеров по индивидуальному проекту.',
			'На фотографии показаны изделия на фасаде жилого комплекса.',
		),
		'PASSPORT' => array(
			array('LABEL' => 'Объект', 'VALUE' => 'ЖК «Символ»'),
			array('LABEL' => 'Продукция', 'VALUE' => 'Декоративные корзины для кондиционеров'),
			array('LABEL' => 'Исполнение', 'VALUE' => 'По индивидуальному проекту'),
			array('LABEL' => 'Год', 'VALUE' => '2025'),
		),
	),
	'zhk-belyy-grad' => array(
		'IMAGE' => 'project-white-city.webp',
		'TITLE_LINES' => array('Декоративные корзины', 'для кондиционеров в ЖК «Белый град»'),
		'SUMMARY' => 'Для жилого комплекса выполнен заказ на декоративные корзины для кондиционеров по индивидуальному проекту.',
		'TASK_PARAGRAPHS' => array(
			'Для ЖК «Белый град» выполнен заказ на декоративные корзины для кондиционеров по индивидуальному проекту.',
			'На фотографии показаны изделия на фасаде жилого комплекса.',
		),
		'PASSPORT' => array(
			array('LABEL' => 'Объект', 'VALUE' => 'ЖК «Белый град»'),
			array('LABEL' => 'Продукция', 'VALUE' => 'Декоративные корзины для кондиционеров'),
			array('LABEL' => 'Исполнение', 'VALUE' => 'По индивидуальному проекту'),
			array('LABEL' => 'Год', 'VALUE' => '2025'),
		),
	),
	'zhk-simvol-2' => array(
		'IMAGE' => 'project-symbol-2.webp',
		'TITLE_LINES' => array('Декоративные корзины', 'для кондиционеров в ЖК «Символ 2»'),
		'SUMMARY' => 'Для жилого комплекса выполнен заказ на декоративные корзины для кондиционеров по индивидуальному проекту.',
		'TASK_PARAGRAPHS' => array(
			'Для ЖК «Символ 2» выполнен заказ на декоративные корзины для кондиционеров по индивидуальному проекту.',
			'На фотографии показаны изделия на фасаде жилого комплекса.',
		),
		'PASSPORT' => array(
			array('LABEL' => 'Объект', 'VALUE' => 'ЖК «Символ 2»'),
			array('LABEL' => 'Продукция', 'VALUE' => 'Декоративные корзины для кондиционеров'),
			array('LABEL' => 'Исполнение', 'VALUE' => 'По индивидуальному проекту'),
			array('LABEL' => 'Год', 'VALUE' => '2025'),
		),
	),
);
$rmProjectPresentation = $rmProjectPresentationMap[$rmProjectCode] ?? null;
$rmProjectIsRealized = is_array($rmProjectPresentation);

$rmProjectDynamicImage = $rmProjectFilePath($arResult['DETAIL_PICTURE'] ?? null);
if ($rmProjectDynamicImage === '') {
	$rmProjectDynamicImage = $rmProjectFilePath($arResult['PREVIEW_PICTURE'] ?? null);
}
$rmProjectHeroImage = $rmProjectIsRealized
	? $rmProjectAssetRoot.$rmProjectPresentation['IMAGE']
	: $rmProjectDynamicImage;

$rmProjectSummary = $rmProjectText($arResult['~DETAIL_TEXT'] ?? $arResult['DETAIL_TEXT'] ?? '');
if ($rmProjectSummary === '') {
	$rmProjectSummary = $rmProjectText($arResult['~PREVIEW_TEXT'] ?? $arResult['PREVIEW_TEXT'] ?? '');
}

$rmProjectTask = $rmProjectPropertyText('TASK_PROJECT');
if ($rmProjectTask === '') {
	$rmProjectTask = $rmProjectSummary;
}

$rmPassportLabels = array(
	'ORDERER' => 'Заказчик',
	'DATA' => 'Дата выполнения',
	'SITE' => 'Сайт проекта',
	'AUTHOR' => 'Ответственный',
	'PLACE' => 'Объект',
);
$rmProjectPassport = array();
foreach ($rmPassportLabels as $rmPropertyCode => $rmPropertyLabel) {
	$rmPropertyValue = $rmProjectPropertyText($rmPropertyCode);
	if ($rmPropertyValue !== '') {
		$rmProjectPassport[] = array('LABEL' => $rmPropertyLabel, 'VALUE' => $rmPropertyValue);
	}
}
if ($rmProjectIsRealized) {
	$rmProjectPassport = $rmProjectPresentation['PASSPORT'];
}

$rmProjectGallery = array();
if (!empty($arResult['PROPERTIES']['PHOTOS']['VALUE'])) {
	$rmProjectPhotoIds = (array)$arResult['PROPERTIES']['PHOTOS']['VALUE'];
	foreach ($rmProjectPhotoIds as $rmProjectPhotoId) {
		$rmProjectPhotoPath = $rmProjectFilePath($rmProjectPhotoId);
		if ($rmProjectPhotoPath !== '') {
			$rmProjectGallery[] = $rmProjectPhotoPath;
		}
	}
}

$rmProjectProductUrl = '/product/baskets/';
if (Loader::includeModule('iblock')) {
	$rmProductSections = CIBlockSection::GetList(
		array('SORT' => 'ASC'),
		array('IBLOCK_ID' => (int)($arParams['CATALOG_IBLOCK_ID'] ?? 41), 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'CODE' => 'baskets'),
		false,
		array('ID', 'SECTION_PAGE_URL')
	);
	if ($rmProductSection = $rmProductSections->GetNext(false, false)) {
		$rmProjectProductUrl = (string)($rmProductSection['SECTION_PAGE_URL'] ?: $rmProjectProductUrl);
	}
}

$arResult['RM_PROJECT'] = array(
	'CODE' => $rmProjectCode,
	'NAME' => $rmProjectName,
	'IS_REALIZED' => $rmProjectIsRealized,
	'TITLE' => $rmProjectIsRealized ? implode(' ', $rmProjectPresentation['TITLE_LINES']) : $rmProjectName,
	'TITLE_LINES' => $rmProjectIsRealized ? $rmProjectPresentation['TITLE_LINES'] : array($rmProjectName),
	'SUMMARY' => $rmProjectIsRealized ? $rmProjectPresentation['SUMMARY'] : ($rmProjectSummary ?: 'Выполненный проект РАЛМАСТЕР.'),
	'TASK' => $rmProjectIsRealized ? implode(' ', $rmProjectPresentation['TASK_PARAGRAPHS']) : ($rmProjectTask ?: 'Описание задачи не опубликовано.'),
	'TASK_PARAGRAPHS' => $rmProjectIsRealized ? $rmProjectPresentation['TASK_PARAGRAPHS'] : array($rmProjectTask ?: 'Описание задачи не опубликовано.'),
	'HERO_IMAGE' => $rmProjectHeroImage,
	'DYNAMIC_IMAGE' => $rmProjectDynamicImage,
	'PASSPORT' => $rmProjectPassport,
	'GALLERY' => $rmProjectGallery,
	'PRODUCT_URL' => $rmProjectProductUrl,
	'PRESENTATION' => array(
		'WORK_IMAGE' => $rmProjectAssetRoot.'generic-project-work.webp',
		'RESULT_IMAGES' => array(
			$rmProjectAssetRoot.'generic-project-result-batch.webp',
			$rmProjectAssetRoot.'generic-project-result-detail.webp',
		),
	),
);
