<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"TITLE" => Array(
		"NAME" => GetMessage("TITLE_BLOCK_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("BLOCK_NAME"),
	),
	"RIGHT_TITLE" => Array(
		"NAME" => GetMessage("TITLE_BLOCK_ALL_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("BLOCK_ALL_NAME"),
	),
	"RIGHT_LINK" => Array(
		"NAME" => GetMessage("ALL_URL_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "product/",
	),
	"SECTION_COUNT" => Array(
		"NAME" => GetMessage("SECTION_COUNT_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "4",
	),
);
?>
