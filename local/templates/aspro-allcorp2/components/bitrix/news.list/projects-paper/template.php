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
			$rmProjectImageSmall = (string)($rmProject['RM_PROJECT']['IMAGE_SMALL'] ?? '');
			?>
			<article class="rm-project-card" id="<?=$this->GetEditAreaId($rmProject['ID'])?>" data-project-code="<?=htmlspecialcharsbx((string)$rmProject['CODE'])?>">
				<div class="rm-project-card__body">
					<div class="rm-project-card__summary">
						<h2><a href="<?=htmlspecialcharsbx($rmProject['DETAIL_PAGE_URL'])?>"><?=htmlspecialcharsbx($rmProject['RM_PROJECT']['NAME'])?></a></h2>
						<p><?=htmlspecialcharsbx($rmProject['RM_PROJECT']['DESCRIPTION'])?></p>
						<div class="rm-project-card__meta">
							<?if ($rmProject['RM_PROJECT']['YEAR']):?><span><?=htmlspecialcharsbx($rmProject['RM_PROJECT']['YEAR'])?></span><?endif;?>
							<?if ($rmProject['RM_PROJECT']['YEAR'] && $rmProject['RM_PROJECT']['TYPE']):?><i aria-hidden="true"></i><?endif;?>
							<?if ($rmProject['RM_PROJECT']['TYPE']):?><span><?=htmlspecialcharsbx($rmProject['RM_PROJECT']['TYPE'])?></span><?endif;?>
						</div>
					</div>
					<a class="rm-project-card__link" href="<?=htmlspecialcharsbx($rmProject['DETAIL_PAGE_URL'])?>">Подробнее о заказе <span aria-hidden="true"><span class="rm-ui-arrow"></span></span></a>
				</div>
				<a class="rm-project-card__image" href="<?=htmlspecialcharsbx($rmProject['DETAIL_PAGE_URL'])?>" aria-label="Открыть проект <?=htmlspecialcharsbx($rmProject['RM_PROJECT']['NAME'])?>">
					<?if ($rmProjectImage):?>
						<img src="<?=htmlspecialcharsbx($rmProjectImage)?>"<?if ($rmProjectImageSmall):?> srcset="<?=htmlspecialcharsbx($rmProjectImageSmall)?> 768w, <?=htmlspecialcharsbx($rmProjectImage)?> 1920w" sizes="(max-width: 767px) calc(100vw - 32px), 50vw"<?endif;?> alt="<?=htmlspecialcharsbx($rmProject['RM_PROJECT']['NAME'])?>"<?=$rmIndex ? ' loading="lazy"' : ' fetchpriority="high"'?>>
					<?else:?>
						<span class="rm-project-card__placeholder" aria-hidden="true"></span>
					<?endif;?>
				</a>
			</article>
		<?endforeach;?>
	</div>
<?else:?>
	<div class="rm-projects-empty" role="status">Проекты временно недоступны.</div>
<?endif;?>
