<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?global $arTheme;?>
<?if($arResult['SECTIONS']):?>
	<div class="maxwidth-theme">
			<div class="col-md-12">
			<div class="sections_wrapper item-views blocks">
				<?if($arParams["TITLE"]):?>
					<div class="title_block">
						<?if($arParams['RIGHT_TITLE'] && $arParams['RIGHT_LINK']):?>
							<a href="<?=SITE_DIR.$arParams["RIGHT_LINK"];?>" class="right_link_block"><?=$arParams["RIGHT_TITLE"] ;?></a>
						<?endif;?>
						<h3><?=$arParams["TITLE"];?></h3>
					</div>
				<?endif;?>
				<?
				$path = SITE_DIR.'/include/mainpage/text/sections.php';
				$bShowContent = CAllcorp2::checkContentFile($path);?>
				<div class="top-text">
					<?$APPLICATION->IncludeFile($path, Array(), Array("MODE" => "html", "TEMPLATE" => "include_area.php", "NAME" => GetMessage("SECTIONS_INCLUDE_TEXT")));?>
				</div>
				<div class="list items catalog_section_list">
					<div class="row margin0 flexbox">
						<?foreach($arResult['SECTIONS'] as $arSection):
							$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "ELEMENT_EDIT"));
							$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>
							<div class="col-md-3 col-sm-4 col-xs-12">
								<div class="item section_item front-section-only" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
									<div class="section_item_inner">
										<div class="img shine">
											<?if($arSection["PICTURE"]["SRC"]):?>
												<?$img = CFile::ResizeImageGet($arSection["PICTURE"]["ID"], array( "width" => 120, "height" => 120 ), BX_RESIZE_IMAGE_EXACT, true );?>
												<a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="thumb"><img src="<?=$img["src"]?>" alt="<?=($arSection["PICTURE"]["ALT"] ? $arSection["PICTURE"]["ALT"] : $arSection["NAME"])?>" title="<?=($arSection["PICTURE"]["TITLE"] ? $arSection["PICTURE"]["TITLE"] : $arSection["NAME"])?>" /></a>
											<?elseif($arSection["~PICTURE"]):?>
												<?$img = CFile::ResizeImageGet($arSection["~PICTURE"], array( "width" => 120, "height" => 120 ), BX_RESIZE_IMAGE_EXACT, true );?>
												<a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="thumb"><img src="<?=$img["src"]?>" alt="<?=($arSection["PICTURE"]["ALT"] ? $arSection["PICTURE"]["ALT"] : $arSection["NAME"])?>" title="<?=($arSection["PICTURE"]["TITLE"] ? $arSection["PICTURE"]["TITLE"] : $arSection["NAME"])?>" /></a>
											<?else:?>
												<a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="thumb"><img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$arSection["NAME"]?>" title="<?=$arSection["NAME"]?>" height="90" /></a>
											<?endif;?>
										</div>
										<div class="name">
											<a href="<?=$arSection['SECTION_PAGE_URL'];?>" class="dark_link"><?=$arSection['NAME'];?></a>
										</div>
									</div>	
								</div>
							</div>
						<?endforeach;?>
					</div>
				</div>
			</div>
		</div>	
	</div>	
<?endif;?>