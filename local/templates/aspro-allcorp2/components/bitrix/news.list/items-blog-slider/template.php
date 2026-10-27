<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);?>
<?if($arResult['ITEMS']):?>
	<div class="projects item-views table with-comments wraps">
		<div class="title-block-big">
			<div class="pull-right">
				<div class="nav-direction">
					<ul class="flex-direction-nav">
						<li class="flex-nav-prev">
							<a href="javascript:void(0)" class="flex-prev"><span>Prev</span></a>
						</li>
						<li class="flex-nav-next">
							<a href="javascript:void(0)" class="flex-next"><span>Next</span></a>
						</li>
					</ul>
				</div>
			</div>
			<?if($arParams['TITLE_BLOCK']):?>
				<?=$arParams['TITLE_BLOCK'];?>
			<?endif;?>
					
		</div>
		<div class="flexslider unstyled row front1 flex_loader_circle  dark-nav2" data-plugin-options='{"animation": "slide", "directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false, "itemMargin": 32, "customDirection": ".projects .nav-direction a", "counts": [3, 2, 1, 1]}'>
			<ul class="slides items">
				<?foreach($arResult["ITEMS"] as $arItem):?>
					<?
					// edit/add/delete buttons for edit mode
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					
					$bImage = (isset($arItem['FIELDS']['PREVIEW_PICTURE']) && $arItem['PREVIEW_PICTURE']['SRC']);
					$imageSrc = ($bImage ? $arItem['PREVIEW_PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/noimage_product.png');
					// show active date period
					$bActiveDate = strlen($arItem['DISPLAY_PROPERTIES']['PERIOD']['VALUE']) || ($arItem['DISPLAY_ACTIVE_FROM'] && in_array('DATE_ACTIVE_FROM', $arParams['FIELD_CODE']));
					?>
					<li class="col-md-4">
						<div class="item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
							<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
								<?// preview picture?>
								<div class="image shine <?=($bImage ? "w-picture" : "wo-picture");?>" style="">
									<div class="img" style="background: url(<?=$imageSrc?>) center top / cover no-repeat;"></div>
								</div>
								<div class="info">
									<?// element name?>
									<div class="title dark-color">
										<span><?=$arItem['NAME']?></span>
									</div>
									<div class="comments-wrapper">
										<?// date active period?>
										<?if($bActiveDate):?>
											<div class="period">
												<?if(strlen($arItem['DISPLAY_PROPERTIES']['PERIOD']['VALUE'])):?>
													<?=$arItem['DISPLAY_PROPERTIES']['PERIOD']['VALUE']?>
												<?else:?>
													<?=$arItem['DISPLAY_ACTIVE_FROM']?>
												<?endif;?>
											</div>
										<?endif;?>
										<div class="comments"></div>
									</div>
								</div>
							</a>
						</div>
					</li>
				<?endforeach;?>
			</ul>
		</div>
	</div>		
<?endif;?>