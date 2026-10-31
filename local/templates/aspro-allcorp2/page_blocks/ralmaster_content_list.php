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

$rmFilterName = 'rmContentFilter'.(int)$arParams['IBLOCK_ID'];
$rmFilter = array();
$rmYears = array();
$rmActiveYear = 0;

if (!empty($rmContentConfig['years'])) {
	$rmYearItems = CCache::CIBLockElement_GetList(
		array('ACTIVE_FROM' => 'DESC', 'CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']))),
		array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y'),
		false,
		false,
		array('ID', 'ACTIVE_FROM')
	);

	foreach ((array)$rmYearItems as $rmYearItem) {
		if (empty($rmYearItem['ACTIVE_FROM'])) {
			continue;
		}
		$rmDate = ParseDateTime($rmYearItem['ACTIVE_FROM'], FORMAT_DATETIME);
		if (!empty($rmDate['YYYY'])) {
			$rmYears[(int)$rmDate['YYYY']] = (int)$rmDate['YYYY'];
		}
	}
	rsort($rmYears);

	$rmRequestedYear = isset($_GET['year']) ? (int)$_GET['year'] : 0;
	if ($rmRequestedYear > 2000 && $rmRequestedYear < 2200 && in_array($rmRequestedYear, $rmYears, true)) {
		$rmActiveYear = $rmRequestedYear;
		$rmFilter['>=DATE_ACTIVE_FROM'] = ConvertDateTime('01.01.'.$rmActiveYear, FORMAT_DATE, '');
		$rmFilter['<DATE_ACTIVE_FROM'] = ConvertDateTime('01.01.'.($rmActiveYear + 1), FORMAT_DATE, '');
	}
}

$GLOBALS[$rmFilterName] = $rmFilter;

$APPLICATION->IncludeComponent(
	'bitrix:news.list',
	'ralmaster-content',
	array(
		'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
		'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'NEWS_COUNT' => $arParams['NEWS_COUNT'],
		'SORT_BY1' => $arParams['SORT_BY1'],
		'SORT_ORDER1' => $arParams['SORT_ORDER1'],
		'SORT_BY2' => $arParams['SORT_BY2'],
		'SORT_ORDER2' => $arParams['SORT_ORDER2'],
		'FIELD_CODE' => array('ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'DATE_ACTIVE_FROM'),
		'PROPERTY_CODE' => array('POST', 'PHONE', 'EMAIL', 'PAY', 'PERIOD', 'SITE'),
		'CHECK_DATES' => $arParams['CHECK_DATES'],
		'FILTER_NAME' => $rmFilterName,
		'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['detail'],
		'ACTIVE_DATE_FORMAT' => $arParams['LIST_ACTIVE_DATE_FORMAT'],
		'CACHE_TYPE' => $arParams['CACHE_TYPE'],
		'CACHE_TIME' => $arParams['CACHE_TIME'],
		'CACHE_FILTER' => 'Y',
		'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
		'PREVIEW_TRUNCATE_LEN' => $arParams['PREVIEW_TRUNCATE_LEN'],
		'SET_TITLE' => 'N',
		'SET_STATUS_404' => 'N',
		'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
		'ADD_SECTIONS_CHAIN' => 'N',
		'HIDE_LINK_WHEN_NO_DETAIL' => $arParams['HIDE_LINK_WHEN_NO_DETAIL'],
		'PARENT_SECTION' => $arResult['VARIABLES']['SECTION_ID'],
		'PARENT_SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
		'INCLUDE_SUBSECTIONS' => 'Y',
		'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
		'DISPLAY_TOP_PAGER' => 'N',
		'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
		'PAGER_TITLE' => $arParams['PAGER_TITLE'],
		'PAGER_SHOW_ALWAYS' => 'N',
		'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],
		'RM_KIND' => $rmContentConfig['kind'],
		'RM_TITLE' => $rmContentConfig['title'],
		'RM_KICKER' => $rmContentConfig['kicker'],
		'RM_INTRO' => $rmContentConfig['intro'],
		'RM_EMPTY' => $rmContentConfig['empty'],
		'RM_YEARS' => $rmYears,
		'RM_ACTIVE_YEAR' => $rmActiveYear,
		'RM_LIST_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['news'],
		'RM_SHOW_REVIEW_FORM' => !empty($rmContentConfig['reviewForm']) ? 'Y' : 'N',
		'RM_SHOW_QUESTION_FORM' => !empty($rmContentConfig['questionForm']) ? 'Y' : 'N',
	),
	$component
);
