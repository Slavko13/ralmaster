<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<?if($arResult['ITEMS']):?>
	<div class="maxwidth-theme">
		<div class="col-md-12">
			<?
			$path = SITE_DIR.'/include/mainpage/text/services.php';
			$bShowContent = CAllcorp2::checkContentFile($path);
			?>
			<div class="float-banners v1 item-views blocks">
				<?if($arParams['TITLE']):?>
					<div class="title_block">
						<?if($arParams['RIGHT_TITLE'] && $arParams['RIGHT_LINK']):?>
							<a href="<?=SITE_DIR.$arParams['RIGHT_LINK']?>" class="right_link_block"><?=$arParams['RIGHT_TITLE'];?></a>
						<?endif;?>
						<h3 <?=($bShowContent ? 'class="line"' : '');?>><?=$arParams['TITLE'];?></h3>
					</div>
				<?endif;?>
				<div class="items row">
					<?if($bShowContent):?>
						<div class="col-md-3 text-block">
							<?$APPLICATION->IncludeFile($path);?>
						</div>
						<div class="col-md-9">
					<?else:?>
						<div class="col-md-12">
					<?endif;?>
						<div class="row indent10">
						<?foreach($arResult['ITEMS'] as $arItem):?>
							<?
							// edit/add/delete buttons for edit mode
							$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
							$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
							// preview picture
							if($arItem['PREVIEW_PICTURE'])
							{
								// $arImage = CFile::ResizeImageGet($arItem['PICTURE'], array('width' => 361, 'height' => 254), BX_RESIZE_IMAGE_EXACT, true);
								$arImage = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width' => 500, 'height' => 500), BX_RESIZE_IMAGE_PROPORTIONAL, true);
								$imageSrc = $arImage['src'];
							}

							$colmd = 3;
							if($arItem['PROPERTIES']['WIDE']['VALUE'] == 'Y')
								$colmd = 6;
							if($arItem['PREVIEW_PICTURE']):?>
								<div class="col-md-<?=$colmd?> col-sm-6 col-xs-6">
									<div class="item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
										<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
											<a href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>" class="dark_block_animate"></a>
										<?endif;?>
										<?// icon or preview picture?>
										<div class="img_block scale_block_animate" style="background-image: url('<?=$imageSrc;?>');"></div>
										<?if($arItem['PROPERTIES']['ONLY_IMAGE']['VALUE'] != 'Y'):?>
											<div class="wrap_tizer <?=($arItem['PROPERTIES']['TEXT_COLOR']['VALUE_XML_ID'] ? $arItem['PROPERTIES']['TEXT_COLOR']['VALUE_XML_ID'] : '')?>">
												<div class="wrapper_inner_tizer">
													<div class="wr_block">
														<span class="wrap_outer">
															<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
																<a href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>" class="outer_text">
															<?else:?>
																<span class="outer_text">
															<?endif;?>
																<span class="inner_text"><?=$arItem["NAME"];?></span>
															<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
																</a>
															<?else:?>
																</span>
															<?endif;?>
														</span>
													</div>
													<?// preview text?>
													<?if(strlen($arItem['PREVIEW_TEXT'])):?>
														<div class="wr_block price">
															<span class="wrap_outer_desc">
																<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
																	<a class="outer_text_desc" href="<?=$arItem['PROPERTIES']['LINK']['VALUE'];?>">
																<?else:?>
																	<span class="outer_text_desc">
																<?endif;?>
																	<span class="inner_text_desc"><?=$arItem['PREVIEW_TEXT']?></span>
																<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
																	</a>
																<?else:?>
																	</span>
																<?endif;?>
															</span>
														</div>
													<?endif;?>
												</div>
											</div>
										<?endif;?>
									</div>
								</div>
							<?endif;?>
						<?endforeach;?>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?endif;?>