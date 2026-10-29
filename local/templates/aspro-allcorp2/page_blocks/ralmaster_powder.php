<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

global $APPLICATION, $arTheme;

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_powder.css');

$rmAssetRoot = SITE_TEMPLATE_PATH.'/images/ralmaster/';
$rmEstimateFormId = 0;
$rmEstimateFormSid = 'legacy';

if (\Bitrix\Main\Loader::includeModule('form')) {
	$rmEstimateForm = CForm::GetBySID('ralmaster_estimate_s1');
	if ($rmEstimateForm && ($rmEstimateFormData = $rmEstimateForm->Fetch())) {
		$rmEstimateFormId = (int)$rmEstimateFormData['ID'];
		$rmEstimateFormSid = 'ralmaster_estimate_s1';
	}
}

if ($rmEstimateFormId <= 0) {
	$rmEstimateFormId = (int)CAllcorp2::getFormID('aspro_allcorp2_order_services');
}

$rmAcceptedItems = array(
	array(
		'title' => 'Плоские изделия',
		'text' => 'Листовые детали, панели и металлокассеты.',
		'image' => 'home/facade-cassettes.webp',
		'alt' => 'Металлические панели и фасадные кассеты',
		'fit' => 'contain',
	),
	array(
		'title' => 'Металлические профили',
		'text' => 'Профили и профильные элементы.',
		'image' => 'home/powder-profiles.webp',
		'alt' => 'Партия металлических профилей',
		'fit' => 'cover',
	),
	array(
		'title' => 'Изделия и детали',
		'text' => 'Корзины для кондиционеров и другие изделия по согласованию.',
		'image' => 'powder/painted-ac-basket.png',
		'alt' => 'Окрашенная корзина для кондиционера',
		'fit' => 'contain',
	),
);

$rmDimensions = array(
	array('label' => 'Длина', 'value' => 'До 6 000 мм'),
	array('label' => 'Ширина', 'value' => 'До 1 500 мм'),
	array('label' => 'Высота', 'value' => 'До 2 300 мм'),
	array('label' => 'Вес', 'value' => 'До 750 кг'),
);

$rmPreparationSteps = array(
	array('title' => 'Механическая очистка', 'text' => 'При необходимости удаляем ржавчину, окалину и другие загрязнения.'),
	array('title' => 'Обезжиривание', 'text' => 'Удаляем масла и жировые загрязнения с поверхности.'),
	array('title' => 'Промывка', 'text' => 'Промываем поверхность после обезжиривания.'),
	array('title' => 'Сушка', 'text' => 'Высушиваем изделия перед нанесением покрытия.'),
);

$rmRequirements = array(
	array(
		'title' => 'Цвет и фактура',
		'text' => 'Укажите код цвета, если он известен, и желаемый вид поверхности.',
		'icon' => '<path d="M4 6H20V18H4ZM4 10H20M9 6V18M15 6V18"/>',
	),
	array(
		'title' => 'Условия эксплуатации',
		'text' => 'Сообщите, где будет использоваться изделие: в помещении или на улице.',
		'icon' => '<circle cx="12" cy="12" r="4"/><path d="M12 2V4M12 20V22M2 12H4M20 12H22M5 5L6.5 6.5M17.5 17.5L19 19M5 19L6.5 17.5M17.5 6.5L19 5"/>',
	),
	array(
		'title' => 'Особые требования',
		'text' => 'Отметьте участки, которые должны остаться без покрытия, и другие требования по чертежу.',
		'icon' => '<path d="M4 4H20V20H4ZM8 4V8M16 4V8M4 12H8M16 12H20M8 16H16M8 20V16M16 20V16"/>',
	),
);

