<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?foreach($arResult['SECTIONS']['SMALL']['ITEMS'] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
		<div class="item_inner">
			<?$isUrl=(strlen($arItem['PROPERTY_LINK_VALUE']) ? true : false);?>
			<?if($isUrl):?>
				<a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="opacity_block1 dark_block_animate" title="<?=$arItem['NAME'];?>"></a>
			<?endif;?>
			<div class="wrap_tizer">
				<div class="wrapper_inner_tizer">
					<div class="wr_block">
						<span class="wrap_outer title">
							<?if($isUrl):?>
								<a class="outer_text" href="<?=$arItem['PROPERTY_LINK_VALUE']?>">
							<?else:?>
								<span class="outer_text">
							<?endif;?>
								<span class="inner_text">
									<?=$arItem['NAME'];?>
								</span>
							<?if($isUrl):?>
								</a>
							<?else:?>
								</span>
							<?endif;?>
						</span>
					</div>
					<?if($arItem['PREVIEW_TEXT']):?>
						<div class="preview"><?=$arItem['PREVIEW_TEXT'];?></div>
					<?endif;?>
				</div>
			</div>
			<?if($arItem['PREVIEW_PICTURE']):?>
				<div class="scale_block_animate img_block" style="background-image:url('<?=$arItem['PREVIEW_PICTURE']?>')"></div>
			<?endif;?>
		</div>
	</div>
<?endforeach;?>