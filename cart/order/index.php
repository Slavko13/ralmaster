<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");
$rmSystemConfig = array(
	'kind' => 'order',
	'kicker' => 'Ваш заказ',
	'title' => 'Оформление заказа',
	'intro' => 'Оставьте контактные данные и описание задачи — менеджер уточнит детали и подготовит расчёт.',
	'parent_title' => 'Корзина',
	'parent_url' => SITE_DIR.'cart/',
);
require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system_start.php');
?>
<?$APPLICATION->IncludeComponent(
	"aspro:basket.allcorp2",
	"order",
	Array(
		"COMPONENT_TEMPLATE" => "order",
		"PATH_TO_CATALOG" => SITE_DIR."product/"
	)
);?>
<?php require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system_end.php'); ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
