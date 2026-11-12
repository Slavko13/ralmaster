<?
$rmMobileCartActive = CSite::InDir(SITE_DIR.'cart/');
$rmMobileBasketCount = 0;
if (class_exists('CAllcorp2')) {
	foreach ((array)CAllcorp2::getBasketItems() as $rmMobileBasketItem) {
		$rmMobileBasketCount += max(1, (int)($rmMobileBasketItem['QUANTITY'] ?? 1));
	}
}
?>
<div class="mobileheader-v1 rm-mobile-header">
	<button class="burger pull-left" type="button" aria-label="Открыть меню" aria-controls="mobilemenu" aria-expanded="false">
		<?=CAllcorp2::showIconSvg("burger dark", SITE_TEMPLATE_PATH."/images/svg/Burger_big_white.svg");?>
		<?=CAllcorp2::showIconSvg("close dark", SITE_TEMPLATE_PATH."/images/svg/Close.svg");?>
	</button>
	<div class="logo-block pull-left rm-mobile-header__logo">
		<a href="<?=SITE_DIR?>" aria-label="РАЛМАСТЕР — на главную">
			<img src="<?=SITE_TEMPLATE_PATH?>/images/ralmaster/ralmaster-logo-dark.svg" width="152" height="24" alt="РАЛМАСТЕР">
		</a>
	</div>
	<div class="right-icons pull-right">
		<div class="pull-right">
			<div class="wrap_icon wrap_basket basket rm-mobile-basket<?=$rmMobileCartActive ? ' is-active' : ''?>">
				<a rel="nofollow" href="<?=SITE_DIR?>cart/" class="basket-link" aria-label="Корзина<?=$rmMobileBasketCount > 0 ? ': '.$rmMobileBasketCount : ''?>"<?=$rmMobileCartActive ? ' aria-current="page"' : ''?>>
					<svg class="rm-header__basket-icon" viewBox="0 0 17 16" aria-hidden="true"><path d="M1500,66.344l-0.63.656h-10.62l-0.75-.781L1486.85,59H1485a1,1,0,1,1,0-2h2c0.26,0,.52.006,0.52.006a1.048,1.048,0,0,1,1.15.763L1489.16,60h11.34l0.5,0.5V62ZM1489.37,62l0.41,3h8.5l0.72-3h-9.63Zm1.13,6a2.5,2.5,0,1,1-2.5,2.5A2.5,2.5,0,0,1,1490.5,68Zm-0.5,3h1V70h-1v1Zm7.5-3a2.5,2.5,0,1,1-2.5,2.5A2.5,2.5,0,0,1,1497.5,68Zm-0.5,3h1V70h-1v1Z" transform="translate(-1484 -57)"/></svg>
					<span class="count<?=$rmMobileBasketCount > 0 ? '' : ' empted'?>"><?=$rmMobileBasketCount?></span>
				</a>
			</div>
		</div>
	</div>
</div>

<script>
(function () {
	function initRalmasterMobileMenuA11y() {
		var menu = document.getElementById('mobilemenu');
		var trigger = document.querySelector('#mobileheader .burger');
		if (!menu || !trigger || trigger.getAttribute('data-rm-a11y-ready') === 'true') return;

		function syncState() {
			var isOpen = menu.classList.contains('show');
			trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			trigger.setAttribute('aria-label', isOpen ? 'Закрыть меню' : 'Открыть меню');
		}

		trigger.setAttribute('data-rm-a11y-ready', 'true');
		new MutationObserver(syncState).observe(menu, {attributes: true, attributeFilter: ['class']});
		syncState();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initRalmasterMobileMenuA11y, {once: true});
	} else {
		initRalmasterMobileMenuA11y();
	}
})();
</script>
