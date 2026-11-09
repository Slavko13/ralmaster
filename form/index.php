<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заполнение формы");
?>
<style type="text/css">
section.page-top {display:none;}
.form.inline{margin-top:15px;}
.form .form-header{/*border-top: 1px solid #f5f5f5 !important;*/padding: 15px 0px 9px 0px;}
</style>
<?

$id = max(0, (int) ($_REQUEST["id"] ?? 0));
$isCallBack = $id == CCache::$arIBlocks[SITE_ID]["aspro_allcorp2_form"]["aspro_allcorp2_callback"][0];
$successMessage = ($isCallBack ? "<p>Наш менеджер перезвонит вам в ближайшее время.</p><p>Спасибо за ваше обращение!</p>" : "Спасибо! Ваше сообщение отправлено!");
$rawDataTrigger = (isset($_REQUEST["data-trigger"]) && is_string($_REQUEST["data-trigger"]) && strlen($_REQUEST["data-trigger"]) <= 8192)
	? $_REQUEST["data-trigger"]
	: '{}';
$arDataTrigger = json_decode($rawDataTrigger, true); // always UTF-8
if (!is_array($arDataTrigger)) {
	$arDataTrigger = [];
}
$arDataTrigger = array_filter(
	$arDataTrigger,
	static fn($value, $key) => is_string($key) && preg_match('/^data-[a-z0-9_-]{1,80}$/D', $key) === 1 && is_scalar($value) && strlen((string) $value) <= 1000,
	ARRAY_FILTER_USE_BOTH
);
?>
<?if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'review'):?>
	<?include_once('../ajax/review.php');?>
<?elseif(isset($_REQUEST['form_id']) && $_REQUEST['form_id'] == 'city_chooser'):?>
	<?include_once('../ajax/city_chooser.php');?>
<?elseif(isset($_REQUEST['type']) && $_REQUEST['type'] == 'auth'):?>
	<?include_once('../ajax/auth.php');?>
<?elseif($id):?>
	<div class="swipeignore">
		<?$APPLICATION->IncludeComponent(
			"aspro:form.allcorp2", "inline",
			Array(
				"IBLOCK_TYPE" => "aspro_allcorp2_form",
				"IBLOCK_ID" => $id,
				"USE_CAPTCHA" => $captcha,
				"AJAX_MODE" => "Y",
				"AJAX_OPTION_JUMP" => "Y",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "100000",
				"AJAX_OPTION_ADDITIONAL" => "",
				"SUCCESS_MESSAGE" => $successMessage,
				"SEND_BUTTON_NAME" => "Отправить",
				"SEND_BUTTON_CLASS" => "btn btn-default",
				"DISPLAY_CLOSE_BUTTON" => "N",
				"CLOSE_BUTTON_NAME" => "Закрыть",
				"CLOSE_BUTTON_CLASS" => "jqmClose btn btn-default bottom-close",
			)
		);?>
	</div>
	<?if($arDataTrigger && strlen($name)):?>
		<script type="text/javascript">
		var name = '<?=$name?>';
		var arTriggerAttrs = <?=json_encode($arDataTrigger)?>;
		$(document).ready(function() {
			$.each(arTriggerAttrs, function(index, val){
				if( /^data\-autoload\-(.+)$/.test(index)){
					var key = index.match(/^data\-autoload\-(.+)$/)[1];
					var el = $('input[data-sid="'+key.toUpperCase()+'"]');
					if(!el.length)
						el = $('input[name="'+key.toUpperCase()+'"]');
					if(el.closest('.form-group').length)
						el.closest('.form-group').addClass('input-filed');
					el.val(val).attr('readonly', 'readonly').attr('title', val);
				}
			});
			
			if(name == 'order_product'){
				if(arTriggerAttrs['data-product'].length){
					$('input[name="PRODUCT"]').val(arTriggerAttrs['data-product']).attr('readonly', 'readonly').attr('title', arTriggerAttrs['data-product']);
				}
			}
		});
		</script>
	<?endif;?>
<?else:?>
	<div class="alert alert-warning">Не указан ID формы</div>
	<?CAllcorp2::goto404Page();?>
<?endif;?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
