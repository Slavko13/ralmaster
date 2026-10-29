<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Loader;

global $APPLICATION, $arTheme, $arRegion;

$APPLICATION->SetAdditionalCSS(
	SITE_TEMPLATE_PATH.'/components/bitrix/news/services/page_blocks/sections_metalworking_paper.css'
);

$rmMetalEscape = static function ($value) {
	return htmlspecialcharsbx((string)$value);
};

$rmMetalNormalizeUrl = static function ($url, $fallback = '/services/') {
	$url = trim((string)$url);
	if ($url === '') {
		return $fallback;
	}

	if (preg_match('~^https?://(?:www\.)?bavrasem\.beget\.tech(?::\d+)?(?<path>/[^?#]*)?(?<query>\?[^#]*)?~i', $url, $match)) {
		return ($match['path'] ?: '/').($match['query'] ?? '');
	}

	if (preg_match('~^(?:javascript|data):~i', $url)) {
		return $fallback;
	}

	return $url;
};

$rmMetalHomeAsset = static function ($file) {
	return SITE_TEMPLATE_PATH.'/images/ralmaster/home/'.ltrim((string)$file, '/');
};

$rmMetalAsset = static function ($file) {
	return SITE_TEMPLATE_PATH.'/images/ralmaster/metalworking/'.ltrim((string)$file, '/');
};

