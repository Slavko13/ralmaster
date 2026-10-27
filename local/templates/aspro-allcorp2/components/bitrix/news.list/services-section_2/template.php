<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<?if($arResult['SECTIONS']):?>
	<div class="float-banners v3 item-views">
		<div class="items row services flexbox">
			<?$arParams["LINE_ELEMENT_COUNT"] = intval($arParams["LINE_ELEMENT_COUNT"]);
			$arParams["LINE_ELEMENT_COUNT"] = (($arParams["LINE_ELEMENT_COUNT"] > 0 && $arParams["LINE_ELEMENT_COUNT"] < 12) ? $arParams["LINE_ELEMENT_COUNT"] : 3);
			$colmd = floor(12 / $arParams['LINE_ELEMENT_COUNT']);
			$colsm = floor(12 / round($arParams['LINE_ELEMENT_COUNT'] / 2));?>
			<?foreach($arResult['SECTIONS'] as $arItem):?>
				<?
				// edit/add/delete buttons for edit mode
				$arSectionButtons = CIBlock::GetPanelButtons($arItem['IBLOCK_ID'], 0, $arItem['ID'], array('SESSID' => false, 'CATALOG' => true));
				$this->AddEditAction($arItem['ID'], $arSectionButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'SECTION_EDIT'));
				$this->AddDeleteAction($arItem['ID'], $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				// preview picture
				if($bShowSectionImage = in_array('PREVIEW_PICTURE', $arParams['FIELD_CODE'])){
					$bImage = strlen($arItem['~PICTURE']);
					$arSectionImage = ($bImage ? CFile::ResizeImageGet($arItem['~PICTURE'], array('width' => 700, 'height' => 700), BX_RESIZE_IMAGE_PROPORTIONAL, true) : array());
					$imageSectionSrc = ($bImage ? $arSectionImage['src'] : SITE_TEMPLATE_PATH.'/images/noimage_sections.png');
				}
				?>
				<div class="col-md-<?=$colmd?> col-sm-<?=$colsm;?>">
					<div class="item <?=($bShowSectionImage ? '' : ' wti')?>" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
						<?// icon or preview picture?>
						<?if($bShowSectionImage):?>
							<div class="img">
								<a href="<?=$arItem['SECTION_PAGE_URL']?>">
									<div class="img_block" style="background-image: url(<?=$imageSectionSrc?>);"></div>
								</a>
							</div>
						<?endif;?>
						
						<div class="info">
							<?// section name?>
							<?if(in_array('NAME', $arParams['FIELD_CODE'])):?>
								<div class="title">
									<a href="<?=$arItem['SECTION_PAGE_URL']?>" class="dark-color">
										<?=$arItem['NAME']?>
									</a>
								</div>
							<?endif;?>

							<?// section child?>
							<?if($arItem['CHILD']):?>
								<div class="text childs">
								<ul class="clearfix">
												<?foreach($arItem['CHILD'] as $arSubItem):?>
													<li>
														<?
														if(is_array($arSubItem['DETAIL_PAGE_URL'])){
															if(isset($arSubItem['CANONICAL_PAGE_URL']) && !empty($arSubItem['CANONICAL_PAGE_URL'])){
																$arSubItem['DETAIL_PAGE_URL'] = $arSubItem['CANONICAL_PAGE_URL'];
															}
															else{
																$arSubItem['DETAIL_PAGE_URL'] = $arSubItem['DETAIL_PAGE_URL'][key($arSubItem['DETAIL_PAGE_URL'])];
															}
														}
														?>
														<?$bDetailLink = $arParams['SHOW_DETAIL_LINK'] != 'N' && (!strlen($arSubItem['DETAIL_TEXT']) ? ($arParams['HIDE_LINK_WHEN_NO_DETAIL'] !== 'Y' && $arParams['HIDE_LINK_WHEN_NO_DETAIL'] != 1) : true);?>
														<?if ($bDetailLink):?>
															<a href="<?=($arSubItem['SECTION_PAGE_URL'] ? $arSubItem['SECTION_PAGE_URL'] : $arSubItem['DETAIL_PAGE_URL'] );?>"><span><?=$arSubItem['NAME']?></span></a>
														<?else:?>
															<span><?=$arSubItem['NAME']?></span>
														<?endif;?>
													</li>
												<?endforeach;?>
											</ul>
								</div>
							<?endif;?>
							
							<?// section preview text?>
							<?if(strlen($arItem['UF_TOP_SEO']) && $arParams['SHOW_SECTION_PREVIEW_DESCRIPTION'] != 'N'):?>
								<div class="preview_text">
									<?=$arItem['UF_TOP_SEO']?>
								</div>
							<?endif;?>
						</div>
					</div>
				</div>
			<?endforeach;?>
		</div>
	</div>
<?endif;?>