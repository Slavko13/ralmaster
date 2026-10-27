<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arTemplateParameters = array(
	'TITLE' => array(
		'NAME' => GetMessage('TITLE'),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage('TITLE_DEFAULT'),
	),
	"RIGHT_TITLE" => Array(
		"NAME" => GetMessage("TITLE_BLOCK_ALL_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("BLOCK_ALL_NAME"),
	),
	"RIGHT_LINK" => Array(
		"NAME" => GetMessage("ALL_URL_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "company/partners/",
	),
);
?>
