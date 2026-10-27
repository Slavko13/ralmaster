<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>
<?if($arResult['ITEMS']):?>
		<div class="maxwidth-theme">
			<div class="col-md-12">
				<?
				$qntyItems = count($arResult['ITEMS']);
				

				global $arTheme;
				$slideshowSpeed = abs(intval($arTheme['PARTNERSBANNER_SLIDESSHOWSPEED']['VALUE']));
				$animationSpeed = abs(intval($arTheme['PARTNERSBANNER_ANIMATIONSPEED']['VALUE']));
				$bAnimation = (bool)$slideshowSpeed;
				?>
				<div class="item-views reviews slider blocks">
					<div class="title_block">
						<div class="pull-right <?=($qntyItems == 2 ? 'hidden' : '')?>" >
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
						<?if($arParams['TITLE']):?>
							<?if($arParams['RIGHT_TITLE'] && $arParams['RIGHT_LINK']):?>
								<a href="<?=SITE_DIR.$arParams['RIGHT_LINK']?>" class="right_link_block"><?=$arParams['RIGHT_TITLE'];?></a>
							<?endif;?>
							<h3 <?=($bShowContent ? 'class="line"' : '');?>><?=$arParams['TITLE'];?></h3>
						<?endif;?>
					</div>
					<div class="flexslider unstyled row dark-nav2 shadow-block flex_loader_circle" data-plugin-options='{"directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false, "customDirection": ".reviews .nav-direction a", "itemMargin": 32, "counts": [2, 2, 1]}'>
						<ul class="slides items" data-slice="Y">
							<?foreach($arResult['ITEMS'] as $i => $arItem):?>
								<?
								// edit/add/delete buttons for edit mode
								$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
								$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

								// preview image
								$bImage = (isset($arItem['FIELDS']['PREVIEW_PICTURE']) && $arItem['PREVIEW_PICTURE']['SRC']);
								$nImageID = ($bImage ? (is_array($arItem['FIELDS']['PREVIEW_PICTURE']) ? $arItem['FIELDS']['PREVIEW_PICTURE']['ID'] : $arItem['FIELDS']['PREVIEW_PICTURE']) : "");
								$arImage = ($bImage ? CFile::ResizeImageGet($nImageID, array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_EXACT, true) : array());
								$imageSrc = ($bImage ? $arImage['src'] : SITE_TEMPLATE_PATH.'/images/noimage_review_sm.png');
								?>
								<li class="col-md-6">
									<div class="item" data-slice-block="Y" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
										<div class="table_block">
											<?if($imageSrc):?>
												<div class="image <?=($bImage ? '' : 'wpi')?>">
													<div class="image-wrapper">
														<div class="image-inner">
															<img class="img-responsive" src="<?=$imageSrc?>" alt="<?=($bImage ? $arItem['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?=($bImage ? $arItem['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" />
														</div>
													</div>
												</div>
											<?endif;?>
											<div class="text">
												<div class="title">
													<?=$arItem['NAME'];?>
												</div>
												<?if($arItem['DISPLAY_PROPERTIES']['POST']['VALUE']):?>
													<div class="company"><?=$arItem['PROPERTIES']['POST']['VALUE'];?></div>
												<?endif;?>
											</div>
										</div>
										<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT'])):?>
											<div class="preview-text-wrapper">
												<div class="quote"><?=CAllcorp2::showIconSvg('', SITE_TEMPLATE_PATH.'/images/svg/Quote.svg', '', false);?></div>
												<div class="preview-text">
													<?if($arItem['PREVIEW_TEXT_TYPE'] == 'text'):?>
														<p><?=$arItem['FIELDS']['PREVIEW_TEXT'];?></p>
													<?else:?>
														<?=$arItem['FIELDS']['PREVIEW_TEXT'];?>
													<?endif;?>
												</div>
												<?if(strlen($arParams['PREVIEW_TRUNCATE_LEN']) && strlen($arItem['~PREVIEW_TEXT']) > $arParams['PREVIEW_TRUNCATE_LEN']):?>
													<div class="link-block-more">
														<span class="btn btn-default btn-sm btn-transparent-bg animate-load" data-event="jqm" data-param-id="<?=$arItem['ID'];?>" data-param-type="review" data-name="review"><?=Loc::getMessage('MORE');?></span>
													</div>
												<?endif;?>
											</div>
										<?endif;?>
									</div>
								</li>
							<?endforeach;?>
						</ul>
					</div>
				</div>
			</div>
		</div>
<?endif;?>