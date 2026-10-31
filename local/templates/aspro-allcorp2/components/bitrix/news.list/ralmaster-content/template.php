<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$this->setFrameMode(true);

$rmEscape = static function ($value) {
	return htmlspecialcharsbx(html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
};
$rmPropertyValue = static function ($item, $code) {
	$value = $item['PROPERTIES'][$code]['VALUE'] ?? '';
	if (is_array($value)) {
		$value = implode(', ', array_filter($value, 'strlen'));
	}
	return trim((string)$value);
};
$rmText = static function ($item) use ($rmEscape) {
	$text = trim((string)($item['PREVIEW_TEXT'] ?? ''));
	if ($text === '') {
		return '';
	}
	if (($item['PREVIEW_TEXT_TYPE'] ?? '') === 'html') {
		return $text;
	}
	return nl2br($rmEscape($text));
};

$rmKind = (string)$arParams['RM_KIND'];
$rmTitle = (string)$arParams['RM_TITLE'];
$rmKicker = (string)$arParams['RM_KICKER'];
$rmIntro = (string)$arParams['RM_INTRO'];
$rmIsFaq = $rmKind === 'faq';
$rmIsReviews = $rmKind === 'reviews';
$rmIsStaff = $rmKind === 'staff';
$rmIsVacancy = $rmKind === 'vacancy';
$rmYears = is_array($arParams['RM_YEARS'] ?? null) ? $arParams['RM_YEARS'] : array();
$rmActiveYear = (int)($arParams['RM_ACTIVE_YEAR'] ?? 0);
$rmListUrl = (string)($arParams['RM_LIST_URL'] ?? '/');
?>
<main class="rm-content-page rm-content-page--<?=$rmEscape($rmKind)?>" data-rm-content-list="<?=$rmEscape($rmKind)?>" data-rm-content-count="<?=count($arResult['ITEMS'])?>">
	<section class="rm-content-hero">
		<div class="rm-content-container">
			<nav class="rm-content-breadcrumb" aria-label="Хлебные крошки"><a href="/">Главная</a><span aria-hidden="true">→</span><span aria-current="page"><?=$rmEscape($rmTitle)?></span></nav>
			<div class="rm-content-hero__layout">
				<div><p class="rm-content-kicker"><?=$rmEscape($rmKicker)?></p><h1><?=$rmEscape($rmTitle)?></h1></div>
				<p class="rm-content-hero__intro"><?=$rmEscape($rmIntro)?></p>
			</div>
		</div>
	</section>

	<section class="rm-content-body" aria-labelledby="rm-content-items-title">
		<div class="rm-content-container">
			<div class="rm-content-toolbar">
				<div class="rm-content-heading"><p class="rm-content-kicker">Актуальная информация</p><h2 id="rm-content-items-title"><?php
					if ($rmIsFaq) { echo 'Ответы на частые вопросы'; }
					elseif ($rmIsReviews) { echo 'Что говорят о нашей работе'; }
					elseif ($rmIsStaff) { echo 'Команда РАЛМАСТЕР'; }
					elseif ($rmIsVacancy) { echo 'Открытые позиции'; }
					else { echo $rmEscape($rmTitle); }
				?></h2></div>
				<?if ($rmYears):?>
					<nav class="rm-content-years" aria-label="Фильтр по году">
						<a href="<?=$rmEscape($rmListUrl)?>"<?=$rmActiveYear === 0 ? ' aria-current="page"' : ''?>>Все</a>
						<?foreach ($rmYears as $rmYear):?><a href="<?=$rmEscape($rmListUrl)?>?year=<?=(int)$rmYear?>"<?=$rmActiveYear === (int)$rmYear ? ' aria-current="page"' : ''?>><?=(int)$rmYear?></a><?endforeach;?>
					</nav>
				<?endif;?>
			</div>

			<?if ($arResult['ITEMS']):?>
				<?if ($rmIsFaq):?>
					<div class="rm-content-faq">
						<?foreach ($arResult['ITEMS'] as $arItem):?>
							<?$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));?>
							<details id="<?=$this->GetEditAreaId($arItem['ID'])?>"><summary><?=$rmEscape($arItem['NAME'])?></summary><div class="rm-content-faq__answer"><?=$rmText($arItem)?></div></details>
						<?endforeach;?>
					</div>
				<?else:?>
					<div class="rm-content-grid<?=$rmIsReviews ? ' rm-content-grid--reviews' : ''?>">
						<?foreach ($arResult['ITEMS'] as $arItem):?>
							<?php
							$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
							$rmImage = !empty($arItem['PREVIEW_PICTURE']['SRC']) ? (string)$arItem['PREVIEW_PICTURE']['SRC'] : '';
							$rmPost = $rmPropertyValue($arItem, 'POST');
							$rmPay = $rmPropertyValue($arItem, 'PAY');
							$rmPeriod = $rmPropertyValue($arItem, 'PERIOD');
							$rmMeta = $rmPost ?: ($rmPay ?: ($rmPeriod ?: (string)$arItem['DISPLAY_ACTIVE_FROM']));
							$rmCanOpen = !$rmIsReviews && !empty($arItem['DETAIL_PAGE_URL']) && $arItem['DETAIL_PAGE_URL'] !== $rmListUrl;
							$rmCardClass = 'rm-content-card';
							if ($rmIsReviews) { $rmCardClass .= ' rm-content-card--review'; }
							elseif ($rmIsStaff) { $rmCardClass .= ' rm-content-card--person'; }
							elseif ($rmIsVacancy) { $rmCardClass .= ' rm-content-card--vacancy'; }
							$rmTag = $rmCanOpen ? 'a' : 'article';
							?>
							<<?=$rmTag?> class="<?=$rmCardClass?>" id="<?=$this->GetEditAreaId($arItem['ID'])?>"<?=$rmCanOpen ? ' href="'.$rmEscape($arItem['DETAIL_PAGE_URL']).'"' : ''?>>
								<?if (!$rmIsReviews && !$rmIsVacancy):?><div class="rm-content-card__media"><?if ($rmImage !== ''):?><img src="<?=$rmEscape($rmImage)?>" alt="" loading="lazy"><?else:?><span class="rm-content-card__placeholder" aria-hidden="true"><?=mb_strtoupper(mb_substr(html_entity_decode((string)$arItem['NAME'], ENT_QUOTES | ENT_HTML5, 'UTF-8'), 0, 1))?></span><?endif;?></div><?endif;?>
								<div class="rm-content-card__body">
									<?if ($rmMeta !== ''):?><p class="rm-content-card__meta"><?=$rmEscape($rmMeta)?></p><?endif;?>
									<h3><?=$rmEscape($arItem['NAME'])?></h3>
									<?if ($rmText($arItem) !== ''):?><div class="rm-content-card__text"><?=$rmText($arItem)?></div><?endif;?>
									<?if ($rmCanOpen):?><span class="rm-content-card__action">Подробнее <span aria-hidden="true">↗</span></span><?endif;?>
								</div>
							</<?=$rmTag?>>
						<?endforeach;?>
					</div>
				<?endif;?>
				<?=$arResult['NAV_STRING']?>
			<?else:?>
				<p class="rm-content-empty" role="status"><?=$rmEscape($arParams['RM_EMPTY'])?></p>
			<?endif;?>
		</div>
	</section>

	<section class="rm-content-contact">
		<div class="rm-content-container rm-content-contact__layout">
			<div><p class="rm-content-kicker">Связаться с нами</p><h2><?=$rmIsVacancy ? 'Хотите стать частью команды?' : 'Есть задача для нашего производства?'?></h2><p><?=$rmIsVacancy ? 'Расскажите о своём опыте — обсудим подходящую позицию и условия работы.' : 'Пришлите чертёж или опишите задачу. Уточним технологию, сроки и подготовим расчёт.'?></p></div>
			<?if (($arParams['RM_SHOW_REVIEW_FORM'] ?? 'N') === 'Y'):?>
				<?php
				$rmReviewId = CAllcorp2::getFormID('aspro_allcorp2_feedback');
				$rmReviewId = $rmReviewId ?: $arParams['IBLOCK_ID'];
				$rmReviewParams = base64_encode(serialize(array('iblock_type' => $arParams['IBLOCK_TYPE'], 'set_name' => 'Y')));
				?>
				<button class="rm-content-button animate-load" type="button" data-event="jqm" data-param-additional="<?=$rmEscape($rmReviewParams)?>" data-param-id="<?=$rmEscape($rmReviewId)?>" data-name="feedback">Оставить отзыв <span aria-hidden="true">↗</span></button>
			<?elseif (($arParams['RM_SHOW_QUESTION_FORM'] ?? 'N') === 'Y'):?>
				<button class="rm-content-button animate-load" type="button" data-event="jqm" data-param-id="<?=$rmEscape(CAllcorp2::getFormID('aspro_allcorp2_question'))?>" data-name="question">Задать вопрос <span aria-hidden="true">↗</span></button>
			<?else:?>
				<a class="rm-content-button" href="/contacts/"><?=$rmIsVacancy ? 'Отправить резюме' : 'Обсудить задачу'?> <span aria-hidden="true">↗</span></a>
			<?endif;?>
		</div>
	</section>
</main>
