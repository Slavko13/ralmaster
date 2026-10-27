<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arTemplateParameters = array(
	'SHOW_DETAIL_LINK' => array(
		'NAME' => GetMessage('SHOW_DETAIL_LINK'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'COUNT_IN_LINE' => array(
		'NAME' => GetMessage('COUNT_IN_LINE'),
		'TYPE' => 'STRING',
		'DEFAULT' => '3',
	),
	'TITLE_BEST' => array(
		'SORT' => 705,
		'NAME' => GetMessage('T_TITLE_BEST'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	)
);
?>
