<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$this->setFrameMode(true);

$rmEscape = static function ($value) {
	return htmlspecialcharsbx(html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
};
$rmPropertyValue = static function ($result, $code) {
	$value = $result['PROPERTIES'][$code]['VALUE'] ?? '';
	if (is_array($value)) {
		$value = implode(', ', array_filter($value, 'strlen'));
	}
	return trim((string)$value);
};
$rmRenderText = static function ($value, $type) use ($rmEscape) {
	$value = trim((string)$value);
	if ($value === '') {
		return '';
	}
	return $type === 'html' ? $value : nl2br($rmEscape($value));
};

$rmKind = (string)$arParams['RM_KIND'];
$rmListTitle = (string)$arParams['RM_LIST_TITLE'];
$rmListUrl = (string)$arParams['RM_LIST_URL'];
$rmKicker = (string)$arParams['RM_KICKER'];
$rmImage = '';
if (!empty($arResult['DETAIL_PICTURE']['SRC'])) {
	$rmImage = (string)$arResult['DETAIL_PICTURE']['SRC'];
} elseif (!empty($arResult['PREVIEW_PICTURE']['SRC'])) {
	$rmImage = (string)$arResult['PREVIEW_PICTURE']['SRC'];
}
$rmPreview = $rmRenderText($arResult['PREVIEW_TEXT'] ?? '', $arResult['PREVIEW_TEXT_TYPE'] ?? 'text');
$rmDetail = $rmRenderText($arResult['DETAIL_TEXT'] ?? '', $arResult['DETAIL_TEXT_TYPE'] ?? 'text');
$rmAsideFields = array(
	'POST' => 'Должность',
	'PAY' => 'Условия',
	'PERIOD' => 'Период',
	'PHONE' => 'Телефон',
	'EMAIL' => 'Email',
	'SITE' => 'Сайт',
);
$rmHasAside = !empty($arResult['DISPLAY_ACTIVE_FROM']);
foreach ($rmAsideFields as $rmCode => $rmLabel) {
	if ($rmPropertyValue($arResult, $rmCode) !== '') {
		$rmHasAside = true;
	}
}
?>
<main class="rm-content-page rm-content-page--detail" data-rm-content-detail="<?=$rmEscape($rmKind)?>">
	<section class="rm-content-hero">
		<div class="rm-content-container">
			<nav class="rm-content-breadcrumb" aria-label="Хлебные крошки"><a href="/">Главная</a><span aria-hidden="true">→</span><a href="<?=$rmEscape($rmListUrl)?>"><?=$rmEscape($rmListTitle)?></a><span aria-hidden="true">→</span><span aria-current="page"><?=$rmEscape($arResult['NAME'])?></span></nav>
			<p class="rm-content-kicker"><?=$rmEscape($rmKicker)?></p>
			<h1 class="rm-content-detail__title"><?=$rmEscape($arResult['NAME'])?></h1>
			<?if ($rmPreview !== ''):?><div class="rm-content-detail__lead"><?=$rmPreview?></div><?endif;?>
		</div>
	</section>

	<section class="rm-content-detail">
		<div class="rm-content-container rm-content-detail__layout">
			<article>
				<?if ($rmImage !== ''):?><img class="rm-content-detail__image" src="<?=$rmEscape($rmImage)?>" alt="" loading="eager"><?endif;?>
				<?if ($rmDetail !== ''):?><div class="rm-content-prose"><?=$rmDetail?></div><?elseif ($rmPreview === ''):?><p class="rm-content-empty">Подробная информация обновляется.</p><?endif;?>
				<a class="rm-content-back" href="<?=$rmEscape($rmListUrl)?>"><span aria-hidden="true">←</span> Вернуться: <?=$rmEscape(mb_strtolower($rmListTitle))?></a>
			</article>
			<aside class="rm-content-detail__aside" aria-label="Дополнительная информация">
				<?if ($rmHasAside):?><dl>
					<?if (!empty($arResult['DISPLAY_ACTIVE_FROM'])):?><dt>Дата</dt><dd><?=$rmEscape($arResult['DISPLAY_ACTIVE_FROM'])?></dd><?endif;?>
					<?foreach ($rmAsideFields as $rmCode => $rmLabel):?><?$rmValue = $rmPropertyValue($arResult, $rmCode);?><?if ($rmValue !== ''):?><dt><?=$rmEscape($rmLabel)?></dt><dd><?if ($rmCode === 'EMAIL'):?><a href="mailto:<?=$rmEscape($rmValue)?>"><?=$rmEscape($rmValue)?></a><?elseif ($rmCode === 'PHONE'):?><a href="tel:<?=preg_replace('/[^0-9+]/', '', $rmValue)?>"><?=$rmEscape($rmValue)?></a><?elseif ($rmCode === 'SITE'):?><a href="<?=$rmEscape(preg_match('~^https?://~i', $rmValue) ? $rmValue : 'https://'.$rmValue)?>" rel="nofollow noopener" target="_blank"><?=$rmEscape($rmValue)?></a><?else:?><?=$rmEscape($rmValue)?><?endif;?></dd><?endif;?><?endforeach;?>
				</dl><?else:?><p>Нужны подробности? Свяжитесь с нами — ответим на вопросы.</p><?endif;?>
				<a class="rm-content-button" href="/contacts/">Связаться с нами <span class="rm-ui-arrow" aria-hidden="true"></span></a>
			</aside>
		</div>
	</section>
</main>
