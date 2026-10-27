<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Web\Json;

if (!Loader::includeModule('iblock'))
	return;

/* get component template pages & params array */
$arPageBlocksParams = array();
if(\Bitrix\Main\Loader::includeModule('aspro.allcorp2')){
	$arPageBlocks = CAllcorp2::GetComponentTemplatePageBlocks(__DIR__);
	$arPageBlocksParams = CAllcorp2::GetComponentTemplatePageBlocksParams($arPageBlocks);

	CAllcorp2::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams, array('SECTION' => 'CATALOG_PAGE', 'OPTION' => 'ELEMENTS_TABLE_TYPE_VIEW')); // add option value FROM_MODULE to TABLE list
}

$arTemplateParameters = array_merge($arPageBlocksParams, array(
	'COUNT_IN_LINE' => array(
		'NAME' => GetMessage('S_COUNT_IN_LINE'),
		'TYPE' => 'STRING',
		'DEFAULT' => 4,
	),
));
?>