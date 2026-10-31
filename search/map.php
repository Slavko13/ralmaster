<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Карта сайта");
$rmSystemConfig = array(
	'kind' => 'site-map',
	'kicker' => 'Все разделы',
	'title' => 'Карта сайта',
	'intro' => 'Полная структура сайта РАЛМАСТЕР: продукция, металлообработка, покраска и информация о компании.',
);
require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system_start.php');

$APPLICATION->IncludeComponent("bitrix:main.map", "ralmaster-system", array(
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"SET_TITLE" => "Y",
	"LEVEL" => "7",
	"COL_NUM" => "2",
	"SHOW_DESCRIPTION" => "Y"
	),
	false
);

require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system_end.php');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
