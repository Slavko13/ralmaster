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
		"DEFAULT" => "info/news/",
	),
	"COUNT_IN_LINE" => Array(
		"NAME" => GetMessage("COUNT_IN_LINE"),
		"TYPE" => "STRING",
		"DEFAULT" => 4,
	),
	"SHOW_DATE" => Array(
		"NAME" => GetMessage("SHOW_DATE_NAME"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SHOW_SECTION" => Array(
		"NAME" => GetMessage("SHOW_SECTION_NAME"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
);
?>
