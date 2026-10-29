<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Loader;

global $APPLICATION, $arTheme;

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_contacts.css');

$rmContactsEscape = static function ($value) {
	return htmlspecialcharsbx((string)$value);
};

$rmContactsReadInclude = static function ($relativePath, $fallback) {
	$path = $_SERVER['DOCUMENT_ROOT'].$relativePath;
	if (!is_file($path) || !is_readable($path)) {
		return $fallback;
	}
	$value = file_get_contents($path);
	if (!is_string($value) || $value === '') {
		return $fallback;
	}
	$value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
	$value = preg_replace('/\s+/u', ' ', $value);
	return trim((string)$value) ?: $fallback;
};

$rmContactsOfficeAddress = $rmContactsReadInclude(
	'/include/header/site-address.php',
	'г. Подольск, Комсомольская ул., д. 59'
);
$rmContactsAddressSource = $rmContactsReadInclude(
	'/include/contacts-site-address.php',
	'Московская область, г. Видное, Белокаменное шоссе, д. 20В, помещ. 2/3'
);
$rmContactsProductionAddress = 'Московская область, г. Видное, Белокаменное шоссе, д. 20В, помещ. 2/3';
if (preg_match('/Производство:\s*(.+)$/ui', $rmContactsAddressSource, $rmContactsAddressMatch)) {
	$rmContactsProductionAddress = trim($rmContactsAddressMatch[1]);
}

$rmContactsEstimateFormId = 0;
if (Loader::includeModule('form')) {
	$rmContactsEstimateResult = CForm::GetBySID('ralmaster_estimate_s1');
	if ($rmContactsEstimateResult && ($rmContactsEstimate = $rmContactsEstimateResult->Fetch())) {
		$rmContactsEstimateFormId = (int)$rmContactsEstimate['ID'];
	}
}

// Paper contains a public company card that is not represented by a dedicated
// local include-area. Keep it as an explicit presentation fallback; no data is
// written to Bitrix and the values are never emitted to test logs.
$rmContactsCompany = array(
	array('Полное наименование', 'Общество с ограниченной ответственностью «РАЛМАСТЕР»'),
	array('ИНН', '5003168900', 'copy'),
	array('КПП', '500301001'),
	array('ОГРН', '1255000036125'),
	array('Юридический адрес', '142700, Московская область, г.о. Ленинский, г. Видное, Белокаменное шоссе, д. 20В, помещ. 2/3'),
);
$rmContactsBank = array(
	array('Банк', 'АО «АЛЬФА-БАНК»'),
	array('БИК', '044525593'),
	array('Расчётный счёт', '40702810801240000909'),
	array('Корреспондентский счёт', '30101810200000000593'),
);
?>

