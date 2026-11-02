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
			$rmIsServices = ($arItem['LINK'] === '/services/');
			if ($rmIsServices && (strpos($rmCurrentPage, '/services/') === 0 || strpos($rmCurrentPage, '/painting/') === 0)) {
				$rmItemSelected = true;
			}
			if (strpos($rmCurrentPage, '/company/partners/') === 0 && $arItem['LINK'] === '/company/') {
				$rmItemSelected = false;
			}
			?>
			<li class="rm-flat-menu__item<?=$rmItemSelected ? ' active' : ''?><?=$rmIsServices ? ' rm-flat-menu__item--services' : ''?>">
				<a
					class="rm-flat-menu__link"
					href="<?=htmlspecialcharsbx($arItem['LINK'])?>"
					<?=$rmItemSelected ? 'aria-current="page"' : ''?>
				><?=htmlspecialcharsbx($arItem['TEXT'])?><?=$rmIsServices ? '<span class="rm-flat-menu__chevron" aria-hidden="true"></span>' : ''?></a>
				<?if ($rmIsServices):?>
					<ul class="rm-flat-menu__submenu" aria-label="Услуги">
						<li><a href="/services/"<?=strpos($rmCurrentPage, '/services/') === 0 ? ' aria-current="page"' : ''?>>Металлообработка</a></li>
						<li><a href="/painting/"<?=strpos($rmCurrentPage, '/painting/') === 0 ? ' aria-current="page"' : ''?>>Порошковая окраска</a></li>
					</ul>
				<?endif;?>
			</li>
		<?endforeach;?>
	</ul>
<?endif;?>
