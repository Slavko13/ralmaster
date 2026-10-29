<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Loader;

global $APPLICATION, $arTheme;

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_production.css');

$rmProductionAssetRoot = SITE_TEMPLATE_PATH.'/images/ralmaster/';
$rmProductionEscape = static function ($value) {
	return htmlspecialcharsbx((string)$value);
};
$rmProductionNormalizeUrl = static function ($url) {
	$url = trim((string)$url);
	if ($url === '') {
		return '';
	}
	if (preg_match('~^https?://(?:www\.)?bavrasem\.beget\.tech(?::\d+)?(?<path>/[^?#]*)?(?<query>\?[^#]*)?~i', $url, $match)) {
		return ($match['path'] ?: '/').($match['query'] ?? '');
	}
	if (preg_match('~^(?:javascript|data):~i', $url)) {
		return '';
	}
	return $url;
};

$rmProductionPartnerSource = array();
if (Loader::includeModule('iblock')) {
	$rmProductionPartnerResult = CIBlockElement::GetList(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array('IBLOCK_ID' => 20, 'ACTIVE' => 'Y'),
		false,
		array('nTopCount' => 20),
		array('ID', 'NAME', 'PROPERTY_LINK', 'PROPERTY_SITE')
	);
	while ($rmProductionPartner = $rmProductionPartnerResult->GetNext(false, false)) {
		$rmProductionPartnerSource[] = array(
			'NAME' => html_entity_decode((string)$rmProductionPartner['NAME'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
			'URL' => $rmProductionNormalizeUrl($rmProductionPartner['PROPERTY_LINK_VALUE'] ?: $rmProductionPartner['PROPERTY_SITE_VALUE']),
		);
	}
}

$rmProductionPartnerSpecs = array(
	array('NAME' => 'Фонд капитального ремонта', 'PATTERN' => '~капиталь|ремонт~iu', 'IMAGE' => 'partner-capital-repair-fund.png'),
	array('NAME' => 'Фонд реновации', 'PATTERN' => '~ренова|минт|mint~iu', 'IMAGE' => 'partner-logo-mint.png'),
	array('NAME' => 'ЭРБЕК', 'PATTERN' => '~зареч|эрбек|erbek~iu', 'IMAGE' => 'partner-zarechye-erbek.webp'),
	array('NAME' => 'DOGMA', 'PATTERN' => '~dogma|догма~iu', 'IMAGE' => 'partner-dogma.jpg'),
	array('NAME' => 'Самолёт Девелопмент', 'PATTERN' => '~самол|samolet~iu', 'IMAGE' => 'partner-samolet.png'),
	array('NAME' => 'Гранель', 'PATTERN' => '~гранел|ране|ranev~iu', 'IMAGE' => 'partner-ranev.svg'),
	array('NAME' => 'INGRAD', 'PATTERN' => '~ingrad|инград~iu', 'IMAGE' => 'partner-ingrad.jpg'),
);
$rmProductionPartners = array();
foreach ($rmProductionPartnerSpecs as $rmProductionPartnerSpec) {
	$rmProductionUrl = '';
	foreach ($rmProductionPartnerSource as $rmProductionPartnerKey => $rmProductionPartner) {
		if (preg_match($rmProductionPartnerSpec['PATTERN'], $rmProductionPartner['NAME'])) {
			$rmProductionUrl = $rmProductionPartner['URL'];
			unset($rmProductionPartnerSource[$rmProductionPartnerKey]);
			break;
		}
	}
	$rmProductionPartners[] = array(
		'NAME' => $rmProductionPartnerSpec['NAME'],
		'URL' => $rmProductionUrl,
		'IMAGE' => $rmProductionAssetRoot.'home/'.$rmProductionPartnerSpec['IMAGE'],
	);
}

$rmProductionCapabilities = array(
	array(
		'OPERATION' => 'Лазерная резка',
		'MATERIALS' => 'Сталь, нержавеющая сталь, алюминий*',
		'PARAMETERS' => '3 000 × 1 500 мм*',
		'PARAMETER_NOTE' => 'Максимальный формат листа',
		'LIMITS' => 'Толщина: сталь до 10 мм, нержавеющая сталь до 6 мм, алюминий до 4 мм*.',
	),
	array(
		'OPERATION' => 'Гибка металла',
		'MATERIALS' => 'Листовая сталь, нержавеющая сталь, алюминий*',
		'PARAMETERS' => 'до 3 000 мм* · до 6 мм*',
		'PARAMETER_NOTE' => 'Длина гиба · толщина металла',
		'LIMITS' => 'Допустимая толщина зависит от материала и геометрии детали*.',
	),
	array(
		'OPERATION' => 'Сварочные работы',
		'MATERIALS' => 'Детали и конструкции из стали и нержавеющей стали*',
		'PARAMETERS' => 'Полуавтоматическая и аргонодуговая сварка*',
		'PARAMETER_NOTE' => '',
		'LIMITS' => 'Габариты и требования к соединениям согласовываются по чертежу.',
	),
	array(
		'OPERATION' => 'Порошковая окраска',
		'MATERIALS' => 'Плоские изделия, профили и металлические конструкции*',
		'PARAMETERS' => '6 000 × 1 500 × 2 300 мм*',
		'PARAMETER_NOTE' => 'Длина × ширина × высота изделия',
		'LIMITS' => 'Вес до 750 кг* — ограничение требует подтверждения.',
		'HIGHLIGHT' => true,
	),
	array(
		'OPERATION' => 'Сборка изделий',
		'MATERIALS' => 'Металлические изделия и узлы*',
		'PARAMETERS' => 'Сварная и механическая сборка*',
		'PARAMETER_NOTE' => '',
		'LIMITS' => 'Установка крепежа и комплектующих по техническому заданию*.',
	),
);

// IBlock 17 currently contains a different legacy FAQ set. These six Paper
// questions remain an explicit presentation fallback until a dedicated CMS
// section is approved; no content is written to the database here.
$rmProductionFaq = array(
	array('Можно ли приехать на производство?', 'Да, посещение возможно по предварительному согласованию даты и времени с менеджером.'),
	array('Можно ли начать с пробного изделия или небольшой партии?', 'Да. Для нового изделия можно начать с образца или небольшой партии, а затем согласовать повторный выпуск.'),
	array('Что делать, если нет готового чертежа?', 'Опишите задачу, приложите размеры, фотографию или эскиз. Мы уточним, какая документация потребуется для расчёта.'),
	array('Работаете ли вы с материалом заказчика?', 'Возможность работы с материалом заказчика согласуем после проверки марки, состояния и количества материала.'),
	array('Можно ли изменить заказ после запуска в производство?', 'Изменения возможны до выполнения соответствующей операции. Их влияние на срок и стоимость согласуем отдельно.'),
	array('Что делать, если при приёмке обнаружено несоответствие?', 'Зафиксируйте несоответствие и свяжитесь с менеджером. Мы проверим заказ по согласованной документации и предложим решение.'),
);

$rmProductionEstimateFormId = 0;
if (Loader::includeModule('form')) {
	$rmProductionEstimateFormResult = CForm::GetBySID('ralmaster_estimate_s1');
	if ($rmProductionEstimateFormResult && ($rmProductionEstimateForm = $rmProductionEstimateFormResult->Fetch())) {
		$rmProductionEstimateFormId = (int)$rmProductionEstimateForm['ID'];
	}
}
if ($rmProductionEstimateFormId <= 0) {
	$rmProductionEstimateFormId = (int)CAllcorp2::getFormID('aspro_allcorp2_order_services');
}
?>

<div class="rm-production" data-rm-production>
	<section class="rm-production-hero" data-rm-production-section="hero">
		<div class="rm-production-container">
			<nav class="rm-production-breadcrumb" aria-label="Хлебные крошки"><a href="/">Главная</a><span aria-hidden="true">/</span><span aria-current="page">О производстве</span></nav>
			<div class="rm-production-hero__heading"><h1>О производстве</h1><p>Производственная площадка Ралмастера находится в Видном. Работаем с заказами компаний, в том числе с повторными партиями металлических изделий.</p></div>
			<div class="rm-production-hero__image"><img src="<?=$rmProductionEscape($rmProductionAssetRoot.'production/workshop-panorama.png')?>" alt="Производственный цех РАЛМАСТЕР" fetchpriority="high"><span aria-hidden="true"></span></div>
		</div>
	</section>

	<section class="rm-production-how" data-rm-production-section="how">
		<div class="rm-production-container">
			<div class="rm-production-heading"><h2>Как устроено производство</h2><p>Резку, гибку, сварку, порошковую окраску и сборку выполняем своими силами. Для комплексного заказа объединяем необходимые операции на собственном производстве.</p></div>
			<div class="rm-production-how__grid">
				<article><div class="rm-production-card-image"><img src="<?=$rmProductionEscape($rmProductionAssetRoot.'home/metal-bending.webp')?>" alt="Оборудование и рабочие места" loading="lazy"><span aria-hidden="true"></span></div><h3>Оборудование и рабочие места</h3></article>
				<article><div class="rm-production-card-image"><img src="<?=$rmProductionEscape($rmProductionAssetRoot.'home/welding.webp')?>" alt="Сотрудники во время выполнения заказа" loading="lazy"><span aria-hidden="true"></span></div><h3>Люди, которые работают над изделиями</h3></article>
				<article><div class="rm-production-card-image"><img src="<?=$rmProductionEscape($rmProductionAssetRoot.'home/custom-metalwork.webp')?>" alt="Партия деталей в процессе изготовления" loading="lazy"><span aria-hidden="true"></span></div><h3>Изделия в процессе изготовления</h3></article>
			</div>
		</div>
	</section>

	<section class="rm-production-capabilities" data-rm-production-section="capabilities">
		<h2>Возможности производства</h2>
		<div class="rm-production-capabilities__table-wrap">
			<table>
				<caption class="sr-only">Примерные возможности производства, требующие подтверждения по документации заказа</caption>
				<thead><tr><th>Операция</th><th>Материалы</th><th>Параметры</th><th>Примечания/ограничения</th></tr></thead>
				<tbody>
					<?foreach ($rmProductionCapabilities as $rmProductionCapability):?>
						<tr<?=!empty($rmProductionCapability['HIGHLIGHT']) ? ' class="rm-production-capability--highlight"' : ''?>>
							<th scope="row"><?=$rmProductionEscape($rmProductionCapability['OPERATION'])?></th>
							<td data-label="Материалы"><?=$rmProductionEscape($rmProductionCapability['MATERIALS'])?></td>
							<td data-label="Параметры"><strong><?=$rmProductionEscape($rmProductionCapability['PARAMETERS'])?></strong><?if ($rmProductionCapability['PARAMETER_NOTE']):?><small><?=$rmProductionEscape($rmProductionCapability['PARAMETER_NOTE'])?></small><?endif;?></td>
							<td data-label="Ограничения"><?=$rmProductionEscape($rmProductionCapability['LIMITS'])?></td>
						</tr>
					<?endforeach;?>
				</tbody>
			</table>
			<p class="rm-production-capabilities__note">* Примерные данные для согласования. Фактические материалы, размеры и ограничения подтвердим после проверки чертежа или технического задания.</p>
		</div>
		<div class="rm-production-capabilities__cta"><div><h3>Проверим ваше изделие по чертежу</h3><p>Пришлите чертёж или техническое задание. Проверим размеры, материал и необходимые операции.</p></div><a href="#rm-production-contact">Отправить чертёж на проверку <span aria-hidden="true">→</span></a></div>
	</section>

	<section class="rm-production-order" data-rm-production-section="order">
		<div class="rm-production-container">
			<div class="rm-production-order__heading"><h2>Организация работы над заказом</h2><p>Для изготовления нужны согласованные требования к изделию: документация, материал, количество и состав работ.</p></div>
			<ol class="rm-production-order__steps">
				<li><span>01</span><h3>Документация</h3><p>Чертежи, размеры, материал и требования к готовому изделию.</p></li>
				<li><span>02</span><h3>Передача задания в цех</h3><p>Согласованная версия задания и состав операций для конкретного заказа.</p></li>
				<li><span>03</span><h3>Связь по заказу</h3><p>Технические уточнения, изменения и информация о ходе выполнения.</p></li>
			</ol>
		</div>
	</section>

	<section class="rm-production-repeat" data-rm-production-section="repeat">
		<div class="rm-production-container rm-production-repeat__layout">
			<div class="rm-production-repeat__image"><img src="<?=$rmProductionEscape($rmProductionAssetRoot.'powder/painted-baskets-batch.png')?>" alt="Партия одинаковых окрашенных изделий" loading="lazy"><span aria-hidden="true"></span></div>
			<div class="rm-production-repeat__copy"><h2>Повторные партии</h2><p>Основные заказчики Ралмастера размещают повторные заказы на порошковую окраску, корзины для кондиционеров и металлокассеты.</p><ul><li>Повтор предыдущего заказа</li><li>Изменения в изделии</li><li>Следующие партии</li></ul></div>
		</div>
	</section>

	<section class="rm-production-shipping" data-rm-production-section="shipping">
		<div class="rm-production-container">
			<h2>Проверка и подготовка к отгрузке</h2>
			<div class="rm-production-shipping__grid">
				<article><div class="rm-production-card-image"><img src="<?=$rmProductionEscape($rmProductionAssetRoot.'production/quality-control.png')?>" alt="Проверка готового изделия" loading="lazy"><span aria-hidden="true"></span></div><h3>Соответствие заданию</h3></article>
				<article><div class="rm-production-card-image"><img src="<?=$rmProductionEscape($rmProductionAssetRoot.'production/packed-batch.png')?>" alt="Укомплектованная партия перед передачей" loading="lazy"><span aria-hidden="true"></span></div><h3>Комплектация и передача</h3><p>Выполняем доставку изготовленных металлических изделий.</p></article>
			</div>
		</div>
	</section>

	<section class="rm-production-partners" data-rm-production-section="partners">
		<div class="rm-production-container"><h2>С нами сотрудничают</h2><div class="rm-production-partners__grid">
			<?foreach ($rmProductionPartners as $rmProductionPartner):?>
				<?if ($rmProductionPartner['URL']):?><a href="<?=$rmProductionEscape($rmProductionPartner['URL'])?>" rel="noopener noreferrer" aria-label="<?=$rmProductionEscape($rmProductionPartner['NAME'])?>"><?else:?><div aria-label="<?=$rmProductionEscape($rmProductionPartner['NAME'])?>"><?endif;?>
					<img src="<?=$rmProductionEscape($rmProductionPartner['IMAGE'])?>" alt="<?=$rmProductionEscape($rmProductionPartner['NAME'])?>" loading="lazy">
				<?if ($rmProductionPartner['URL']):?></a><?else:?></div><?endif;?>
			<?endforeach;?>
		</div></div>
	</section>

	<section class="rm-production-faq" data-rm-production-section="faq">
		<div class="rm-production-container"><h2>Ответы на вопросы</h2><div class="rm-production-faq__list">
			<?foreach ($rmProductionFaq as $rmProductionFaqIndex => $rmProductionFaqItem):?>
				<details name="rm-production-faq"><summary><span><?=str_pad((string)($rmProductionFaqIndex + 1), 2, '0', STR_PAD_LEFT)?></span><strong><?=$rmProductionEscape($rmProductionFaqItem[0])?></strong><i aria-hidden="true">↗</i></summary><div class="rm-production-faq__answer"><p><?=$rmProductionEscape($rmProductionFaqItem[1])?></p></div></details>
			<?endforeach;?>
		</div></div>
	</section>

	<section class="rm-production-contact" id="rm-production-contact" data-rm-production-section="contact" data-rm-estimate-context="production">
		<div class="rm-production-contact__inner"><div><h2>Обсудить заказ или регулярные поставки</h2><p>Расскажите, какие изделия нужны, в каком количестве и планируются ли повторные партии. Прикрепите документацию, если она уже подготовлена.</p></div><div class="rm-production-contact__actions"><div class="rm-production-contact__phone"><?CAllcorp2::ShowHeaderPhones('', 'Phone_black.svg');?></div><?if (CAllcorp2::checkContentBlock(SITE_DIR.'include/footer/site-email.php', 'PROPERTY_EMAIL_VALUE')):?><?CAllcorp2::showEmail('rm-production-contact__email');?><?endif;?><?if ($rmProductionEstimateFormId):?><button type="button" data-rm-discuss-open="production">Обсудить заказ <span aria-hidden="true">↗</span></button><?else:?><a class="rm-production-contact__fallback" href="/contacts/">Обсудить заказ <span aria-hidden="true">↗</span></a><?endif;?></div></div>
	</section>

	<section class="rm-production-photo" data-rm-production-section="photo" aria-label="Порошковая линия РАЛМАСТЕР"><img src="<?=$rmProductionEscape($rmProductionAssetRoot.'home/production-powder-line.png')?>" alt="Порошковая линия на производстве РАЛМАСТЕР" loading="lazy"></section>
</div>

<?php
if ($rmProductionEstimateFormId):
	$rmDiscussOptions = array(
		'title' => 'Обсудить заказ',
		'intro' => 'Расскажите, какие изделия или работы нужны. Если планируется регулярное сотрудничество, укажите предполагаемый объём и периодичность заказов.',
		'task_label' => 'Описание задачи',
		'task_placeholder' => 'Например: нужны корзины для кондиционеров по нашим чертежам. Первая партия 50 штук, далее планируем повторные заказы.',
		'upload_label' => 'Прикрепить чертежи, фотографии или ТЗ',
		'button' => 'Отправить запрос',
		'note' => 'Свяжемся с вами для обсуждения задачи и уточнения деталей.',
		'context' => 'production',
		'form_id' => $rmProductionEstimateFormId,
	);
	require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_discuss_order.php';
endif;
?>
