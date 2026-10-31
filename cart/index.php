<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
$rmSystemConfig = array(
	'kind' => 'cart',
	'kicker' => 'Ваш заказ',
	'title' => 'Корзина',
	'intro' => 'Проверьте выбранные позиции и отправьте заявку специалистам РАЛМАСТЕР.',
);
require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system_start.php');
?>
<?$APPLICATION->IncludeComponent(
	"aspro:basket.allcorp2", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?>
<?php require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system_end.php'); ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
