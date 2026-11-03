<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?
global $APPLICATION;
$rmCurrentPage = $APPLICATION->GetCurPage(false);
$rmServicesSubmenuId = 'rm-services-submenu-'.$this->randString(8);
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
					<?=$rmIsServices ? 'aria-expanded="false" aria-controls="'.htmlspecialcharsbx($rmServicesSubmenuId).'"' : ''?>
					<?=$rmItemSelected ? 'aria-current="page"' : ''?>
				><?=htmlspecialcharsbx($arItem['TEXT'])?><?=$rmIsServices ? '<span class="rm-flat-menu__chevron" aria-hidden="true"></span>' : ''?></a>
				<?if ($rmIsServices):?>
					<ul class="rm-flat-menu__submenu" id="<?=htmlspecialcharsbx($rmServicesSubmenuId)?>" aria-label="Услуги">
						<li><a href="/services/"<?=strpos($rmCurrentPage, '/services/') === 0 ? ' aria-current="page"' : ''?>>Металлообработка</a></li>
						<li><a href="/painting/"<?=strpos($rmCurrentPage, '/painting/') === 0 ? ' aria-current="page"' : ''?>>Порошковая окраска</a></li>
					</ul>
				<?endif;?>
			</li>
		<?endforeach;?>
	</ul>
	<script>
	(function () {
		'use strict';
		function initMobileServicesMenu() {
			var item = document.querySelector('#mobilemenu .rm-flat-menu__item--services');
			if (!item || item.getAttribute('data-rm-services-ready') === 'true') return;
			var trigger = item.querySelector(':scope > .rm-flat-menu__link');
			if (!trigger) return;
			item.setAttribute('data-rm-services-ready', 'true');
			trigger.addEventListener('click', function (event) {
				if (!window.matchMedia('(max-width: 991px)').matches) return;
				event.preventDefault();
				var expanded = item.classList.toggle('is-open');
				trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');
			});
		}
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', initMobileServicesMenu, {once: true});
		} else {
			initMobileServicesMenu();
		}
	})();
	</script>
<?endif;?>
