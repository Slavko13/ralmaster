<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<?if($arResult['ITEMS']):?>
	<?if($arParams['SHOW_TITLE'] == 'Y'):?>
		<div class="title-tab-heading visible-xs"><?=$arParams['T_TITLE'];?></div>
	<?endif;?>
	<div class="item-views catalog sections list-item">
		<div class="items row margin0 row_block flexbox">
			<?foreach($arResult['ITEMS'] as $arItem):?>
				<?
				// edit/add/delete buttons for edit mode
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				// preview picture
				if($bShowSectionImage = in_array('PREVIEW_PICTURE', $arParams['FIELD_CODE'])){
					$bImage = !empty($arItem['PREVIEW_PICTURE']);
					$arSectionImage = ($bImage ? CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width' => 254, 'height' => 254), BX_RESIZE_IMAGE_PROPORTIONAL, true) : array());
					$imageSectionSrc = ($bImage ? $arSectionImage['src'] : SITE_TEMPLATE_PATH.'/images/noimage_sections.png');
				}
				?>
				<div class="col-md-6 col-sm-12">
					<div class="item <?=($bShowSectionImage ? '' : ' wti')?>  slice-item <?=$arParams['IMAGE_CATALOG_POSITION'];?>" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
						<?// icon or preview picture?>
						<?if($bShowSectionImage):?>
							<div class="image">
								<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
									<img src="<?=$imageSectionSrc?>" alt="<?=( $arItem['PREVIEW_PICTURE']['ALT'] ? $arItem['PREVIEW_PICTURE']['ALT'] : $arItem['NAME']);?>" title="<?=( $arItem['PREVIEW_PICTURE']['TITLE'] ? $arItem['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME']);?>" class="img-responsive" />
								</a>
							</div>
						<?endif;?>
						
						<div class="info">
							<?// section name?>
							<?if(in_array('NAME', $arParams['FIELD_CODE'])):?>
								<div class="title">
									<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="dark-color">
										<?=$arItem['NAME']?>
									</a>
								</div>
							<?endif;?>
							
							<?// section preview text?>
							<?if(strlen($arItem['PREVIEW_TEXT'])):?>
								<div class="text hidden-text-block">
									<?=$arItem['PREVIEW_TEXT']?>
								</div>
							<?endif;?>
						</div>
					</div>
				</div>
			<?endforeach;?>
		</div>
	</div>
<?endif;?>