<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arTemplateParameters = array(
	'TITLE' => array(
		'NAME' => GetMessage('T_TITLE'),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage('V_TITLE'),
	),
	'COUNT_IN_LINE' => array(
		'NAME' => GetMessage('T_COUNT_IN_LINE'),
		'TYPE' => 'STRING',
		'DEFAULT' => 3,
	),
);
?>