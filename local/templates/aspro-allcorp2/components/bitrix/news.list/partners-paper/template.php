<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$this->setFrameMode(true);
$rmPartnerEscape = static function ($value) {
	return htmlspecialcharsbx((string)$value);
};
?>
<main class="rm-partners-page" data-rm-partners-page data-rm-partner-count="<?=count($arResult['ITEMS'])?>">
	<section class="rm-partners-hero">
		<div class="rm-partners-container">
			<nav class="rm-partners-breadcrumb" aria-label="Хлебные крошки"><a href="/">Главная</a><span aria-hidden="true">→</span><span aria-current="page">Партнёры</span></nav>
			<div class="rm-partners-hero__layout">
				<div><p class="rm-partners-kicker">Сотрудничество</p><h1>Партнёры</h1></div>
				<p>Работаем с девелоперами, производственными и строительными компаниями. Ценим долгосрочное сотрудничество, точные договорённости и повторяемое качество партий.</p>
			</div>
		</div>
	</section>

	<section class="rm-partners-list" aria-labelledby="rm-partners-list-title">
		<div class="rm-partners-container">
			<div class="rm-partners-heading"><p class="rm-partners-kicker">Нам доверяют</p><h2 id="rm-partners-list-title">Компании, с которыми мы работаем</h2></div>
			<?if ($arResult['ITEMS']):?>
				<div class="rm-partners-grid">
					<?foreach ($arResult['ITEMS'] as $arItem):?>
						<?php
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
						$rmPartnerName = html_entity_decode((string)$arItem['NAME'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
						$rmPartnerImage = !empty($arItem['PREVIEW_PICTURE']['SRC']) ? (string)$arItem['PREVIEW_PICTURE']['SRC'] : '';
						$rmPartnerSite = trim((string)($arItem['PROPERTIES']['SITE']['VALUE'] ?? ''));
						$rmPartnerHref = $rmPartnerSite;
						if ($rmPartnerHref !== '' && !preg_match('~^https?://~i', $rmPartnerHref)) {
							$rmPartnerHref = 'https://'.$rmPartnerHref;
						}
						?>
						<article class="rm-partner-card" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
							<div class="rm-partner-card__logo">
								<?if ($rmPartnerImage !== ''):?><img src="<?=$rmPartnerEscape($rmPartnerImage)?>" alt="Логотип <?=$rmPartnerEscape($rmPartnerName)?>" loading="lazy"><?else:?><span aria-hidden="true"><?=mb_strtoupper(mb_substr($rmPartnerName, 0, 1))?></span><?endif;?>
							</div>
							<div class="rm-partner-card__body">
								<h3><?=$rmPartnerEscape($rmPartnerName)?></h3>
								<?if (trim((string)$arItem['PREVIEW_TEXT']) !== ''):?><div class="rm-partner-card__text"><?=$arItem['PREVIEW_TEXT']?></div><?endif;?>
								<?if ($rmPartnerHref !== ''):?><a href="<?=$rmPartnerEscape($rmPartnerHref)?>" rel="nofollow noopener" target="_blank">Перейти на сайт <span aria-hidden="true">↗</span></a><?endif;?>
							</div>
						</article>
					<?endforeach;?>
				</div>
				<?=$arResult['NAV_STRING']?>
			<?else:?>
				<p class="rm-partners-empty" role="status">Информация о партнёрах обновляется.</p>
			<?endif;?>
		</div>
	</section>

	<section class="rm-partners-contact">
		<div class="rm-partners-container rm-partners-contact__layout">
			<div><p class="rm-partners-kicker">Новый проект</p><h2>Ищете производственного партнёра?</h2><p>Обсудим задачу, требования к изделиям и формат регулярных поставок.</p></div>
			<a href="/contacts/">Связаться с нами <span aria-hidden="true">↗</span></a>
		</div>
	</section>
</main>

