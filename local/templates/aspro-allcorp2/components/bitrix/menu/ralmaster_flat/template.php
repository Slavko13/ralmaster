<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?
global $APPLICATION;
$rmCurrentPage = $APPLICATION->GetCurPage(false);
?>

<?if (!empty($arResult)):?>
	<ul class="rm-flat-menu">
		<?foreach ($arResult as $arItem):?>
			<?
			$rmItemSelected = (bool)$arItem['SELECTED'];
			if (strpos($rmCurrentPage, '/company/partners/') === 0 && $arItem['LINK'] === '/company/') {
				$rmItemSelected = false;
			}
			?>
			<li class="rm-flat-menu__item<?=$rmItemSelected ? ' active' : ''?>">
				<a
					class="rm-flat-menu__link"
					href="<?=htmlspecialcharsbx($arItem['LINK'])?>"
					<?=$rmItemSelected ? 'aria-current="page"' : ''?>
				><?=htmlspecialcharsbx($arItem['TEXT'])?></a>
			</li>
		<?endforeach;?>
	</ul>
<?endif;?>
