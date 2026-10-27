<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);

use \Bitrix\Main\Localization\Loc;

if($arResult['CURRENT_REGION'])
{?>
	<?if(!$arResult['POPUP']):?>
		<div class="region_wrapper">
			<div class="io_wrapper">
				<div>
					<div class="js_city_chooser popup_link dark-color with_dropdown" data-event="jqm" data-name="city_chooser_small" data-param-url="<?=urlencode($APPLICATION->GetCurUri());?>" data-param-form_id="city_chooser">
						<?//=CAllcorp2::showIconSvg('region colored', SITE_TEMPLATE_PATH.'/images/svg/region.svg');?>
						<span><?=$arResult['CURRENT_REGION']['NAME'];?></span><span class="arrow"><?=CAllcorp2::showIconSvg("", SITE_TEMPLATE_PATH."/images/svg/more_arrow.svg", "", "", false);?></span>
					</div>
				</div>
				<?if($arResult['SHOW_REGION_CONFIRM']):?>
					<div class="confirm_region">
                        <div class="js-confirm-region-close close_confirm_region hidden-xs hidden-sm" data-id="<?=$arResult['CURRENT_REGION']['ID'];?>"></div>
						<?
						$href = 'data-href="'.$arResult['REGIONS'][$arResult['REAL_REGION']['ID']]['URL'].'"';
						if($arTheme['USE_REGIONALITY']['DEPENDENT_PARAMS']['REGIONALITY_TYPE']['VALUE'] == 'SUBDOMAIN' && ($arResult['HOST'].$_SERVER['HTTP_HOST'].$arResult['URI'] == $arResult['REGIONS'][$arResult['REAL_REGION']['ID']]['URL']))
						$href = '';?>
						<div class="title"><?=Loc::getMessage('CITY_TITLE');?><span class="city"><?=$arResult['REAL_REGION']['NAME'];?>?</span></div>
						<div class="buttons">
							<span><span class="btn btn-default aprove" data-id="<?=$arResult['REAL_REGION']['ID'];?>" <?=$href;?>><?=Loc::getMessage('CITY_YES');?></span></span>
							<span><span class="btn btn-default btn-transparent-bg js_city_change"><?=Loc::getMessage('CITY_CHANGE');?></span></span>
						</div>
					</div>
				<?endif;?>
			</div>
		</div>
	<?else:?>
		<div class="popup_regions">
			<div class="search-page autocomplete-block h-search" id="title-search-city">
				<div class="wrapper searchinput">
					<input id="search" class="autocomplete text" type="text" placeholder="<?=Loc::getMessage('CITY_PLACEHOLDER');?>">
					<div class="search_btn">
						<button class="btn btn-search" type="submit" name="s" value=""><?=CAllcorp2::showIconSvg("search", SITE_TEMPLATE_PATH."/images/svg/Search_black.svg");?></button>
					</div>
					<div class="js-autocomplete-block"></div>
				</div>
			</div>
			<div class="items only_city">
				<?if($arResult['FAVORITS']):?>
					<div class="favorits">
						<span class="title"><?=GetMessage('EXAMPLE_CITY');?></span>
						<div class="cities">
							<?foreach($arResult['FAVORITS'] as $arItem):?>
								<div class="item">
									<a href="<?=$arItem['URL'];?>" data-id="<?=$arItem['ID'];?>" class="name"><?=$arItem['NAME'];?></a>
								</div>
							<?endforeach;?>
						</div>
					</div>
				<?endif;?>
				<?if($arResult['REGIONS']):?>
					<div class="block cities">
						<div class="items_block">
							<?foreach($arResult['REGIONS'] as $key => $arItem):?>
								<?$bCurrent = ($arResult['CURRENT_REGION']['ID'] == $arItem['ID']);?>
								<div class="item <?=($bCurrent ? 'current' : '');?>" data-id="<?=((isset($arItem['IBLOCK_SECTION_ID']) && $arItem['IBLOCK_SECTION_ID']) ? $arItem['IBLOCK_SECTION_ID'] : 0);?>">
									<?if($bCurrent):?>
										<a href="<?=$arItem['URL'];?>" data-id="<?=$arItem['ID'];?>" class="dark-color"><span class="name"><?=$arItem['NAME'];?></span></a>
									<?else:?>
										<a href="<?=$arItem['URL'];?>" data-id="<?=$arItem['ID'];?>" class="dark-color name"><?=$arItem['NAME'];?></a>
									<?endif;?>
								</div>
							<?endforeach;?>
						</div>
					</div>
				<?endif;?>
			</div>
			<script>
				var arRegions = <?=CUtil::PhpToJsObject($arResult['JS_REGIONS']);?>
			</script>
		</div>
	<?endif;?>
<?}?>
