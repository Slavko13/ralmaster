<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
global $arTheme, $arRegion;
$bShowImage = in_array('PREVIEW_PICTURE', $arParams['FIELD_CODE']);
$bOrderViewBasket = $arParams['ORDER_VIEW'];
if (isset($arTheme['ORDER_VIEW']['DEPENDENT_PARAMS'])) {
	$basketURL = CAllcorp2::GetFrontParametrValue('URL_BASKET_SECTION');
} else{
	$basketURL = \Bitrix\Main\Config\Option::get(
		"aspro.allcorp2",
		"URL_BASKET_SECTION",
		SITE_DIR."cart/",
		SITE_ID
	);
	$basketURL = str_replace('#'.'SITE_DIR'.'#', SITE_DIR, $basketURL);
}
// print_r($arRegion);
?>
<?if($arResult['SECTIONS'] || $arResult['ITEMS']):?>
	<?
	$frame = $this->createFrame()->begin();
	$frame->setAnimation(true);
	?>
	<?
	$qntyItems = count($arResult['ITEMS']);
	$countmd = 4;
	$countsm = 2;
	$countxs = 2;
	$countxs1 = 2;
	$colmd = 3;
	$colsm = 4;
	$colxs = 6;
	$bShowImage = in_array('PREVIEW_PICTURE', $arParams['FIELD_CODE']);
	?>
	<div class="row margin0">
		<div class="catalog item-views table front catalog_table_2">
			<div class="flexslider unstyled row dark-nav2" data-plugin-options='{"animation": "slide", "directionNav": true, "customDirection": ".catalog .nav-direction a", "controlNav" :true, "animationLoop": true, "slideshow": false, "itemMargin": 0, "counts": [<?=$countmd?>, <?=$countsm?>, <?=$countxs?>, <?=$countxs1?>]}'>
				<ul class="slides" itemscope itemtype="http://schema.org/ItemList">
					<?foreach($arResult["ITEMS"] as $i => $arItem):?>
						<?
						// edit/add/delete buttons for edit mode
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
						// use detail link?
						$bDetailLink = $arParams['SHOW_DETAIL_LINK'] != 'N' && (!strlen($arItem['DETAIL_TEXT']) ? ($arParams['HIDE_LINK_WHEN_NO_DETAIL'] !== 'Y' && $arParams['HIDE_LINK_WHEN_NO_DETAIL'] != 1) : true);
						// preview image
						if($bShowImage){
							$bImage = (isset($arItem['FIELDS']['PREVIEW_PICTURE']) && $arItem['PREVIEW_PICTURE']['SRC']);
							$nImageID = ($bImage ? (is_array($arItem['FIELDS']['PREVIEW_PICTURE']) ? $arItem['FIELDS']['PREVIEW_PICTURE']['ID'] : $arItem['FIELDS']['PREVIEW_PICTURE']) : "");
							$arImage = ($bImage ? CFile::ResizeImageGet($nImageID, array('width' => 400, 'height' => 200), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true) : array());
							$imageSrc = ($bImage ? $arImage['src'] : SITE_TEMPLATE_PATH.'/images/noimage_product.png');
							$imageDetailSrc = ($bImage ? $arItem['DETAIL_PICTURE']['SRC'] : false);
						}
						// use order button?
						$bOrderButton = $arItem["DISPLAY_PROPERTIES"]["FORM_ORDER"]["VALUE_XML_ID"] == "YES";
						$dataItem = ($bOrderViewBasket ? CAllcorp2::getDataItem($arItem) : false);
						?>
						<li class="col-md-<?=$colmd?> col-sm-<?=$colsm?> col-xs-<?=$colxs?> js-popup-block">
							<div class="item<?=($bShowImage ? '' : ' wti')?> <?=$arItem['HOVER_PROPS'] ? "hover_props" : ""?>" id="<?=$this->GetEditAreaId($arItem['ID'])?>"<?=($bOrderViewBasket ? ' data-item="'.$dataItem.'"' : '')?> itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
								<meta itemprop="position" content="<?=$i;?>" />
								<div class="inner-wrap">
									<?if($arItem['DISPLAY_PROPERTIES']['HIT']['VALUE']):?>
										<div class="stickers">
											<div class="stickers-wrapper">
												<?foreach($arItem['DISPLAY_PROPERTIES']['HIT']['VALUE_XML_ID'] as $key => $class):?>
													<div class="sticker_<?=strtolower($class);?>"><?=$arItem['DISPLAY_PROPERTIES']['HIT']['VALUE'][$key]?></div>
												<?endforeach;?>
											</div>
										</div>
									<?endif;?>
									<?if($bShowImage):?>
										<div class="image <?=$arItem['HOVER_PROPS'] ? "" : "shine"?> image-list-wrapper">
											<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="blink-block">
											<?elseif($imageDetailSrc):?><a href="<?=$imageDetailSrc?>" alt="<?=($bImage ? $arItem['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?=($bImage ? $arItem['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" class="img-inside fancybox">
											<?endif;?>
												<img class="img-responsive" src="<?=$imageSrc?>" alt="<?=($bImage ? $arItem['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?=($bImage ? $arItem['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" itemprop="image" />
											<?if($bDetailLink):?></a>
											<?elseif($imageDetailSrc):?><span class="zoom"><i class="fa fa-16 fa-white-shadowed fa-search"></i></span></a>
											<?endif;?>
											<?if($arItem['HOVER_PROPS']):?>
													<div class="props_wrapper chars">
														<div class="props_inner scrollbar char-wrapp">
															<table class="props_table">
																<tbody>
																	<?foreach($arItem['HOVER_PROPS'] as $code => $arProp):?>
																		<tr class="char">
																			<td class="char_name"><span><?=$arProp['NAME'];?></span></td>
																			<td class="char_value"><span><?=$arProp['VALUE'];?></span></td>
																		</tr>
																	<?endforeach;?>
																</tbody>
															</table>
														</div>
													</div>
												<?endif;?>
										</div>
									<?endif;?>

									<div class="text">
										<div class="cont">
											<div class="cont_inner">
												<?// element name?>
												<?if(strlen($arItem['FIELDS']['NAME'])):?>
													<div class="title js-popup-title">
														<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="dark-color" itemprop="url"><?endif;?>
															<span itemprop="name"><?=$arItem['NAME']?></span>
														<?if($bDetailLink):?></a><?endif;?>
													</div>
												<?endif;?>

												<?// element section name?>
												<?if($arItem['IBLOCK_SECTION_ID'] && $arParams['SHOW_SECTION'] == 'Y'):?>
													<div class="section_name"><?=implode(', ', $arItem['SECTIONS']);?></div>
												<?endif;?>

												<div class="arts">
													<?// element status?>
													<?if(strlen($arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE'])):?>
														<span class="status-icon <?=$arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE_XML_ID']?>" itemprop="description"><?=$arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE']?></span>
													<?endif;?>

													<?// element article?>
													<?if(strlen($arItem['DISPLAY_PROPERTIES']['ARTICLE']['VALUE'])):?>
														<span class="article" itemprop="description"><?=GetMessage('S_ARTICLE')?>&nbsp;<span><?=$arItem['DISPLAY_PROPERTIES']['ARTICLE']['VALUE']?></span></span>
													<?endif;?>
												</div>

											</div>
										</div>

										<div class="row foot">
											<div class="col-md-12 col-sm-12 col-xs-12 clearfix slice_price">
												<?=\Aspro\Functions\CAsproAllcorp2::showPrice($arItem, $arParams, $bOrderViewBasket, false, false);?>
											</div>

											<div class="col-md-12 col-sm-12 col-xs-12 button_block">
												<div class="desktop-wrapper">
													<?=\Aspro\Functions\CAsproAllcorp2::showBasketButton($arItem, $arParams, $bOrderButton, $bOrderViewBasket, $basketURL, false, true);?>
												</div>
												<div class="mobile-wrapper">
													<?=\Aspro\Functions\CAsproAllcorp2::showBasketButton($arItem, $arParams, $bOrderButton, $bOrderViewBasket, $basketURL);?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</li>
					<?endforeach;?>
				</ul>
			</div>
		</div>
	</div>
	<?$frame->end();?>
<?endif;?>