$rmExamples = array(
	array(
		'title' => 'Порошковая окраска партии металлических профилей',
		'text' => 'Подготовка поверхности и окраска готовых профилей заказчика.',
		'meta' => 'Сталь · 200 шт.<br>RAL 7016 · Матовая поверхность',
		'image' => 'powder/coated-profiles-batch.png',
		'alt' => 'Готовая партия окрашенных металлических профилей',
	),
	array(
		'title' => 'Порошковая окраска плоских панелей',
		'text' => 'Обезжиривание, промывка, сушка и окраска партии листовых деталей.',
		'meta' => 'Сталь · 150 шт.<br>RAL 9003 · Гладкая поверхность',
		'image' => 'powder/powder-coated-panels.png',
		'alt' => 'Партия окрашенных плоских панелей',
	),
	array(
		'title' => 'Порошковая окраска корзин для кондиционеров',
		'text' => 'Подготовка и окраска изделий в составе заказа на изготовление.',
		'meta' => 'Сталь · 60 шт.<br>RAL 7024 · Матовая поверхность',
		'image' => 'powder/painted-baskets-batch.png',
		'alt' => 'Партия корзин для кондиционеров после окраски',
	),
);
?>
<main class="rm-powder-page" data-rm-powder-page>
	<section class="rm-powder-hero" data-rm-powder-section="hero">
		<div class="rm-powder-container">
			<nav class="rm-powder-breadcrumb" aria-label="Хлебные крошки">
				<a href="/">Главная</a>
				<span aria-hidden="true">/</span>
				<span aria-current="page">Порошковая окраска</span>
			</nav>
			<div class="rm-powder-hero__layout">
				<div class="rm-powder-hero__copy">
					<h1>Порошковая окраска металлических изделий</h1>
					<p>Окрашиваем плоские изделия, металлические профили и партии деталей на собственном производстве в Видном. Принимаем готовые изделия заказчика и выполняем окраску в составе заказа на изготовление.</p>
					<button class="rm-powder-button rm-powder-button--primary" type="button" data-rm-discuss-open="powder-coating">
						<span>Рассчитать окраску</span>
						<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 19L19 5M5 5H19V19"/></svg>
					</button>
				</div>
				<div class="rm-powder-hero__image">
					<img src="<?=htmlspecialcharsbx($rmAssetRoot.'home/powder-flat-panels.webp')?>" alt="Порошковая окраска металлических изделий">
					<span aria-hidden="true"></span>
				</div>
			</div>
		</div>
	</section>

	<section class="rm-powder-accepted" data-rm-powder-section="accepted">
		<div class="rm-powder-container">
			<h2>Какие изделия принимаем на окраску</h2>
			<div class="rm-powder-accepted__cards">
				<?foreach ($rmAcceptedItems as $rmItem):?>
					<article class="rm-powder-accepted__card">
						<img src="<?=htmlspecialcharsbx($rmAssetRoot.$rmItem['image'])?>" alt="<?=htmlspecialcharsbx($rmItem['alt'])?>" style="object-fit: <?=$rmItem['fit']?>">
						<div>
							<h3><?=htmlspecialcharsbx($rmItem['title'])?></h3>
							<p><?=htmlspecialcharsbx($rmItem['text'])?></p>
						</div>
					</article>
				<?endforeach;?>
			</div>
			<div class="rm-powder-dimensions" aria-label="Габариты изделий">
				<div class="rm-powder-dimensions__grid">
					<?foreach ($rmDimensions as $rmDimension):?>
						<div class="rm-powder-dimensions__item">
							<span><?=htmlspecialcharsbx($rmDimension['label'])?></span>
							<strong><?=htmlspecialcharsbx($rmDimension['value'])?></strong>
						</div>
					<?endforeach;?>
				</div>
				<div class="rm-powder-dimensions__action">
					<p>Возможность окраски проверим по размерам, весу и конструкции изделия.</p>
					<a class="rm-powder-button rm-powder-button--secondary" href="#powder-estimate">
						<span>Проверить возможность окраски</span>
						<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 19L19 5M5 5H19V19"/></svg>
					</a>
				</div>
			</div>
			<p class="rm-powder-dimensions__note">Предельные размеры и вес приведены по макету и требуют подтверждения производством. Совместимость максимальных значений уточняется для каждого изделия.</p>
		</div>
	</section>

	<section class="rm-powder-preparation" data-rm-powder-section="preparation">
		<div class="rm-powder-container">
			<header class="rm-powder-split-heading">
				<h2>Подготовка поверхности перед окраской</h2>
				<p>Состав подготовки зависит от состояния изделий. Перед окраской выполняем необходимую очистку, обезжиривание, промывку и сушку.</p>
			</header>
			<div class="rm-powder-preparation__layout">
				<div class="rm-powder-cut-image">
					<img src="<?=htmlspecialcharsbx($rmAssetRoot.'powder/surface-preparation.png')?>" alt="Подготовка металлических изделий перед порошковой окраской" loading="lazy">
					<span aria-hidden="true"></span>
				</div>
				<ol class="rm-powder-preparation__steps">
					<?foreach ($rmPreparationSteps as $rmIndex => $rmStep):?>
						<li>
							<span class="rm-powder-preparation__number"><?=str_pad((string)($rmIndex + 1), 2, '0', STR_PAD_LEFT)?></span>
							<div><h3><?=htmlspecialcharsbx($rmStep['title'])?></h3><p><?=htmlspecialcharsbx($rmStep['text'])?></p></div>
						</li>
					<?endforeach;?>
				</ol>
			</div>
		</div>
	</section>

	<section class="rm-powder-requirements" data-rm-powder-section="requirements">
		<div class="rm-powder-container">
			<header class="rm-powder-split-heading">
				<h2>Согласуем требования к покрытию</h2>
				<p>Укажите желаемый цвет, внешний вид покрытия и условия эксплуатации изделия. Если окраска должна соответствовать образцу или техническому заданию, приложите их к запросу.</p>
			</header>
			<div class="rm-powder-requirements__layout">
				<div class="rm-powder-requirements__list">
					<?foreach ($rmRequirements as $rmRequirement):?>
						<article>
							<svg aria-hidden="true" viewBox="0 0 24 24"><?=$rmRequirement['icon']?></svg>
							<div><h3><?=htmlspecialcharsbx($rmRequirement['title'])?></h3><p><?=htmlspecialcharsbx($rmRequirement['text'])?></p></div>
						</article>
					<?endforeach;?>
				</div>
				<div class="rm-powder-cut-image rm-powder-cut-image--requirements">
					<img src="<?=htmlspecialcharsbx($rmAssetRoot.'powder/coated-surface-detail.webp')?>" alt="Крупный план порошкового покрытия" loading="lazy">
					<span aria-hidden="true"></span>
				</div>
			</div>
		</div>
	</section>

	<section class="rm-powder-examples" data-rm-powder-section="examples">
		<div class="rm-powder-container">
			<h2>Примеры порошковой окраски</h2>
			<div class="rm-powder-examples__grid">
				<?foreach ($rmExamples as $rmExample):?>
					<article class="rm-powder-example">
						<div class="rm-powder-example__image">
							<img src="<?=htmlspecialcharsbx($rmAssetRoot.$rmExample['image'])?>" alt="<?=htmlspecialcharsbx($rmExample['alt'])?>" loading="lazy">
							<span aria-hidden="true"></span>
						</div>
						<div class="rm-powder-example__body">
							<h3><?=htmlspecialcharsbx($rmExample['title'])?></h3>
							<p><?=htmlspecialcharsbx($rmExample['text'])?></p>
							<div class="rm-powder-example__meta"><?=$rmExample['meta']?></div>
							<a href="#powder-estimate"><span>Подробнее о заказе</span><span aria-hidden="true">↗</span></a>
							<small>Демонстрационный пример. Данные и фотографию необходимо заменить реальным заказом перед публикацией.</small>
						</div>
					</article>
				<?endforeach;?>
			</div>
		</div>
	</section>

	<section class="rm-powder-orders" data-rm-powder-section="orders">
		<div class="rm-powder-container">
			<h2>Окраска для производственных заказов</h2>
			<div class="rm-powder-orders__grid">
				<article>
					<div class="rm-powder-orders__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 7H13V17H4ZM13 10H17L20 13V17H13M7 7V4H16V8M8 17V18M17 17V18"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg></div>
					<h3>Передать готовые изделия</h3>
					<p>Принимаем изделия заказчика на порошковую окраску. Работаем с разовыми и повторными партиями. Постоянные клиенты размещают у нас заказы на окраску металлических изделий.</p>
					<a href="#powder-estimate">Рассчитать окраску <span aria-hidden="true">↗</span></a>
				</article>
				<article>
					<div class="rm-powder-orders__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 3H14L19 8V21H5ZM14 3V8H19M8 12H16M8 16H13"/></svg></div>
					<h3>Заказать изготовление с окраской</h3>
					<p>Выполняем резку, гибку, сварку, порошковую окраску и сборку на собственном производстве. Можно заказать готовое изделие по чертежу или техническому заданию.</p>
					<a href="/services/">Услуги металлообработки <span aria-hidden="true">↗</span></a>
				</article>
			</div>
		</div>
	</section>

	<section class="rm-powder-estimate" id="powder-estimate" data-rm-powder-section="estimate" data-rm-form-backend="<?=htmlspecialcharsbx($rmEstimateFormSid)?>">
		<div class="rm-powder-container rm-powder-estimate__layout">
			<div class="rm-powder-estimate__aside">
				<div class="rm-powder-estimate__intro">
					<h2>Рассчитать порошковую окраску</h2>
					<p>Пришлите фотографии или чертежи изделий. Укажите размеры, количество и состояние поверхности. Если цвет и требования к покрытию уже определены, добавьте их к запросу.</p>
					<h3>От чего зависит стоимость</h3>
					<p>Для расчёта учитываем размеры и форму изделий, объём партии, необходимую подготовку и требования к покрытию.</p>
					<span aria-hidden="true"></span>
				</div>
				<div class="rm-powder-estimate__contacts">
					<p>Можно связаться напрямую</p>
					<div class="rm-powder-estimate__phone"><?CAllcorp2::ShowHeaderPhones('', 'Phone_black.svg');?></div>
					<?CAllcorp2::showEmail('rm-powder-estimate__email');?>
				</div>
			</div>

			<div class="rm-powder-estimate__form" data-rm-estimate-form data-rm-estimate-context="powder-coating" aria-label="Форма запроса расчёта порошковой окраски">
				<?if ($rmEstimateFormId > 0):?>
					<?$APPLICATION->IncludeComponent(
						'aspro:form.allcorp2',
						'contacts',
						array(
							'IBLOCK_TYPE' => 'aspro_allcorp2_form',
							'IBLOCK_ID' => $rmEstimateFormId,
							'USE_CAPTCHA' => 'N',
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
							'AJAX_OPTION_ADDITIONAL' => 'rm-powder-estimate-form',
						),
						false
					);?>
				<?else:?>
					<p class="rm-powder-estimate__unavailable" role="status">Форма временно недоступна. Свяжитесь с нами по телефону или электронной почте.</p>
				<?endif;?>
			</div>
		</div>
	</section>

	<section class="rm-powder-photo" data-rm-powder-section="photo" aria-label="Производственная линия порошковой окраски">
		<img src="<?=htmlspecialcharsbx($rmAssetRoot.'home/production-powder-line.png')?>" alt="Производственная линия порошковой окраски" loading="lazy">
		<span aria-hidden="true"></span>
	</section>
</main>

<?php
if ($rmEstimateFormId > 0):
	$rmDiscussOptions = array(
		'title' => 'Расчёт порошковой окраски',
		'intro' => 'Пришлите фотографии или чертежи. Проверим возможность окраски и подготовим расчёт по вашему запросу.',
		'task_label' => 'Что нужно окрасить',
		'task_placeholder' => 'Например: 200 стальных профилей длиной 2 м, без старого покрытия. Нужен цвет RAL 7016, для использования на улице.',
		'task_help' => 'Укажите размеры, вес, количество и состояние изделий. Цвет и требования к покрытию добавьте, если они уже известны.',
		'upload_label' => 'Прикрепить фотографии, чертежи или ТЗ',
		'button' => 'Отправить запрос на расчёт',
		'note' => 'Свяжемся с вами для уточнения деталей и согласования условий заказа.',
		'context' => 'powder-coating',
		'form_id' => $rmEstimateFormId,
		'height' => 898,
	);
	require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_discuss_order.php';
endif;
?>