$rmMetalSections = array();
if (Loader::includeModule('iblock')) {
	$rmMetalSectionResult = CIBlockSection::GetList(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array('IBLOCK_ID' => (int)$arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1),
		false,
		array('ID', 'NAME', 'CODE', 'SECTION_PAGE_URL')
	);

	while ($rmMetalSection = $rmMetalSectionResult->GetNext(false, false)) {
		$rmMetalSections[(string)$rmMetalSection['CODE']] = array(
			'NAME' => html_entity_decode((string)$rmMetalSection['NAME'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
			'URL' => $rmMetalNormalizeUrl($rmMetalSection['SECTION_PAGE_URL']),
		);
	}
}

$rmMetalServiceSpecs = array(
	array(
		'CODE' => 'laser-cutting',
		'NUMBER' => '01',
		'NAME' => 'Лазерная резка',
		'DESCRIPTION' => 'Раскраиваем листовой металл на детали по контурам и размерам из вашей документации.',
		'IMAGE' => $rmMetalHomeAsset('hero-laser-cutting.webp'),
		'CTA' => 'Рассчитать резку',
		'DETAILS' => array(
			array('Материалы', 'Сталь, нержавеющая сталь, алюминий'),
			array('Размер листа', 'До 3000 × 1500 мм'),
			array('Толщина металла', 'Сталь до 10 мм, нержавеющая сталь до 6 мм, алюминий до 4 мм'),
		),
	),
	array(
		'CODE' => 'metal-bending',
		'NUMBER' => '02',
		'NAME' => 'Гибка металла',
		'DESCRIPTION' => 'Придаём листовым деталям нужную форму по размерам и углам, указанным в чертеже.',
		'IMAGE' => $rmMetalHomeAsset('metal-bending.webp'),
		'CTA' => 'Рассчитать гибку',
		'DETAILS' => array(
			array('Материалы', 'Сталь, нержавеющая сталь, алюминий'),
			array('Длина гиба', 'До 3000 мм'),
			array('Толщина металла', 'До 6 мм. Возможности гибки зависят от материала и геометрии детали'),
		),
	),
	array(
		'CODE' => 'laser-welding',
		'NUMBER' => '03',
		'NAME' => 'Сварочные работы',
		'DESCRIPTION' => 'Соединяем металлические детали в узлы и конструкции по техническому заданию.',
		'IMAGE' => $rmMetalHomeAsset('welding.webp'),
		'CTA' => 'Рассчитать сварку',
		'DETAILS' => array(
			array('Материалы', 'Сталь и нержавеющая сталь'),
			array('Виды сварки', 'Полуавтоматическая и аргонодуговая'),
			array('Габариты конструкции', 'Возможности изготовления согласуем после изучения чертежа'),
		),
	),
);

foreach ($rmMetalServiceSpecs as &$rmMetalService) {
	$rmMetalDynamic = $rmMetalSections[$rmMetalService['CODE']] ?? array();
	$rmMetalService['URL'] = $rmMetalDynamic['URL'] ?? '/services/';
}
unset($rmMetalService);

$rmMetalExamples = array(
	array(
		'TITLE' => 'Крепёжные кронштейны по чертежам заказчика',
		'TEXT' => 'Выполнены лазерная резка заготовок и гибка кронштейнов по заданным размерам.',
		'META' => 'Сталь 3 мм · Партия 300 шт.',
		'IMAGE' => $rmMetalAsset('production-chain.png'),
	),
	array(
		'TITLE' => 'Листовые панели для оборудования',
		'TEXT' => 'Выполнена лазерная резка панелей с монтажными отверстиями по документации заказчика.',
		'META' => 'Нержавеющая сталь 2 мм · 120 шт.',
		'IMAGE' => $rmMetalAsset('perforated-sheet-panels.png'),
	),
	array(
		'TITLE' => 'Сварные рамы для оборудования',
		'TEXT' => 'Выполнена сварка подготовленных деталей и порошковая окраска готовых рам.',
		'META' => 'Сталь · Партия 20 шт.',
		'IMAGE' => $rmMetalAsset('welded-frames.png'),
	),
);

$rmMetalEstimateFormId = 0;
if (Loader::includeModule('form')) {
	$rmMetalFormResult = CForm::GetBySID('ralmaster_estimate_s1');
	if ($rmMetalForm = $rmMetalFormResult->Fetch()) {
		$rmMetalEstimateFormId = (int)$rmMetalForm['ID'];
	}
}
if (!$rmMetalEstimateFormId) {
	$rmMetalEstimateFormId = (int)CAllcorp2::getFormID('aspro_allcorp2_order_services');
}

$rmMetalHasPhone = $arRegion ? !empty($arRegion['PHONES']) : (bool)(int)$arTheme['HEADER_PHONES'];
?>

<div class="rm-metalworking" data-rm-metalworking>
	<section class="rm-metal-hero" data-rm-section="hero">
		<div class="rm-metal-container">
			<nav class="rm-metal-breadcrumb" aria-label="Хлебные крошки"><a href="/">Главная</a><span aria-hidden="true">/</span><span>Металлообработка</span></nav>
			<div class="rm-metal-hero__row">
				<div>
					<h1>Металлообработка на заказ</h1>
					<p>Выполняем лазерную резку, гибку и сварку металла на собственном производстве в Видном. Принимаем заказы на отдельные операции и комплексное изготовление по чертежам и техническому заданию.</p>
				</div>
				<a class="rm-metal-button rm-metal-link-arrow" href="#rm-metal-estimate">Отправить чертёж на расчёт</a>
			</div>
		</div>
	</section>

	<section class="rm-metal-services" data-rm-section="services">
		<div class="rm-metal-container">
			<h2>Услуги металлообработки</h2>
			<div class="rm-metal-services__list">
				<?foreach ($rmMetalServiceSpecs as $rmMetalService):?>
					<article class="rm-metal-service-card" data-rm-service-code="<?=$rmMetalEscape($rmMetalService['CODE'])?>">
						<div class="rm-metal-service-card__copy">
							<div class="rm-metal-service-card__heading">
								<h3><a href="<?=$rmMetalEscape($rmMetalService['URL'])?>"><?=$rmMetalEscape($rmMetalService['NAME'])?></a></h3>
								<span><?=$rmMetalEscape($rmMetalService['NUMBER'])?></span>
							</div>
							<p><?=$rmMetalEscape($rmMetalService['DESCRIPTION'])?></p>
							<dl>
								<?foreach ($rmMetalService['DETAILS'] as $rmMetalDetail):?>
									<div><dt><?=$rmMetalEscape($rmMetalDetail[0])?></dt><dd><?=$rmMetalEscape($rmMetalDetail[1])?></dd></div>
								<?endforeach;?>
							</dl>
							<a class="rm-metal-button rm-metal-button--small rm-metal-link-arrow" href="#rm-metal-estimate"><?=$rmMetalEscape($rmMetalService['CTA'])?></a>
						</div>
						<a class="rm-metal-service-card__image" href="<?=$rmMetalEscape($rmMetalService['URL'])?>" aria-label="Подробнее: <?=$rmMetalEscape($rmMetalService['NAME'])?>"><img src="<?=$rmMetalEscape($rmMetalService['IMAGE'])?>" alt="" aria-hidden="true" loading="lazy"></a>
					</article>
				<?endforeach;?>
			</div>
			<p class="rm-metal-services__note">Демонстрационные характеристики для проектного расчёта. Материалы, размеры, толщины и виды сварки необходимо подтвердить в документации к заказу.</p>
		</div>
	</section>

	<section class="rm-metal-operations" data-rm-section="operations">
		<div class="rm-metal-container">
			<div class="rm-metal-section-heading">
				<h2>Несколько операций в одном заказе</h2>
				<p>Можем заказать отдельную обработку или изготовить изделие с последующей окраской и сборкой. Состав работ согласуем по вашему чертежу.</p>
			</div>
			<img class="rm-metal-operations__image" src="<?=$rmMetalEscape($rmMetalAsset('production-chain.png'))?>" alt="Последовательность изготовления металлического изделия" loading="lazy">
			<ol class="rm-metal-operations__steps">
				<li><span>01.</span><div><strong>После резки</strong><p>Заготовки с отверстиями и заданным контуром.</p></div><i aria-hidden="true">↗</i></li>
				<li><span>02.</span><div><strong>После гибки</strong><p>Деталь с формой и углами по чертежу.</p></div><i aria-hidden="true">↗</i></li>
				<li><span>03.</span><div><strong>После окраски</strong><p>Готовая деталь с согласованным покрытием.</p></div></li>
			</ol>
			<div class="rm-metal-operations__links"><span>При необходимости включаем в заказ сварку и сборку.</span><nav aria-label="Связанные услуги"><a class="rm-metal-link-arrow" href="/product/">Изготовление по чертежам</a><a class="rm-metal-link-arrow" href="/painting/">Порошковая окраска</a></nav></div>
		</div>
	</section>

	<section class="rm-metal-examples" data-rm-section="examples">
		<div class="rm-metal-container">
			<h2>Примеры выполненных работ</h2>
			<div class="rm-metal-examples__grid">
				<?foreach ($rmMetalExamples as $rmMetalExample):?>
					<article class="rm-metal-example-card">
						<img src="<?=$rmMetalEscape($rmMetalExample['IMAGE'])?>" alt="" aria-hidden="true" loading="lazy">
						<div class="rm-metal-example-card__copy"><h3><?=$rmMetalEscape($rmMetalExample['TITLE'])?></h3><p><?=$rmMetalEscape($rmMetalExample['TEXT'])?></p><strong><?=$rmMetalEscape($rmMetalExample['META'])?></strong><a class="rm-metal-link-arrow" href="#rm-metal-estimate">Подробнее о заказе</a></div>
					</article>
				<?endforeach;?>
			</div>
			<p class="rm-metal-examples__note">Демонстрационный пример. Закажите расчёт своего изделия.</p>
		</div>
	</section>

	<section class="rm-metal-estimate" id="rm-metal-estimate" data-rm-section="estimate">
		<div class="rm-metal-estimate__inner">
			<div class="rm-metal-estimate__intro" style="--rm-metal-estimate-image: url('<?=$rmMetalEscape($rmMetalHomeAsset('custom-metalwork.webp'))?>')">
				<div class="rm-metal-estimate__lead"><h2>Рассчитать<br>металлообработку</h2><p>Пришлите чертежи или техническое задание. Укажите материал, количество деталей и необходимые операции.</p><p>Если состав работ ещё не определён, опишите, какой результат нужен.</p><strong>Что поможет подготовить расчёт</strong><ul><li>Материал и толщина металла</li><li>Размеры и количество деталей</li><li>Требования к точности и покрытию</li><li>Желаемый срок готовности</li></ul></div>
				<div class="rm-metal-estimate__contact">
					<span>Можно связаться напрямую</span>
					<?if ($rmMetalHasPhone):?><div class="rm-metal-estimate__phone"><?CAllcorp2::ShowHeaderPhones('', 'Phone_black.svg');?></div><?endif;?>
					<?if (CAllcorp2::checkContentBlock(SITE_DIR.'include/footer/site-email.php', 'PROPERTY_EMAIL_VALUE')):?><?CAllcorp2::showEmail('rm-metal-estimate__email');?><?endif;?>
				</div>
			</div>
			<div class="rm-metal-estimate__form rm-home-order__form" data-rm-estimate-form data-rm-estimate-context="metalworking">
				<?if ($rmMetalEstimateFormId):?>
					<?$APPLICATION->IncludeComponent(
						'aspro:form.allcorp2',
						'contacts',
						array(
							'IBLOCK_TYPE' => 'aspro_allcorp2_form',
							'IBLOCK_ID' => $rmMetalEstimateFormId,
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
							'AJAX_OPTION_ADDITIONAL' => 'rm-metal-estimate-form',
						),
						false
					);?>
				<?else:?>
					<p class="rm-metal-estimate__unavailable">Форма временно недоступна. Свяжитесь с нами через раздел «Контакты».</p>
				<?endif;?>
			</div>
		</div>
	</section>

	<section class="rm-metal-photo" data-rm-section="photo" aria-label="Производство РАЛМАСТЕР"><img src="<?=$rmMetalEscape($rmMetalHomeAsset('production-powder-line.png'))?>" alt="Производственная линия РАЛМАСТЕР" loading="lazy"></section>
</div>
