<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$rmSearchQuery = isset($arResult['REQUEST']['~QUERY']) ? (string)$arResult['REQUEST']['~QUERY'] : '';
$rmSearchHow = $arResult['REQUEST']['HOW'] === 'd' ? 'd' : 'r';
?>
<div class="rm-search" data-rm-search>
	<form class="rm-search-form" action="" method="get" role="search">
		<div class="rm-search-field">
			<label for="rm-search-query">Поисковый запрос</label>
			<?php if ($arParams['USE_SUGGEST'] === 'Y') {
				if ($rmSearchQuery !== '' && is_object($arResult['NAV_RESULT'])) {
					$arResult['FILTER_MD5'] = $arResult['NAV_RESULT']->GetFilterMD5();
					$obSearchSuggest = new CSearchSuggest($arResult['FILTER_MD5'], $rmSearchQuery);
					$obSearchSuggest->SetResultCount($arResult['NAV_RESULT']->NavRecordCount);
				}
				$APPLICATION->IncludeComponent(
					'bitrix:search.suggest.input',
					'',
					array(
						'NAME' => 'q',
						'VALUE' => $rmSearchQuery,
						'INPUT_SIZE' => 40,
						'DROPDOWN_SIZE' => 10,
						'FILTER_MD5' => $arResult['FILTER_MD5'],
					),
					$component,
					array('HIDE_ICONS' => 'Y')
				);
			} else { ?>
				<input id="rm-search-query" type="search" name="q" value="<?=htmlspecialcharsbx($rmSearchQuery)?>" placeholder="Например, порошковая покраска" autocomplete="off">
			<?php } ?>
		</div>
		<?php if ($arParams['SHOW_WHERE']) { ?>
			<div class="rm-search-field">
				<label for="rm-search-where">Раздел</label>
				<select id="rm-search-where" name="where">
					<option value=""><?=GetMessage('SEARCH_ALL')?></option>
					<?php foreach ($arResult['DROPDOWN'] as $key => $value) { ?>
						<option value="<?=htmlspecialcharsbx($key)?>"<?=$arResult['REQUEST']['WHERE'] === $key ? ' selected' : ''?>><?=htmlspecialcharsbx($value)?></option>
					<?php } ?>
				</select>
			</div>
		<?php } ?>
		<button class="rm-system-button" type="submit" name="s" value="Y">Найти</button>
		<input type="hidden" name="how" value="<?=$rmSearchHow?>">
		<?php if ($arParams['SHOW_WHEN']) { ?>
			<div class="rm-search-dates">
				<?php $APPLICATION->IncludeComponent(
					'bitrix:main.calendar',
					'',
					array(
						'SHOW_INPUT' => 'Y',
						'INPUT_NAME' => 'from',
						'INPUT_VALUE' => $arResult['REQUEST']['~FROM'],
						'INPUT_NAME_FINISH' => 'to',
						'INPUT_VALUE_FINISH' => $arResult['REQUEST']['~TO'],
						'INPUT_ADDITIONAL_ATTR' => 'size="10"',
					),
					null,
					array('HIDE_ICONS' => 'Y')
				); ?>
			</div>
		<?php } ?>
	</form>

	<?php if (isset($arResult['REQUEST']['ORIGINAL_QUERY'])) { ?>
		<div class="rm-system-note">
			<?=GetMessage('CT_BSP_KEYBOARD_WARNING', array('#query#' => '<a href="'.htmlspecialcharsbx($arResult['ORIGINAL_QUERY_URL']).'">'.htmlspecialcharsbx($arResult['REQUEST']['ORIGINAL_QUERY']).'</a>'))?>
		</div>
	<?php } ?>

	<?php if ($arResult['REQUEST']['QUERY'] === false && $arResult['REQUEST']['TAGS'] === false) { ?>
		<div class="rm-system-note">Введите запрос — мы найдём подходящие услуги, продукцию и материалы.</div>
	<?php } elseif ((int)$arResult['ERROR_CODE'] !== 0) { ?>
		<div class="rm-system-note" role="alert">
			<h2><?=GetMessage('SEARCH_ERROR')?></h2>
			<?php ShowError($arResult['ERROR_TEXT']); ?>
			<p><?=GetMessage('SEARCH_CORRECT_AND_CONTINUE')?></p>
		</div>
	<?php } elseif (!empty($arResult['SEARCH'])) { ?>
		<?php if ($arParams['DISPLAY_TOP_PAGER'] !== 'N') { echo $arResult['NAV_STRING']; } ?>
		<div class="rm-search-results" aria-live="polite">
			<?php foreach ($arResult['SEARCH'] as $arItem) { ?>
				<article class="rm-search-result">
					<h2><a href="<?=htmlspecialcharsbx($arItem['URL'])?>"><?=$arItem['TITLE_FORMATED']?></a></h2>
					<?php if ($arItem['CHAIN_PATH']) { ?><div class="rm-search-result__path"><?=$arItem['CHAIN_PATH']?></div><?php } ?>
					<div class="rm-search-result__text"><?=$arItem['BODY_FORMATED']?></div>
				</article>
			<?php } ?>
		</div>
		<?php if ($arParams['DISPLAY_BOTTOM_PAGER'] !== 'N') { echo $arResult['NAV_STRING']; } ?>
		<p class="rm-search-sort">
			<?php if ($rmSearchHow === 'd') { ?>
				<a href="<?=htmlspecialcharsbx($arResult['URL'])?>&amp;how=r<?=$arResult['REQUEST']['FROM'] ? '&amp;from='.urlencode($arResult['REQUEST']['FROM']) : ''?><?=$arResult['REQUEST']['TO'] ? '&amp;to='.urlencode($arResult['REQUEST']['TO']) : ''?>"><?=GetMessage('SEARCH_SORT_BY_RANK')?></a> · <strong><?=GetMessage('SEARCH_SORTED_BY_DATE')?></strong>
			<?php } else { ?>
				<strong><?=GetMessage('SEARCH_SORTED_BY_RANK')?></strong> · <a href="<?=htmlspecialcharsbx($arResult['URL'])?>&amp;how=d<?=$arResult['REQUEST']['FROM'] ? '&amp;from='.urlencode($arResult['REQUEST']['FROM']) : ''?><?=$arResult['REQUEST']['TO'] ? '&amp;to='.urlencode($arResult['REQUEST']['TO']) : ''?>"><?=GetMessage('SEARCH_SORT_BY_DATE')?></a>
			<?php } ?>
		</p>
	<?php } else { ?>
		<div class="rm-system-note"><?=GetMessage('SEARCH_NOTHING_TO_FOUND')?></div>
	<?php } ?>
</div>
