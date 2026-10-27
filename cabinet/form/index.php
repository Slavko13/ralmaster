<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Написать директору");
?>
<?$APPLICATION->IncludeComponent(
	"aspro:form.allcorp2", 
	"director", 
	array(
		"IBLOCK_TYPE" => "aspro_allcorp2_form",
		"IBLOCK_ID" => CAllcorp2::getFormID("aspro_allcorp2_director"),
		"USE_CAPTCHA" => "Y",
		"IS_PLACEHOLDER" => "N",
		"SUCCESS_MESSAGE" => "Спасибо! Ваше сообщение отправлено!",
		"SEND_BUTTON_NAME" => "Отправить",
		"SEND_BUTTON_CLASS" => "btn btn-default",
		"DISPLAY_CLOSE_BUTTON" => "N",
		"CLOSE_BUTTON_NAME" => "Обновить страницу",
		"CLOSE_BUTTON_CLASS" => "btn btn-default refresh-page",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "Y",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "100000",
		"AJAX_OPTION_ADDITIONAL" => "",
		"COMPONENT_TEMPLATE" => "director",
		"SHOW_LICENCE" => $arTheme["SHOW_LICENCE"]["VALUE"],
		"CACHE_GROUPS" => "Y"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>