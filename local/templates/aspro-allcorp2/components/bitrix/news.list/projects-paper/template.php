<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$this->setFrameMode(true);
?>

<?if ($arResult['ITEMS']):?>
	<div class="rm-projects-card-list" data-rm-project-count="<?=count($arResult['ITEMS'])?>">
		<?foreach ($arResult['ITEMS'] as $rmIndex => $rmProject):?>
			<?
			$rmButtons = CIBlock::GetPanelButtons($rmProject['IBLOCK_ID'], $rmProject['ID'], 0, array('SESSID' => false, 'CATALOG' => true));
			$this->AddEditAction($rmProject['ID'], $rmButtons['edit']['edit_element']['ACTION_URL'], CIBlock::GetArrayByID($rmProject['IBLOCK_ID'], 'ELEMENT_EDIT'));
			$this->AddDeleteAction($rmProject['ID'], $rmButtons['edit']['delete_element']['ACTION_URL'], CIBlock::GetArrayByID($rmProject['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			$rmProjectImage = (string)($rmProject['RM_PROJECT']['IMAGE'] ?? '');
			?>
			<article class="rm-project-card<?=$rmIndex % 2 ? ' rm-project-card--reverse' : ''?>" id="<?=$this->GetEditAreaId($rmProject['ID'])?>" data-project-code="<?=htmlspecialcharsbx((string)$rmProject['CODE'])?>">
				<a class="rm-project-card__image" href="<?=htmlspecialcharsbx($rmProject['DETAIL_PAGE_URL'])?>" aria-label="Открыть проект <?=htmlspecialcharsbx($rmProject['NAME'])?>">
					<?if ($rmProjectImage):?>
						<img src="<?=htmlspecialcharsbx($rmProjectImage)?>" alt="<?=htmlspecialcharsbx($rmProject['NAME'])?>"<?=$rmIndex ? ' loading="lazy"' : ''?>>
					<?else:?>
						<span class="rm-project-card__placeholder" aria-hidden="true"></span>
					<?endif;?>
				</a>
				<div class="rm-project-card__body">
					<span class="rm-project-card__number"><?=str_pad((string)($rmIndex + 1), 2, '0', STR_PAD_LEFT)?></span>
					<h2><a href="<?=htmlspecialcharsbx($rmProject['DETAIL_PAGE_URL'])?>"><?=$rmProject['NAME']?></a></h2>
					<p><?=htmlspecialcharsbx($rmProject['RM_PROJECT']['DESCRIPTION'])?></p>
					<a class="rm-project-card__link" href="<?=htmlspecialcharsbx($rmProject['DETAIL_PAGE_URL'])?>">Подробнее о проекте <span aria-hidden="true">↗</span></a>
				</div>
			</article>
		<?endforeach;?>
	</div>
<?else:?>
	<div class="rm-projects-empty" role="status">Проекты временно недоступны.</div>
<?endif;?>
