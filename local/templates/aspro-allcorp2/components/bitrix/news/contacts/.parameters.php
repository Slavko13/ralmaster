<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/* get component template pages & params array */
$arPageBlocksParams = array();
if(\Bitrix\Main\Loader::includeModule('aspro.allcorp2')){
	$arPageBlocks = CAllcorp2::GetComponentTemplatePageBlocks(__DIR__);
	$arPageBlocksParams = CAllcorp2::GetComponentTemplatePageBlocksParams($arPageBlocks);
}

$arTemplateParameters = array_merge($arPageBlocksParams, array(
	'SHOW_TOP_MAP' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 100,
		'NAME' => GetMessage('SHOW_TOP_MAP_TITLE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'SHOOSE_REGION_TEXT' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 100,
		'NAME' => GetMessage('SHOOSE_REGION_TEXT'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
));
?>