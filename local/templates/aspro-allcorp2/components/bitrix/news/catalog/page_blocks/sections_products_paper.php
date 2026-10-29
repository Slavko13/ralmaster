<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

global $APPLICATION, $arTheme;

$rmProductFormId = (int)CAllcorp2::getFormID('aspro_allcorp2_order_product');
if (\Bitrix\Main\Loader::includeModule('form')) {
	$rmEstimateFormResult = CForm::GetBySID('ralmaster_estimate_s1');
	$rmEstimateForm = $rmEstimateFormResult ? $rmEstimateFormResult->Fetch() : false;
	if (!empty($rmEstimateForm['ID'])) {
		$rmProductFormId = (int)$rmEstimateForm['ID'];
	}
}
$rmProductAssetRoot = SITE_TEMPLATE_PATH.'/images/ralmaster/';
?>

<div class="rm-products-catalog" data-rm-products-section="catalog">
	<div class="rm-products-container">
		<?$APPLICATION->IncludeComponent(
			'bitrix:news.list',
			'product-sections-paper',
			array(
				'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				'NEWS_COUNT' => '20',
				'SORT_BY1' => 'SORT',
				'SORT_ORDER1' => 'ASC',
				'SORT_BY2' => 'NAME',
				'SORT_ORDER2' => 'ASC',
				'FIELD_CODE' => array('NAME', 'PREVIEW_PICTURE'),
				'PROPERTY_CODE' => array(),
				'CHECK_DATES' => 'Y',
				'PARENT_SECTION' => '',
				'PARENT_SECTION_CODE' => '',
				'INCLUDE_SUBSECTIONS' => 'N',
				'SHOW_SECTION_PREVIEW_DESCRIPTION' => 'Y',
				'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
				'CACHE_TYPE' => $arParams['CACHE_TYPE'],
				'CACHE_TIME' => $arParams['CACHE_TIME'],
				'CACHE_FILTER' => $arParams['CACHE_FILTER'],
				'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
				'SET_TITLE' => 'N',
				'SET_STATUS_404' => 'N',
				'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
				'ADD_SECTIONS_CHAIN' => 'N',
				'DISPLAY_TOP_PAGER' => 'N',
				'DISPLAY_BOTTOM_PAGER' => 'N',
			),
			$component
		);?>
	</div>
</div>

<section class="rm-product-estimate" id="product-estimate" data-rm-products-section="estimate">
	<div class="rm-products-container rm-product-estimate__layout">
		<div class="rm-product-estimate__aside">
			<div class="rm-product-estimate__visual" style="--rm-product-estimate-image: url('<?=htmlspecialcharsbx($rmProductAssetRoot.'home/metal-bending.webp')?>')">
				<h2>Рассчитать<br>изготовление изделий</h2>
				<p>Пришлите чертёж или техническое задание и укажите количество изделий. Если нужной продукции нет в каталоге, опишите задачу: проверим возможность изготовления.</p>
				<div class="rm-product-estimate__brief">
					<strong>Что поможет подготовить расчёт</strong>
					<p>Материал, размеры, количество изделий, требования к покрытию и желаемый срок.</p>
				</div>
				<span class="rm-product-estimate__cut" aria-hidden="true"></span>
			</div>
			<div class="rm-product-estimate__contacts">
				<p>Удобнее обсудить задачу напрямую?</p>
				<div class="rm-product-estimate__phone"><?CAllcorp2::ShowHeaderPhones('', 'Phone_black.svg');?></div>
				<?if (CAllcorp2::checkContentBlock(SITE_DIR.'include/footer/site-email.php', 'PROPERTY_EMAIL_VALUE')):?>
					<?CAllcorp2::showEmail('rm-product-estimate__email');?>
				<?endif;?>
			</div>
		</div>

		<div class="rm-product-estimate__form" data-rm-estimate-form data-rm-estimate-context="product" aria-label="Форма запроса расчёта">
			<?$APPLICATION->IncludeComponent(
				'aspro:form.allcorp2',
				'contacts',
				array(
					'IBLOCK_TYPE' => 'aspro_allcorp2_form',
					'IBLOCK_ID' => $rmProductFormId,
					'USE_CAPTCHA' => 'Y',
					'IS_PLACEHOLDER' => 'Y',
					'SUCCESS_MESSAGE' => 'Спасибо! Ваш запрос отправлен.',
					'SEND_BUTTON_NAME' => 'Отправить запрос на расчёт',
					'SEND_BUTTON_CLASS' => 'btn btn-default',
					'DISPLAY_CLOSE_BUTTON' => 'N',
					'SHOW_LICENCE' => $arTheme['SHOW_LICENCE']['VALUE'],
					'LICENCE_TEXT' => $arTheme['SHOW_LICENCE']['DEPENDENT_PARAMS']['LICENCE_TEXT']['VALUE'],
					'AJAX_MODE' => 'Y',
					'AJAX_OPTION_JUMP' => 'N',
					'AJAX_OPTION_STYLE' => 'Y',
					'AJAX_OPTION_HISTORY' => 'N',
					'CACHE_TYPE' => 'A',
					'CACHE_TIME' => '100000',
					'AJAX_OPTION_ADDITIONAL' => 'rm-product-estimate-form',
				),
				false
			);?>
		</div>
	</div>
</section>

<section class="rm-product-production-photo" data-rm-products-section="production-photo" aria-label="Производство РАЛМАСТЕР">
	<img src="<?=htmlspecialcharsbx($rmProductAssetRoot.'home/production-powder-line.png')?>" alt="Производство РАЛМАСТЕР" loading="lazy">
	<span aria-hidden="true"></span>
</section>
