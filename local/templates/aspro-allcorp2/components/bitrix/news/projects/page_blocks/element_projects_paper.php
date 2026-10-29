<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<?$APPLICATION->IncludeComponent(
	'bitrix:news.detail',
	'project-paper',
	array(
		'DISPLAY_DATE' => 'N',
		'DISPLAY_NAME' => 'Y',
		'DISPLAY_PICTURE' => 'Y',
		'DISPLAY_PREVIEW_TEXT' => 'Y',
		'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
		'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'FIELD_CODE' => array('ID', 'IBLOCK_ID', 'CODE', 'NAME', 'PREVIEW_TEXT', 'DETAIL_TEXT', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'),
		'PROPERTY_CODE' => $arParams['DETAIL_PROPERTY_CODE'],
		'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['detail'],
		'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
		'META_KEYWORDS' => $arParams['META_KEYWORDS'],
		'META_DESCRIPTION' => $arParams['META_DESCRIPTION'],
		'BROWSER_TITLE' => $arParams['BROWSER_TITLE'],
		'SET_CANONICAL_URL' => $arParams['DETAIL_SET_CANONICAL_URL'],
		'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
		'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
		'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
		'SET_TITLE' => $arParams['SET_TITLE'],
		'SET_STATUS_404' => $arParams['SET_STATUS_404'],
		'INCLUDE_IBLOCK_INTO_CHAIN' => $arParams['INCLUDE_IBLOCK_INTO_CHAIN'],
		'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],
		'ADD_ELEMENT_CHAIN' => $arParams['ADD_ELEMENT_CHAIN'],
		'ACTIVE_DATE_FORMAT' => $arParams['DETAIL_ACTIVE_DATE_FORMAT'],
		'CACHE_TYPE' => $arParams['CACHE_TYPE'],
		'CACHE_TIME' => $arParams['CACHE_TIME'],
		'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
		'USE_PERMISSIONS' => $arParams['USE_PERMISSIONS'],
		'GROUP_PERMISSIONS' => $arParams['GROUP_PERMISSIONS'],
		'CHECK_DATES' => $arParams['CHECK_DATES'],
		'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'],
		'ELEMENT_CODE' => $arResult['VARIABLES']['ELEMENT_CODE'],
		'IBLOCK_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['news'],
	),
	$component
);?>
