<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if($arResult["ITEMS"]){?>
	<div class="maxwidth-theme">
		<div class="col-md-12">
			<?if($arParams['SHOW_TITLE'] == 'Y'):?>
				<div class="title-tab-heading visible-xs"><?=$arParams['T_PROJECTS'];?></div>
			<?endif;?>
			<div class="news_block item-views table-elements blocks">
				<?if($arParams['TITLE']):?>
					<div class="title_block">
						<?if($arParams['RIGHT_TITLE'] && $arParams['RIGHT_LINK']):?>
							<a href="<?=SITE_DIR.$arParams['RIGHT_LINK']?>" class="right_link_block"><?=$arParams['RIGHT_TITLE'];?></a>
						<?endif;?>
						<h3 <?=($bShowContent ? 'class="line"' : '');?>><?=$arParams['TITLE'];?></h3>
					</div>
				<?endif;?>
				<div class="items row flexbox">
					<?$arParams["COUNT_IN_LINE"] = intval($arParams["COUNT_IN_LINE"]);
					$arParams["COUNT_IN_LINE"] = (($arParams["COUNT_IN_LINE"] > 0 && $arParams["COUNT_IN_LINE"] < 12) ? $arParams["COUNT_IN_LINE"] : 3);
					$colmd = floor(12 / $arParams['COUNT_IN_LINE']);
					$colsm = floor(12 / round($arParams['COUNT_IN_LINE'] / 2));?>
					<?foreach($arResult["ITEMS"] as $arItem){
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
						$img_source = ($arItem["PREVIEW_PICTURE"] ? $arItem["PREVIEW_PICTURE"] : ($arItem["DETAIL_PICTURE"] ? $arItem["DETAIL_PICTURE"] : ''));
						$arItem["DETAIL_PAGE_URL"] = CAllcorp2::FormatNewsUrl($arItem);
						?>
						<div class="col-md-<?=$colmd?> col-sm-<?=$colsm?> col-xs-<?=$colsm?>">
							<div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="item <?=(!$img_source ? 'nimg' : '');?>">
								<?if($img_source):?>
									<?if(isset($arItem['SECTIONS']) && $arItem['SECTIONS']):?>
										<div class="stickers">
											<div class="stickers-wrapper">
												<?foreach($arItem['SECTIONS'] as $section):?>
													<div class="sticker"><?=$section;?></div>
												<?endforeach;?>
											</div>
										</div>
									<?endif;?>
									<div class="img image shine">
										<?$img = CFile::ResizeImageGet($img_source, array("width" => 700, "height" => 700), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true, false, false, 75 );?>
										<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
											<img src="<?=$img["src"]?>" alt="<?=$arItem["NAME"];?>"  />
										</a>
									</div>
								<?endif;?>
								<?if(($arItem["DISPLAY_ACTIVE_FROM"] || $arItem["DISPLAY_PROPERTIES"]["PERIOD"]["VALUE"]) && $arParams["SHOW_DATE"]=="Y"){?>
									<div class="date"><?=($arItem["DISPLAY_PROPERTIES"]["PERIOD"]["VALUE"] ? $arItem["DISPLAY_PROPERTIES"]["PERIOD"]["VALUE"] : $arItem["DISPLAY_ACTIVE_FROM"]);?></div>
								<?}?>
								<div class="info">
									<?if(!$img_source):?>
										<?if(isset($arItem['SECTIONS']) && $arItem['SECTIONS']):?>
											<div class="sticker-block">
												<?=implode(', ', $arItem['SECTIONS']);?>
											</div>
										<?endif;?>
									<?endif;?>
									<a class="name dark_link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
									<?if(!$img_source && $arItem['PREVIEW_TEXT']):?>
										<div class="text"><?=$arItem['PREVIEW_TEXT'];?></div>
									<?endif;?>
								</div>
							</div>
						</div>
					<?}?>
				</div>
			</div>
		</div>
	</div>
<?}?>