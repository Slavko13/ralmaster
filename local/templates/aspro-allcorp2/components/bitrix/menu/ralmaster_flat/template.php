<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>

<?if (!empty($arResult)):?>
	<ul class="rm-flat-menu">
		<?foreach ($arResult as $arItem):?>
			<li class="rm-flat-menu__item<?=$arItem['SELECTED'] ? ' active' : ''?>">
				<a
					class="rm-flat-menu__link"
					href="<?=htmlspecialcharsbx($arItem['LINK'])?>"
					<?=$arItem['SELECTED'] ? 'aria-current="page"' : ''?>
				><?=htmlspecialcharsbx($arItem['TEXT'])?></a>
			</li>
		<?endforeach;?>
	</ul>
<?endif;?>
