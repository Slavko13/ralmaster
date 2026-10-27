<?
$bUseMap = CAllcorp2::GetFrontParametrValue('CONTACTS_USE_MAP', SITE_ID) != 'N';
$bUseFeedback = CAllcorp2::GetFrontParametrValue('CONTACTS_USE_FEEDBACK', SITE_ID) != 'N';
?>
<div itemscope itemtype="http://schema.org/Organization">
	<?if($bUseMap):?>
		<div class="contacts-page-map">
		<?$APPLICATION->IncludeFile(SITE_DIR."include/contacts-site-map".(CAllcorp2::GetFrontParametrValue('CONTACTS_TYPE_MAP') == 'GOOGLE' ? '-google' : '').".php", Array(), Array("MODE" => "html", "TEMPLATE" => "include_area.php", "NAME" => "Map"));?>
		</div>
	<?endif;?>

	<div class="contacts contacts-page-map-overlay maxwidth-theme">
		<div class="contacts-wrapper">
			<div class="row">
				<div class="col-md-3">
					<?CAllcorp2::showContactAddr('Адрес');?>
				</div>
				<div class="col-md-3">
					<?CAllcorp2::showContactPhones('Телефон');?>
				</div>
				<div class="col-md-3">
					<?CAllcorp2::showContactEmail('E-mail');?>
				</div>
				<div class="col-md-3">
					<?CAllcorp2::showContactShcedule('Режим работы');?>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="contacts maxwidth-theme <?=($bUseMap ? 'top-cart' : '');?>">
			<div class="col-md-12" itemprop="description">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/contacts-about.php", Array(), Array("MODE" => "html", "NAME" => "Contacts about"));?>
			</div>
		</div>
	</div>
	<?//hidden text for validate microdata?>
	<div class="hidden">
		<?global $arSite;?>
		<span itemprop="name"><?=$arSite["NAME"];?></span>
	</div>
</div>
<?if($bUseFeedback):?>
	<div class="row contacts">
		<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("contacts-form-block");?>
		<?global $arTheme;?>
		<?$APPLICATION->IncludeComponent(
			"aspro:form.allcorp2",
			"contacts",
			array(
				"IBLOCK_TYPE" => "aspro_allcorp2_form",
				"IBLOCK_ID" => CAllcorp2::getFormID("aspro_allcorp2_question"),
				"USE_CAPTCHA" => "Y",
				"IS_PLACEHOLDER" => "N",
				"SUCCESS_MESSAGE" => "Спасибо! Ваше сообщение отправлено!",
				"SEND_BUTTON_NAME" => "Отправить",
				"SEND_BUTTON_CLASS" => "btn btn-default",
				"DISPLAY_CLOSE_BUTTON" => "Y",
				"CLOSE_BUTTON_NAME" => "Обновить страницу",
				"CLOSE_BUTTON_CLASS" => "btn btn-default refresh-page",
				"SHOW_LICENCE" => $arTheme["SHOW_LICENCE"]["VALUE"],
				"LICENCE_TEXT" => $arTheme["SHOW_LICENCE"]["DEPENDENT_PARAMS"]["LICENCE_TEXT"]["VALUE"],
				"AJAX_MODE" => "Y",
				"AJAX_OPTION_JUMP" => "Y",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "100000",
				"AJAX_OPTION_ADDITIONAL" => ""
			),
			false
		);?>
		<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("contacts-form-block", "");?>
	</div>
<?endif;?>