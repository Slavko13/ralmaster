<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?global $arTheme;?>
<? $this->setFrameMode( true );?>
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
							$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'ELEMENT_EDIT'));
							$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>
							<?$colxs = 6; ?>
							<div class="col-md-6 col-sm-6 col-xs-<?=$colxs?>">
								<div class="item section_item" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
									<div class="section_item_inner">
										<div class="img">
											<?if(is_array($arSection['PICTURE']) && $arSection['PICTURE']['SRC']):?>
												<?$img = CFile::ResizeImageGet($arSection['PICTURE']['ID'], array( "width" => 120, "height" => 120 ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true );?>
												<a href="<?=$arSection['SECTION_PAGE_URL']?>" class="thumb"><img src="<?=$img['src']?>" alt="<?=($arSection['PICTURE']['ALT'] ? $arSection['PICTURE']['ALT'] : $arSection['NAME'])?>" title="<?=($arSection['PICTURE']['TITLE'] ? $arSection['PICTURE']['TITLE'] : $arSection['NAME'])?>" /></a>
											<?elseif($arSection['~PICTURE']):?>
												<?$img = CFile::ResizeImageGet($arSection['~PICTURE'], array( "width" => 120, "height" => 120 ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true );?>
												<a href="<?=$arSection['SECTION_PAGE_URL']?>" class="thumb"><img src="<?=$img['src']?>" alt="<?=$arSection['NAME']?>" title="<?=$arSection['NAME']?>" /></a>
											<?else:?>
												<a href="<?=$arSection['SECTION_PAGE_URL']?>" class="thumb"><img src="<?=SITE_TEMPLATE_PATH?>/images/noimage.png" alt="<?=$arSection['NAME']?>" title="<?=$arSection['NAME']?>" /></a>
											<?endif;?>
										</div>
										<div class="section_info toggle">
											<div class="name">
												<a href="<?=$arSection['SECTION_PAGE_URL']?>" class="dark_link"><span><?=$arSection['NAME']?></span></a>
											</div>
											<?if($arSection['ITEMS']):?>
												<ul>
													<?foreach($arSection['ITEMS'] as $key => $arItem):?>
														<li class="sect"><a href="<?=$arItem['SECTION_PAGE_URL']?>" class=""><?=$arItem['NAME']?><? echo $arItem['ELEMENT_CNT']?'&nbsp;<span>'.$arItem['ELEMENT_CNT'].'</span>':'';?></a></li>
													<?endforeach;?>
												</ul>
											<?endif;?>
											<?if($arSection['UF_TOP_SEO'] ):?>
												<div class="text"><?=$arSection['UF_TOP_SEO'];?></div>
											<?endif;?>
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