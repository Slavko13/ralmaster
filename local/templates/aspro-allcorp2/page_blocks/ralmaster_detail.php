<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

global $APPLICATION;

$rmDetailPage = is_array($rmDetailPage ?? null) ? $rmDetailPage : array();
$rmDetailPage += array(
	'context' => 'detail',
	'attribute' => 'data-rm-detail-page',
	'root_label' => 'Главная',
	'root_url' => '/',
	'eyebrow' => '',
	'title' => 'РАЛМАСТЕР',
	'lead' => '',
	'description_html' => '',
	'image' => SITE_TEMPLATE_PATH.'/images/ralmaster/home/hero-laser-cutting.webp',
	'image_alt' => '',
	'cta_label' => 'Рассчитать заказ',
	'about_title' => 'Производство под задачу',
	'about_text' => '',
	'features' => array(),
	'process_title' => 'Как проходит заказ',
	'process' => array(),
	'contact_title' => 'Обсудить задачу',
	'contact_text' => '',
	'dialog' => array(),
);

$APPLICATION->SetTitle($rmDetailPage['title']);
$APPLICATION->SetPageProperty('HIDETITLE', 'Y');
$APPLICATION->SetPageProperty('FULLWIDTH', 'Y');
$APPLICATION->SetPageProperty('MENU', 'N');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_detail.css');

$rmDetailEscape = static function ($value) {
	return htmlspecialcharsbx((string)$value);
};
$rmDetailDescription = trim((string)$rmDetailPage['description_html']);
$rmDetailAttribute = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$rmDetailPage['attribute']);
?>
<main class="rm-detail-page rm-detail-page--<?=$rmDetailEscape($rmDetailPage['context'])?>" <?=$rmDetailAttribute?>="true">
	<section class="rm-detail-hero">
		<div class="rm-detail-container">
			<nav class="rm-detail-breadcrumb" aria-label="Хлебные крошки">
				<a href="/">Главная</a><span aria-hidden="true">→</span>
				<a href="<?=$rmDetailEscape($rmDetailPage['root_url'])?>"><?=$rmDetailEscape($rmDetailPage['root_label'])?></a><span aria-hidden="true">→</span>
				<span aria-current="page"><?=$rmDetailEscape($rmDetailPage['title'])?></span>
			</nav>
			<div class="rm-detail-hero__layout">
				<div class="rm-detail-hero__copy">
					<?if ($rmDetailPage['eyebrow'] !== ''):?><p class="rm-detail-eyebrow"><?=$rmDetailEscape($rmDetailPage['eyebrow'])?></p><?endif;?>
					<h1><?=$rmDetailEscape($rmDetailPage['title'])?></h1>
					<p class="rm-detail-lead"><?=$rmDetailEscape($rmDetailPage['lead'])?></p>
					<button type="button" class="rm-detail-button" data-rm-discuss-open="<?=$rmDetailEscape($rmDetailPage['context'])?>"><?=$rmDetailEscape($rmDetailPage['cta_label'])?><span aria-hidden="true">↗</span></button>
				</div>
				<figure class="rm-detail-hero__visual">
					<img src="<?=$rmDetailEscape($rmDetailPage['image'])?>" alt="<?=$rmDetailEscape($rmDetailPage['image_alt'])?>">
					<span aria-hidden="true"></span>
				</figure>
			</div>
		</div>
	</section>

	<section class="rm-detail-about" aria-labelledby="rm-detail-about-title">
		<div class="rm-detail-container rm-detail-about__layout">
			<div>
				<p class="rm-detail-kicker">Возможности производства</p>
				<h2 id="rm-detail-about-title"><?=$rmDetailEscape($rmDetailPage['about_title'])?></h2>
			</div>
			<div class="rm-detail-about__text">
				<?if ($rmDetailDescription !== ''):?>
					<div class="rm-detail-richtext"><?=$rmDetailDescription?></div>
				<?else:?>
					<p><?=$rmDetailEscape($rmDetailPage['about_text'])?></p>
				<?endif;?>
			</div>
		</div>
		<div class="rm-detail-container rm-detail-features">
			<?foreach ($rmDetailPage['features'] as $rmFeature):?>
				<article class="rm-detail-feature">
					<span><?=$rmDetailEscape($rmFeature['number'])?></span>
					<h3><?=$rmDetailEscape($rmFeature['title'])?></h3>
					<p><?=$rmDetailEscape($rmFeature['text'])?></p>
				</article>
			<?endforeach;?>
		</div>
	</section>

	<section class="rm-detail-process" aria-labelledby="rm-detail-process-title">
		<div class="rm-detail-container">
			<p class="rm-detail-kicker">Порядок работы</p>
			<h2 id="rm-detail-process-title"><?=$rmDetailEscape($rmDetailPage['process_title'])?></h2>
			<ol class="rm-detail-process__grid">
				<?foreach ($rmDetailPage['process'] as $rmIndex => $rmStep):?>
					<li><span><?=str_pad((string)($rmIndex + 1), 2, '0', STR_PAD_LEFT)?></span><h3><?=$rmDetailEscape($rmStep['title'])?></h3><p><?=$rmDetailEscape($rmStep['text'])?></p></li>
				<?endforeach;?>
			</ol>
		</div>
	</section>

	<section class="rm-detail-contact" aria-labelledby="rm-detail-contact-title">
		<div class="rm-detail-container rm-detail-contact__layout">
			<div><p class="rm-detail-kicker">Расчёт заказа</p><h2 id="rm-detail-contact-title"><?=$rmDetailEscape($rmDetailPage['contact_title'])?></h2><p><?=$rmDetailEscape($rmDetailPage['contact_text'])?></p></div>
			<div class="rm-detail-contact__actions">
				<div class="rm-detail-contact__phone"><?CAllcorp2::ShowHeaderPhones('', 'Phone_black.svg');?></div>
				<?if (CAllcorp2::checkContentBlock(SITE_DIR.'include/footer/site-email.php', 'PROPERTY_EMAIL_VALUE')):?><?CAllcorp2::showEmail('rm-detail-contact__email');?><?endif;?>
				<button type="button" class="rm-detail-button rm-detail-button--light" data-rm-discuss-open="<?=$rmDetailEscape($rmDetailPage['context'])?>"><?=$rmDetailEscape($rmDetailPage['cta_label'])?><span aria-hidden="true">↗</span></button>
			</div>
		</div>
	</section>
</main>
<?php
$rmDiscussOptions = array_merge(array(
	'context' => $rmDetailPage['context'],
	'title' => $rmDetailPage['contact_title'],
	'intro' => $rmDetailPage['contact_text'],
	'task_label' => 'Описание задачи',
	'task_placeholder' => 'Опишите изделие, услугу и объём партии',
	'task_help' => 'Укажите материал, размеры и количество, если они уже известны.',
	'button' => 'Отправить на расчёт',
	'note' => 'Менеджер свяжется с вами, чтобы уточнить детали и срок.',
	'height' => 898,
), is_array($rmDetailPage['dialog']) ? $rmDetailPage['dialog'] : array());
require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_discuss_order.php';
unset($rmDiscussOptions, $rmDetailPage);

