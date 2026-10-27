<?
$bUseMap = CAllcorp2::GetFrontParametrValue('CONTACTS_USE_MAP', SITE_ID) != 'N';
$bUseFeedback = CAllcorp2::GetFrontParametrValue('CONTACTS_USE_FEEDBACK', SITE_ID) != 'N';
?>

<?CAllcorp2::ShowPageType('page_title');?>

<div class="contacts maxwidth-theme" itemscope itemtype="http://schema.org/Organization">
	<div class="row margin0 border">
		<div class="<?=($bUseMap ? 'col-md-4' : 'col-md-12')?>">
			<div>
				<span itemprop="description"><?$APPLICATION->IncludeFile(SITE_DIR."include/contacts-about.php", Array(), Array("MODE" => "html", "NAME" => "Contacts about"));?></span>
			</div>
			<br />
			<table>
				<tbody>
					<?CAllcorp2::showContactAddr('Адрес', false);?>
					<?CAllcorp2::showContactPhones('Телефон', false);?>
					<?CAllcorp2::showContactEmail('E-mail', false);?>
					<?CAllcorp2::showContactShcedule('Режим работы', false);?>
				</tbody>
			</table>
		</div>
		<?if($bUseMap):?>
			<div class="col-md-8">
			<?$APPLICATION->IncludeFile(SITE_DIR."include/contacts-site-map".(CAllcorp2::GetFrontParametrValue('CONTACTS_TYPE_MAP') == 'GOOGLE' ? '-google' : '').".php", Array(), Array("MODE" => "html", "TEMPLATE" => "include_area.php", "NAME" => "Карта"));?>
			</div>
		<?endif;?>
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