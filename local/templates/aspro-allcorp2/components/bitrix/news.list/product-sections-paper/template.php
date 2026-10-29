<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$this->setFrameMode(true);
?>

<?if ($arResult['PAPER_SECTIONS']):?>
	<div class="rm-product-grid" data-rm-product-count="<?=count($arResult['PAPER_SECTIONS'])?>">
		<?foreach ($arResult['PAPER_SECTIONS'] as $rmIndex => $rmProduct):?>
			<?
			$rmButtons = CIBlock::GetPanelButtons($rmProduct['IBLOCK_ID'], 0, $rmProduct['ID'], array('SESSID' => false, 'CATALOG' => true));
			$this->AddEditAction($rmProduct['ID'], $rmButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($rmProduct['IBLOCK_ID'], 'SECTION_EDIT'));
			$this->AddDeleteAction($rmProduct['ID'], $rmButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($rmProduct['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<a class="rm-product-card<?=$rmIndex < 3 ? ' rm-product-card--large' : ' rm-product-card--small'?>" href="<?=htmlspecialcharsbx($rmProduct['URL'])?>" id="<?=$this->GetEditAreaId($rmProduct['ID'])?>">
				<span class="rm-product-card__header">
					<strong><?=$rmProduct['NAME']?></strong>
					<span class="rm-product-card__arrow" aria-hidden="true">↗</span>
				</span>
				<img class="rm-product-card__image" src="<?=htmlspecialcharsbx($rmProduct['IMAGE'])?>" alt=""<?=$rmIndex > 2 ? ' loading="lazy"' : ''?>>
				<span class="rm-product-card__description"><?=$rmProduct['DESCRIPTION']?></span>
			</a>
		<?endforeach;?>
	</div>
<?else:?>
	<div class="rm-product-grid__empty" role="status">Каталог временно недоступен.</div>
<?endif;?>
