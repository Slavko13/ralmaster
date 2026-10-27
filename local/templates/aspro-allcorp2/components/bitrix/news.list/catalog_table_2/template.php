<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
use Bitrix\Main\Page\Asset;
$frame = $this->createFrame()->begin();
$frame->setAnimation(true);
global $USER;
$userID = $USER->GetID();
$userID = ($userID > 0 ? $userID : 0);
global $arTheme;
$arParams["COUNT_IN_LINE"] = intval($arParams["COUNT_IN_LINE"]);
$arParams["COUNT_IN_LINE"] = (($arParams["COUNT_IN_LINE"] > 0 && $arParams["COUNT_IN_LINE"] < 12) ? $arParams["COUNT_IN_LINE"] : 3);
$colmd = floor(12 / $arParams['COUNT_IN_LINE']);
$colsm = floor(12 / round($arParams['COUNT_IN_LINE'] / 2));
$bShowImage = in_array('PREVIEW_PICTURE', $arParams['FIELD_CODE']);
$bOrderViewBasket = $arParams['ORDER_VIEW'];
$basketURL = CAllcorp2::GetFrontParametrValue('URL_BASKET_SECTION');
$removealert == "Y";
?>
<?
$bFromAjax = (isset($arParams['AJAX_REQUEST']) && $arParams['AJAX_REQUEST'] == 'Y');
$bHasSection = false;
if(isset($arResult['SECTION_CURRENT']) && $arResult['SECTION_CURRENT'] && !$bFromAjax)
	$bHasSection = true;
if($bHasSection)
{
	// edit/add/delete buttons for edit mode
	$arSectionButtons = CIBlock::GetPanelButtons($arParams['IBLOCK_ID'], 0, $arResult['SECTION_CURRENT']['ID'], array('SESSID' => false, 'CATALOG' => true));
	$this->AddEditAction($arResult['SECTION_CURRENT']['ID'], $arSectionButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_EDIT'));
	$this->AddDeleteAction($arResult['SECTION_CURRENT']['ID'], $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="section" id="<?=$this->GetEditAreaId($arResult['SECTION_CURRENT']['ID'])?>">
	<?
}?>


<?if(empty($arResult["ITEMS"])) :?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div> 
<?endif;?>
<?if($arResult["ITEMS"]):?>
	<?if(!$bFromAjax):?>
	<div class="catalog item-views table <?=$templateName;?>" data-slice="Y">
		<?if($arParams["DISPLAY_TOP_PAGER"]):?>
			<?=$arResult["NAV_STRING"]?>
		<?endif;?>
		<div itemscope itemtype="http://schema.org/ItemList"  class="row items flexbox">
	<?endif;?>
			<?foreach($arResult["ITEMS"] as $arItem):?>
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
				$bOrderButton = ($arItem["DISPLAY_PROPERTIES"]["FORM_ORDER"]["VALUE_XML_ID"] == "YES");
				$dataItem = ($bOrderViewBasket ? CAllcorp2::getDataItem($arItem) : false);
				$elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);
				?>
				<div class="col-md-<?=$colmd?> col-sm-<?=$colsm?> col-xs-<?=$colsm?> js-popup-block">
					<div itemprop="itemListElement" itemscope="" itemtype="http://schema.org/Product"  class="item<?=($bShowImage ? '' : ' wti')?> js-popup-block <?=$arItem['HOVER_PROPS'] ? "hover_props" : ""?>" id="<?=$this->GetEditAreaId($arItem['ID'])?>"<?=($bOrderViewBasket ? ' data-item="'.$dataItem.'"' : '')?>>
						<div class="inner-wrap">
							<meta itemprop="position" content="<?=(++$positionProduct)?>" />
							<?if($arItem['PROPERTIES']['HIT']['VALUE']):?>
								<div class="stickers">
									<div class="stickers-wrapper">
										<?foreach($arItem['PROPERTIES']['HIT']['VALUE_XML_ID'] as $key => $class):?>
											<div class="sticker_<?=strtolower($class);?>"><?=$arItem['PROPERTIES']['HIT']['VALUE'][$key]?></div>
										<?endforeach;?>
									</div>
								</div>
							<?endif;?>
							<?if($bShowImage):?>
								<?
								$a_alt=($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"] );
								$a_title=($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"] );
								?>
								<div class="image image-list-wrapper">
									<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>">
									<?elseif($imageDetailSrc):?><a href="<?=$imageDetailSrc?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" class="img-inside fancybox">
									<?endif;?>
										<img itemprop="image" class="img-responsive" src="<?=$imageSrc?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
									<?if($bDetailLink):?></a>
									<?elseif($imageDetailSrc):?><span class="zoom"></span></a>
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
							<meta itemprop="description" content="<?=$arItem['FIELDS']['PREVIEW_TEXT'] ? strip_tags($arItem['FIELDS']['PREVIEW_TEXT']) : $arItem['NAME']?>">
							<meta itemprop="name" content="<?=$arItem['NAME']?>">
							<link itemprop="url" href="<?=$arItem['DETAIL_PAGE_URL']?>"/>
							<div class="text" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
								<div class="cont">
									<div class="cont_inner">
										<?// element name?>
										<?if(strlen($arItem['FIELDS']['NAME'])):?>
											<div class="title js-popup-title">											    
											<?if($bDetailLink):?><link itemprop="url" href="<?=$arItem['DETAIL_PAGE_URL']?>"><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="dark-color"><?endif;?>
													<?=$elementName?>
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
												<?=\Aspro\Functions\CAsproAllcorp2::showSchemaAvailabilityMeta($arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE_XML_ID']);?>
												<span class="status-icon <?=$arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE_XML_ID']?>"><?=$arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE']?></span>
											<?endif;?>

											<?// element article?>
											<?if(strlen($arItem['DISPLAY_PROPERTIES']['ARTICLE']['VALUE'])):?>
												<span class="article"><?=GetMessage('S_ARTICLE')?>&nbsp;<span><?=$arItem['DISPLAY_PROPERTIES']['ARTICLE']['VALUE']?></span></span>
											<?endif;?>
										</div>
									</div>
								</div>

								<div class="row foot">
									<div class="col-md-12 col-sm-12 col-xs-12 clearfix slice_price">
										<?=\Aspro\Functions\CAsproAllcorp2::showPrice($arItem, $arParams, $bOrderViewBasket, false, true);?>
									</div>
									
									<div class="col-md-12 col-sm-12 col-xs-12 button_block">
										<?=\Aspro\Functions\CAsproAllcorp2::showBasketButton($arItem, $arParams, $bOrderButton, $bOrderViewBasket, $basketURL, false, true);?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?endforeach;?>
			<?if($arParams['AJAX_MODE'] == 'Y'):?>
				<script type="text/javascript">
					startActions();
				</script>
			<?endif;?>
		<?if(!$bFromAjax):?>
		</div>
		<?endif;?>

		<?if($bFromAjax):?>
			<div class="wrap_nav">
		<?endif;?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"] == 'Y'):?>
			<div class="bottom_nav <?=$arParams["DISPLAY_TYPE"];?>" <?=($bFromAjax ? "style='display: none; '" : "");?>>
				<?if( $arParams["DISPLAY_BOTTOM_PAGER"] == "Y" ){?><?=$arResult["NAV_STRING"]?><?}?>
			</div>
		<?endif;?>
		<?if($bFromAjax):?>
			</div>
		<?endif;?>

	<?if(!$bFromAjax):?>
	</div>
	<?endif;?>
<?endif;?>
<?if($bHasSection):?>
	</div>
<?endif;?>
<?$frame->end();?>