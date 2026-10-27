<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="row">
	<div class="maxwidth-theme">
		<div class="col-md-12">
			<div class="form order<?=($arResult['FORM_NOTE'] ? ' success' : '')?><?=($arResult['isFormErrors'] == 'Y' ? ' error' : '')?>">
				<!--noindex-->
				<?if($arResult["isFormErrors"] == "Y"):?>
					<div class="form-error alert alert-danger"><?=$arResult["FORM_ERRORS_TEXT"]?></div>
				<?endif;?>
				<?=$arResult["FORM_HEADER"]?>
				<?=bitrix_sessid_post();?>
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<?if($arResult["isFormDescription"] == "Y"):?>
								<div class="description"><?=$arResult["FORM_DESCRIPTION"]?></div>
							<?endif;?>
						</div>
						<div class="col-md-12 col-sm-12">
							<?if(is_array($arResult["QUESTIONS"])):?>
								<?foreach($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion):?>
									<?if($FIELD_SID == "ORDER_LIST"):?>
										<div class="hidden">
									<?endif;?>
									<?CAllcorp2::drawFormField($FIELD_SID, $arQuestion, 'ORDER', $arParams);?>
									<?if($FIELD_SID == "ORDER_LIST"):?>
										</div>
									<?endif;?>
								<?endforeach;?>
							<?endif;?>
							<?if($arResult["isUseCaptcha"] == "Y"):?>
								<div class="form-control captcha-row clearfix">
									<label><span><?=GetMessage("FORM_CAPRCHE_TITLE")?>&nbsp;<span class="required-star">*</span></span></label>
									<div class="captcha_image">
										<img src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult["CAPTCHACode"])?>" border="0" />
										<input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsbx($arResult["CAPTCHACode"])?>" />
										<div class="captcha_reload"></div>
									</div>
									<div class="captcha_input">
										<input type="text" class="inputtext captcha" name="captcha_word" size="30" maxlength="50" value="" required />
									</div>
								</div>
							<?elseif($arParams["HIDDEN_CAPTCHA"] == "Y"):?>
								<textarea name="nspm" style="display:none;"></textarea>
							<?endif;?>
							<div class="form-footer1" style="margin-top: 26px;">
								<?if($arParams["SHOW_LICENCE"] == "Y"):?>
									<div class="licence_block bx_filter">
										<input type='hidden' name='aspro_allcorp2_form_validate'/>
										<input type="checkbox" id="licenses_inline_<?=$arResult["arForm"]["ID"];?>" <?=(COption::GetOptionString("aspro.allcorp2", "LICENCE_CHECKED", "N") == "Y" ? "checked" : "");?> name="licenses_popup" required value="Y">
										<label for="licenses_inline_<?=$arResult["arForm"]["ID"];?>">
											<?$APPLICATION->IncludeFile(SITE_DIR."include/licenses_text.php", Array(), Array("MODE" => "html", "NAME" => "LICENSES")); ?>
										</label>
									</div>
								<?endif;?>
								<div class="inner-table-block">
									<input type="submit" class="btn btn-default btn-lg" value="<?=$arResult["arForm"]["BUTTON"]?>" name="web_form_submit">
									<input type="hidden" value="<?=$arResult["arForm"]["BUTTON"]?>" name="web_form_submit">
								</div>
								<div class="inner-table-block">
									<div class="more_text_small">
										<span class="required-star">*</span> &mdash; <?=GetMessage("FORM_NOTE_REQUIRED")?>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?=$arResult["FORM_FOOTER"]?>
				<!--/noindex-->
				<script type="text/javascript">
				$(document).ready(function(){
					if(arAllcorp2Options['THEME']['USE_SALE_GOALS'] !== 'N'){
						var eventdata = {goal: 'goal_order_begin'};
						BX.onCustomEvent('onCounterGoals', [eventdata]);
					}
					var sessionID = '<?=bitrix_sessid()?>';
					$('input[data-sid=SESSION_ID]').val(sessionID);

					$('form[name="<?=$arResult["arForm"]["VARNAME"]?>"]').validate({
						highlight: function( element ){
							$(element).parent().addClass('error');
						},
						unhighlight: function( element ){
							$(element).parent().removeClass('error');
						},
						submitHandler: function( form ){
							if( $('form[name="<?=$arResult["arForm"]["VARNAME"]?>"]').valid() ){
								setTimeout(function() {
									$(form).find('button[type="submit"]').attr("disabled", "disabled");
								}, 300);
								var eventdata = {type: 'form_submit', form: form, form_name: '<?=$arResult["arForm"]["VARNAME"]?>'};
								BX.onCustomEvent('onSubmitForm', [eventdata]);
							}
						},
						errorPlacement: function( error, element ){
							error.insertBefore(element);
						},
						messages:{
					      licenses_popup: {
					        required : BX.message('JS_REQUIRED_LICENSES')
					      }
						}
					});

					
					if(arAllcorp2Options['THEME']['PHONE_MASK'].length){
						var base_mask = arAllcorp2Options['THEME']['PHONE_MASK'].replace( /(\d)/g, '_' );
						$('form[name="<?=$arResult["arForm"]["VARNAME"]?>"] input.phone').inputmask('mask', {'mask': arAllcorp2Options['THEME']['PHONE_MASK'], 'showMaskOnHover': false });
						$('form[name="<?=$arResult["arForm"]["VARNAME"]?>"] input.phone').blur(function(){
							if( $(this).val() == base_mask || $(this).val() == '' ){
								if( $(this).hasClass('required') ){
									$(this).parent().find('div.error').html(BX.message('JS_REQUIRED'));
								}
							}
						});
					}
					
					if(arAllcorp2Options['THEME']['DATE_MASK'].length)
					{
						$('form[name="<?=$arResult["arForm"]["VARNAME"]?>"] input.date').inputmask('datetime', {
							'inputFormat':  arAllcorp2Options['THEME']['DATE_MASK'],
							'placeholder': arAllcorp2Options['THEME']['DATE_PLACEHOLDER'],
							'showMaskOnHover': false
						});
					}

					if(arAllcorp2Options['THEME']['DATETIME_MASK'].length)
					{
						$('form[name="<?=$arResult["arForm"]["VARNAME"]?>"] input.datetime').inputmask('datetime', {
							'inputFormat':  arAllcorp2Options['THEME']['DATETIME_MASK'],
							'placeholder': arAllcorp2Options['THEME']['DATETIME_PLACEHOLDER'],
							'showMaskOnHover': false
						});
					}

					$('.jqmClose').on('click', function(e){
						e.preventDefault();
						$(this).closest('.jqmWindow').jqmHide();
					})

					$('input[type=file]').uniform({fileButtonHtml: BX.message('JS_FILE_BUTTON_NAME'), fileDefaultHtml: BX.message('JS_FILE_DEFAULT')});
					$(document).on('change', 'input[type=file]', function(){
						if($(this).val())
						{
							$(this).closest('.uploader').addClass('files_add');
						}
						else
						{
							$(this).closest('.uploader').removeClass('files_add');
						}
					})
					$('.form .add_file').on('click', function(){
						var index = $(this).closest('.input').find('input[type=file]').length+1;
						$('<input type="file" id="POPUP_FILE" name="FILE_n'+index+'"   class="inputfile" value="" />').insertBefore($(this));
						$('input[type=file]').uniform({fileButtonHtml: BX.message('JS_FILE_BUTTON_NAME'), fileDefaultHtml: BX.message('JS_FILE_DEFAULT')});
					})
					var dateFormat = 'dd.mm.yyyy';
					var now = new Date();
					var tomorrow = now.getFullYear() + "-0" + (now.getMonth() + 1) + "-" + (now.getDate() + 1) + " " + now.getHours() + ":" + now.getMinutes();

					$('.form.order .date').datetimepicker({
						weekStart: 1,
						todayBtn:  1,
						autoclose: 1,
						todayHighlight: 1,
						startView: 2,
						forceParse: 0,	
						format: dateFormat,
						autoclose: true,
						language: 'ru',
						keyboardNavigation: false,
						minView: 2, 
						pickTime: false,
					}).on('changeDate', function(ev){
						$('.form.order .date').closest('.form-group').addClass('input-filed');
					});

					$('.form.order .date').datetimepicker('setStartDate', '22.01.2019');
					var datePickerHtml = $('.datetimepicker').detach();


					var field = $('.form.order .date').closest('.form-group');
					field.append(datePickerHtml);

					$('.form.order .date').on('click', function(e){
						e.stopPropagation();
					});

					$('body').on('click', function(e){
						if($(e.target)[0] != field[0]) {
							$('.datetimepicker').hide();
						}
					});
				});
				</script>
			</div>
		</div>
	</div>
</div>