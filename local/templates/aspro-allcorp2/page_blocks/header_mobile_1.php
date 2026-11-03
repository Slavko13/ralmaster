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
			<img src="<?=SITE_TEMPLATE_PATH?>/images/ralmaster/logo-dark@2x.png" width="152" height="24" alt="РАЛМАСТЕР">
		</a>
	</div>
	<div class="right-icons pull-right">
		<div class="pull-right">
			<div class="wrap_icon wrap_basket basket rm-mobile-basket<?=$rmMobileCartActive ? ' is-active' : ''?>">
				<a rel="nofollow" href="<?=SITE_DIR?>cart/" class="basket-link" aria-label="Корзина<?=$rmMobileBasketCount > 0 ? ': '.$rmMobileBasketCount : ''?>"<?=$rmMobileCartActive ? ' aria-current="page"' : ''?>>
					<svg class="rm-header__basket-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 4h2l2.1 9.8a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 7H6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="20" r="1.2" fill="currentColor"/><circle cx="18" cy="20" r="1.2" fill="currentColor"/></svg>
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
