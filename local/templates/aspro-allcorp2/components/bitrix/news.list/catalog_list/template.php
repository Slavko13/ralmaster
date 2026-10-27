<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?
$frame = $this->createFrame()->begin();
$frame->setAnimation(true);
global $arTheme;
$bShowImage = in_array('PREVIEW_PICTURE', $arParams['FIELD_CODE']);
$bOrderViewBasket = $arParams['ORDER_VIEW'];

$basketURL = CAllcorp2::GetFrontParametrValue('URL_BASKET_SECTION');
?>
<?
$bHasSection = false;
if(isset($arResult['SECTION_CURRENT']) && $arResult['SECTION_CURRENT'] && $arParams['AJAX_REQUEST'] != 'Y')
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
<?if(empty($arResult["ITEMS"]) ) :?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div> 
<?endif;?>
<?if($arResult['ITEMS']):?>
	<?if($arParams['AJAX_REQUEST'] != 'Y'):?>
	<div class="catalog item-views list image-<?=$arParams['IMAGE_POSITION']?>">
		<?if($arParams['DISPLAY_TOP_PAGER']):?>
			<?=$arResult['NAV_STRING']?>
		<?endif;?>
		<div class="row items" itemscope itemtype="http://schema.org/ItemList">
	<?endif;?>
			<?foreach($arResult['ITEMS'] as $arItem):?>
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
					$arImage = ($bImage ? CFile::ResizeImageGet($nImageID, array('width' => 244, 'height' => 244), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true) : array());
					$imageSrc = ($bImage ? $arImage['src'] : SITE_TEMPLATE_PATH.'/images/noimage_product.png');
					$imageDetailSrc = ($bImage ? $arItem['DETAIL_PICTURE']['SRC'] : false);
				}
				// use order button?
				$bOrderButton = ($arItem['DISPLAY_PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'] == 'YES');
				$dataItem = ($bOrderViewBasket ? CAllcorp2::getDataItem($arItem) : false);
				$elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);
				?>
				<?ob_start();?>
					<?if($bShowImage):?>
						<div class="image-wrapper">
							<?if($arItem['PROPERTIES']['HIT']['VALUE']):?>
								<div class="stickers">
									<div class="stickers-wrapper">
										<?foreach($arItem['PROPERTIES']['HIT']['VALUE_XML_ID'] as $key => $class):?>
											<div class="sticker_<?=strtolower($class);?>"><?=$arItem['PROPERTIES']['HIT']['VALUE'][$key]?></div>
										<?endforeach;?>
									</div>
								</div>
							<?endif;?>
							<div class="image image-list-wrapper">
								<?
								$a_alt=($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"] );
								$a_title=($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"] );
								?>
								<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>">
								<?elseif($imageDetailSrc):?><a href="<?=$imageDetailSrc?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" class="img-inside fancybox">
								<?endif;?>
									<img class="img-responsive" src="<?=$imageSrc?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" itemprop="image" />
								<?if($bDetailLink):?></a>
								<?elseif($imageDetailSrc):?><span class="zoom"></span></a>
								<?endif;?>
							</div>
						</div>
					<?endif;?>
				<?$imgPart = ob_get_clean();?>

				<?ob_start();?>
					<div class="text">
						<div class="row">
							<?$colmd = 12 - (strlen($arItem['DISPLAY_PROPERTIES']['PRICE']['VALUE']) || $bOrderButton ? ($bShowImage ? 3 : 2) : 0);?>
							<div class="col-md-7 col-sm-6 col-xs-12">
								<div class="cont">
									<?// element name?>
									<?if(strlen($arItem['FIELDS']['NAME'])):?>
										<div class="title js-popup-title">
											<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" itemprop="url"><?endif;?>
												<span itemprop="name"><?=$elementName?></span>
											<?if($bDetailLink):?></a><?endif;?>
										</div>
									<?endif;?>

									<?// element status?>
									<?if(strlen($arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE'])):?>
										<?=\Aspro\Functions\CAsproAllcorp2::showSchemaAvailabilityMeta($arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE_XML_ID']);?>
										<span class="status-icon <?=$arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE_XML_ID']?>">
											<?=$arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE']?>	
										</span>
									<?endif;?>

									<?// element article?>
									<?if(strlen($arItem['DISPLAY_PROPERTIES']['ARTICLE']['VALUE'])):?>
										<span class="article" ><?=GetMessage('S_ARTICLE')?>&nbsp;<span><?=$arItem['DISPLAY_PROPERTIES']['ARTICLE']['VALUE']?></span></span>
									<?endif;?>

									<?// element preview text?>
									<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT'])):?>
										<div class="description" itemprop="description">
											<?if($arItem['PREVIEW_TEXT_TYPE'] == 'text'):?>
												<p><?=$arItem['FIELDS']['PREVIEW_TEXT']?></p>
											<?else:?>
												<?=$arItem['FIELDS']['PREVIEW_TEXT']?>
											<?endif;?>
										</div>
									<?endif;?>
									<?if($arItem['HOVER_PROPS']):?>
										<div class="props_list_wrapp">
											<div class="props_list prod">
												<?foreach( $arItem["HOVER_PROPS"] as $arProp ){?>
													<?if( !empty( $arProp["VALUE"] ) ){?>
														<div class="char">
															<span class="name"><?=$arProp["NAME"]?>:</span>
															<span><?=$arProp["VALUE"]?></span>
														</div>
													<?}?>
												<?}?>
											</div>
											<div class="show_props">
												<span class="icons_fa char_title dark_link opened">
													<span><?=GetMessage('PROPERTIES')?></span>
													<?=CAllcorp2::showIconSvg('char op', SITE_TEMPLATE_PATH.'/images/svg/more_arrow.svg');?>
													<?=CAllcorp2::showIconSvg('char cl', SITE_TEMPLATE_PATH.'/images/svg/less_arrow.svg');?>
												</span>
											</div>
										</div>
									<?endif;?>
								</div>
							</div>

							<?if(strlen($arItem['DISPLAY_PROPERTIES']['PRICE']['VALUE']) || $bOrderButton):?>
								<div class="col-md-5 col-sm-6 col-xs-12">
									<div class="foot pull-right">
										<?=\Aspro\Functions\CAsproAllcorp2::showPrice($arItem, $arParams, false, false, true);?>
										<?=\Aspro\Functions\CAsproAllcorp2::showBasketButton($arItem, $arParams, $bOrderButton, $bOrderViewBasket, $basketURL);?>
									</div>
								</div>
							<?endif;?>
						</div>
					</div>
				<?$textPart = ob_get_clean();?>

				<div class="col-md-12 col-sm-12 col-xs-12 js-popup-block">
					<div itemprop="itemListElement" itemscope="" itemtype="http://schema.org/Product" class="item<?=($bShowImage ? '' : ' wti')?>" id="<?=$this->GetEditAreaId($arItem['ID'])?>"<?=($bOrderViewBasket ? ' data-item="'.$dataItem.'"' : '')?> itemscope="" itemtype="http://schema.org/Product">
						<?='<meta itemprop="position" content="'.(++$contenPosition).'" />';?>
						<meta itemprop="description" content="<?=$arItem['FIELDS']['PREVIEW_TEXT'] ? strip_tags($arItem['FIELDS']['PREVIEW_TEXT']) : $arItem['NAME']?>">
						<meta itemprop="name" content="<?=$arItem['NAME']?>">
						<link itemprop="url" href="<?=$arItem['DETAIL_PAGE_URL']?>"/>
						<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="row">							
							<?if(!$bShowImage):?>
								<div class="col-md-12">
									<?=$textPart?>
								</div>
							<?elseif($arParams['IMAGE_POSITION'] == 'right'):?>
								<div class="col-md-9 col-sm-9">
									<?=$textPart?>
								</div>
								<div class="col-md-3 col-sm-3">
									<?=$imgPart?>
								</div>
							<?else:?>
								<div class="col-md-3 col-sm-3">
									<?=$imgPart?>
								</div>
								<div class="col-md-9 col-sm-9">
									<?=$textPart?>
								</div>
							<?endif;?>
						</div>
					</div>
				</div>
			<?endforeach;?>
			<script>
			$(document).ready(function(){
				setBasketItemsClasses();
			});
			</script>
	<?if($arParams['AJAX_REQUEST'] != 'Y'):?>
		</div>
	<?endif;?>
		<?if($arParams["AJAX_REQUEST"]=="Y"):?>
			<div class="wrap_nav">
		<?endif;?>
		<div class="bottom_nav <?=$arParams["DISPLAY_TYPE"];?>" <?=($arParams["AJAX_REQUEST"]=="Y" ? "style='display: none; '" : "");?>>
			<?if( $arParams["DISPLAY_BOTTOM_PAGER"] == "Y" ){?><?=$arResult["NAV_STRING"]?><?}?>
		</div>
		<?if($arParams["AJAX_REQUEST"]=="Y"):?>
			</div>
		<?endif;?>
	<?if($arParams['AJAX_REQUEST'] != 'Y'):?>
	</div>
	<?endif;?>
<?endif;?>
<?if($bHasSection):?>
	</div>
<?endif;?>
<?$frame->end();?>