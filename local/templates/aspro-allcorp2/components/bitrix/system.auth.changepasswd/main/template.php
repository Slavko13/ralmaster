<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$lastLogin = $arResult["LAST_LOGIN"]?: (isset($_SESSION['lastLoginSave']) ? $_SESSION['lastLoginSave'] : ''); ?>

<?global $arTheme;?>
<div class="module-form-block-wr lk-page border_block">
	<div class="max-form-block">
		<?if(isset($APPLICATION->arAuthResult)) {
				$arResult['ERROR_MESSAGE'] = $APPLICATION->arAuthResult;
				ShowMessage($arResult['ERROR_MESSAGE']);
			}?>
			
		<?
		if( isset($_POST["LAST_LOGIN"]) && empty( $_POST["LAST_LOGIN"] ) ){
					$arResult["ERRORS"]["LAST_LOGIN"] = GetMessage("REQUIRED_FIELD");
				}
				if( isset($_POST["USER_PASSWORD"]) && strlen( $_POST["USER_PASSWORD"] ) < 6 ){
					$arResult["ERRORS"]["USER_PASSWORD"] = GetMessage("PASSWORD_MIN_LENGTH_2");
				}
				if( isset($_POST["USER_PASSWORD"]) && empty( $_POST["USER_PASSWORD"] ) ){
					$arResult["ERRORS"]["USER_PASSWORD"] = GetMessage("REQUIRED_FIELD");
				}
				if( isset($_POST["USER_CONFIRM_PASSWORD"]) && strlen( $_POST["USER_CONFIRM_PASSWORD"] ) < 6 ){
					$arResult["ERRORS"]["USER_CONFIRM_PASSWORD"] = GetMessage("PASSWORD_MIN_LENGTH_2");
				}
				if( isset($_POST["USER_CONFIRM_PASSWORD"]) && empty( $_POST["USER_CONFIRM_PASSWORD"] ) ){
					$arResult["ERRORS"]["USER_CONFIRM_PASSWORD"] = GetMessage("REQUIRED_FIELD");
				}
				if( $_POST["USER_PASSWORD"] != $_POST["USER_CONFIRM_PASSWORD"] ){
					$arResult["ERRORS"]["USER_CONFIRM_PASSWORD"] = GetMessage("WRONG_PASSWORD_CONFIRM");
				}
		if ($arResult['SHOW_ERRORS'] == 'Y' ){
			ShowMessage($arResult['ERROR_MESSAGE']);?>
			<p><font class="errortext"><?=GetMessage("WRONG_LOGIN_OR_PASSWORD")?></font></p>
		<?}?>
		<?if( (empty($arResult["ERRORS"]) && $arResult['ERROR_MESSAGE']['TYPE'] != 'ERROR') && (!empty( $_POST["change_pwd"] ) || $_POST['TYPE']) ){?>
			<p><?=GetMessage("CHANGE_SUCCESS")?></p>
			<div class="but-r"><a href="<?=$arParams["AUTH_URL"];?>" class="btn btn-default"><span><?=GetMessage("LOGIN")?></span></a></div>
		<?}else{?>
		<script>
		$(document).ready(function(){
			$(".form-block form").validate({
				<?if($arTheme["LOGIN_EQUAL_EMAIL"]["VALUE"] == "Y"):?>
				rules:{
					USER_LOGIN: {email: true}
				},
				<?endif;?>
				messages:{USER_CONFIRM_PASSWORD: {equalTo: '<?=GetMessage("PASSWORDS_DONT_MATCH")?>'}},
				submitHandler: function( form ){
					if( $('form[name=bform]').valid() ){
						var eventdata = {type: 'form_submit', form: form, form_name: 'FORGOT'};
						BX.onCustomEvent('onSubmitForm', [eventdata]);
					}
				},
			});
		})
		</script>
	    <div class="form-block form">
	        <form method="post" action="<?=$arParams["URL"];?>" name="bform" class="bf">
				<?if (strlen($arResult["BACKURL"]) > 0): ?><input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" /><?endif;?>
				<input type="hidden" name="AUTH_FORM" value="Y">
				<input type="hidden" name="TYPE" value="CHANGE_PWD">	
	            <div class="form-group animated-labels input-filed">
	                <label for="USER_LOGIN"><?=($arTheme["LOGIN_EQUAL_EMAIL"]["VALUE"] != "Y" ? GetMessage("AUTH_LOGIN_MAIN") : GetMessage("AUTH_LOGIN"));?> <span class="required-star">*</span></label>
	                <div class="input">
					<input type="text" id="USER_LOGIN" maxlength="50" required value="<?=$lastLogin?>" class="form-control bg-color" />
			        <input type="hidden" name="USER_LOGIN" value="<?=$lastLogin?>" />
					</div>
	            </div>
				<?if($arResult["USE_PASSWORD"]):?>
					<div class="form-group fill-animate <?=($arResult["USER_CURRENT_PASSWORD"] ? 'input-filed' : '');?>">
						<div class="wrap_md">
							<div class="iblock label_block">
								<label for="USER_CURRENT_PASSWORD" class="font_13 color_999"><?=GetMessage("AUTH_CURRENT_PASSWORD")?> <span class="required-star">*</span></label>
								<div class="input">
									<input type="password" name="USER_CURRENT_PASSWORD" id="USER_CURRENT_PASSWORD" maxlength="50" required value="<?=$arResult["USER_CURRENT_PASSWORD"]?>" class="form-control bg-color current_password <?=( isset($arResult["ERRORS"]) && array_key_exists( "USER_CURRENT_PASSWORD", $arResult["ERRORS"] ))? "error": ''?>" />
								</div>
							</div>
							<div class="iblock text_block">
								<?=GetMessage("PASSWORD_MIN_LENGTH")?>
							</div>
						</div>
					</div>
				<?else:?>
					<input type="hidden" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" class="bx-auth-input"  />
				<?endif;?>	
	            <div class="form-group bg-color animated-labels <?=($arResult["USER_PASSWORD"] ? 'input-filed' : '');?>">
					<div class="wrap_md">
						<div class="iblock label_block">
							<label for="USER_PASSWORD"><?=GetMessage("AUTH_NEW_PASSWORD_REQ")?> <span class="required-star">*</span></label>
							<div class="input">
								<input type="password" name="USER_PASSWORD" id="USER_PASSWORD" maxlength="50" required value="<?=$arResult["USER_PASSWORD"]?>" class="form-control bg-color password <?=( isset($arResult["ERRORS"]) && array_key_exists( "USER_PASSWORD", $arResult["ERRORS"] ))? "error": ''?>" />
							</div>
						</div>
						<div class="iblock text_block">
							<?=GetMessage("PASSWORD_MIN_LENGTH")?>
						</div>
					</div>
	            </div>	
	            <div class="form-group bg-color animated-labels <?=($arResult["USER_CONFIRM_PASSWORD"] ? 'input-filed' : '');?>">
	                <label for="USER_CONFIRM_PASSWORD"><?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?> <span class="required-star">*</span></label>
	                <div class="input">
						<input type="password" name="USER_CONFIRM_PASSWORD" id="USER_CONFIRM_PASSWORD" maxlength="50" required value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" class="form-control bg-color confirm_password <?=(isset($arResult["ERRORS"]) && array_key_exists( "USER_CONFIRM_PASSWORD", $arResult["ERRORS"] ))? "error": ''?>"  />
					</div>
	            </div>
	            <?if ($arResult["USE_CAPTCHA"]):?>
					<?$captcha = \Aspro\Allcorp2\Captcha::getInstance();?>
					<div class="form-control captcha-row forget_block clearfix">
                        <label><span><?=$captcha->isService() && $captcha->isActive() ? GetMessage('FORM_GENERAL_RECAPTCHA') : GetMessage('REGISTER_CAPTCHA_PROMT');?>&nbsp;<span class="required-star">*</span></span></label>
						<div class="captcha_image">
                            <img data-src src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE'];?>" class="captcha_img">
							<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" class="captcha_img" width="180" height="40" alt="CAPTCHA" />
							<div class="captcha_reload"></div>
							<span class="refresh"><a href="javascript:;" rel="nofollow"><?=GetMessage("REFRESH")?></a></span>
						</div>
						<div class="captcha_input">
							<input type="text" class="inputtext form-control captcha" name="captcha_word" maxlength="50" value="" required />
						</div>
					</div>

				<?endif?>
	            <div class="but-r">
					<button class="btn btn-default btn-lg bold" type="submit" name="change_pwd" value="<?=GetMessage("AUTH_CHANGE")?>"><span><?=GetMessage("CHANGE_PASSWORD")?></span></button>		
				</div> 		
	    	</form> 
	    </div>
		<script type="text/javascript">document.bform.USER_LOGIN.focus();</script>
		<?}?>
	</div>
</div>