<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/* get component template pages & params array */
$arPageBlocksParams = array();
if(\Bitrix\Main\Loader::includeModule('aspro.allcorp2')){
	$arPageBlocks = CAllcorp2::GetComponentTemplatePageBlocks(__DIR__);
	$arPageBlocksParams = CAllcorp2::GetComponentTemplatePageBlocksParams($arPageBlocks);

}

$arTemplateParameters = array_merge($arPageBlocksParams, array(
	'SHOW_SECTION_PREVIEW_DESCRIPTION' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 500,
		'NAME' => GetMessage('SHOW_SECTION_PREVIEW_DESCRIPTION'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'SHOW_FORM' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 500,
		'NAME' => GetMessage('SHOW_FORM'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'FORM_CREATE_DEACTIVATED' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('FORM_CREATE_DEACTIVATED'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
));
?>