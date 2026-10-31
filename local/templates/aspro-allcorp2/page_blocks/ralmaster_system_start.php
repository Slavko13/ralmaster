<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (empty($rmSystemConfig) || !is_array($rmSystemConfig)) {
	return;
}

global $APPLICATION;
$APPLICATION->SetPageProperty('HIDETITLE', 'Y');
$APPLICATION->SetPageProperty('FULLWIDTH', 'Y');
$APPLICATION->SetPageProperty('MENU', 'N');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system.css');

$rmSystemEscape = static function ($value) {
	return htmlspecialcharsbx((string)$value);
};
?>
<div class="rm-system-page rm-system-page--<?=$rmSystemEscape($rmSystemConfig['kind'])?>" data-rm-system-page="<?=$rmSystemEscape($rmSystemConfig['kind'])?>">
	<section class="rm-system-hero">
		<div class="rm-system-container">
			<nav class="rm-system-breadcrumb" aria-label="Хлебные крошки"><a href="/">Главная</a><span aria-hidden="true">→</span><span aria-current="page"><?=$rmSystemEscape($rmSystemConfig['title'])?></span></nav>
			<div class="rm-system-hero__layout">
				<div><p class="rm-system-kicker"><?=$rmSystemEscape($rmSystemConfig['kicker'])?></p><h1><?=$rmSystemEscape($rmSystemConfig['title'])?></h1></div>
				<p><?=$rmSystemEscape($rmSystemConfig['intro'])?></p>
			</div>
		</div>
	</section>
	<section class="rm-system-body">
		<div class="rm-system-container rm-system-content">

