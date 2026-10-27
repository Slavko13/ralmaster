<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(isset($APPLICATION->arAuthResult))
	$arResult['ERROR_MESSAGE'] = $APPLICATION->arAuthResult;?>

<div class="border_block">
	<div class="module-form-block-wr lk-page form">
		<?ShowMessage($arResult['ERROR_MESSAGE']);?>
		<div class="form-block">
			<form name="bform" method="post" target="_top" class="bf" action="<?=$arParams["URL"];?>">
				<?if (strlen($arResult["BACKURL"]) > 0){?><input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" /><?}?>
				<input type="hidden" name="AUTH_FORM" value="Y">
				<input type="hidden" name="TYPE" value="SEND_PWD">
				<div class="top-text-block"><?=GetMessage("AUTH_FORGOT_PASSWORD_1")?></div>
				<div class="max-form-block">
					<div class="form-group animated-labels input-filed bg-color">
						<label for="USER_EMAIL"><?=GetMessage("AUTH_EMAIL")?> <span class="required-star">*</span></label>
						<div class="input">
							<input type="email" class="form-control bg-color required" name="USER_EMAIL" id="USER_EMAIL" maxlength="255" />
						</div>
					</div>

					<?if($arResult["USE_CAPTCHA"]):?>
						<?$captcha = \Aspro\Allcorp2\Captcha::getInstance();?>
						<div class="form-control form-group captcha-row clearfix forget_block">
                        	<label><span><?=$captcha->isService() && $captcha->isActive() ? GetMessage('FORM_GENERAL_RECAPTCHA') : GetMessage('REGISTER_CAPTCHA_PROMT');?>&nbsp;<span class="required-star">*</span></span></label>
							<div class="captcha_image">
								<input type="hidden" name="captcha_sid" class="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
								<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" class="captcha_img" width="180" height="40" alt="CAPTCHA" />
								<div class="captcha_reload"></div>
								<span class="refresh"><a href="javascript:;" rel="nofollow"><?=GetMessage("REFRESH")?></a></span>
							</div>
							<div class="captcha_input">
								<input type="text" class="inputtext form-control captcha" name="captcha_word" size="30" maxlength="50" value="" required />
							</div>
						</div>
					<?endif?>

					<div class="but-r">
						<button class="btn btn-default btn-lg bold" type="submit" name="send_account_info" value=""><span><?=GetMessage("RETRIEVE")?></span></button>
					</div>
				</div>
			</form>			
		</div>
		<script type="text/javascript">
			document.bform.USER_EMAIL.focus();
			$('form[name=bform]').validate({
				highlight: function( element ){
					$(element).parent().addClass('error');
				},
				unhighlight: function( element ){
					$(element).parent().removeClass('error');
				},
				submitHandler: function( form ){
					if( $('form[name=bform]').valid() ){
						var eventdata = {type: 'form_submit', form: form, form_name: 'FORGOT'};
						BX.onCustomEvent('onSubmitForm', [eventdata]);
					}
				},
			})
		</script>
	</div>
</div>