<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if($arResult["ITEMS"]){?>
	<div class="maxwidth-theme">
		<div class="col-md-12">
			<div class="news_block item-views table-elements blocks portfolio">
				<?if($arParams['TITLE']):?>
					<div class="title_block">
						<?if($arParams['RIGHT_TITLE'] && $arParams['RIGHT_LINK']):?>
							<a href="<?=SITE_DIR.$arParams['RIGHT_LINK']?>" class="right_link_block"><?=$arParams['RIGHT_TITLE'];?></a>
						<?endif;?>
						<h3 <?=($bShowContent ? 'class="line"' : '');?>><?=$arParams['TITLE'];?></h3>
					</div>
				<?endif;?>
				<div class="items row flexbox">
					<?if(count($arResult["ITEMS"]) != 5):?>
						<?foreach($arResult["ITEMS"] as $key => $arItem){
							$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
							$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
							$img_source = ($arItem["PREVIEW_PICTURE"] ? $arItem["PREVIEW_PICTURE"] : ($arItem["DETAIL_PICTURE"] ? $arItem["DETAIL_PICTURE"] : ''));
							?>
							<div class="col-md-3 col-sm-6 col-xs-6">
								<div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="item sliced <?=(!$img_source ? 'nimg' : '');?>">
									<?if($img_source):?>
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
										<?if(isset($arItem['SECTIONS']) && $arItem['SECTIONS']):?>
											<div class="sticker-block">
												<?=implode(', ', $arItem['SECTIONS']);?>
											</div>
										<?endif;?>
										<a class="name dark_link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
										<?if(!$img_source && $arItem['PREVIEW_TEXT']):?>
											<div class="text"><?=$arItem['PREVIEW_TEXT'];?></div>
										<?endif;?>
									</div>
								</div>
							</div>
						<?}?>
					<?else:?>
						<?$bEvenPage = (($arResult['NAV_RESULT'] && $arResult['NAV_RESULT']->NavPageCount && $arResult['NAV_RESULT']->NavPageNomer %2 == 0) || false);?>
						<?ob_start();?>
							<?foreach($arResult["ITEMS"] as $key => $arItem){
								if($key)
									continue;
								$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
								$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
								$img_source = ($arItem["PREVIEW_PICTURE"] ? $arItem["PREVIEW_PICTURE"] : ($arItem["DETAIL_PICTURE"] ? $arItem["DETAIL_PICTURE"] : ''));
								?>
								<div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="item sliced big <?=(!$img_source ? 'nimg' : '');?>">
									<?if($img_source):?>
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
										<?if(isset($arItem['SECTIONS']) && $arItem['SECTIONS']):?>
											<div class="sticker-block">
												<?=implode(', ', $arItem['SECTIONS']);?>
											</div>
										<?endif;?>
										<a class="name dark_link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
										<?if($arItem['PREVIEW_TEXT'] && in_array('PREVIEW_TEXT', $arParams['FIELD_CODE'])):?>
											<div class="text"><?=$arItem['PREVIEW_TEXT'];?></div>
										<?endif;?>
									</div>
								</div>
							<?}?>
						<?$big_block = ob_get_clean();?>
						<?ob_start();?>
							<?$count = count($arResult["ITEMS"])-1;?>
							<div class="<?=($count > 2 ? 'item' : '');?> wrapper s_<?=$count;?>">
								<div class="row <?=($count > 2 ? 'items' : '');?> flexbox">
									<?foreach($arResult["ITEMS"] as $key => $arItem){
										if(!$key)
											continue;
										$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
										$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
										$img_source = ($arItem["PREVIEW_PICTURE"] ? $arItem["PREVIEW_PICTURE"] : ($arItem["DETAIL_PICTURE"] ? $arItem["DETAIL_PICTURE"] : ''));
										?>
										<div class="col-md-6 col-sm-6 col-xs-6">
											<div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="item sliced <?=(!$img_source ? 'nimg' : '');?>">
												<?if($img_source):?>
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
													<?if(isset($arItem['SECTIONS']) && $arItem['SECTIONS']):?>
														<div class="sticker-block">
															<?=implode(', ', $arItem['SECTIONS']);?>
														</div>
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
						<?$items_block = ob_get_clean();?>
						<div class="col-md-6 col-sm-12 custom">
							<?if($bEvenPage):?>
								<?=$items_block;?>
							<?else:?>
								<?=$big_block;?>
							<?endif;?>
						</div>
						<div class="col-md-6 col-sm-12 custom">
							<?if($bEvenPage):?>
								<?=$big_block;?>
							<?else:?>
								<?=$items_block;?>
							<?endif;?>
						</div>
					<?endif;?>
				</div>
				<div class="nav_wrapper">
					<div class="bottom_nav index_block" data-class=".news_block.blocks.portfolio" data-item=">.items">
						<?if( $arParams["DISPLAY_BOTTOM_PAGER"] == "Y" ){?><?=$arResult["NAV_STRING"]?><?}?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?}?>