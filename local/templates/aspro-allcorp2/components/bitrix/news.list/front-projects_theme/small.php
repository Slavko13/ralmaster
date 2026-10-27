<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?
$qntyItems = count($arResult['ITEMS']);
$countmd = 4;
$countsm = 3;
$countxs = 2;
$countxsm = 1;
$colmd = 3;
$colsm = 4;
$colxs = 6;
$bShowImage = in_array('PREVIEW_PICTURE', $arParams['FIELD_CODE']);
?>
<div class="maxwidth-theme">
	<div class="col-md-12">
		<div class="item-views portfolio blocks front">
			<div class="top_wrapper_block">
				<div class="row" itemscope itemtype="http://schema.org/ItemList">
					<div class="col-md-<?=$colmd?> col-sm-<?=$colsm?> col-xs-<?=$colxs?> link">
						<div class="item">
							<div class="top_title"><div><?=$arParams['TITLE'];?></div></div>
							<div class="bottom_title"><div><a href="<?=str_replace('#SITE'.'_DIR#', SITE_DIR, $arResult['LIST_PAGE_URL'])?>" class="white_link"><span><?=$arParams['RIGHT_TITLE'];?></span></a></div></div>
						</div>
					</div>
					<?foreach($arResult["ITEMS"] as $i => $arItem):
						if(!$arItem['FIELDS']['PREVIEW_PICTURE']['SRC'])
							continue;?>
						<?
						// edit/add/delete buttons for edit mode
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
						// use detail link?
						$bDetailLink = $arParams['SHOW_DETAIL_LINK'] != 'N' && (!strlen($arItem['DETAIL_TEXT']) ? $arParams['HIDE_LINK_WHEN_NO_DETAIL'] !== 'Y' : true);
						// preview image
						if($bShowImage){
							$bImage = strlen($arItem['FIELDS']['PREVIEW_PICTURE']['SRC']);
							$arImage = ($bImage ? CFile::ResizeImageGet($arItem['FIELDS']['PREVIEW_PICTURE']['ID'], array('width' => 400, 'height' => 400), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true) : array());
							$imageSrc = ($bImage ? $arImage['src'] : SITE_TEMPLATE_PATH.'/images/noimage_product.png');
							$imageDetailSrc = ($bImage ? $arItem['FIELDS']['DETAIL_PICTURE']['SRC'] : false);
						}
						?>
						<div class="col-md-<?=$colmd?> col-sm-<?=$colsm?> col-xs-<?=$colxs?>">
							<div class="item<?=($bShowImage ? '' : ' wti')?> wline" id="<?=$this->GetEditAreaId($arItem['ID'])?>" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/Product">
								<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="dark_block_animate">
									<div class="text">
										<div class="cont">
											<?// element section name?>
											<?if(isset($arItem['SECTIONS']) && $arItem['SECTIONS']):?>
												<div class="section_name"><?=implode(', ', $arItem['SECTIONS'] );?></div>
											<?endif;?>
											<?// element name?>
											<?if(strlen($arItem['FIELDS']['NAME'])):?>
												<div class="title">
													<span itemprop="name"><?=$arItem['NAME']?></span>
												</div>
											<?endif;?>									
										</div>
									</div>
								</a>
								<div class="img_block scale_block_animate" style="background-image: url('<?=$imageSrc;?>');"></div>						
							</div>
						</div>
					<?endforeach;?>				
				</div>
			</div>
		</div>
	</div>
</div>