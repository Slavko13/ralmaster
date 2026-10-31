<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (empty($rmContentConfig) || !is_array($rmContentConfig)) {
	return;
}

global $APPLICATION;

$APPLICATION->SetPageProperty('HIDETITLE', 'Y');
$APPLICATION->SetPageProperty('FULLWIDTH', 'Y');
$APPLICATION->SetPageProperty('MENU', 'N');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_content.css');

$APPLICATION->IncludeComponent(
	'bitrix:news.detail',
	'ralmaster-content',
	array(
		'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
		'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'],
		'ELEMENT_CODE' => $arResult['VARIABLES']['ELEMENT_CODE'],
		'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
		'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
		'FIELD_CODE' => array('ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'DETAIL_TEXT', 'DETAIL_PICTURE', 'DATE_ACTIVE_FROM'),
		'PROPERTY_CODE' => array('POST', 'PHONE', 'EMAIL', 'PAY', 'PERIOD', 'SITE', 'DOCUMENTS', 'PHOTOS'),
		'META_KEYWORDS' => $arParams['META_KEYWORDS'],
		'META_DESCRIPTION' => $arParams['META_DESCRIPTION'],
		'BROWSER_TITLE' => $arParams['BROWSER_TITLE'],
		'SET_CANONICAL_URL' => $arParams['DETAIL_SET_CANONICAL_URL'],
		'SET_TITLE' => $arParams['SET_TITLE'],
		'SET_STATUS_404' => $arParams['SET_STATUS_404'],
		'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
		'INCLUDE_IBLOCK_INTO_CHAIN' => $arParams['INCLUDE_IBLOCK_INTO_CHAIN'],
		'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],
		'ADD_ELEMENT_CHAIN' => $arParams['ADD_ELEMENT_CHAIN'],
		'ACTIVE_DATE_FORMAT' => $arParams['DETAIL_ACTIVE_DATE_FORMAT'],
		'CACHE_TYPE' => $arParams['CACHE_TYPE'],
		'CACHE_TIME' => $arParams['CACHE_TIME'],
		'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
		'CHECK_DATES' => $arParams['CHECK_DATES'],
		'IBLOCK_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['news'],
		'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['detail'],
		'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
		'RM_KIND' => $rmContentConfig['kind'],
		'RM_KICKER' => $rmContentConfig['kicker'],
		'RM_LIST_TITLE' => $rmContentConfig['title'],
		'RM_LIST_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['news'],
	),
	$component
);
