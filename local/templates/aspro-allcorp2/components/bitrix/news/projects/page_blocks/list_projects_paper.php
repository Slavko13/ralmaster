<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

global $APPLICATION, $arTheme;

$rmProjectsAssetRoot = SITE_TEMPLATE_PATH.'/images/ralmaster/';
$rmProjectsEstimateFormId = (int)CAllcorp2::getFormID('aspro_allcorp2_order_services');

if (\Bitrix\Main\Loader::includeModule('form')) {
	$rmProjectsEstimateFormResult = CForm::GetBySID('ralmaster_estimate_s1');
	$rmProjectsEstimateForm = $rmProjectsEstimateFormResult ? $rmProjectsEstimateFormResult->Fetch() : false;
	if (!empty($rmProjectsEstimateForm['ID'])) {
		$rmProjectsEstimateFormId = (int)$rmProjectsEstimateForm['ID'];
	}
}
?>

<main class="rm-projects-page" data-rm-projects-page="listing">
	<section class="rm-projects-hero" data-rm-projects-section="hero">
		<div class="rm-projects-container">
			<nav class="rm-projects-breadcrumbs" aria-label="Хлебные крошки">
				<a href="/">Главная</a><span aria-hidden="true">/</span><span>Реализованные проекты</span>
			</nav>
			<h1>Реализованные проекты</h1>
			<p>Изделия для жилых комплексов и строительных объектов.</p>
		</div>
	</section>

	<section class="rm-projects-list" data-rm-projects-section="projects" aria-label="Список реализованных проектов">
		<div class="rm-projects-container">
			<?$APPLICATION->IncludeComponent(
				'bitrix:news.list',
				'projects-paper',
				array(
					'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
					'IBLOCK_ID' => $arParams['IBLOCK_ID'],
					'NEWS_COUNT' => '3',
					'SORT_BY1' => $arParams['SORT_BY1'],
					'SORT_ORDER1' => $arParams['SORT_ORDER1'],
					'SORT_BY2' => $arParams['SORT_BY2'],
					'SORT_ORDER2' => $arParams['SORT_ORDER2'],
					'FIELD_CODE' => array('ID', 'IBLOCK_ID', 'CODE', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'),
					'PROPERTY_CODE' => array('TASK_PROJECT', 'PHOTOS'),
					'CHECK_DATES' => 'Y',
					'INCLUDE_SUBSECTIONS' => 'Y',
					'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['detail'],
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
	</section>

	<section class="rm-projects-products" data-rm-projects-section="products">
		<div class="rm-projects-container">
			<div class="rm-projects-heading">
				<h2>Продукция</h2>
				<a class="rm-projects-text-link" href="/product/">Вся продукция <span aria-hidden="true">↗</span></a>
			</div>
			<?$APPLICATION->IncludeComponent(
				'bitrix:news.list',
				'project-products-paper',
				array(
					'IBLOCK_TYPE' => 'aspro_allcorp2_catalog',
					'IBLOCK_ID' => (int)$arParams['CATALOG_IBLOCK_ID'],
					'NEWS_COUNT' => '3',
					'CACHE_TYPE' => $arParams['CACHE_TYPE'],
					'CACHE_TIME' => $arParams['CACHE_TIME'],
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
	</section>

	<section class="rm-projects-operations" data-rm-projects-section="operations">
		<div class="rm-projects-container">
			<div class="rm-projects-operations__heading">
				<h2>Несколько операций<br>в одном заказе</h2>
				<p>Можно заказать отдельную обработку или изготовление изделия с последующей окраской и сборкой. Состав работ согласуем по вашему чертежу.</p>
			</div>
			<div class="rm-projects-operation-grid">
				<article><img src="<?=htmlspecialcharsbx($rmProjectsAssetRoot.'home/hero-laser-cutting.webp')?>" srcset="<?=htmlspecialcharsbx($rmProjectsAssetRoot.'home/hero-laser-cutting-768.webp')?> 768w, <?=htmlspecialcharsbx($rmProjectsAssetRoot.'home/hero-laser-cutting.webp')?> 1672w" sizes="(max-width: 767px) calc(100vw - 32px), 33vw" alt="" loading="lazy"><strong>Резка</strong></article>
				<article><img src="<?=htmlspecialcharsbx($rmProjectsAssetRoot.'home/metal-bending.webp')?>" srcset="<?=htmlspecialcharsbx($rmProjectsAssetRoot.'home/metal-bending-768.webp')?> 768w, <?=htmlspecialcharsbx($rmProjectsAssetRoot.'home/metal-bending.webp')?> 1536w" sizes="(max-width: 767px) calc(100vw - 32px), 33vw" alt="" loading="lazy"><strong>Гибка</strong></article>
				<article><img src="<?=htmlspecialcharsbx($rmProjectsAssetRoot.'home/production-powder-line.webp')?>" srcset="<?=htmlspecialcharsbx($rmProjectsAssetRoot.'home/production-powder-line-768.webp')?> 768w, <?=htmlspecialcharsbx($rmProjectsAssetRoot.'home/production-powder-line.webp')?> 1672w" sizes="(max-width: 767px) calc(100vw - 32px), 33vw" alt="" loading="lazy"><strong>Окраска</strong></article>
			</div>
			<div class="rm-projects-operations__footer">
				<p>При необходимости включим в заказ сварку и сборку.</p>
				<nav aria-label="Связанные услуги"><a href="/product/">Изготовление по чертежам <span aria-hidden="true">↗</span></a><a href="/painting/">Порошковая окраска <span aria-hidden="true">↗</span></a></nav>
			</div>
		</div>
	</section>

	<section class="rm-projects-estimate" id="projects-estimate" data-rm-projects-section="estimate">
		<div class="rm-projects-estimate__layout">
			<div class="rm-projects-estimate__aside">
				<span class="rm-projects-kicker">Новый проект</span>
				<h2>Рассчитать проект</h2>
				<p>Пришлите чертёж или техническое задание и укажите количество изделий. Подготовим расчёт и предложим технологию изготовления.</p>
			</div>
			<div class="rm-projects-estimate__form" data-rm-estimate-form data-rm-communication-selector="true" data-rm-estimate-context="projects" aria-label="Форма запроса расчёта проекта">
				<?$APPLICATION->IncludeComponent(
					'aspro:form.allcorp2',
					'contacts',
					array(
						'IBLOCK_TYPE' => 'aspro_allcorp2_form',
						'IBLOCK_ID' => $rmProjectsEstimateFormId,
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
						'AJAX_OPTION_ADDITIONAL' => 'rm-projects-estimate-form',
					),
					false
				);?>
			</div>
		</div>
	</section>

	<section class="rm-projects-production-photo" data-rm-projects-section="production-photo" aria-label="Производство РАЛМАСТЕР">
		<img src="<?=htmlspecialcharsbx($rmProjectsAssetRoot.'home/production-powder-line.webp')?>" srcset="<?=htmlspecialcharsbx($rmProjectsAssetRoot.'home/production-powder-line-768.webp')?> 768w, <?=htmlspecialcharsbx($rmProjectsAssetRoot.'home/production-powder-line.webp')?> 1672w" sizes="100vw" alt="Производство РАЛМАСТЕР" loading="lazy">
		<span aria-hidden="true"></span>
	</section>
</main>
