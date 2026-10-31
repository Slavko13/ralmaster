<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

global $APPLICATION;
$APPLICATION->SetPageProperty('HIDETITLE', 'Y');
$APPLICATION->SetPageProperty('FULLWIDTH', 'Y');
$APPLICATION->SetPageProperty('MENU', 'N');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/components/bitrix/news.list/partners-paper/style.css');

$APPLICATION->IncludeComponent(
	'bitrix:news.list',
	'partners-paper',
	array(
		'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
		'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'NEWS_COUNT' => $arParams['NEWS_COUNT'],
		'SORT_BY1' => $arParams['SORT_BY1'],
		'SORT_ORDER1' => $arParams['SORT_ORDER1'],
		'SORT_BY2' => $arParams['SORT_BY2'],
		'SORT_ORDER2' => $arParams['SORT_ORDER2'],
		'FIELD_CODE' => array('NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE'),
		'PROPERTY_CODE' => array('SITE', 'PHONE'),
		'CHECK_DATES' => $arParams['CHECK_DATES'],
		'FILTER_NAME' => $arParams['FILTER_NAME'],
		'CACHE_TYPE' => $arParams['CACHE_TYPE'],
		'CACHE_TIME' => $arParams['CACHE_TIME'],
		'CACHE_FILTER' => $arParams['CACHE_FILTER'],
		'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
		'SET_TITLE' => 'N',
		'SET_STATUS_404' => $arParams['SET_STATUS_404'],
		'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
		'ADD_SECTIONS_CHAIN' => 'N',
		'DISPLAY_TOP_PAGER' => 'N',
		'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
		'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
	),
	$component
);

