<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(
	$arResult['ERROR'] &&
	$arResult['ERROR_MESSAGE']['TYPE'] === 'ERROR' &&
	$arResult['ERROR_MESSAGE']['ERROR_TYPE'] === 'CHANGE_PASSWORD' &&
	$arParams['CHANGE_PASSWORD_URL']
):?>
	
	<?$_SESSION['arAuthResult'] = $APPLICATION->arAuthResult;
	$_SESSION['lastLoginSave'] = $arResult["USER_LOGIN"];
	?>

	<script>
	location.href = '<?=$arParams['CHANGE_PASSWORD_URL'].(strlen($arResult['BACKURL']) ? (strpos($arParams['CHANGE_PASSWORD_URL'], '?') ? '&' : '?').'backurl='.$arResult['BACKURL'] : '')?>';
	</script>
<?else:?>
<?/*<link rel="stylesheet" type="text/css" href="/bitrix/js/socialservices/css/ss.css">*/?>
<?if( $arResult["FORM_TYPE"] == "login" ){?>
	<div id="ajax_auth">
		<div class="auth_wrapp form-block">
			<div class="wrap_md">
				<div class="main_info form">
					<div class="form-wr form-body">
						<?if( $arResult["ERROR"] ){?>
							<div class="alert alert-danger">
								<p><?=GetMessage('AUTH_ERROR')?></p>
							</div>
						<?}?>
						<form id="avtorization-form" name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arParams["AUTH_URL"]?>?login=yes">
							<?if($arResult["BACKURL"] <> ''):?>
								<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
							<?endif?>
							<?/*foreach ($arResult["POST"] as $key => $value):?><input type="hidden" name="<?=$key?>" value="<?=$value?>" /><?endforeach */?>
							<input type="hidden" name="AUTH_FORM" value="Y" />
							<input type="hidden" name="TYPE" value="AUTH" />

							<div class="row" data-sid="USER_LOGIN_POPUP">
								<div class="form-group animated-labels input-filed">
									<div class="col-md-12">
										<label for="USER_LOGIN_POPUP"><?=GetMessage("AUTH_LOGIN")?> <span class="required-star">*</span></label>
										<div class="input">
											<input type="text" name="USER_LOGIN" id="USER_LOGIN_POPUP" class="form-control required" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" autocomplete="on" tabindex="1"/>
										</div>
									</div>
								</div>
							</div>
							<div class="row" data-sid="USER_PASSWORD_POPUP">
								<div class="form-group animated-labels input-filed">
									<div class="col-md-12">
										<label for="USER_PASSWORD_POPUP"><?=GetMessage("AUTH_PASSWORD")?> <span class="required-star">*</span></label>
										<div class="input">
											<input type="password" name="USER_PASSWORD" id="USER_PASSWORD_POPUP" class="form-control required" maxlength="50" value="" autocomplete="on" tabindex="2"/>
										</div>
									</div>
								</div>
							</div>

							<?if ($arResult["CAPTCHA_CODE"]):?>
								<div class="row captcha-row form-group">
									<div class="col-md-12">
										<label for="captcha_word"><?=GetMessage("AUTH_CAPTCHA_PROMT")?> <span class="required-star">*</span></label>
										<div class="captcha_image">
											<img src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult["CAPTCHA_CODE"])?>" class="captcha_img" border="0" />
											<input type="hidden" name="captcha_sid" class="captcha_sid" value="<?=htmlspecialcharsbx($arResult["CAPTCHA_CODE"])?>" />
											<div class="captcha_reload"></div>
											<span class="refresh"><a href="javascript:;" rel="nofollow"><?=GetMessage("REFRESH")?></a></span>
										</div>
										<div class="captcha_input">
											<input type="text" class="inputtext form-control captcha" name="captcha_word" size="30" maxlength="50" value="" required />
										</div>
									</div>
								</div>
							<?endif?>
							<div class="but-r">
								<div class="filter block">
									<a class="forgot pull-right" href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"];?>" tabindex="3"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a>
									<div class="prompt remember pull-left">
										<input type="checkbox" id="USER_REMEMBER_frm" name="USER_REMEMBER" value="Y" tabindex="5"/>
										<label for="USER_REMEMBER_frm" title="<?=GetMessage("AUTH_REMEMBER_ME")?>" tabindex="5"><?echo GetMessage("AUTH_REMEMBER_SHORT")?></label>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="buttons clearfix">
									<button type="submit" class="btn btn-default bold" name="Login" value="" tabindex="4">
										<span><?=GetMessage("AUTH_LOGIN_BUTTON")?></span>
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<script>
				$(document).ready(function(){
					$("form[name=bx_auth_servicesform]").validate();
					$('form#avtorization-form').validate({
						rules:{
							USER_LOGIN:{
								required:true
							}
						},
						submitHandler: function( form ){
							if( $( form ).valid() ){
								if(BX('wrap_ajax_auth')){
									jsAjaxUtil.CloseLocalWaitWindow( 'id', 'wrap_ajax_auth' );
									jsAjaxUtil.ShowLocalWaitWindow( 'id', 'wrap_ajax_auth', true );
								}

								var bCaptchaInvisible = false;
								if(window.renderRecaptchaById && window.asproRecaptcha && window.asproRecaptcha.key)
								{
									if(window.asproRecaptcha.params.recaptchaSize == 'invisible' && $(form).find('.g-recaptcha').length)
									{ 
										if(!$(form).find('.g-recaptcha-response').val())
										{
										if(typeof grecaptcha != 'undefined'){
											// there need to remove the second recaptcha on sibligs form
											$(form).closest('.tab-pane').siblings().find('.g-recaptcha').remove();

											bCaptchaInvisible = true;
											grecaptcha.execute($(form).find('.g-recaptcha').data('widgetid'));
										}
									}
									}
								}
								if(!bCaptchaInvisible)
								{	
									$.ajax({
										type: "POST",
										url: $(form).attr('action'),
										data: $(form).serialize()
									}).done(function( html ){
										if($(html).find('.alert').length){
											$('#ajax_auth').html( html );
											$('#ajax_auth').find(".form-group:not(.eye-password-ignore) [type=password]").each(function (item) {
													$(this).closest(".form-group").addClass("eye-password");
											});
										}
										else{
											var match = html.match(/location\.href\s*=\s*['"]([^'"]*)['"]/);
											if (match) {
												location.href = match[1];
											}
											else{
												BX.reload(false);
											}
										}

										if(BX('wrap_ajax_auth')){
											jsAjaxUtil.CloseLocalWaitWindow( 'id', 'wrap_ajax_auth' );
										}
									});
								}

							}
						},
						errorPlacement: function( error, element ){
							$(error).attr( 'alt', $(error).text());
							$(error).attr( 'title', $(error).text());
							error.insertBefore( element );
						}
					});
				})
				</script>
			
			<?if($arResult["AUTH_SERVICES"]):?>
				<div class="reg-new">
					<div class="soc-avt">
						<div class="title"><?=GetMessage("SOCSERV_AS_USER_FORM");?></div>
						<?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons", 
							array(
								"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
								"AUTH_URL" => SITE_DIR."cabinet/?login=yes",
								"POST" => $arResult["POST"],
								"SUFFIX" => "form",
							), 
							$component, array("HIDE_ICONS"=>"Y")
						);
						?>
					</div>
				</div>
			<?endif;?>
			<?if(\Bitrix\Main\Config\Option::get('main', 'new_user_registration', 'N', NULL) == 'Y'):?>
				<div class="form-footer socserv">
					<div class="inner-table-block">
					<!--noindex--><a href="<?=$arResult["AUTH_REGISTER_URL"];?>" rel="nofollow" class="btn transparent bold register" tabindex="6"><?=GetMessage("AUTH_REGISTER_NEW")?></a><!--/noindex-->
					</div>
					<div class="inner-table-block">						
						<div class="more_text_small">
							<?$APPLICATION->IncludeFile(SITE_DIR."include/top_auth.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("TOP_AUTH_REGISTER")));?>
						</div>
						<div class="more_text_small">
							<span class="required-star">*</span> &mdash; <?=GetMessage("FORM_NOTE_REQUIRED")?>
						</div>
					</div>
				</div>
			<?endif;?>
		</div>
	</div>
<?}else{?>
	<script>
			BX.reload(true);
	</script>
<?}?>

<?// need pageobject.js for BX.reload()?>
<script>
BX.loadScript(['<?=Bitrix\Main\Page\Asset::getInstance()->getFullAssetPath('/bitrix/js/main/pageobject/pageobject.js')?>']);
</script>
<?endif;?>