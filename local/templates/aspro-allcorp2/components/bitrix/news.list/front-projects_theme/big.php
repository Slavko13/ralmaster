<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<div class="maxwidth-theme">
	<div class="col-md-12">
		<div class="news_block item-views table-elements blocks portfolio">
			<?if($arParams['TITLE']):?>
				<div class="title_block">
					<h3 <?=($bShowContent ? 'class="line"' : '');?>><?=$arParams['TITLE'];?></h3>
					<?if($arParams['RIGHT_TITLE'] && $arParams['RIGHT_LINK']):?>
						<a href="<?=SITE_DIR.$arParams['RIGHT_LINK']?>" class="right_link_block"><?=$arParams['RIGHT_TITLE'];?></a>
					<?endif;?>
				</div>
			<?endif;?>
			<div class="items row">
				<div class="col-md-6 custom">
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
									<?$img = CFile::ResizeImageGet($img_source, array("width" => 400, "height" => 270), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true, false, false, 75 );?>
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
				</div>
				<?$count = count($arResult["ITEMS"])-1;?>
				<div class="col-md-6 custom">
					<div class="<?=($count > 2 ? 'item' : '');?> wrapper s_<?=$count;?>">
					<div class="row <?=($count > 2 ? 'items' : '');?>">
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
											<?$img = CFile::ResizeImageGet($img_source, array("width" => 400, "height" => 270), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true, false, false, 75 );?>
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
				</div>
			</div>
		</div>
	</div>
</div>