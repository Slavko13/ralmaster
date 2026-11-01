<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$this->setFrameMode(true);
?>

<?if ($arResult['RM_PROJECT_PRODUCTS']):?>
	<div class="rm-projects-product-grid">
		<?foreach ($arResult['RM_PROJECT_PRODUCTS'] as $rmProduct):?>
			<?
			$rmButtons = CIBlock::GetPanelButtons($rmProduct['IBLOCK_ID'], 0, $rmProduct['ID'], array('SESSID' => false, 'CATALOG' => true));
			$this->AddEditAction($rmProduct['ID'], $rmButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($rmProduct['IBLOCK_ID'], 'SECTION_EDIT'));
			$this->AddDeleteAction($rmProduct['ID'], $rmButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($rmProduct['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<a class="rm-projects-product-card" href="<?=htmlspecialcharsbx($rmProduct['URL'])?>" id="<?=$this->GetEditAreaId($rmProduct['ID'])?>" data-product-code="<?=htmlspecialcharsbx($rmProduct['CODE'])?>">
				<span class="rm-projects-product-card__header"><strong><?=$rmProduct['NAME']?></strong><span aria-hidden="true">↗</span></span>
				<img src="<?=htmlspecialcharsbx($rmProduct['IMAGE'])?>" alt="" loading="lazy">
				<span class="rm-projects-product-card__description"><?=$rmProduct['DESCRIPTION']?></span>
			</a>
		<?endforeach;?>
	</div>
<?else:?>
	<div class="rm-projects-empty" role="status">Категории продукции временно недоступны.</div>
<?endif;?>
