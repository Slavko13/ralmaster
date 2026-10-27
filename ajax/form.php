<?define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
use CAllcorp2 as Solution;
if(\Bitrix\Main\Loader::includeModule('aspro.allcorp2'))
	$arTheme = CAllcorp2::GetFrontParametrsValues(SITE_ID);
$id = (isset($_REQUEST["id"]) ? $_REQUEST["id"] : false);

if (isset($_REQUEST["additional"])){
    $additionalParams = Solution::unserialize(base64_decode($_REQUEST["additional"]));
}

$isCallBack = $id == CCache::$arIBlocks[SITE_ID]["aspro_allcorp2_form"]["aspro_allcorp2_callback"][0];
$successMessage = ($isCallBack ? "<p>Наш менеджер перезвонит вам в ближайшее время.</p><p>Спасибо за ваше обращение!</p>" : "Спасибо! Ваше сообщение отправлено!");
?>
<span class="jqmClose top-close fa fa-close"></span>
<?if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'review'):?>
	<?include_once('review.php');?>
<?elseif(isset($_REQUEST['form_id']) && $_REQUEST['form_id'] == 'city_chooser'):?>
	<?include_once('city_chooser.php');?>
<?elseif(isset($_REQUEST['type']) && $_REQUEST['type'] == 'auth'):?>
	<?include_once('auth.php');?>
<?elseif($id):?>
	<?$APPLICATION->IncludeComponent(
		"aspro:form.allcorp2", "popup",
		Array(
			"IBLOCK_TYPE" => "aspro_allcorp2_form",
			"IBLOCK_ID" => $id,
			"AJAX_MODE" => "Y",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "N",
			"AJAX_OPTION_HISTORY" => "N",
			"SHOW_LICENCE" => $arTheme["SHOW_LICENCE"],
			"LICENCE_TEXT" => $arTheme["LICENCE_TEXT"],
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "100000",
			"AJAX_OPTION_ADDITIONAL" => "",
			//"IS_PLACEHOLDER" => "Y",
			"SUCCESS_MESSAGE" => $successMessage,
			"SEND_BUTTON_NAME" => "Отправить",
			"SEND_BUTTON_CLASS" => "btn btn-default",
			"SET_NAME_AND_PREVIEW" => isset($additionalParams["set_name"]) ? $additionalParams["set_name"] : "N",
			"CREATE_DEACTIVATED" => isset($additionalParams["deactivate"]) ? $additionalParams["deactivate"] : "N",
			"DISPLAY_CLOSE_BUTTON" => "Y",
			"POPUP" => "Y",
			"CLOSE_BUTTON_NAME" => "Закрыть",
			"CLOSE_BUTTON_CLASS" => "jqmClose btn btn-default bottom-close"
		)
	);?>
<?endif;?>