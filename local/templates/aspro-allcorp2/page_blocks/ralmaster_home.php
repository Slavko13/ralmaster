<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Loader;

global $APPLICATION, $arTheme;

$rmEscape = static function ($value) {
	return htmlspecialcharsbx((string)$value);
};

$rmDecode = static function ($value) {
	return html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
};

$rmNormalizeUrl = static function ($url, $fallback = '/') {
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

$rmAssetRoot = SITE_TEMPLATE_PATH.'/images/ralmaster/home/';
$rmAsset = static function ($file) use ($rmAssetRoot) {
	return $rmAssetRoot.ltrim((string)$file, '/');
};

$rmCatalogSections = array();
$rmServices = array();
$rmProjects = array();
$rmPartners = array();

if (Loader::includeModule('iblock')) {
	$sectionResult = CIBlockSection::GetList(
		array('SORT' => 'ASC', 'NAME' => 'ASC'),
		array('IBLOCK_ID' => 41, 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1),
		false,
		array('ID', 'NAME', 'CODE', 'SECTION_PAGE_URL', 'PICTURE'),
		array('nTopCount' => 12)
	);
	while ($section = $sectionResult->GetNext(false, false)) {
		$rmCatalogSections[] = array(
			'NAME' => $rmDecode($section['NAME']),
			'URL' => $rmNormalizeUrl($section['SECTION_PAGE_URL'], '/product/'),
			'CODE' => $section['CODE'],
			'IMAGE' => $section['PICTURE'] ? CFile::GetPath($section['PICTURE']) : '',
		);
	}

	$serviceResult = CIBlockSection::GetList(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array('IBLOCK_ID' => 40, 'ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1),
		false,
		array('ID', 'NAME', 'CODE', 'SECTION_PAGE_URL', 'PICTURE'),
		array('nTopCount' => 20)
	);
	while ($service = $serviceResult->GetNext(false, false)) {
		$rmServices[] = array(
			'NAME' => $rmDecode($service['NAME']),
			'URL' => $rmNormalizeUrl($service['SECTION_PAGE_URL'], '/services/'),
			'CODE' => $service['CODE'],
			'IMAGE' => $service['PICTURE'] ? CFile::GetPath($service['PICTURE']) : '',
		);
	}

	$projectResult = CIBlockElement::GetList(
		array('SORT' => 'ASC', 'ACTIVE_FROM' => 'DESC', 'ID' => 'DESC'),
		array('IBLOCK_ID' => 36, 'ACTIVE' => 'Y'),
		false,
		array('nTopCount' => 4),
		array('ID', 'NAME', 'CODE', 'DETAIL_PAGE_URL', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'ACTIVE_FROM')
	);
	while ($project = $projectResult->GetNext(false, false)) {
		$pictureId = $project['PREVIEW_PICTURE'] ?: $project['DETAIL_PICTURE'];
		$year = $project['ACTIVE_FROM'] ? date('Y', MakeTimeStamp($project['ACTIVE_FROM'])) : '';
		$rmProjects[] = array(
			'NAME' => $rmDecode($project['NAME']),
			'URL' => $rmNormalizeUrl($project['DETAIL_PAGE_URL'], '/projects/'),
			'TEXT' => $rmDecode(trim(strip_tags((string)$project['PREVIEW_TEXT']))),
			'IMAGE' => $pictureId ? CFile::GetPath($pictureId) : '',
			'YEAR' => $year,
		);
	}

	$partnerResult = CIBlockElement::GetList(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array('IBLOCK_ID' => 20, 'ACTIVE' => 'Y'),
		false,
		array('nTopCount' => 7),
		array('ID', 'NAME', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'PROPERTY_LINK', 'PROPERTY_SITE')
	);
	while ($partner = $partnerResult->GetNext(false, false)) {
		$pictureId = $partner['PREVIEW_PICTURE'] ?: $partner['DETAIL_PICTURE'];
		$link = $partner['PROPERTY_LINK_VALUE'] ?: $partner['PROPERTY_SITE_VALUE'];
		$rmPartners[] = array(
			'NAME' => $rmDecode($partner['NAME']),
			'URL' => $rmNormalizeUrl($link, ''),
			'IMAGE' => $pictureId ? CFile::GetPath($pictureId) : '',
		);
	}
}

$rmTakeMatching = static function (&$items, $pattern, $fallback) {
	foreach ($items as $key => $item) {
		$haystack = ($item['NAME'] ?? '').' '.($item['CODE'] ?? '');
		if (preg_match($pattern, $haystack)) {
			unset($items[$key]);
			return array_merge($fallback, $item, array('NAME' => $fallback['NAME']));
		}
	}

	return $fallback;
};

$catalogPool = $rmCatalogSections;
$rmProductCards = array(
	$rmTakeMatching($catalogPool, '~фасад|кассет|facad~iu', array('NAME' => 'Фасадные металлокассеты', 'URL' => '/product/', 'IMAGE' => '')),
	$rmTakeMatching($catalogPool, '~корзин|condition|basket~iu', array('NAME' => 'Корзины для кондиционеров', 'URL' => '/product/', 'IMAGE' => '')),
	$rmTakeMatching($catalogPool, '~чертеж|заказ|custom|изготов~iu', array('NAME' => 'Изготовление по чертежам', 'URL' => '/product/', 'IMAGE' => '')),
);

$servicePool = $rmServices;
$rmServiceCards = array(
	$rmTakeMatching($servicePool, '~лазер|резк|laser|cut~iu', array('NAME' => 'Лазерная резка', 'URL' => '/services/', 'IMAGE' => '')),
	$rmTakeMatching($servicePool, '~гибк|bending~iu', array('NAME' => 'Гибка металла', 'URL' => '/services/metal-bending/', 'IMAGE' => '')),
	$rmTakeMatching($servicePool, '~свар|weld~iu', array('NAME' => 'Сварочные работы', 'URL' => '/services/', 'IMAGE' => '')),
);

$projectFallbackImages = array(
	$rmAsset('project-symbol.webp'),
	$rmAsset('project-white-city.webp'),
	$rmAsset('project-symbol-2.webp'),
	$rmAsset('project-symbol-2.webp'),
);
foreach ($rmProjects as $index => &$project) {
	if (!$project['IMAGE']) {
		$project['IMAGE'] = $projectFallbackImages[$index] ?? $projectFallbackImages[0];
	}
	if (!$project['YEAR']) {
		$project['YEAR'] = '2025';
	}
	if (!$project['TEXT']) {
		$project['TEXT'] = 'Изготовление металлических изделий по индивидуальному проекту';
	}
}
unset($project);

if (!$rmProjects) {
	$rmProjects = array(
		array('NAME' => 'ЖК «Символ»', 'URL' => '/projects/zhk-simvol/', 'TEXT' => 'Декоративные корзины для кондиционеров по индивидуальному проекту', 'IMAGE' => $projectFallbackImages[0], 'YEAR' => '2025'),
		array('NAME' => 'ЖК «Белый град»', 'URL' => '/projects/', 'TEXT' => 'Декоративные корзины для кондиционеров по индивидуальному проекту', 'IMAGE' => $projectFallbackImages[1], 'YEAR' => '2025'),
		array('NAME' => 'ЖК «Символ 2»', 'URL' => '/projects/', 'TEXT' => 'Декоративные корзины для кондиционеров по индивидуальному проекту', 'IMAGE' => $projectFallbackImages[2], 'YEAR' => '2025'),
	);
}

while (count($rmProjects) < 4) {
	$index = count($rmProjects);
	$rmProjects[] = array(
		'NAME' => $index === 3 ? 'ЖК «Символ 2»' : 'Выполненный заказ',
		'URL' => '/projects/',
		'TEXT' => 'Декоративные корзины для кондиционеров по индивидуальному проекту',
		'IMAGE' => $projectFallbackImages[$index] ?? $projectFallbackImages[0],
		'YEAR' => '2025',
	);
}

$partnerFallbackImages = array(
	$rmAsset('partner-capital-repair-fund.png'),
	$rmAsset('partner-logo-mint.png'),
	$rmAsset('partner-zarechye-erbek.webp'),
	$rmAsset('partner-dogma.jpg'),
	$rmAsset('partner-samolet.png'),
	$rmAsset('partner-ranev.svg'),
	$rmAsset('partner-ingrad.jpg'),
);
$partnerPool = $rmPartners;
$partnerSpecs = array(
	array('NAME' => 'Фонд капитального ремонта', 'PATTERN' => '~капиталь|ремонт~iu'),
	array('NAME' => 'МИНТ', 'PATTERN' => '~минт|mint~iu'),
	array('NAME' => 'Заречье / ERBEK', 'PATTERN' => '~зареч|эрбек|erbek~iu'),
	array('NAME' => 'DOGMA', 'PATTERN' => '~dogma|догма~iu'),
	array('NAME' => 'Самолёт', 'PATTERN' => '~самол|samolet~iu'),
	array('NAME' => 'РАНЕВ', 'PATTERN' => '~ране|ranev~iu'),
	array('NAME' => 'INGRAD', 'PATTERN' => '~ingrad|инград~iu'),
);
$rmPartners = array();
foreach ($partnerSpecs as $index => $spec) {
	$url = '';
	foreach ($partnerPool as $key => $partner) {
		if (preg_match($spec['PATTERN'], (string)$partner['NAME'])) {
			$url = $partner['URL'];
			unset($partnerPool[$key]);
			break;
		}
	}
	$rmPartners[] = array('NAME' => $spec['NAME'], 'URL' => $url, 'IMAGE' => $partnerFallbackImages[$index]);
}

$rmFaq = array(
	array('Что отправить для расчёта?', 'Чертёж или техническое задание. Укажите материал, размеры, количество изделий, необходимые операции и желаемый срок.'),
	array('Можно обратиться без чертежа?', 'Да. Опишите задачу, размеры, материал и количество. Мы уточним данные и подскажем, что потребуется для расчёта.'),
	array('С какими металлами вы работаете?', 'Работаем с листовой сталью, оцинкованной и нержавеющей сталью, алюминием и другими материалами по согласованию.'),
	array('Можно заказать отдельную операцию?', 'Да. Можно заказать лазерную резку, гибку, сварку или порошковую окраску как отдельную операцию.'),
	array('Как узнать стоимость и срок?', 'Стоимость и срок рассчитываем после изучения чертежа, объёма партии, материала и состава необходимых операций.'),
	array('Как получить готовые изделия?', 'Согласуем самовывоз или доставку при оформлении заказа. Перед передачей изделия проходят приёмку и упаковку.'),
);

$rmOrderFormId = 0;
if (Loader::includeModule('form')) {
	$rmOrderFormResult = CForm::GetBySID('ralmaster_estimate_s1');
	if ($rmOrderFormResult && ($rmOrderForm = $rmOrderFormResult->Fetch())) {
		$rmOrderFormId = (int)$rmOrderForm['ID'];
	}
}
if ($rmOrderFormId <= 0) {
	$rmOrderFormId = (int)CAllcorp2::getFormID('aspro_allcorp2_order_services');
}
?>
<div class="rm-home" data-rm-home>
	<section class="rm-home-hero" data-rm-section="hero" style="--rm-home-hero-image: url('<?=$rmEscape($rmAsset('hero-laser-cutting.webp'))?>')">
		<div class="rm-home-hero__overlay"></div>
		<div class="rm-home-hero__inner rm-home-container">
			<div class="rm-home-hero__content">
				<h1>Изготовление<br>металлических изделий<br>и металлообработка</h1>
				<p>Изготавливаем изделия по чертежам и техническому заданию. <br>Выполняем резку, гибку, сварку, порошковую окраску и сборку <br>на собственном производстве в Видном.</p>
				<div class="rm-home-hero__actions">
					<button class="rm-button rm-button--primary rm-link-arrow" type="button" data-rm-discuss-open="home">Рассчитать заказ</button>
					<a class="rm-button rm-button--secondary rm-link-arrow" href="/services/">Услуги металлообработки</a>
				</div>
			</div>
			<a class="rm-home-hero__feature" href="/product/">
				<img src="<?=$rmEscape($rmAsset('facade-cassettes.webp'))?>" alt="Фасадные металлокассеты">
				<span class="rm-home-hero__feature-copy"><strong>Фасадные металлокассеты</strong><span class="rm-link-arrow">Вся продукция</span></span>
			</a>
		</div>
		<div class="rm-home-hero__cut" aria-hidden="true"></div>
	</section>

	<section class="rm-home-work-model" data-rm-section="work-model" aria-label="Формат работы">
		<div class="rm-home-work-model__inner">
			<article class="rm-home-work-card">
				<span class="rm-home-work-card__icon" aria-hidden="true"><svg width="32" height="32" viewBox="0 0 32 32"><path d="M5 28V5h14v23m0-17h8v17M3 28h26M9 10h2m3 0h2M9 15h2m3 0h2M9 20h2m3 0h2m6-5h2m-2 5h2m-2 5h2m-13 3v-4h4v4" fill="none" stroke="currentColor" stroke-width="1.7"/></svg></span>
				<span><strong>Корпоративным заказчикам</strong><span>Принимаем заказы компаний под задачи и требования конкретного проекта.</span></span>
			</article>
			<article class="rm-home-work-card">
				<span class="rm-home-work-card__icon" aria-hidden="true"><svg width="32" height="32" viewBox="0 0 32 32"><path d="M5 14V8h20m-5-5 5 5-5 5m5 5v6H5m5-5-5 5 5 5" fill="none" stroke="currentColor" stroke-width="1.7"/></svg></span>
				<span><strong>Разовые и регулярные заказы</strong><span>Выполняем отдельные заказы и повторяем партии для постоянных клиентов.</span></span>
			</article>
		</div>
	</section>

	<section class="rm-home-products" data-rm-section="products">
		<div class="rm-home-container">
			<h2>Продукция и услуги</h2>

			<div class="rm-home-group-heading">
				<div><h3>Порошковая окраска</h3><a class="rm-link-arrow" href="/painting/">Подробнее об окраске</a></div>
				<p>Окрашиваем партии металлических изделий. Выполняем подготовку поверхности перед окраской</p>
			</div>
			<div class="rm-home-powder-grid">
				<a class="rm-home-split-card" href="/painting/"><span class="rm-home-split-card__copy"><strong>Окраска плоских изделий</strong><span class="rm-card-arrow" aria-hidden="true">↗</span></span><img src="<?=$rmEscape($rmAsset('powder-flat-panels.webp'))?>" alt="Порошковая окраска плоских изделий"></a>
				<a class="rm-home-split-card" href="/painting/"><span class="rm-home-split-card__copy"><strong>Окраска профилей</strong><span class="rm-card-arrow" aria-hidden="true">↗</span></span><img src="<?=$rmEscape($rmAsset('powder-profiles.webp'))?>" alt="Порошковая окраска профилей"></a>
			</div>

			<div class="rm-home-group-heading">
				<div><h3>Изготовление металлических изделий</h3><a class="rm-link-arrow" href="/product/">Вся продукция</a></div>
				<p>Изготавливаем металлические изделия по чертежам<br>и техническому заданию заказчика</p>
			</div>
			<div class="rm-home-product-grid">
				<?$productImages = array('facade-cassettes.webp', 'air-conditioner-baskets.png', 'custom-metalwork.webp');?>
				<?foreach ($rmProductCards as $index => $card):?>
					<a class="rm-home-product-card" href="<?=$rmEscape($card['URL'])?>">
						<span><strong><?=$rmEscape($card['NAME'])?></strong><span class="rm-card-arrow" aria-hidden="true">↗</span></span>
						<img src="<?=$rmEscape($rmAsset($productImages[$index]))?>" alt="" aria-hidden="true">
					</a>
				<?endforeach;?>
			</div>

			<div class="rm-home-group-heading">
				<div><h3>Металлообработка</h3><a class="rm-link-arrow" href="/services/">Все услуги металлообработки</a></div>
				<p>Выполняем резку, гибку и сварку металла отдельно<br>или в составе одного заказа</p>
			</div>
			<div class="rm-home-service-grid">
				<?$serviceImages = array('hero-laser-cutting.webp', 'metal-bending.webp', 'welding.webp');?>
				<?foreach ($rmServiceCards as $index => $card):?>
					<a class="rm-home-service-card" href="<?=$rmEscape($card['URL'])?>">
						<img src="<?=$rmEscape($rmAsset($serviceImages[$index]))?>" alt="" aria-hidden="true">
						<span><strong><?=$rmEscape($card['NAME'])?></strong><span class="rm-card-arrow" aria-hidden="true">↗</span></span>
					</a>
				<?endforeach;?>
			</div>
		</div>
	</section>

	<section class="rm-home-projects" data-rm-section="projects">
		<div class="rm-home-container">
			<header class="rm-home-section-heading"><h2>Выполненные заказы</h2><a class="rm-link-arrow" href="/projects/">Все выполненные заказы</a></header>
			<div class="rm-home-projects__filters" aria-label="Категории проектов"><span class="is-active">Все работы</span><span>Изготовление изделий</span><span>Металлообработка</span><span>Порошковая окраска</span></div>
			<div class="rm-home-project-grid">
				<?foreach ($rmProjects as $project):?>
					<a class="rm-home-project-card" data-rm-card="project" href="<?=$rmEscape($project['URL'])?>">
						<span class="rm-home-project-card__copy"><small><?=$rmEscape($project['YEAR'])?></small><strong><?=$rmEscape($project['NAME'])?></strong><span><?=$rmEscape($project['TEXT'])?></span><em class="rm-link-arrow">Подробнее о заказе</em></span>
						<img src="<?=$rmEscape($project['IMAGE'])?>" alt="<?=$rmEscape($project['NAME'])?>">
					</a>
				<?endforeach;?>
			</div>
		</div>
	</section>

	<section class="rm-home-partners" data-rm-section="partners">
		<div class="rm-home-container">
			<h2>С нами сотрудничают</h2>
			<div class="rm-home-partner-grid">
				<?foreach ($rmPartners as $partner):?>
					<?if ($partner['URL']):?><a class="rm-home-partner" data-rm-card="partner" href="<?=$rmEscape($partner['URL'])?>" rel="noopener noreferrer"><?else:?><div class="rm-home-partner" data-rm-card="partner"><?endif;?>
						<img src="<?=$rmEscape($partner['IMAGE'])?>" alt="<?=$rmEscape($partner['NAME'])?>" loading="lazy">
					<?if ($partner['URL']):?></a><?else:?></div><?endif;?>
				<?endforeach;?>
			</div>
		</div>
	</section>

	<section class="rm-home-production" data-rm-section="production">
		<div class="rm-home-container">
			<header class="rm-home-section-heading rm-home-section-heading--production"><h2>О производстве</h2><p>Производственная площадка Ралмастера находится в Видном.<br>Работаем с корпоративными заказчиками, выполняем разовые<br>заказы и повторные партии.</p></header>
			<div class="rm-home-production__main">
				<img src="<?=$rmEscape($rmAsset('production-laser-floor.png'))?>" alt="Производственная площадка РАЛМАСТЕР">
				<div class="rm-home-production__panel"><span>Производственная площадка</span><strong>Видное</strong><ul><li>Лазерная резка</li><li>Гибка металла</li><li>Сварочные работы</li><li>Порошковая окраска</li><li>Сборка изделий</li></ul><a class="rm-button rm-button--secondary rm-link-arrow" href="/company/">Как устроено производство</a></div>
			</div>
			<div class="rm-home-production__gallery"><img src="<?=$rmEscape($rmAsset('production-powder-line.png'))?>" alt="Участок порошковой окраски" loading="lazy"><img src="<?=$rmEscape($rmAsset('production-worker-bending.png'))?>" alt="Гибка металла" loading="lazy"><img src="<?=$rmEscape($rmAsset('production-welding.png'))?>" alt="Сварочные работы" loading="lazy"></div>
		</div>
	</section>

	<section class="rm-home-process" data-rm-section="process">
		<div class="rm-home-container">
			<header class="rm-home-section-heading"><h2>Как заказать</h2><p>От задачи до готовых изделий</p></header>
			<div class="rm-home-process__layout">
				<article class="rm-home-process__lead">
					<small>01</small>
					<h3>Пришлите задачу</h3>
					<p>Отправьте чертёж или техническое задание. Укажите материал, количество изделий и необходимые работы. Если документации пока нет, опишите задачу</p>
					<div class="rm-home-process__drawing" aria-hidden="true">
						<svg width="420" height="150" viewBox="0 0 420 150" xmlns="http://www.w3.org/2000/svg" focusable="false">
							<path d="M94 107V43L163 20L230 49V113L161 137L94 107Z" fill="none" stroke="#8EB8FF" stroke-width="1.5"/>
							<path d="M94 43L161 72L230 49M161 72V137M111 51V100L147 116V66" fill="none" stroke="#8EB8FF" stroke-width="1.5"/>
							<path d="M181 72V112M199 65V106M216 59V100M75 30V119M70 30H80M70 119H80M84 23L156 0M84 18V28M156 0V8M248 43V117M243 43H253M243 117H253" fill="none" stroke="#526985"/>
							<path d="M270 50H395M270 71H350M270 102H395M270 123H374" fill="none" stroke="#344B6A"/>
							<circle cx="75" cy="75" r="4" fill="#175BEF"/>
							<circle cx="248" cy="80" r="4" fill="#175BEF"/>
						</svg>
					</div>
					<button class="rm-button rm-button--primary rm-link-arrow" type="button" data-rm-discuss-open="home">Прикрепить файлы</button>
				</article>
				<ol class="rm-home-process__steps">
					<li><small>02</small><span><strong>Согласуем условия</strong><span>Проверим возможность выполнения, уточним требования и согласуем состав работ, стоимость, срок и порядок оплаты</span></span></li>
					<li><small>03</small><span><strong>Выполним заказ</strong><span>Изготовим изделия или выполним согласованные операции по вашему заданию</span></span></li>
					<li><small>04</small><span><strong>Передадим изделия</strong><span>Согласуем порядок приёмки и передачи готового заказа, при необходимости организуем доставку</span></span></li>
				</ol>
			</div>
		</div>
	</section>

	<section class="rm-home-faq" data-rm-section="faq">
		<div class="rm-home-container">
			<h2>Часто задаваемые вопросы</h2>
			<div class="rm-home-faq__items">
				<?foreach ($rmFaq as $index => $item):?>
					<?$panelId = 'rm-home-faq-panel-'.($index + 1);?>
					<article class="rm-home-faq__item<?=$index === 0 ? ' is-open' : ''?>">
						<button type="button" aria-expanded="<?=$index === 0 ? 'true' : 'false'?>" aria-controls="<?=$panelId?>"><small><?=str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT)?></small><strong><?=$rmEscape($item[0])?></strong><span aria-hidden="true">↗</span></button>
						<div id="<?=$panelId?>" class="rm-home-faq__answer"<?=$index === 0 ? '' : ' hidden'?>><p><?=$rmEscape($item[1])?></p></div>
					</article>
				<?endforeach;?>
			</div>
		</div>
	</section>

	<section class="rm-home-order" data-rm-section="order">
		<div class="rm-home-container">
			<div class="rm-home-order__inner">
				<div class="rm-home-order__intro" style="--rm-home-order-image: url('<?=$rmEscape($rmAsset('hero-laser-cutting.webp'))?>')"><h2>Рассчитать заказ</h2><p>Расскажите, что нужно изготовить или обработать. Приложите чертёж или техническое задание, если они есть. Мы изучим запрос и свяжемся с вами для уточнения деталей</p><span>Удобнее обсудить задачу напрямую?</span><div class="rm-home-order__contacts"><a href="tel:+79919472000">+7 (991) 947-20-00</a><a href="mailto:ralmaster@inbox.ru">ralmaster@inbox.ru</a></div></div>
				<div class="rm-home-order__form" data-rm-estimate-form data-rm-estimate-context="home" aria-label="Форма запроса расчёта">
					<?$APPLICATION->IncludeComponent(
						'aspro:form.allcorp2',
						'contacts',
						array(
							'IBLOCK_TYPE' => 'aspro_allcorp2_form',
							'IBLOCK_ID' => $rmOrderFormId,
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
							'AJAX_OPTION_ADDITIONAL' => 'rm-home-order-form',
						),
						false
					);?>
				</div>
				<script>
				(function () {
					'use strict';
					var root = document.querySelector('.rm-home-order__form');
					if (!root) { return; }
					function decorateControl(sid, placeholder) {
						var group = root.querySelector('[data-SID="' + sid + '"]');
						var control = group ? group.querySelector('input, textarea') : null;
						if (!control) { return; }
						if (control.placeholder !== placeholder) { control.placeholder = placeholder; }
						if (control.getAttribute('aria-label') !== placeholder) { control.setAttribute('aria-label', placeholder); }
					}
					function decorateForm() {
						decorateControl('NAME', 'Ваше имя');
						decorateControl('CONTACT', 'Телефон или электронная почта *');
						decorateControl('TASK', 'Описание задачи');
						var file = root.querySelector('[data-SID="FILE"] input[type="file"]');
						if (file && file.getAttribute('aria-label') !== 'Прикрепить файлы') {
							file.setAttribute('aria-label', 'Прикрепить файлы');
						}
						var consent = root.querySelector('.licence_block label');
						if (consent && consent.getAttribute('data-rm-home-consent') !== 'true') {
							consent.innerHTML = 'Я согласен на обработку персональных данных и ознакомлен с <a href="/include/licenses_detail.php">политикой конфиденциальности</a>';
							consent.setAttribute('data-rm-home-consent', 'true');
						}
					}
					decorateForm();
					if (window.MutationObserver) {
						var observer = new MutationObserver(function () {
							observer.disconnect();
							decorateForm();
							observer.observe(root, { childList: true, subtree: true });
						});
						observer.observe(root, { childList: true, subtree: true });
					}
				}());
				</script>
			</div>
		</div>
	</section>

	<section class="rm-home-photo" data-rm-section="photo" aria-label="Производство РАЛМАСТЕР"><img src="<?=$rmEscape($rmAsset('production-powder-line.png'))?>" alt="Производство РАЛМАСТЕР" loading="lazy"></section>
</div>

<?php
$rmDiscussOptions = array(
	'title' => 'Обсудить заказ',
	'intro' => 'Расскажите, какие изделия или работы нужны. Если планируется регулярное сотрудничество, укажите предполагаемый объём и периодичность заказов.',
	'task_label' => 'Описание задачи',
	'task_placeholder' => 'Например: нужны корзины для кондиционеров по нашим чертежам. Первая партия 50 штук, далее планируем повторные заказы.',
	'upload_label' => 'Прикрепить чертежи, фотографии или ТЗ',
	'button' => 'Отправить запрос',
	'note' => 'Свяжемся с вами для обсуждения задачи и уточнения деталей.',
	'context' => 'home',
	'form_id' => $rmOrderFormId,
);
require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_discuss_order.php';
?>