<main class="rm-contacts-page" data-rm-contacts-page>
	<section class="rm-contacts-title" data-rm-contacts-section="title">
		<div class="rm-contacts-container">
			<nav class="rm-contacts-breadcrumb" aria-label="Хлебные крошки"><a href="/">Главная</a><span aria-hidden="true">/</span><span aria-current="page">Контакты</span></nav>
			<h1>Контакты</h1>
		</div>
	</section>

	<section class="rm-contacts-summary" data-rm-contacts-section="summary">
		<div class="rm-contacts-container rm-contacts-summary__layout">
			<div class="rm-contacts-summary__primary">
				<h2>Связаться с нами</h2>
				<div class="rm-contacts-summary__label">Телефон</div>
				<div class="rm-contacts-summary__phone"><?CAllcorp2::ShowHeaderPhones('', 'Phone_black.svg');?></div>
				<div class="rm-contacts-summary__label">Электронная почта</div>
				<div class="rm-contacts-summary__email"><?CAllcorp2::showEmail('rm-contacts-summary__email-value');?></div>
				<p>Для запросов, чертежей и технических заданий</p>
				<?if ($rmContactsEstimateFormId > 0):?>
					<button type="button" class="rm-contacts-button" data-rm-discuss-open="contacts">Отправить запрос <span aria-hidden="true">↗</span></button>
				<?else:?>
					<a class="rm-contacts-button" href="mailto:ralmaster@inbox.ru">Отправить запрос <span aria-hidden="true">↗</span></a>
				<?endif;?>
			</div>
			<div class="rm-contacts-summary__locations">
				<article>
					<a href="#route-production" aria-label="Перейти к схеме проезда на производство">↗</a>
					<h3>Производство</h3>
					<p><?=$rmContactsEscape($rmContactsProductionAddress)?></p>
					<dl><dt>Время работы</dt><dd>Понедельник – пятница, 8:00–18:00</dd></dl>
				</article>
				<article>
					<a href="#route-production" aria-label="Перейти к схеме проезда от офиса продаж">↗</a>
					<h3>Офис продаж</h3>
					<p><?=$rmContactsEscape($rmContactsOfficeAddress)?>, офис 328</p>
					<dl><dt>Время работы</dt><dd>Понедельник – пятница, 9:00–18:00</dd></dl>
				</article>
			</div>
		</div>
	</section>

	<section class="rm-contacts-route" id="route-production" data-rm-contacts-section="route">
		<div class="rm-contacts-container">
			<h2>Как добраться</h2>
			<div class="rm-contacts-route__layout">
				<div class="rm-contacts-route__copy">
					<h3>Производство в Видном</h3>
					<p><?=$rmContactsEscape($rmContactsProductionAddress)?></p>
					<a class="rm-contacts-button" href="https://yandex.ru/maps/?rtext=~55.54758061777226,37.77257843324959&amp;rtt=auto" target="_blank" rel="noopener noreferrer">Построить маршрут <span aria-hidden="true">↗</span></a>
					<div class="rm-contacts-route__notice"><h4>Въезд на территорию</h4><p>Свяжитесь с нами, чтобы уточнить время посещения и порядок прохода на территорию.</p><div><?CAllcorp2::ShowHeaderPhones('', 'Phone_black.svg');?></div></div>
				</div>
				<div class="rm-contacts-route__map" aria-label="Карта проезда на производство">
					<?$APPLICATION->IncludeFile(SITE_DIR.'include/contacts-site-map.php', array(), array('MODE' => 'html', 'TEMPLATE' => 'include_area.php', 'NAME' => 'Карта проезда'));?>
					<span>Карта проезда</span>
				</div>
			</div>
		</div>
	</section>

	<section class="rm-contacts-requisites" id="requisites" data-rm-contacts-section="requisites">
		<div class="rm-contacts-container">
			<header><h2>Реквизиты компании</h2><button type="button" data-rm-copy-requisites>Скопировать реквизиты <span aria-hidden="true">↗</span></button></header>
			<div class="rm-contacts-requisites__table" data-rm-requisites-content>
				<?foreach ($rmContactsCompany as $rmContactsRow):?>
					<div class="rm-contacts-requisites__row"><span><?=$rmContactsEscape($rmContactsRow[0])?></span><strong><?=$rmContactsEscape($rmContactsRow[1])?></strong><?if (!empty($rmContactsRow[2])):?><button type="button" data-rm-copy-value="<?=$rmContactsEscape($rmContactsRow[1])?>">Скопировать ИНН</button><?endif;?></div>
				<?endforeach;?>
			</div>
			<div class="rm-contacts-requisites__bank-title"><h3>Банковские реквизиты</h3><span aria-hidden="true">↗</span></div>
			<div class="rm-contacts-requisites__table">
				<?foreach ($rmContactsBank as $rmContactsRow):?>
					<div class="rm-contacts-requisites__row"><span><?=$rmContactsEscape($rmContactsRow[0])?></span><strong><?=$rmContactsEscape($rmContactsRow[1])?></strong></div>
				<?endforeach;?>
			</div>
			<span class="rm-contacts-requisites__download" role="link" aria-disabled="true" title="PDF-файл карточки компании пока не опубликован">Скачать карточку компании · PDF <span aria-hidden="true">↗</span></span>
		</div>
	</section>

	<section class="rm-contacts-visit" data-rm-contacts-section="visit">
		<div class="rm-contacts-container">
			<h2>Посещение и получение заказа</h2>
			<div class="rm-contacts-visit__grid">
				<article><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h14v16H5zM8 2v4M16 2v4M8 10h8M8 14h5"/></svg><h3>Перед приездом</h3><p>Свяжитесь с нами, чтобы уточнить время посещения и порядок прохода на территорию.</p></article>
				<article><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 7 8-4 8 4-8 4zM4 7v10l8 4 8-4V7M12 11v10"/></svg><h3>Получение изделий</h3><p>Перед отправкой транспорта уточните готовность заказа и согласуйте время получения.</p></article>
				<article><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h12v10H3zM15 10h4l2 3v3h-6zM7 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM18 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/></svg><h3>Грузовой транспорт</h3><p>Уточните маршрут к месту погрузки и условия въезда для вашего автомобиля.</p></article>
			</div>
		</div>
	</section>

	<section class="rm-contacts-photo" data-rm-contacts-section="photo" aria-label="Порошковая линия РАЛМАСТЕР">
		<img src="<?=$rmContactsEscape(SITE_TEMPLATE_PATH.'/images/ralmaster/home/production-powder-line.png')?>" alt="Порошковая линия РАЛМАСТЕР" loading="lazy">
		<span aria-hidden="true"></span>
	</section>
</main>

<?
$rmDiscussOptions = array(
	'title' => 'Обсудить заказ',
	'intro' => 'Расскажите, какие изделия или работы нужны. Если планируется регулярное сотрудничество, укажите предполагаемый объём и периодичность заказов.',
	'task_label' => 'Описание задачи',
	'task_placeholder' => 'Например: нужны корзины для кондиционеров по нашим чертежам. Первая партия 50 штук, далее планируем повторные заказы.',
	'upload_label' => 'Прикрепить чертежи, фотографии или ТЗ',
	'button' => 'Отправить запрос',
	'note' => 'Свяжемся с вами для обсуждения задачи и уточнения деталей.',
	'context' => 'contacts',
);
require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_discuss_order.php';
?>

<script>
(function () {
	'use strict';
	function copyText(value, button) {
		if (!navigator.clipboard || !value) { return; }
		navigator.clipboard.writeText(value).then(function () {
			var original = button.firstChild.nodeValue;
			button.firstChild.nodeValue = 'Скопировано ';
			window.setTimeout(function () { button.firstChild.nodeValue = original; }, 1600);
		});
	}

	document.querySelectorAll('[data-rm-copy-value]').forEach(function (button) {
		button.addEventListener('click', function () { copyText(button.getAttribute('data-rm-copy-value'), button); });
	});
	var copyAll = document.querySelector('[data-rm-copy-requisites]');
	if (copyAll) {
		copyAll.addEventListener('click', function () {
			var rows = Array.prototype.map.call(document.querySelectorAll('.rm-contacts-requisites__row'), function (row) {
				return row.querySelector('span').textContent.trim() + ': ' + row.querySelector('strong').textContent.trim();
			});
			copyText(rows.join('\n'), copyAll);
		});
	}
}());
</script>
