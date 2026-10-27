<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>
<?if($arResult['ITEMS']):?>
	<div class="maxwidth-theme">
		<div class="col-md-12">
			<?
			$path = SITE_DIR.'/include/mainpage/text/services.php';
			$bShowContent = CAllcorp2::checkContentFile($path);
			?>
			<?
			$qntyItems = count($arResult['ITEMS']);
			?>
			<div class="float-banners v2 item-views blocks">
				<?if($arParams['TITLE']):?>
					<div class="title_block">
						<?if($arParams['RIGHT_TITLE'] && $arParams['RIGHT_LINK']):?>
							<a href="<?=SITE_DIR.$arParams['RIGHT_LINK']?>" class="right_link_block"><?=$arParams['RIGHT_TITLE'];?></a>
						<?endif;?>
						<h3 <?=($bShowContent ? 'class="line"' : '');?>><?=$arParams['TITLE'];?></h3>
					</div>
				<?endif;?>
				<div class="row items">
					<?if($bShowContent):?>
						<div class="col-md-3 text-block">
							<?$APPLICATION->IncludeFile($path);?>
						</div>
						<div class="col-md-9">
					<?else:?>
						<div class="col-md-12">
					<?endif;?>
						<div class="row items indent2 flexbox nmac">
							<?$arParams["COUNT_IN_LINE"] = intval($arParams["COUNT_IN_LINE"]);
							$arParams["COUNT_IN_LINE"] = (($arParams["COUNT_IN_LINE"] > 0 && $arParams["COUNT_IN_LINE"] < 12) ? $arParams["COUNT_IN_LINE"] : 3);
							$colmd = floor(12 / $arParams['COUNT_IN_LINE']);
							$colsm = floor(12 / round($arParams['COUNT_IN_LINE'] / 2));?>
							<?foreach($arResult['ITEMS'] as $arItem):?>
								<?
								// edit/add/delete buttons for edit mode
								$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
								$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
								$arImage = array();
								// preview picture
								if($arItem['DISPLAY_PROPERTIES']['FILE']['VALUE'])
								{
									$arImage = CFile::ResizeImageGet($arItem['DISPLAY_PROPERTIES']['FILE']['VALUE'], array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_PROPORTIONAL, true);
									$imageSrc = $arImage['src'];
								}?>
								<div class="col-md-<?=$colmd?> col-sm-<?=$colsm?> col-xs-<?=$colsm?> col-xxs-12">
									<div class="item">
										<?if($arImage):?>
											<div class="img">
												<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
													<a href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>" class="">
												<?endif;?>
												<img src="<?=$arImage['src'];?>" alt="<?=$arItem['NAME'];?>" title="<?=$arItem['NAME'];?>" class="img-responsive">
												<?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
													</a>
												<?endif;?>
											</div>
										<?endif;?>
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
								</div>
							<?endforeach;?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?endif;?>