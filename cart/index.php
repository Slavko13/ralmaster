<?php
$rmBasketAjax = (
	isset($_SERVER['HTTP_X_REQUESTED_WITH'])
	&& strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
	&& (string)$_SERVER['REQUEST_METHOD'] === 'POST'
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
if ($rmBasketAjax) {
	$APPLICATION->RestartBuffer();
}
$APPLICATION->SetTitle("Корзина");
$rmSystemConfig = array(
	'kind' => 'cart',
	'kicker' => '',
	'title' => 'Корзина',
	'intro' => 'Проверьте выбранные позиции и отправьте заявку специалистам РАЛМАСТЕР.',
);
if (!$rmBasketAjax) {
	require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system_start.php');
}
?>
<?$APPLICATION->IncludeComponent(
	"aspro:basket.allcorp2", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"INNER_ONLY" => ($rmBasketAjax ? "Y" : "N")
	),
	false
);?>
<?php
if ($rmBasketAjax) {
	die();
}
require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system_end.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
