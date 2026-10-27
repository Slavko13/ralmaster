<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>
<?if($arResult['ITEMS']):?>
	<div class="maxwidth-theme">
		<div class="col-md-12">
			<div class="float-banners v3 list item-views blocks">
				<?if($arParams['TITLE']):?>
					<div class="title_block">
						<?if($arParams['RIGHT_TITLE'] && $arParams['RIGHT_LINK']):?>
							<a href="<?=SITE_DIR.$arParams['RIGHT_LINK']?>" class="right_link_block"><?=$arParams['RIGHT_TITLE'];?></a>
						<?endif;?>
						<h3 <?=($bShowContent ? 'class="line"' : '');?>><?=$arParams['TITLE'];?></h3>
					</div>
				<?endif;?>
				<div class="row items">
					<div class="col-md-12">
						<div class="row margin0 items flexbox nmac">
							<?$arParams["COUNT_IN_LINE"] = intval($arParams["COUNT_IN_LINE"]);
							$arParams["COUNT_IN_LINE"] = (($arParams["COUNT_IN_LINE"] > 0 && $arParams["COUNT_IN_LINE"] < 12) ? $arParams["COUNT_IN_LINE"] : 3);
							$colmd = floor(12 / $arParams['COUNT_IN_LINE']);?>
							<?foreach($arResult['ITEMS'] as $arItem):?>
								<?
								// edit/add/delete buttons for edit mode
								$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
								$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
								$arImage = array();
								// preview picture
								if($arItem['PREVIEW_PICTURE'])
								{
									$arImage = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width' => 640, 'height' => 420), BX_RESIZE_IMAGE_PROPORTIONAL, true);
									$imageSrc = $arImage['src'];
								}?>
								<div class="col-lg-<?=$colmd?> col-md-6 col-sm-6 col-xs-12">
									<div class="item">
										<div class="item-inner">
											<?if($arItem['PREVIEW_PICTURE']):?>
												<div class="img">
													<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
														<a href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>" class="">
													<?endif;?>
													<div class="img_block scale_block_animate" style="background-image: url('<?=$imageSrc?>');"></div>
													<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
														</a>
													<?endif;?>
												</div>
											<?endif;?>
											<div class="text-wrapper">
												<div class="title">
													<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
														<a href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>" class="colored">
													<?endif;?>
													<span><?=$arItem['NAME'];?></span>
													<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
														</a>
													<?endif;?>
												</div>
												<?// preview text?>
												<?if(strlen($arItem['PREVIEW_TEXT'])):?>
													<div class="preview_text">
														<?=$arItem['PREVIEW_TEXT'];?>
													</div>
												<?endif;?>
											</div>
											<div class="clearfix"></div>
										</div>
									</div>
								</div>
							<?endforeach;?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?endif;?>